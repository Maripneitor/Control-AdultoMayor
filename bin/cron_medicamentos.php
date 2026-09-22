<?php
declare(strict_types=1);

/**
 * VitalSenior - Cron Job de Automatización de Medicamentos y Alertas Clínicas
 * Ejecución vía CLI / Cron / Windows Task Scheduler:
 * php bin/cron_medicamentos.php
 */

// Asegurar ejecución en zona horaria adecuada y memoria suficiente
ini_set('memory_limit', '128M');
set_time_limit(300);

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

// Autoloader PSR-4 para ejecución CLI
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) { return; }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) { require_once $file; }
});

use Config\Database;
use App\Services\TwilioService;
use App\Services\EmailNotificationService;

$inicioEjecucion = microtime(true);
$fechaHoraUtc = gmdate('Y-m-d H:i:s');
echo "===========================================================\n";
echo " VitalSenior Cron Medicamentos & Alertas - Inicio: {$fechaHoraUtc} UTC\n";
echo "===========================================================\n";

$twilioService = new TwilioService();
$totalOmitidas = 0;
$totalAlertasEnviadas = 0;
$totalAlertasFallidas = 0;

try {
    $db = Database::getInstance()->getConnection();

    // =========================================================================
    // 1. INACTIVIDAD: DETECCIÓN Y BLOQUEO DE TOMAS RETRASADAS (>60 MIN)
    // =========================================================================
    $db->beginTransaction();

    $stmtOmitidas = $db->prepare("
        SELECT th.id, th.paciente_id, th.medicamento_id, m.nombre_comercial, 
               p.nombre, p.apellidos, th.fecha_programada, th.hora_programada
        FROM tomas_historial th
        JOIN medicamentos m ON th.medicamento_id = m.id
        JOIN pacientes p ON th.paciente_id = p.id
        WHERE th.estado = 'pendiente'
          AND th.notificacion_enviada_at IS NULL
          AND (
              th.fecha_programada < CURRENT_DATE 
              OR (th.fecha_programada = CURRENT_DATE AND (th.hora_programada + interval '60 minutes') < CURRENT_TIME)
          )
        ORDER BY th.fecha_programada ASC, th.hora_programada ASC
        FOR UPDATE OF th SKIP LOCKED
    ");
    $stmtOmitidas->execute();
    $tomasAtrasadas = $stmtOmitidas->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($tomasAtrasadas)) {
        $updateTomaStmt = $db->prepare("
            UPDATE tomas_historial 
            SET estado = 'omitido', 
                nota_incidencia = 'Marcado automáticamente por inactividad (>60 min)',
                notificacion_enviada_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $auditLogStmt = $db->prepare("
            INSERT INTO logs_auditoria (paciente_id, accion, detalles, fecha_utc)
            VALUES (:pid, :accion, :detalles, CURRENT_TIMESTAMP)
        ");

        foreach ($tomasAtrasadas as $toma) {
            $totalOmitidas++;
            $tomaId = (int)$toma['id'];
            $pacienteId = (int)$toma['paciente_id'];
            $nombrePaciente = trim("{$toma['nombre']} {$toma['apellidos']}");
            $medicamento = $toma['nombre_comercial'];
            $horaProgramada = substr((string)$toma['hora_programada'], 0, 5);

            // Actualizar estado de toma en base de datos
            $updateTomaStmt->execute([':id' => $tomaId]);

            echo "[TOMA OMITIDA #{$tomaId}] Paciente: {$nombrePaciente} | Fármaco: {$medicamento} | Hora: {$horaProgramada}\n";

            // Buscar contactos prioritarios de emergencia del paciente
            $stmtContactos = $db->prepare("
                SELECT nombre, telefono, email, es_prioritario 
                FROM contactos_emergencia 
                WHERE paciente_id = :pid 
                ORDER BY es_prioritario DESC, id ASC
            ");
            $stmtContactos->execute([':pid' => $pacienteId]);
            $contactos = $stmtContactos->fetchAll(PDO::FETCH_ASSOC);

            // Si no tiene contactos directos en contactos_emergencia, buscar familiares/cuidadores vinculados
            if (empty($contactos)) {
                $stmtCuidadores = $db->prepare("
                    SELECT u.nombre, u.email, pc.parentesco, NULL as telefono, TRUE as es_prioritario
                    FROM paciente_cuidador pc
                    JOIN usuarios u ON pc.usuario_cuidador_id = u.id
                    WHERE pc.paciente_id = :pid
                ");
                $stmtCuidadores->execute([':pid' => $pacienteId]);
                $contactos = $stmtCuidadores->fetchAll(PDO::FETCH_ASSOC);
            }

            $enviadoExitoso = false;
            $canalesNotificados = [];

            foreach ($contactos as $contacto) {
                $tel = trim((string)($contacto['telefono'] ?? ''));
                $email = trim((string)($contacto['email'] ?? ''));
                $contactoNombre = $contacto['nombre'] ?? 'Contacto de Emergencia';

                // Disparo por WhatsApp / SMS si hay teléfono disponible
                if (!empty($tel)) {
                    $twilioRes = $twilioService->sendDosisOmitidaAlert($tel, $nombrePaciente, $medicamento, $horaProgramada);
                    if ($twilioRes['success']) {
                        $enviadoExitoso = true;
                        $canalesNotificados[] = "Twilio/WhatsApp a {$contactoNombre} ({$tel})";
                        echo "  -> Notificación WhatsApp/SMS despachada a {$contactoNombre} ({$tel})\n";
                    }
                }

                // Disparo por Correo Electrónico si hay email disponible
                if (!empty($email)) {
                    $emailRes = EmailNotificationService::enviarAlertaOmitida($email, $medicamento, $horaProgramada, $nombrePaciente);
                    if ($emailRes) {
                        $enviadoExitoso = true;
                        $canalesNotificados[] = "Email a {$contactoNombre} ({$email})";
                        echo "  -> Notificación Correo despachada a {$contactoNombre} ({$email})\n";
                    }
                }
            }

            // Registrar en Logs de Auditoría
            if ($enviadoExitoso) {
                $totalAlertasEnviadas++;
                $detallesLog = json_encode([
                    'toma_id' => $tomaId,
                    'medicamento' => $medicamento,
                    'hora_programada' => $horaProgramada,
                    'canales' => $canalesNotificados,
                    'estado' => 'ENVIADO'
                ], JSON_UNESCAPED_UNICODE);

                $auditLogStmt->execute([
                    ':pid' => $pacienteId,
                    ':accion' => 'ALERTA_DOSIS_OMITIDA_ENVIADA',
                    ':detalles' => $detallesLog
                ]);
            } else {
                $totalAlertasFallidas++;
                $detallesLog = json_encode([
                    'toma_id' => $tomaId,
                    'medicamento' => $medicamento,
                    'hora_programada' => $horaProgramada,
                    'motivo' => 'Sin contactos registrados o falla en proveedores',
                    'estado' => 'FALLIDO'
                ], JSON_UNESCAPED_UNICODE);

                $auditLogStmt->execute([
                    ':pid' => $pacienteId,
                    ':accion' => 'ALERTA_DOSIS_OMITIDA_FALLIDA',
                    ':detalles' => $detallesLog
                ]);
                echo "  [ADVERTENCIA] No fue posible notificar contactos para el paciente #{$pacienteId}\n";
            }
        }
    } else {
        echo "No hay tomas retrasadas pendientes de marcar.\n";
    }

    $db->commit();

    // =========================================================================
    // 2. DESABASTECIMIENTO PREDICTIVO DE INVENTARIO
    // =========================================================================
    echo "\n-----------------------------------------------------------\n";
    echo " Verificando Inventario Predictivo de Medicamentos...\n";
    echo "-----------------------------------------------------------\n";

    $stmtStock = $db->query("
        SELECT m.id, m.paciente_id, m.nombre_comercial, m.stock_actual, 
               m.dosis_por_toma, m.frecuencia_horas, m.stock_minimo_alerta,
               p.nombre, p.apellidos
        FROM medicamentos m
        JOIN pacientes p ON m.paciente_id = p.id
        WHERE m.activo = TRUE 
          AND m.stock_actual IS NOT NULL 
          AND m.dosis_por_toma > 0 
          AND m.frecuencia_horas > 0
    ");

    $medicamentos = $stmtStock->fetchAll(PDO::FETCH_ASSOC);
    $alertasStockCount = 0;

    foreach ($medicamentos as $med) {
        $tomasAlDia = floor(24 / max(1, (int)$med['frecuencia_horas']));
        $consumoDiario = $tomasAlDia * (int)$med['dosis_por_toma'];
        
        if ($consumoDiario > 0) {
            $diasRestantes = (float)$med['stock_actual'] / $consumoDiario;
            $stockMinimo = (int)($med['stock_minimo_alerta'] ?? 5);

            if ($diasRestantes < 4.0 || (int)$med['stock_actual'] <= $stockMinimo) {
                $alertasStockCount++;
                $pacienteNombre = trim("{$med['nombre']} {$med['apellidos']}");
                echo "  [ALERTA STOCK] {$med['nombre_comercial']} (Paciente: {$pacienteNombre}): Stock {$med['stock_actual']} uds (~" . round($diasRestantes, 1) . " días restantes)\n";
            }
        }
    }

    if ($alertasStockCount === 0) {
        echo "Inventarios dentro de umbrales normales.\n";
    }

} catch (\Throwable $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "\n[ERROR CRÍTICO EN CRON]: " . $e->getMessage() . "\n";
    error_log("[VitalSenior Cron Error] " . $e->getMessage());
}

$tiempoTotal = round((microtime(true) - $inicioEjecucion) * 1000, 2);
echo "\n===========================================================\n";
echo " RESUMEN OPERATIVO:\n";
echo " - Tomas Omitidas Procesadas: {$totalOmitidas}\n";
echo " - Alertas Externas Enviadas: {$totalAlertasEnviadas}\n";
echo " - Alertas Fallidas/Sin Canal: {$totalAlertasFallidas}\n";
echo " - Tiempo de Ejecución: {$tiempoTotal} ms\n";
echo "===========================================================\n";

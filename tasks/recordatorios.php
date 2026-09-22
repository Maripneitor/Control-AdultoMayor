<?php
declare(strict_types=1);

/**
 * VitalSenior - Script CLI de Tareas Programadas (Cron Job)
 * Ejecución recomendada: * * * * * php /ruta/Control-AdultoMayor/tasks/recordatorios.php >> /ruta/logs/cron.log 2>&1
 */

// 1. Validar entorno de ejecución (Solo CLI)
if (php_sapi_name() !== 'cli' && (!isset($_GET['secret']) || $_GET['secret'] !== getenv('CRON_WEB_SECRET'))) {
    http_response_code(403);
    echo "Acceso denegado. Este script solo puede ejecutarse desde la terminal (CLI).\n";
    exit(1);
}

// 2. Cargar dependencias de configuración y servicios
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Services/TwilioService.php';

use Config\Database;
use App\Services\TwilioService;

$inicioEjecucion = microtime(true);
$ahoraUtc = gmdate('Y-m-d H:i:s');
echo "===============================================================\n";
echo "[CRON VitalSenior] Inicio de barrido en UTC: {$ahoraUtc}\n";
echo "===============================================================\n";

try {
    $db = Database::getInstance()->getConnection();
    $twilio = new TwilioService();
    
    $notificacionesMedicamentos = 0;
    $notificacionesCitas = 0;
    $alertasOmision = 0;

    // =========================================================================
    // TAREA 1: Recordatorios de Medicación Inmediata con Agrupación (Polifarmacia)
    // =========================================================================
    // Busca tomas pendientes cuya hora programada (o pospuesta) haya llegado o esté en ventana de 5 minutos
    $queryMedicamentos = "
        SELECT 
            th.id AS toma_id,
            th.fecha_programada_utc,
            th.pospuesto_hasta,
            m.nombre_comercial,
            m.dosis,
            m.instrucciones,
            p.id AS paciente_id,
            p.nombre_completo AS paciente_nombre,
            p.zona_horaria,
            COALESCE(u_pac.telefono, u_fam.telefono) AS telefono_destino,
            COALESCE(u_pac.nombre_completo, u_fam.nombre_completo) AS destinatario_nombre
        FROM tomas_historial th
        JOIN medicamentos m ON th.medicamento_id = m.id
        JOIN pacientes p ON th.paciente_id = p.id
        LEFT JOIN usuarios u_pac ON p.usuario_id = u_pac.id AND u_pac.acepta_whatsapp = TRUE AND u_pac.activo = TRUE
        LEFT JOIN vinculaciones v ON p.id = v.paciente_id AND v.es_tutor_principal = TRUE
        LEFT JOIN usuarios u_fam ON v.familiar_id = u_fam.id AND u_fam.acepta_whatsapp = TRUE AND u_fam.activo = TRUE
        WHERE th.estado IN ('pendiente', 'pospuesta')
          AND th.recordatorio_enviado = FALSE
          AND (
              (th.pospuesto_hasta IS NOT NULL AND th.pospuesto_hasta <= (NOW() AT TIME ZONE 'UTC' + INTERVAL '5 minutes'))
              OR 
              (th.pospuesto_hasta IS NULL AND th.fecha_programada_utc <= (NOW() AT TIME ZONE 'UTC' + INTERVAL '5 minutes'))
          )
          AND th.fecha_programada_utc >= (NOW() AT TIME ZONE 'UTC' - INTERVAL '60 minutes')
        FOR UPDATE SKIP LOCKED
    ";

    $stmtMeds = $db->query($queryMedicamentos);
    $tomasPendientes = $stmtMeds->fetchAll(PDO::FETCH_ASSOC);

    // Agrupación por destinatario y paciente (Solución de Polifarmacia)
    $gruposPolifarmacia = [];
    foreach ($tomasPendientes as $toma) {
        $tel = trim((string)($toma['telefono_destino'] ?? ''));
        if (empty($tel)) {
            continue;
        }

        $claveGrupo = $tel . '_' . $toma['paciente_id'];
        if (!isset($gruposPolifarmacia[$claveGrupo])) {
            $gruposPolifarmacia[$claveGrupo] = [
                'telefono' => $tel,
                'paciente' => $toma['paciente_nombre'],
                'destinatario' => $toma['destinatario_nombre'],
                'tomas' => []
            ];
        }
        $gruposPolifarmacia[$claveGrupo]['tomas'][] = $toma;
    }

    // Despacho de un ÚNICO mensaje por paciente/horario
    foreach ($gruposPolifarmacia as $grupo) {
        $cantidad = count($grupo['tomas']);
        $paciente = $grupo['paciente'];
        $telefono = $grupo['telefono'];
        $destinatario = $grupo['destinatario'];

        if ($cantidad === 1) {
            $toma = $grupo['tomas'][0];
            $indicaciones = !empty($toma['instrucciones']) ? "\n   📝 Indicaciones: " . $toma['instrucciones'] : '';
            $cuerpo = "Hola *{$paciente}*, es hora de tu medicina:\n\n"
                    . "• *{$toma['nombre_comercial']}* ({$toma['dosis']}){$indicaciones}\n\n"
                    . "Por favor ingresa a la plataforma para marcarla como tomada o posponerla si es necesario.";
        } else {
            // Formato de lista amigable y legible para el adulto mayor
            $cuerpo = "Hola *{$paciente}*, es momento de tomar tus *{$cantidad} medicamentos* programados:\n\n";
            $emojis = ['1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟'];

            foreach ($grupo['tomas'] as $indice => $toma) {
                $icono = $emojis[$indice] ?? '👉';
                $indicaciones = !empty($toma['instrucciones']) ? "\n   📝 Indicaciones: " . $toma['instrucciones'] : '';
                $cuerpo .= "{$icono} *{$toma['nombre_comercial']}* ({$toma['dosis']}){$indicaciones}\n\n";
            }

            $cuerpo .= "Recuerda acompañarlos con un vaso con agua y marcar tus tomas en la plataforma al finalizar.";
        }

        $mensajeFinal = "💊 *Recordatorio de Medicación - VitalSenior*\n\n" . $cuerpo;

        // Disparo único vía WhatsApp con Twilio
        $resultado = $twilio->sendWhatsAppMessage($telefono, $mensajeFinal);

        if ($resultado['success']) {
            $tomaIds = array_column($grupo['tomas'], 'toma_id');
            // Actualizar en bloque todas las tomas del grupo
            $placeholders = implode(',', array_fill(0, count($tomaIds), '?'));
            $stmtUpdate = $db->prepare("
                UPDATE tomas_historial 
                SET recordatorio_enviado = TRUE, notificado_at = NOW() AT TIME ZONE 'UTC' 
                WHERE id IN ({$placeholders})
            ");
            $stmtUpdate->execute($tomaIds);

            $notificacionesMedicamentos += $cantidad;
            $nombresMeds = implode(', ', array_column($grupo['tomas'], 'nombre_comercial'));
            echo "  [OK POLIFARMACIA] 1 mensaje enviado a {$destinatario} ({$telefono}) cubriendo {$cantidad} medicamentos: [{$nombresMeds}]\n";
        } else {
            echo "  [ERROR POLIFARMACIA] Falló envío para grupo de {$paciente} ({$telefono}): " . ($resultado['error'] ?? 'Error desconocido') . "\n";
        }
    }

    // =========================================================================
    // TAREA 2: Recordatorios de Citas Médicas (Ventana de 2 Horas Previas)
    // =========================================================================
    $queryCitas = "
        SELECT 
            c.id AS cita_id,
            c.titulo,
            c.lugar,
            c.fecha_hora_utc,
            c.notas,
            p.nombre_completo AS paciente_nombre,
            d.nombre_completo AS doctor_nombre,
            d.especialidad,
            COALESCE(u_pac.telefono, u_fam.telefono) AS telefono_destino
        FROM citas c
        JOIN pacientes p ON c.paciente_id = p.id
        LEFT JOIN doctores d ON c.doctor_id = d.id
        LEFT JOIN usuarios u_pac ON p.usuario_id = u_pac.id AND u_pac.acepta_whatsapp = TRUE AND u_pac.activo = TRUE
        LEFT JOIN vinculaciones v ON p.id = v.paciente_id AND v.es_tutor_principal = TRUE
        LEFT JOIN usuarios u_fam ON v.familiar_id = u_fam.id AND u_fam.acepta_whatsapp = TRUE AND u_fam.activo = TRUE
        WHERE c.estado = 'programada'
          AND c.recordatorio_enviado = FALSE
          AND c.fecha_hora_utc <= (NOW() AT TIME ZONE 'UTC' + INTERVAL '2 hours')
          AND c.fecha_hora_utc >= (NOW() AT TIME ZONE 'UTC')
        FOR UPDATE SKIP LOCKED
    ";

    $stmtCitas = $db->query($queryCitas);
    $citasPendientes = $stmtCitas->fetchAll(PDO::FETCH_ASSOC);

    foreach ($citasPendientes as $cita) {
        if (empty($cita['telefono_destino'])) {
            continue;
        }

        $especialista = !empty($cita['doctor_nombre']) ? "\n👨‍⚕️ Especialista: Dr(a). {$cita['doctor_nombre']} ({$cita['especialidad']})" : '';
        $notas = !empty($cita['notas']) ? "\n📌 Notas: {$cita['notas']}" : '';

        $mensajeCita = "🗓️ *Recordatorio de Cita Médica - VitalSenior*\n\n"
                     . "Atención para *{$cita['paciente_nombre']}*:\n"
                     . "Tienes una cita programada en breve:\n"
                     . "🏥 *{$cita['titulo']}*\n"
                     . "📍 Lugar: {$cita['lugar']}"
                     . "{$especialista}{$notas}\n\n"
                     . "Te sugerimos llegar con 15 minutos de anticipación con tu expediente y carnet.";

        $resCita = $twilio->sendWhatsAppMessage($cita['telefono_destino'], $mensajeCita);

        if ($resCita['success']) {
            $stmtUpdCita = $db->prepare("
                UPDATE citas 
                SET recordatorio_enviado = TRUE, notificado_at = NOW() AT TIME ZONE 'UTC' 
                WHERE id = :id
            ");
            $stmtUpdCita->execute([':id' => $cita['cita_id']]);
            $notificacionesCitas++;
            echo "  [OK] Cita notificada a {$cita['telefono_destino']} - Cita: {$cita['titulo']}\n";
        }
    }

    // =========================================================================
    // TAREA 3: Escalamiento por Tomas Omitidas (>60 min sin confirmación)
    // =========================================================================
    // Si pasaron más de 60 minutos desde la hora programada y sigue pendiente, notifica al tutor familiar
    $queryOmisiones = "
        SELECT 
            th.id AS toma_id,
            m.nombre_comercial,
            p.nombre_completo AS paciente_nombre,
            u_fam.telefono AS familiar_telefono,
            u_fam.nombre_completo AS familiar_nombre
        FROM tomas_historial th
        JOIN medicamentos m ON th.medicamento_id = m.id
        JOIN pacientes p ON th.paciente_id = p.id
        JOIN vinculaciones v ON p.id = v.paciente_id AND v.es_tutor_principal = TRUE
        JOIN usuarios u_fam ON v.familiar_id = u_fam.id AND u_fam.acepta_whatsapp = TRUE AND u_fam.activo = TRUE
        WHERE th.estado = 'pendiente'
          AND th.fecha_programada_utc <= (NOW() AT TIME ZONE 'UTC' - INTERVAL '60 minutes')
          AND th.fecha_programada_utc >= (NOW() AT TIME ZONE 'UTC' - INTERVAL '120 minutes')
          AND th.recordatorio_enviado = TRUE
          AND th.nota_incidencia IS NULL
        FOR UPDATE SKIP LOCKED
    ";

    $stmtOmisiones = $db->query($queryOmisiones);
    $omisiones = $stmtOmisiones->fetchAll(PDO::FETCH_ASSOC);

    foreach ($omisiones as $omision) {
        if (empty($omision['familiar_telefono'])) {
            continue;
        }

        $mensajeTutor = "⚠️ *Alerta de Omisión de Medicamento - VitalSenior*\n\n"
                      . "Estimado(a) *{$omision['familiar_nombre']}*:\n"
                      . "El paciente *{$omision['paciente_nombre']}* tiene un retraso mayor a 1 hora en la toma de su medicamento:\n"
                      . "💊 *{$omision['nombre_comercial']}*\n\n"
                      . "Por favor comunícate con él/ella para verificar su estado de salud.";

        $resTutor = $twilio->sendWhatsAppMessage($omision['familiar_telefono'], $mensajeTutor);

        if ($resTutor['success']) {
            $stmtUpdOmision = $db->prepare("
                UPDATE tomas_historial 
                SET nota_incidencia = 'Alerta de retraso >60 min enviada al tutor familiar' 
                WHERE id = :id
            ");
            $stmtUpdOmision->execute([':id' => $omision['toma_id']]);
            $alertasOmision++;
            echo "  [ALERTA] Omisión notificada a tutor {$omision['familiar_nombre']} ({$omision['familiar_telefono']})\n";
        }
    }

    $duracion = round(microtime(true) - $inicioEjecucion, 3);
    echo "---------------------------------------------------------------\n";
    echo "[RESUMEN CRON] Finalizado en {$duracion}s | Meds: {$notificacionesMedicamentos} | Citas: {$notificacionesCitas} | Omisiones: {$alertasOmision}\n";
    echo "===============================================================\n\n";

} catch (Throwable $e) {
    error_log("[CRON ERROR FATAL] " . $e->getMessage());
    echo "[ERROR CRITICO] Ocurrió una excepción durante la ejecución del cron: " . $e->getMessage() . "\n";
    exit(1);
}

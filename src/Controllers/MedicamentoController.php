<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Medicamento;
use App\Models\Paciente;
use App\Core\Controller;

class MedicamentoController extends Controller {
    private Medicamento $medicamentoModel;
    private Paciente $pacienteModel;

    public function __construct() {
        $this->medicamentoModel = new Medicamento();
        $this->pacienteModel = new Paciente();
    }

    public function index(): void {
        \App\Middlewares\AuthMiddleware::handle();
        $userId = $_SESSION['user_id'] ?? null;
        
        // 1. Obtener ID del paciente activo
        $pacienteId = $_GET['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? null);
        if (!$pacienteId) {
            $paciente = $this->pacienteModel->findByUserId($userId);
            $pacienteId = $paciente['id'] ?? null;
        } else {
            $paciente = $this->pacienteModel->find($pacienteId);
        }

        // 2. Aislamiento Multitenant con ofuscación 404
        $acceso = \App\Middlewares\AuthMiddleware::requirePatientAccess($pacienteId);
        $puedeEditar = $acceso['puede_editar']; // true solo para familiar con 'edicion_completa' o admin
        
        $medicamentos = $this->medicamentoModel->getAllByPacienteId($pacienteId);
        
        // Calcular adherencia
        $db = \Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) FILTER (WHERE estado = 'tomada') as tomadas,
                COUNT(*) FILTER (WHERE estado IN ('tomada', 'omitido')) as total_evaluadas
            FROM tomas_historial
            WHERE paciente_id = :pid
        ");
        $stmt->execute([':pid' => $pacienteId]);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
        $adherencia = $stats['total_evaluadas'] > 0 
            ? round(($stats['tomadas'] / $stats['total_evaluadas']) * 100) 
            : 100;

        // 3. Renderizado condicional en la vista pasando el booleano
        $this->render('medicamentos/index', [
            'paciente' => $paciente,
            'medicamentos' => $medicamentos,
            'adherencia' => $adherencia,
            'puedeEditar' => $puedeEditar,
            'nivelAcceso' => $acceso['nivel_acceso']
        ], false);
    }

    public function controlTomas(): void {
        \App\Middlewares\AuthMiddleware::handle();
        $userId = $_SESSION['user_id'] ?? null;
        $pacienteId = $_GET['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? null);
        
        if (!$pacienteId) {
            $paciente = $this->pacienteModel->findByUserId($userId);
            $pacienteId = $paciente['id'] ?? null;
        } else {
            $paciente = $this->pacienteModel->find($pacienteId);
        }

        // Aislamiento Multitenant con ofuscación 404
        $acceso = \App\Middlewares\AuthMiddleware::requirePatientAccess($pacienteId);
        $puedeEditar = $acceso['puede_editar'];

        $this->medicamentoModel->generarTomasDiarias($pacienteId);
        $tomasDelDia = $this->medicamentoModel->getTodayDoses($pacienteId);

        require_once __DIR__ . '/../Views/medicamentos/control_tomas.php';
    }

    public function store(): void {
        \App\Middlewares\AuthMiddleware::requirePermission('gestionar_medicamentos');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);

        $nombre = trim($_POST['nombre_comercial'] ?? '');
        $dosis = trim($_POST['dosis'] ?? '');
        $frecuenciaHoras = (int)($_POST['frecuencia_horas'] ?? 8);
        $instrucciones = trim($_POST['instrucciones'] ?? '');

        if (!empty($nombre) && !empty($dosis) && $frecuenciaHoras > 0) {
            $this->medicamentoModel->agregar([
                'paciente_id' => (int)$paciente['id'],
                'nombre_comercial' => $nombre,
                'dosis' => $dosis,
                'frecuencia_horas' => $frecuenciaHoras,
                'instrucciones' => $instrucciones
            ]);
            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('MEDICAMENTO_REGISTRADO', "Medicamento $nombre registrado para paciente {$paciente['id']}.");
            $this->setFlash('success', 'Medicamento registrado exitosamente.');
        } else {
            $this->setFlash('danger', 'Por favor completa todos los campos correctamente.');
        }

        header('Location: ' . APP_URL . '/medicamentos');
        exit;
    }

    public function marcarToma(): void {
        \App\Middlewares\AuthMiddleware::requirePermission('marcar_tomas');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);
        
        $registroId = (int)($_POST['registro_id'] ?? 0);
        
        if ($registroId > 0 && $paciente) {
            try {
                $db = \Config\Database::getInstance()->getConnection();
                $db->beginTransaction();

                $stmt = $db->prepare("SELECT estado, medicamento_id, hora_programada, fecha_programada FROM tomas_historial WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $registroId]);
                $toma = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($toma && $toma['estado'] !== 'tomada') {
                    $nota = null;
                    if (!empty($toma['hora_programada'])) {
                        $horaProgramada = strtotime($toma['fecha_programada'] . ' ' . $toma['hora_programada']);
                        $ahora = time();
                        $diff = ($ahora - $horaProgramada) / 60;
                        if ($diff > 45) {
                            $nota = 'Tomada con retraso (' . round($diff) . ' min)';
                        } elseif ($diff < -45) {
                            $nota = 'Tomada con antelación (' . round(abs($diff)) . ' min)';
                        }
                    }

                    $this->medicamentoModel->actualizarEstadoToma($registroId, (int)$paciente['id'], 'tomada', $userId, $nota);

                    $medId = (int)$toma['medicamento_id'];
                    $stmtMed = $db->prepare("SELECT stock_actual, dosis_por_toma, stock_minimo_alerta FROM medicamentos WHERE id = :mid FOR UPDATE");
                    $stmtMed->execute(['mid' => $medId]);
                    $medicamento = $stmtMed->fetch(\PDO::FETCH_ASSOC);

                    if ($medicamento) {
                        $nuevoStock = max(0, $medicamento['stock_actual'] - $medicamento['dosis_por_toma']);
                        $stmtUpdateMed = $db->prepare("UPDATE medicamentos SET stock_actual = :stock WHERE id = :mid");
                        $stmtUpdateMed->execute(['stock' => $nuevoStock, 'mid' => $medId]);

                        if ($nuevoStock <= $medicamento['stock_minimo_alerta']) {
                            $_SESSION['flash_inventory_alert'] = "Te quedan dosis limitadas para este medicamento. Recuerda surtir tu receta.";
                        }
                    }

                    $db->commit();
                    $usuarioModel = new \App\Models\Usuario();
                    $usuarioModel->registrarAuditoria('TOMA_MARCADA', "Toma $registroId marcada como tomada.");
                    $this->setFlash('success', 'Toma registrada correctamente.');
                } else {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma ya estaba registrada o no existe.');
                }

            } catch (\Exception $e) {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
                $this->setFlash('danger', 'Error al registrar la toma: ' . $e->getMessage());
            }
        }

        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }

    /**
     * POST /medicamentos/posponer
     * Aplica la regla clínica de prevención de sobredosis:
     * La nueva hora pospuesta (+30 min) NO debe quedar a menos de 2 horas de la siguiente dosis programada.
     */
    public function posponerToma(): void {
        \App\Middlewares\AuthMiddleware::requirePermission('marcar_tomas');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);
        $registroId = trim($_POST['registro_id'] ?? '');

        if (!empty($registroId) && $paciente) {
            try {
                $db = \Config\Database::getInstance()->getConnection();
                $db->beginTransaction();

                // 1. Obtener la toma actual
                $stmt = $db->prepare(
                    "SELECT id, medicamento_id, paciente_id, fecha_programada_utc, pospuesto_hasta, estado 
                     FROM tomas_historial 
                     WHERE id = :id FOR UPDATE"
                );
                $stmt->execute([':id' => $registroId]);
                $toma = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$toma || !in_array($toma['estado'], ['pendiente', 'pospuesta'], true)) {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma no se encuentra pendiente para ser pospuesta.');
                    header('Location: ' . APP_URL . '/dashboard');
                    exit;
                }

                // 2. Determinar la hora base actual y la nueva hora pospuesta (+30 min) en UTC
                $horaBaseStr = $toma['pospuesto_hasta'] ?? $toma['fecha_programada_utc'];
                $horaBaseUtc = new \DateTime($horaBaseStr, new \DateTimeZone('UTC'));
                
                // Si la hora base ya quedó en el pasado, posponer desde el instante actual
                $ahoraUtc = new \DateTime('now', new \DateTimeZone('UTC'));
                if ($horaBaseUtc < $ahoraUtc) {
                    $horaBaseUtc = clone $ahoraUtc;
                }

                $nuevaHoraPospuesta = (clone $horaBaseUtc)->modify('+30 minutes');

                // 3. REGLA CLÍNICA DE PREVENCIÓN DE SOBREDOSIS:
                // Buscar la siguiente toma programada del MISMO medicamento para el MISMO paciente
                $stmtNext = $db->prepare(
                    "SELECT id, fecha_programada_utc 
                     FROM tomas_historial 
                     WHERE medicamento_id = :mid 
                       AND paciente_id = :pid 
                       AND id != :current_id
                       AND fecha_programada_utc > :hora_referencia
                       AND estado IN ('pendiente', 'pospuesta')
                     ORDER BY fecha_programada_utc ASC 
                     LIMIT 1"
                );
                $stmtNext->execute([
                    ':mid' => $toma['medicamento_id'],
                    ':pid' => $toma['paciente_id'],
                    ':current_id' => $toma['id'],
                    ':hora_referencia' => $horaBaseUtc->format('Y-m-d H:i:sP')
                ]);
                $siguienteToma = $stmtNext->fetch(\PDO::FETCH_ASSOC);

                if ($siguienteToma) {
                    $siguienteHoraUtc = new \DateTime($siguienteToma['fecha_programada_utc'], new \DateTimeZone('UTC'));
                    $margenSegundos = $siguienteHoraUtc->getTimestamp() - $nuevaHoraPospuesta->getTimestamp();
                    $margenMinutos = $margenSegundos / 60;

                    // Bloqueo estricto si el margen resultante es menor a 2 horas (120 minutos)
                    if ($margenMinutos < 120) {
                        $db->rollBack();
                        
                        // Convertir a formato legible en zona horaria
                        $horaProximaLegible = $siguienteHoraUtc->format('H:i');
                        $this->setFlash(
                            'danger', 
                            "⚠️ Bloqueo de Seguridad Clínica: No es posible posponer la toma 30 minutos más. Quedaría a menos de 2 horas (margen de " . round(max(0, $margenMinutos)) . " min) de tu próxima dosis programada de las {$horaProximaLegible} UTC, existiendo riesgo de sobredosis o acumulación tóxica. Por favor marca la dosis como tomada o consulta a tu médico."
                        );
                        header('Location: ' . APP_URL . '/dashboard');
                        exit;
                    }
                }

                // 4. Margen seguro validado: Actualizar estado y posponer
                $stmtUpdate = $db->prepare(
                    "UPDATE tomas_historial 
                     SET estado = 'pospuesta', 
                         pospuesto_hasta = :nueva_hora,
                         recordatorio_enviado = FALSE, 
                         nota_incidencia = 'Pospuesta 30 minutos tras validación de margen clínico seguro (>2h)'
                     WHERE id = :id"
                );
                $stmtUpdate->execute([
                    ':nueva_hora' => $nuevaHoraPospuesta->format('Y-m-d H:i:sP'),
                    ':id' => $registroId
                ]);

                $db->commit();

                $usuarioModel = new \App\Models\Usuario();
                $usuarioModel->registrarAuditoria('TOMA_POSPUESTA_SEGURA', "Toma $registroId pospuesta a {$nuevaHoraPospuesta->format('H:i:s')} UTC. Margen clínico validado.");

                $this->setFlash('info', 'Medicamento pospuesto 30 minutos de forma segura. Te recordaremos en el nuevo horario.');
            } catch (\Throwable $e) {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
                error_log('[MedicamentoController::posponerToma] ' . $e->getMessage());
                $this->setFlash('danger', 'Error al procesar la postergación de la dosis.');
            }
        }

        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }

    /**
     * POST /medicamentos/deshacer
     * Deshace una toma registrada por error dentro de una ventana de 15 minutos (UTC)
     */
    public function deshacerToma(): void {
        \App\Middlewares\AuthMiddleware::requirePermission('marcar_tomas');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);
        $registroId = trim($_POST['registro_id'] ?? '');

        if (!empty($registroId) && $paciente) {
            try {
                $db = \Config\Database::getInstance()->getConnection();
                $db->beginTransaction();

                $stmt = $db->prepare("SELECT id, estado, medicamento_id, tomado_at FROM tomas_historial WHERE id = :id FOR UPDATE");
                $stmt->execute([':id' => $registroId]);
                $toma = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$toma || $toma['estado'] !== 'tomada') {
                    $db->rollBack();
                    $this->setFlash('danger', 'Solo es posible deshacer tomas que figuren como tomadas.');
                    header('Location: ' . APP_URL . '/dashboard');
                    exit;
                }

                // Evaluar ventana de 15 minutos estricta en UTC
                if (empty($toma['tomado_at'])) {
                    $db->rollBack();
                    $this->setFlash('danger', 'No se dispone del registro de tiempo de la toma.');
                    header('Location: ' . APP_URL . '/dashboard');
                    exit;
                }

                $tomadoUtc = new \DateTime($toma['tomado_at'], new \DateTimeZone('UTC'));
                $ahoraUtc = new \DateTime('now', new \DateTimeZone('UTC'));
                $diferenciaMinutos = ($ahoraUtc->getTimestamp() - $tomadoUtc->getTimestamp()) / 60;

                if ($diferenciaMinutos > 15) {
                    $db->rollBack();
                    $this->setFlash('warning', 'La ventana de 15 minutos para deshacer la toma ha expirado.');
                    header('Location: ' . APP_URL . '/dashboard');
                    exit;
                }

                // Actualizar estado a deshecha
                $stmtUpdate = $db->prepare(
                    "UPDATE tomas_historial 
                     SET estado = 'deshecha', deshecho_at = CURRENT_TIMESTAMP, nota_incidencia = 'Toma deshecha por el usuario' 
                     WHERE id = :id"
                );
                $stmtUpdate->execute([':id' => $registroId]);

                // Restaurar stock del medicamento si aplica
                if (!empty($toma['medicamento_id'])) {
                    $stmtStock = $db->prepare("UPDATE medicamentos SET stock_actual = stock_actual + COALESCE(dosis_por_toma, 1) WHERE id = :mid");
                    $stmtStock->execute([':mid' => $toma['medicamento_id']]);
                }

                $db->commit();
                $usuarioModel = new \App\Models\Usuario();
                $usuarioModel->registrarAuditoria('TOMA_DESHECHA', "Toma $registroId revertida dentro de la ventana de 15 min.");
                $this->setFlash('success', 'Toma deshecha correctamente. El inventario ha sido restablecido.');
            } catch (\Throwable $e) {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
                error_log('[MedicamentoController::deshacerToma] ' . $e->getMessage());
                $this->setFlash('danger', 'Error al procesar la anulación de la toma.');
            }
        }

        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }
}

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
                COUNT(*) FILTER (WHERE estado = 'tomado') as tomadas,
                COUNT(*) FILTER (WHERE estado IN ('tomado', 'omitido')) as total_evaluadas
            FROM tomas_historial
            WHERE paciente_id = :pid
        ");
        $stmt->execute([':pid' => $pacienteId]);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
        $adherencia = !empty($stats['total_evaluadas']) && $stats['total_evaluadas'] > 0 
            ? round(((int)$stats['tomadas'] / (int)$stats['total_evaluadas']) * 100) 
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
        \App\Middlewares\AuthMiddleware::handle();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido.');
            header('Location: ' . APP_URL . '/medicamentos');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        if ($pacienteId === 0) {
            $paciente = $this->pacienteModel->findByUserId($userId);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        // Validación Multitenant
        $acceso = \App\Middlewares\AuthMiddleware::requirePatientAccess($pacienteId);
        if (!$acceso['puede_editar']) {
            http_response_code(403);
            die('Error 403: No tienes permisos para agregar medicamentos a este paciente.');
        }

        $nombre = trim($_POST['nombre_comercial'] ?? '');
        $dosis = trim($_POST['dosis'] ?? '');
        $frecuenciaHoras = (int)($_POST['frecuencia_horas'] ?? 8);
        $stockActual = max(0, (int)($_POST['stock_actual'] ?? 30));
        $dosisPorToma = max(1, (int)($_POST['dosis_por_toma'] ?? 1));
        $stockMinimo = max(1, (int)($_POST['stock_minimo_alerta'] ?? 5));
        $instrucciones = trim($_POST['instrucciones'] ?? '');

        if (!empty($nombre) && !empty($dosis) && $frecuenciaHoras > 0 && $pacienteId > 0) {
            $this->medicamentoModel->agregar([
                'paciente_id' => $pacienteId,
                'nombre_comercial' => $nombre,
                'dosis' => $dosis,
                'frecuencia_horas' => $frecuenciaHoras,
                'stock_actual' => $stockActual,
                'dosis_por_toma' => $dosisPorToma,
                'stock_minimo_alerta' => $stockMinimo,
                'instrucciones' => $instrucciones
            ]);
            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('MEDICAMENTO_REGISTRADO', "Medicamento $nombre registrado para paciente {$pacienteId}.");
            $this->setFlash('success', 'Medicamento registrado exitosamente.');
        } else {
            $this->setFlash('danger', 'Por favor completa todos los campos requeridos correctamente.');
        }

        header('Location: ' . APP_URL . '/medicamentos');
        exit;
    }

    public function marcarToma(): void {
        \App\Middlewares\AuthMiddleware::handle();

        $redirectUrl = (!empty($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], '/medicamentos/control')) 
            ? (APP_URL . '/medicamentos/control') 
            : (APP_URL . '/dashboard');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o sesión expirada.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $registroId = (int)($_POST['toma_id'] ?? ($_POST['registro_id'] ?? 0));
        $accion = trim($_POST['accion'] ?? 'marcar_tomada');

        if ($registroId > 0) {
            try {
                $db = \Config\Database::getInstance()->getConnection();
                $db->beginTransaction();

                $stmt = $db->prepare("SELECT id, paciente_id, estado, medicamento_id, hora_programada, fecha_programada FROM tomas_historial WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $registroId]);
                $toma = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$toma) {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma solicitada no existe.');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                // Validación Multitenant para asegurar que el usuario puede interactuar con el paciente de esta toma
                $acceso = \App\Middlewares\AuthMiddleware::requirePatientAccess($toma['paciente_id']);

                if ($toma['estado'] !== 'tomado') {
                    $nota = null;
                    if ($accion === 'justificar_retraso' || $toma['estado'] === 'omitido') {
                        $nota = 'Tomada con retraso';
                    } elseif (!empty($toma['hora_programada'])) {
                        $horaProgramada = strtotime($toma['fecha_programada'] . ' ' . $toma['hora_programada']);
                        $ahora = time();
                        $diff = ($ahora - $horaProgramada) / 60;
                        if ($diff > 45) {
                            $nota = 'Tomada con retraso (' . round($diff) . ' min)';
                        } elseif ($diff < -45) {
                            $nota = 'Tomada con antelación (' . round(abs($diff)) . ' min)';
                        }
                    }

                    $this->medicamentoModel->actualizarEstadoToma($registroId, (int)$toma['paciente_id'], 'tomado', $userId, $nota);

                    $medId = (int)$toma['medicamento_id'];
                    $stmtMed = $db->prepare("SELECT stock_actual, dosis_por_toma, stock_minimo_alerta FROM medicamentos WHERE id = :mid FOR UPDATE");
                    $stmtMed->execute(['mid' => $medId]);
                    $medicamento = $stmtMed->fetch(\PDO::FETCH_ASSOC);

                    if ($medicamento) {
                        $dosisADescontar = max(1, (int)($medicamento['dosis_por_toma'] ?? 1));
                        $nuevoStock = max(0, (int)$medicamento['stock_actual'] - $dosisADescontar);
                        $stmtUpdateMed = $db->prepare("UPDATE medicamentos SET stock_actual = :stock WHERE id = :mid");
                        $stmtUpdateMed->execute(['stock' => $nuevoStock, 'mid' => $medId]);

                        if ($nuevoStock <= (int)$medicamento['stock_minimo_alerta']) {
                            $_SESSION['flash_inventory_alert'] = "Te quedan dosis limitadas para este medicamento. Recuerda surtir tu receta.";
                        }
                    }

                    $db->commit();
                    $usuarioModel = new \App\Models\Usuario();
                    $usuarioModel->registrarAuditoria('TOMA_MARCADA', "Toma $registroId marcada como tomada (acción: $accion).");
                    $this->setFlash('success', 'Toma registrada correctamente.');
                } else {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma ya estaba registrada como tomada.');
                }

            } catch (\Exception $e) {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
                $this->setFlash('danger', 'Error al registrar la toma: ' . $e->getMessage());
            }
        }

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * POST /medicamentos/posponer
     * Aplica la regla clínica de prevención de sobredosis:
     * La nueva hora pospuesta (+30 min) NO debe quedar a menos de 2 horas de la siguiente dosis programada.
     */
    public function posponerToma(): void {
        \App\Middlewares\AuthMiddleware::handle();

        $redirectUrl = (!empty($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], '/medicamentos/control')) 
            ? (APP_URL . '/medicamentos/control') 
            : (APP_URL . '/dashboard');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $registroId = (int)($_POST['toma_id'] ?? ($_POST['registro_id'] ?? 0));

        if ($registroId > 0) {
            try {
                $db = \Config\Database::getInstance()->getConnection();
                $db->beginTransaction();

                // 1. Obtener la toma actual con bloqueo FOR UPDATE
                $stmt = $db->prepare(
                    "SELECT id, medicamento_id, paciente_id, fecha_programada_utc, pospuesto_hasta, estado 
                     FROM tomas_historial 
                     WHERE id = :id FOR UPDATE"
                );
                $stmt->execute([':id' => $registroId]);
                $toma = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$toma) {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma solicitada no existe.');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                // Validación Multitenant
                $acceso = \App\Middlewares\AuthMiddleware::requirePatientAccess($toma['paciente_id']);

                if (!in_array($toma['estado'], ['pendiente', 'pospuesto'], true)) {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma no se encuentra pendiente para ser pospuesta.');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                // 2. Determinar la hora base actual y la nueva hora pospuesta (+30 min) en UTC
                $horaBaseStr = $toma['pospuesto_hasta'] ?? $toma['fecha_programada_utc'];
                $horaBaseUtc = new \DateTime($horaBaseStr ?? 'now', new \DateTimeZone('UTC'));
                
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
                       AND estado IN ('pendiente', 'pospuesto')
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
                        
                        $horaProximaLegible = $siguienteHoraUtc->format('H:i');
                        $this->setFlash(
                            'danger', 
                            "⚠️ Bloqueo de Seguridad Clínica: No es posible posponer la toma 30 minutos más. Quedaría a menos de 2 horas (margen de " . round(max(0, $margenMinutos)) . " min) de tu próxima dosis programada de las {$horaProximaLegible} UTC, existiendo riesgo de sobredosis o acumulación tóxica. Por favor marca la dosis como tomada o consulta a tu médico."
                        );
                        header('Location: ' . $redirectUrl);
                        exit;
                    }
                }

                // 4. Margen seguro validado: Actualizar estado y posponer
                $stmtUpdate = $db->prepare(
                    "UPDATE tomas_historial 
                     SET estado = 'pospuesto', 
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

        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * POST /medicamentos/deshacer
     * Deshace una toma registrada por error dentro de una ventana de 15 minutos (UTC)
     */
    public function deshacerToma(): void {
        \App\Middlewares\AuthMiddleware::handle();

        $redirectUrl = (!empty($_SERVER['HTTP_REFERER']) && str_contains($_SERVER['HTTP_REFERER'], '/medicamentos/control')) 
            ? (APP_URL . '/medicamentos/control') 
            : (APP_URL . '/dashboard');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $registroId = (int)($_POST['toma_id'] ?? ($_POST['registro_id'] ?? 0));

        if ($registroId > 0) {
            try {
                $db = \Config\Database::getInstance()->getConnection();
                $db->beginTransaction();

                $stmt = $db->prepare("SELECT id, paciente_id, estado, medicamento_id, tomado_at FROM tomas_historial WHERE id = :id FOR UPDATE");
                $stmt->execute([':id' => $registroId]);
                $toma = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$toma) {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma solicitada no existe.');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                // Validación Multitenant
                $acceso = \App\Middlewares\AuthMiddleware::requirePatientAccess($toma['paciente_id']);

                if ($toma['estado'] !== 'tomado') {
                    $db->rollBack();
                    $this->setFlash('danger', 'Solo es posible deshacer tomas que figuren como tomadas.');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                // Evaluar ventana de 15 minutos estricta en UTC
                if (empty($toma['tomado_at'])) {
                    $db->rollBack();
                    $this->setFlash('danger', 'No se dispone del registro de tiempo de la toma.');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                $tomadoUtc = new \DateTime($toma['tomado_at'], new \DateTimeZone('UTC'));
                $ahoraUtc = new \DateTime('now', new \DateTimeZone('UTC'));
                $diferenciaMinutos = ($ahoraUtc->getTimestamp() - $tomadoUtc->getTimestamp()) / 60;

                if ($diferenciaMinutos > 15) {
                    $db->rollBack();
                    $this->setFlash('warning', 'La ventana de 15 minutos para deshacer la toma ha expirado.');
                    header('Location: ' . $redirectUrl);
                    exit;
                }

                // Actualizar estado a pendiente (o estado válido del enum)
                $stmtUpdate = $db->prepare(
                    "UPDATE tomas_historial 
                     SET estado = 'pendiente', tomado_at = NULL, nota_incidencia = 'Toma deshecha por el usuario' 
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

        header('Location: ' . $redirectUrl);
        exit;
    }
}

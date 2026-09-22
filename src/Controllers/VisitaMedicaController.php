<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VisitaMedica;
use App\Models\Paciente;
use App\Models\Doctor;
use App\Models\CitaLaboratorio;
use App\Services\ImageOptimizerService;
use App\Middlewares\AuthMiddleware;
use Config\Database;
use finfo;
use PDO;

class VisitaMedicaController extends Controller {
    private VisitaMedica $visitaModel;
    private Paciente $pacienteModel;
    private Doctor $doctorModel;
    private CitaLaboratorio $citaModel;
    private ImageOptimizerService $imageOptimizer;
    private string $storageDir;
    private PDO $db;

    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf'
    ];

    private const MAX_BYTES = 5 * 1024 * 1024; // 5 MB

    public function __construct() {
        $this->visitaModel = new VisitaMedica();
        $this->pacienteModel = new Paciente();
        $this->doctorModel = new Doctor();
        $this->citaModel = new CitaLaboratorio();
        $this->imageOptimizer = new ImageOptimizerService(1600, 1600, 80);
        $this->db = Database::getInstance()->getConnection();
        $this->storageDir = dirname(__DIR__, 2) . '/storage/uploads/expedientes';

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0750, true);
        }
    }

    /**
     * Muestra el formulario guiado mobile-first para registrar visita médica post-consulta
     */
    public function create(): void {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $pacienteId = (int)($_GET['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        if ($pacienteId > 0) {
            $paciente = $this->pacienteModel->find($pacienteId);
        } else {
            $paciente = $this->pacienteModel->findByUserId($userId);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        if (!$paciente || $pacienteId === 0) {
            $this->setFlash('warning', 'Debes seleccionar un paciente antes de registrar una visita médica.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (empty($acceso['puede_editar'])) {
            $this->setFlash('danger', 'No tienes permisos de edición para registrar visitas médicas en este expediente.');
            header('Location: ' . APP_URL . '/expediente');
            exit;
        }

        $_SESSION['paciente_activo_id'] = $pacienteId;

        $doctores = $this->doctorModel->getByPacienteId($pacienteId);

        $this->render('visitas/nueva', [
            'paciente' => $paciente,
            'doctores' => $doctores,
            'puedeEditar' => $acceso['puede_editar']
        ], 'main');
    }

    /**
     * Guarda la visita médica en transacción ACID, procesa fotos de recetas y genera cita de seguimiento si aplica
     */
    public function store(): void {
        AuthMiddleware::handle();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o expirado.');
            header('Location: ' . APP_URL . '/expediente');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (empty($acceso['puede_editar'])) {
            $this->setFlash('danger', 'No tienes permisos de edición en este expediente.');
            header('Location: ' . APP_URL . '/expediente');
            exit;
        }

        $lugarAtencion = trim($_POST['lugar_atencion'] ?? '');
        $motivoConsulta = trim($_POST['motivo_consulta'] ?? '');
        $diagnosticoNotas = trim($_POST['diagnostico_notas'] ?? '');
        $doctorNombre = trim($_POST['doctor_nombre'] ?? '');
        $fechaVisita = !empty($_POST['fecha_visita']) ? $_POST['fecha_visita'] : date('Y-m-d H:i:s');
        $requiereSeguimiento = !empty($_POST['requiere_seguimiento']);
        $fechaSeguimiento = !empty($_POST['fecha_seguimiento']) ? trim($_POST['fecha_seguimiento']) : null;
        $instruccionesSeguimiento = trim($_POST['instrucciones_seguimiento'] ?? '');

        if (empty($motivoConsulta)) {
            $this->setFlash('danger', 'El motivo de consulta es un campo obligatorio.');
            header('Location: ' . APP_URL . '/visitas/nueva');
            exit;
        }

        // Si seleccionó doctor de la lista existente
        if (!empty($_POST['doctor_id'])) {
            $docId = (int)$_POST['doctor_id'];
            $doctores = $this->doctorModel->getByPacienteId($pacienteId);
            foreach ($doctores as $d) {
                if ((int)$d['id'] === $docId) {
                    $doctorNombre = $d['nombre'];
                    if (!empty($d['hospital_clinica']) && empty($lugarAtencion)) {
                        $lugarAtencion = $d['hospital_clinica'];
                    }
                    break;
                }
            }
        }

        try {
            $this->db->beginTransaction();

            // 1. Crear Visita Médica
            $visitaId = $this->visitaModel->crear([
                'paciente_id' => $pacienteId,
                'usuario_registra_id' => $userId,
                'fecha_visita' => $fechaVisita,
                'lugar_atencion' => $lugarAtencion,
                'motivo_consulta' => $motivoConsulta,
                'diagnostico_notas' => $diagnosticoNotas,
                'doctor_nombre' => $doctorNombre,
                'requiere_seguimiento' => $requiereSeguimiento,
                'fecha_seguimiento' => $fechaSeguimiento,
                'instrucciones_seguimiento' => $instruccionesSeguimiento
            ]);

            // 2. Procesar Archivos/Fotos de Recetas Adjuntas
            $archivosProcesados = 0;
            if (!empty($_FILES['fotos_recetas']) && is_array($_FILES['fotos_recetas']['name'])) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $fileCount = count($_FILES['fotos_recetas']['name']);

                for ($i = 0; $i < $fileCount; $i++) {
                    $error = $_FILES['fotos_recetas']['error'][$i];
                    if ($error === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    if ($error !== UPLOAD_ERR_OK) {
                        continue;
                    }

                    $tmpName = $_FILES['fotos_recetas']['tmp_name'][$i];
                    $fileSize = $_FILES['fotos_recetas']['size'][$i];
                    $originalName = htmlspecialchars(basename($_FILES['fotos_recetas']['name'][$i]), ENT_QUOTES, 'UTF-8');

                    if ($fileSize > self::MAX_BYTES) {
                        continue; // Omitir archivos que superen 5MB para seguridad
                    }

                    $detectedMime = $finfo->file($tmpName);
                    if (!array_key_exists($detectedMime, self::ALLOWED_MIMES)) {
                        continue; // Omitir mimes no soportados
                    }

                    $ext = self::ALLOWED_MIMES[$detectedMime];
                    $nombreAleatorio = bin2hex(random_bytes(16)) . '.' . $ext;
                    $destinoFinal = $this->storageDir . '/' . $nombreAleatorio;
                    $tamanoFinal = 0;

                    if (strpos($detectedMime, 'image/') === 0) {
                        $resultadoCompresion = $this->imageOptimizer->optimizeAndSave($tmpName, $destinoFinal, $detectedMime);
                        if ($resultadoCompresion['success']) {
                            $tamanoFinal = $resultadoCompresion['size_bytes'];
                        } else {
                            if (move_uploaded_file($tmpName, $destinoFinal)) {
                                chmod($destinoFinal, 0640);
                                $tamanoFinal = filesize($destinoFinal);
                            }
                        }
                    } else {
                        if (move_uploaded_file($tmpName, $destinoFinal)) {
                            chmod($destinoFinal, 0640);
                            $tamanoFinal = filesize($destinoFinal);
                        }
                    }

                    if ($tamanoFinal > 0) {
                        $this->visitaModel->registrarArchivo([
                            'paciente_id' => $pacienteId,
                            'visita_medica_id' => $visitaId,
                            'nombre_original' => $originalName,
                            'nombre_archivo_almacenado' => $nombreAleatorio,
                            'ruta_almacenamiento' => $destinoFinal,
                            'mime_type' => $detectedMime,
                            'tamano_bytes' => $tamanoFinal,
                            'tipo_documento' => 'receta',
                            'subido_por_usuario_id' => $userId
                        ]);
                        $archivosProcesados++;
                    }
                }
            }

            // 3. Generar Cita Automática en Agenda si requiere seguimiento con fecha
            if ($requiereSeguimiento && !empty($fechaSeguimiento)) {
                $fechaHoraCita = $fechaSeguimiento . ' 09:00:00';
                $this->citaModel->agregar([
                    'paciente_id' => $pacienteId,
                    'tipo_evento' => 'consulta',
                    'titulo' => 'Seguimiento: ' . $motivoConsulta,
                    'lugar' => $lugarAtencion ?: 'Consultorio Médico',
                    'fecha_hora' => $fechaHoraCita,
                    'preparacion_previa' => $instruccionesSeguimiento ?: 'Llevar receta previa y estudios solicitados.',
                    'estado' => 'programada',
                    'notas' => 'Cita agendada automáticamente desde Bitácora de Consulta.'
                ]);
            }

            $this->db->commit();

            // 4. Registro en Auditoría
            AuthMiddleware::logAudit(
                $pacienteId,
                'VISITA_MEDICA_REGISTRADA',
                "Visita médica registrada: '{$motivoConsulta}' en '{$lugarAtencion}' con Dr(a). '{$doctorNombre}'. ({$archivosProcesados} fotos adjuntas)."
            );

            $this->setFlash('success', '¡Visita médica y bitácora clínica guardadas con éxito!');
            header('Location: ' . APP_URL . '/expediente?paciente_id=' . $pacienteId . '#tab-visitas');
            exit;

        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[VisitaMedicaController::store] ' . $e->getMessage());
            $this->setFlash('danger', 'Ocurrió un error al guardar la visita médica: ' . $e->getMessage());
            header('Location: ' . APP_URL . '/visitas/nueva');
            exit;
        }
    }
}

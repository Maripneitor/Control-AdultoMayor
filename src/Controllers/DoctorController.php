<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\RegistroSaludDiario;
use App\Models\ContactoEmergencia;
use App\Models\CitaLaboratorio;
use App\Services\PdfReportService;
use App\Security\AuditLogger;

class DoctorController extends Controller {
    private Doctor $doctorModel;
    private Paciente $pacienteModel;
    private Medicamento $medicamentoModel;
    private RegistroSaludDiario $saludModel;
    private ContactoEmergencia $contactoModel;
    private CitaLaboratorio $citaModel;

    public function __construct() {
        $this->doctorModel = new Doctor();
        $this->pacienteModel = new Paciente();
        $this->medicamentoModel = new Medicamento();
        $this->saludModel = new RegistroSaludDiario();
        $this->contactoModel = new ContactoEmergencia();
        $this->citaModel = new CitaLaboratorio();
    }

    /**
     * Dashboard principal para el perfil de Doctor
     */
    public function dashboard(): void {
        AuthMiddleware::requireDoctorRole();
        $userId = (int)($_SESSION['user_id'] ?? 0);

        $doctor = $this->doctorModel->findByUserId($userId);
        if (!$doctor) {
            $this->setFlash('danger', 'No se encontró un perfil médico asociado a esta cuenta.');
            header('Location: ' . APP_URL . '/perfil');
            exit;
        }

        $doctorId = (int)$doctor['id'];
        $pacientes = $this->doctorModel->obtenerPacientesAsignados($doctorId);

        // Resumen y métricas
        $totalPacientes = count($pacientes);
        $alertasCriticas = 0;
        $sumaAdherencia = 0;
        foreach ($pacientes as &$p) {
            if (!empty($p['alerta_emitida'])) {
                $alertasCriticas++;
            }
            $p['adherencia_semanal'] = $this->medicamentoModel->calcularAdherenciaSemanal((int)$p['id']);
            $sumaAdherencia += $p['adherencia_semanal'];
        }
        unset($p);

        $promedioAdherencia = $totalPacientes > 0 ? round($sumaAdherencia / $totalPacientes, 1) : 100.0;

        // Obtener recetas emitidas por este médico
        $recetas = $this->doctorModel->obtenerRecetasPorDoctor($doctorId);
        $totalRecetas = count($recetas);

        require_once dirname(__DIR__) . '/Views/doctor/dashboard.php';
    }

    /**
     * Vista de expediente clínico integral para el médico
     */
    public function expediente(): void {
        AuthMiddleware::requireDoctorRole();
        $pacienteId = (int)($_GET['id'] ?? ($_GET['paciente_id'] ?? 0));

        if ($pacienteId === 0) {
            $this->setFlash('warning', 'Selecciona un paciente para ver su expediente.');
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        $userId = (int)$_SESSION['user_id'];
        $doctor = $this->doctorModel->findByUserId($userId);

        $paciente = $this->pacienteModel->getExpedienteById($pacienteId);
        if (!$paciente) {
            http_response_code(404);
            die('Error 404: Expediente no encontrado.');
        }

        // Información clínica integral
        $signosHistorial = $this->saludModel->getHistorial($pacienteId, 15);
        $medicamentosActivos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);
        $adherenciaSemanal = $this->medicamentoModel->calcularAdherenciaSemanal($pacienteId);
        $recetas = $this->doctorModel->obtenerRecetasPorPaciente($pacienteId);
        $contactos = $this->contactoModel->getByPacienteId($pacienteId);
        $proximasCitas = $this->citaModel->getUpcomingByPacienteId($pacienteId, 5);

        // Obtener notas de evolución médica registradas por este doctor
        $doctorId = (int)($doctor['id'] ?? 0);
        $medicosTratantes = $this->pacienteModel->obtenerMedicosTratantes($pacienteId);

        $notasDoctorActual = '';
        foreach ($medicosTratantes as $mt) {
            if ((int)$mt['id'] === $doctorId) {
                $notasDoctorActual = $mt['notas_medicas'] ?? '';
                break;
            }
        }

        require_once dirname(__DIR__) . '/Views/doctor/expediente.php';
    }

    /**
     * Formulario para emitir una nueva receta médica
     */
    public function crearReceta(): void {
        AuthMiddleware::requireDoctorRole();
        $pacienteId = (int)($_GET['paciente_id'] ?? ($_GET['id'] ?? 0));

        if ($pacienteId === 0) {
            $this->setFlash('warning', 'Especifica un paciente válido.');
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        $paciente = $this->pacienteModel->getExpedienteById($pacienteId);
        $userId = (int)$_SESSION['user_id'];
        $doctor = $this->doctorModel->findByUserId($userId);

        require_once dirname(__DIR__) . '/Views/doctor/receta_form.php';
    }

    /**
     * Procesa y guarda la receta médica
     */
    public function guardarReceta(): void {
        AuthMiddleware::requireDoctorRole();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o sesión expirada.');
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? 0);
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);

        $userId = (int)$_SESSION['user_id'];
        $doctor = $this->doctorModel->findByUserId($userId);
        $doctorId = (int)($doctor['id'] ?? 0);

        $diagnostico = trim($_POST['diagnostico'] ?? '');
        $indicaciones = trim($_POST['indicaciones_generales'] ?? '');
        $fechaEmision = !empty($_POST['fecha_emision']) ? $_POST['fecha_emision'] : date('Y-m-d');

        if (empty($diagnostico)) {
            $this->setFlash('warning', 'El diagnóstico clínico es obligatorio.');
            header("Location: " . APP_URL . "/doctor/recetas/nueva?paciente_id={$pacienteId}");
            exit;
        }

        try {
            $db = \Config\Database::getInstance()->getConnection();
            $db->beginTransaction();

            $recetaId = $this->doctorModel->guardarReceta([
                'doctor_id' => $doctorId,
                'paciente_id' => $pacienteId,
                'diagnostico' => $diagnostico,
                'indicaciones_generales' => $indicaciones,
                'fecha_emision' => $fechaEmision
            ]);

            // Insertar automáticamente los fármacos indicados al catálogo del paciente
            $medicamentosInput = $_POST['medicamentos'] ?? [];
            if (is_array($medicamentosInput) && !empty($medicamentosInput)) {
                $stmtMed = $db->prepare("
                    INSERT INTO medicamentos (
                        paciente_id, receta_id, nombre_comercial, dosis, frecuencia_horas, 
                        instrucciones, stock_actual, dosis_por_toma, stock_minimo_alerta, activo
                    ) VALUES (
                        :pid, :rid, :nombre, :dosis, :frecuencia, 
                        :instrucciones, :stock, 1, 5, TRUE
                    ) RETURNING id
                ");

                foreach ($medicamentosInput as $med) {
                    $nombreMed = trim($med['nombre'] ?? '');
                    $dosisMed = trim($med['dosis'] ?? '');
                    $frecuencia = max(1, (int)($med['frecuencia_horas'] ?? 8));
                    $instruccionesMed = trim($med['instrucciones'] ?? '');
                    $stock = max(1, (int)($med['stock_actual'] ?? 30));

                    if (!empty($nombreMed) && !empty($dosisMed)) {
                        $stmtMed->execute([
                            'pid' => $pacienteId,
                            'rid' => $recetaId,
                            'nombre' => $nombreMed,
                            'dosis' => $dosisMed,
                            'frecuencia' => $frecuencia,
                            'instrucciones' => $instruccionesMed,
                            'stock' => $stock
                        ]);
                        $nuevoMedId = (int)$stmtMed->fetchColumn();

                        // Registrar esquema programado en registro_tomas
                        try {
                            $stmtRegToma = $db->prepare("
                                INSERT INTO registro_tomas (
                                    medicamento_id, paciente_id, usuario_registro_id, 
                                    fecha_hora_programada, estado, observaciones
                                ) VALUES (
                                    :mid, :pid, :uid, NOW(), 'pendiente', :obs
                                )
                            ");
                            $stmtRegToma->execute([
                                'mid' => $nuevoMedId,
                                'pid' => $pacienteId,
                                'uid' => $userId,
                                'obs' => "Prescripción inicial emitida en Receta #{$recetaId} por Dr(a). {$doctor['nombre']}"
                            ]);
                        } catch (\Throwable $eLog) {
                            error_log("[registro_tomas insert error] " . $eLog->getMessage());
                        }
                    }
                }
            }

            $db->commit();

            // Programar tomas del día en tomas_historial
            $this->medicamentoModel->generarTomasDiarias($pacienteId);

            AuthMiddleware::logAudit(
                $pacienteId,
                'RECETA_MEDICA_EMITIDA',
                "Dr(a). {$doctor['nombre']} emitió la receta ID {$recetaId} y programó el esquema farmacológico para el paciente ID {$pacienteId}."
            );

            $this->setFlash('success', '¡Receta médica emitida con éxito! Los fármacos fueron integrados al esquema de tomas del paciente.');
            header("Location: " . APP_URL . "/doctor/paciente/expediente?id={$pacienteId}#tab-recetas");
            exit;
        } catch (\Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log("[DoctorController::guardarReceta Error] " . $e->getMessage());
            $this->setFlash('danger', 'Error al guardar la receta: ' . $e->getMessage());
            header("Location: " . APP_URL . "/doctor/recetas/nueva?paciente_id={$pacienteId}");
            exit;
        }
    }

    /**
     * Vincula a un médico con un paciente mediante NIP
     */
    public function vincularPaciente(): void {
        AuthMiddleware::requireDoctorRole();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o sesión expirada.');
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $doctor = $this->doctorModel->findByUserId($userId);
        $doctorId = (int)($doctor['id'] ?? 0);

        $codigo = trim($_POST['codigo_vinculacion'] ?? '');

        if (empty($codigo)) {
            $this->setFlash('warning', 'Ingresa el código o NIP de vinculación proporcionado por el familiar.');
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }

        try {
            $paciente = $this->doctorModel->vincularConPacientePorNip($doctorId, $codigo);
            $pacienteId = (int)$paciente['id'];

            AuthMiddleware::logAudit(
                $pacienteId,
                'DOCTOR_VINCULADO_NIP',
                "Dr(a). {$doctor['nombre']} vinculado al paciente {$paciente['nombre']} mediante código {$codigo}."
            );

            $this->setFlash('success', "¡Paciente {$paciente['nombre']} {$paciente['apellidos']} vinculado con éxito a tu panel clínico!");
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        } catch (\Throwable $e) {
            error_log("[DoctorController::vincularPaciente Error] " . $e->getMessage());
            $this->setFlash('danger', $e->getMessage());
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }
    }

    /**
     * Guarda o actualiza las notas médicas de evolución del doctor
     */
    public function guardarNotasEvolucion(): void {
        AuthMiddleware::requireDoctorRole();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido.');
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? 0);
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);

        $userId = (int)$_SESSION['user_id'];
        $doctor = $this->doctorModel->findByUserId($userId);
        $doctorId = (int)($doctor['id'] ?? 0);
        $notas = trim($_POST['notas_medicas'] ?? '');

        $this->doctorModel->actualizarNotasEvolucion($pacienteId, $doctorId, $notas);
        AuthMiddleware::logAudit(
            $pacienteId,
            'NOTAS_EVOLUCION_ACTUALIZADAS',
            "Notas de evolución médica actualizadas por Dr(a). {$doctor['nombre']}."
        );

        $this->setFlash('success', 'Notas de evolución clínica actualizadas.');
        header("Location: " . APP_URL . "/doctor/paciente/expediente?id={$pacienteId}");
        exit;
    }

    /**
     * Descarga del expediente clínico oficial en PDF con cédula y firma del médico
     */
    public function descargarReporteClinico(): void {
        AuthMiddleware::requireDoctorRole();
        $pacienteId = (int)($_GET['id'] ?? ($_GET['paciente_id'] ?? 0));

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        $paciente = $this->pacienteModel->getExpedienteById($pacienteId);

        if (!$paciente) {
            http_response_code(404);
            die('Error 404: Expediente no encontrado.');
        }

        $userId = (int)$_SESSION['user_id'];
        $doctor = $this->doctorModel->findByUserId($userId);

        $signos = $this->saludModel->getHistorial($pacienteId, 30);
        $medicamentos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);
        $contactos = $this->contactoModel->getByPacienteId($pacienteId);
        $recetas = $this->doctorModel->obtenerRecetasPorPaciente($pacienteId);

        $visitaModel = new \App\Models\VisitaMedica();
        $visitas = $visitaModel->getByPacienteId($pacienteId);
        $adherencia = $this->medicamentoModel->calcularAdherenciaSemanal($pacienteId);

        $pdfService = new PdfReportService();
        $pdfService->generarResumenPaciente([
            'paciente' => $paciente,
            'signos' => $signos,
            'medicamentos' => $medicamentos,
            'adherencia' => $adherencia,
            'contactos' => $contactos,
            'recetas' => $recetas,
            'visitas' => $visitas,
            'doctor' => $doctor,
            'medico_emisor' => $doctor['nombre'] ?? 'Médico Tratante',
            'cedula_profesional' => $doctor['cedula_profesional'] ?? 'N/D'
        ]);
    }
}

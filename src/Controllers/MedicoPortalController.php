<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\TokenAccesoMedico;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\VisitaMedica;
use App\Models\CitaLaboratorio;
use App\Middlewares\AuthMiddleware;
use Config\Database;
use PDO;

class MedicoPortalController extends Controller {
    private TokenAccesoMedico $tokenModel;
    private Paciente $pacienteModel;
    private Medicamento $medicamentoModel;
    private VisitaMedica $visitaModel;
    private CitaLaboratorio $citaModel;

    public function __construct() {
        $this->tokenModel = new TokenAccesoMedico();
        $this->pacienteModel = new Paciente();
        $this->medicamentoModel = new Medicamento();
        $this->visitaModel = new VisitaMedica();
        $this->citaModel = new CitaLaboratorio();
    }

    /**
     * POST /api/medico/generar-pin
     * Genera un PIN temporal de 6 dígitos con vigencia (12 o 24 hrs) para compartir con el médico
     */
    public function generarPin(): void {
        AuthMiddleware::handle();
        AuthMiddleware::requirePermission('generar_pin_medico');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));

        if ($pacienteId <= 0) {
            $this->jsonResponse(false, 'Identificador de paciente no válido.', null, 400);
            return;
        }

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (empty($acceso['puede_editar'])) {
            $this->jsonResponse(false, 'No tienes permisos para generar PIN médico en este expediente.', null, 403);
            return;
        }

        $medicoNombre = trim($_POST['medico_nombre'] ?? '');
        $hospital = trim($_POST['hospital_institucion'] ?? '');
        $duracionHoras = (int)($_POST['duracion_horas'] ?? 24);
        if ($duracionHoras <= 0 || $duracionHoras > 72) {
            $duracionHoras = 24;
        }

        $pinData = $this->tokenModel->generarPin(
            $pacienteId,
            $userId,
            $medicoNombre,
            $hospital,
            $duracionHoras
        );

        $enlaceDirecto = APP_URL . '/medico/consulta?pin=' . $pinData['pin_seguridad'];

        AuthMiddleware::logAudit(
            $pacienteId,
            'PIN_MEDICO_GENERADO',
            "PIN médico '{$pinData['pin_seguridad']}' generado por usuario ID {$userId} (Vigencia: {$duracionHoras}h)."
        );

        $this->jsonResponse(true, 'PIN de acceso médico generado con éxito.', [
            'pin' => $pinData['pin_seguridad'],
            'token_url' => $pinData['token_url'],
            'enlace_directo' => $enlaceDirecto,
            'expira_at' => $pinData['expira_at'],
            'duracion_horas' => $duracionHoras
        ]);
    }

    /**
     * GET /medico/consulta?pin=XXXXXX (o ?token=...)
     * Portal clínico seguro para el médico tratante (sin menús familiares)
     */
    public function consulta(): void {
        $pinOrToken = trim($_GET['pin'] ?? ($_GET['token'] ?? ''));

        if (empty($pinOrToken)) {
            $this->renderErrorPin('Se requiere un PIN de seguridad de 6 dígitos o enlace temporal.');
            return;
        }

        $tokenData = $this->tokenModel->validarAcceso($pinOrToken, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        if (!$tokenData) {
            $this->renderErrorPin('El PIN médico es inválido, ha expirado o superó el límite de accesos permitidos. Solicita uno nuevo al familiar responsable.');
            return;
        }

        $pacienteId = (int)$tokenData['paciente_id'];
        $paciente = $this->pacienteModel->getExpedienteById($pacienteId);

        if (!$paciente) {
            $this->renderErrorPin('Expediente clínico no localizado.');
            return;
        }

        // Consultar tratamientos activos
        $medicamentosActivos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);

        // Consultar historial de visitas y estudios adjuntos
        $visitasMedicas = $this->visitaModel->getByPacienteId($pacienteId);

        // Consultar signos vitales recientes
        $saludModel = new \App\Models\RegistroSaludDiario();
        $ultimoSalud = $saludModel->getTodayByPacienteId($pacienteId);
        $registroHistoricoSalud = $saludModel->getUltimoRegistro($pacienteId);

        // Registro de Auditoría
        AuthMiddleware::logAudit(
            $pacienteId,
            'CONSULTA_PORTAL_MEDICO',
            "Acceso al Portal Médico vía PIN '{$tokenData['pin_seguridad']}' desde IP " . ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')
        );

        // Renderizar vista clínica especializada (sin layout de navegación familiar)
        $this->render('medico/portal_consulta', [
            'paciente' => $paciente,
            'tokenData' => $tokenData,
            'medicamentosActivos' => $medicamentosActivos,
            'visitasMedicas' => $visitasMedicas,
            'ultimoSalud' => $ultimoSalud ?: $registroHistoricoSalud,
            'pinActual' => $tokenData['pin_seguridad']
        ], false);
    }

    /**
     * POST /medico/guardar-nota
     * Permite al médico tratante asentar su diagnóstico, receta o indicaciones directamente
     */
    public function guardarNota(): void {
        $pinOrToken = trim($_POST['pin'] ?? '');

        if (empty($pinOrToken)) {
            $this->setFlash('danger', 'PIN de autorización ausente.');
            header('Location: ' . APP_URL . '/medico/consulta?pin=' . urlencode($pinOrToken));
            exit;
        }

        $tokenData = $this->tokenModel->validarAcceso($pinOrToken, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        if (!$tokenData) {
            $this->setFlash('danger', 'PIN expirado o no autorizado.');
            header('Location: ' . APP_URL . '/medico/consulta?pin=' . urlencode($pinOrToken));
            exit;
        }

        $pacienteId = (int)$tokenData['paciente_id'];
        $motivo = trim($_POST['motivo_consulta'] ?? 'Consulta Médica Especializada');
        $diagnostico = trim($_POST['diagnostico_notas'] ?? '');
        $doctorNombre = trim($_POST['doctor_nombre'] ?? ($tokenData['medico_nombre'] ?? 'Médico Tratante'));
        $lugar = trim($_POST['lugar_atencion'] ?? ($tokenData['hospital_institucion'] ?? 'Consulta Médica'));
        $requiereSeguimiento = !empty($_POST['requiere_seguimiento']);
        $fechaSeguimiento = !empty($_POST['fecha_seguimiento']) ? trim($_POST['fecha_seguimiento']) : null;
        $instruccionesSeguimiento = trim($_POST['instrucciones_seguimiento'] ?? '');

        if (empty($diagnostico)) {
            $this->setFlash('danger', 'Debes ingresar el diagnóstico, indicaciones o receta de la consulta.');
            header('Location: ' . APP_URL . '/medico/consulta?pin=' . urlencode($pinOrToken));
            exit;
        }

        try {
            $visitaId = $this->visitaModel->crear([
                'paciente_id' => $pacienteId,
                'usuario_registra_id' => (int)$tokenData['generado_por_usuario_id'],
                'fecha_visita' => date('Y-m-d H:i:s'),
                'lugar_atencion' => $lugar,
                'motivo_consulta' => $motivo,
                'diagnostico_notas' => $diagnostico,
                'doctor_nombre' => $doctorNombre,
                'requiere_seguimiento' => $requiereSeguimiento,
                'fecha_seguimiento' => $fechaSeguimiento,
                'instrucciones_seguimiento' => $instruccionesSeguimiento
            ]);

            // Si se estipuló seguimiento, programar cita
            if ($requiereSeguimiento && !empty($fechaSeguimiento)) {
                $this->citaModel->agregar([
                    'paciente_id' => $pacienteId,
                    'tipo_evento' => 'consulta',
                    'titulo' => 'Seguimiento: ' . $motivo,
                    'lugar' => $lugar,
                    'fecha_hora' => $fechaSeguimiento . ' 09:00:00',
                    'preparacion_previa' => $instruccionesSeguimiento ?: 'Llevar receta anterior y estudios indicados.',
                    'estado' => 'programada',
                    'notas' => 'Agendada por Dr(a). ' . $doctorNombre . ' desde Portal Médico.'
                ]);
            }

            AuthMiddleware::logAudit(
                $pacienteId,
                'NOTA_MEDICA_PORTAL_REGISTRADA',
                "Nota médica y receta registradas por Dr(a). '{$doctorNombre}' desde Portal PIN '{$tokenData['pin_seguridad']}'."
            );

            $this->setFlash('success', '¡Nota clínica y receta asentadas correctamente en el expediente de VitalSenior!');
        } catch (\Throwable $e) {
            error_log('[MedicoPortalController::guardarNota] ' . $e->getMessage());
            $this->setFlash('danger', 'Error al asentar la nota clínica.');
        }

        header('Location: ' . APP_URL . '/medico/consulta?pin=' . urlencode($pinOrToken));
        exit;
    }

    private function renderErrorPin(string $mensaje): void {
        require_once dirname(__DIR__) . '/Views/medico/pin_invalido.php';
        exit;
    }
}

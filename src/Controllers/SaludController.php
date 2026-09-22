<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\RegistroSaludDiario;
use App\Models\Paciente;
use App\Middlewares\AuthMiddleware;
use App\Services\SaludEvaluatorService;

class SaludController extends Controller {
    private RegistroSaludDiario $saludModel;
    private SaludEvaluatorService $evaluatorService;
    private Paciente $pacienteModel;

    public function __construct() {
        $this->saludModel = new RegistroSaludDiario();
        $this->evaluatorService = new SaludEvaluatorService();
        $this->pacienteModel = new Paciente();
    }

    /**
     * Muestra el Stepper interactivo adaptado a móviles y personas mayores
     */
    public function stepper(): void {
        AuthMiddleware::handle();
        AuthMiddleware::requirePermission('registrar_signos');

        $pacienteId = (int)($_SESSION['paciente_activo_id'] ?? 0);
        if ($pacienteId === 0) {
            $this->setFlash('danger', 'No hay un paciente activo seleccionado.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // Validación multitenant de acceso
        AuthMiddleware::requirePatientAccess($pacienteId);

        $paciente = $this->pacienteModel->find($pacienteId);
        if (!$paciente) {
            $this->setFlash('danger', 'Paciente no encontrado.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $registroHoy = $this->saludModel->getTodayByPacienteId($pacienteId);
        $ultimoRegistro = $this->saludModel->getUltimoRegistro($pacienteId);

        require_once dirname(__DIR__) . '/Views/salud/stepper.php';
    }

    /**
     * Procesa y valida el registro de signos vitales, glucosa e IMC
     */
    public function registrar(): void {
        AuthMiddleware::handle();
        AuthMiddleware::requirePermission('registrar_signos');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlash('danger', 'Método HTTP no permitido.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad inválido o sesión expirada.');
            header('Location: ' . APP_URL . '/salud');
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? $_SESSION['paciente_activo_id'] ?? 0);
        if ($pacienteId === 0) {
            $this->setFlash('danger', 'No hay paciente seleccionado.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // Validación estricta multitenant
        AuthMiddleware::requirePatientAccess($pacienteId);

        $presionRaw = trim((string)($_POST['presion'] ?? ''));
        $glucosaRaw = trim((string)($_POST['glucosa'] ?? ''));
        $momentoGlucosa = $_POST['momento_glucosa'] ?? 'ayunas';
        $ritmoRaw = trim((string)($_POST['ritmo_cardiaco'] ?? ''));
        $horasSuenoRaw = trim((string)($_POST['horas_sueno'] ?? ''));
        $nivelDolorRaw = trim((string)($_POST['nivel_dolor'] ?? ''));
        $pesoRaw = trim((string)($_POST['peso'] ?? ''));
        $alturaRaw = trim((string)($_POST['altura'] ?? ''));
        $confirmado = isset($_POST['confirmado_por_usuario']) && ($_POST['confirmado_por_usuario'] === '1' || $_POST['confirmado_por_usuario'] === 1);

        // Procesar presión sistólica / diastólica
        $sistolica = null;
        $diastolica = null;
        $datosEval = [];

        if (!empty($presionRaw)) {
            $partes = explode('/', $presionRaw);
            if (count($partes) === 2) {
                $sis = (int)trim($partes[0]);
                $dia = (int)trim($partes[1]);
                if ($sis > 0 && $dia > 0) {
                    $sistolica = $sis;
                    $diastolica = $dia;
                    $datosEval['presion_sistolica'] = $sistolica;
                    $datosEval['presion_diastolica'] = $diastolica;
                }
            }
        } elseif (!empty($_POST['presion_sistolica']) && !empty($_POST['presion_diastolica'])) {
            $sistolica = (int)$_POST['presion_sistolica'];
            $diastolica = (int)$_POST['presion_diastolica'];
            $datosEval['presion_sistolica'] = $sistolica;
            $datosEval['presion_diastolica'] = $diastolica;
        }

        // Glucosa
        $glucosaVal = null;
        if ($glucosaRaw !== '') {
            $glucosaVal = (float)$glucosaRaw;
            $datosEval['glucosa'] = $glucosaVal;
            $datosEval['momento_glucosa'] = $momentoGlucosa;
        }

        // Ritmo cardíaco
        $ritmoVal = null;
        if ($ritmoRaw !== '') {
            $ritmoVal = (int)$ritmoRaw;
            $datosEval['ritmo_cardiaco'] = $ritmoVal;
        }

        // Peso y Altura para IMC
        $pesoVal = null;
        $alturaVal = null;
        if ($pesoRaw !== '') {
            $pesoVal = (float)$pesoRaw;
            $datosEval['peso'] = $pesoVal;
        }
        if ($alturaRaw !== '') {
            $alturaVal = (float)$alturaRaw;
            $datosEval['altura'] = $alturaVal;
        }

        // Evaluación clínica
        $evaluacion = $this->evaluatorService->evaluar($datosEval, $confirmado);

        if ($evaluacion['requiere_confirmacion']) {
            $_SESSION['salud_datos_pendientes'] = $_POST;
            $_SESSION['salud_evaluacion_mensaje'] = $evaluacion['mensaje_confirmacion'];
            $this->setFlash('warning', $evaluacion['mensaje_confirmacion']);
            header('Location: ' . APP_URL . '/salud/confirmar');
            exit;
        }

        // Limpiar sesión de confirmación
        unset($_SESSION['salud_datos_pendientes'], $_SESSION['salud_evaluacion_mensaje']);

        $horasVal = $horasSuenoRaw !== '' ? (float)$horasSuenoRaw : null;
        $dolorVal = $nivelDolorRaw !== '' ? (int)$nivelDolorRaw : null;
        $imcVal = $evaluacion['imc'] ?? null;
        $alertaEmitida = ($evaluacion['estado'] === SaludEvaluatorService::STATUS_CRITICAL);

        $registroData = [
            'paciente_id' => $pacienteId,
            'presion_sistolica' => $sistolica,
            'presion_diastolica' => $diastolica,
            'glucosa' => $glucosaVal,
            'momento_glucosa' => $momentoGlucosa,
            'ritmo_cardiaco' => $ritmoVal,
            'horas_sueno' => $horasVal,
            'nivel_dolor' => $dolorVal,
            'peso' => $evaluacion['peso'] ?? $pesoVal,
            'altura' => $evaluacion['altura_m'] ?? $alturaVal,
            'imc' => $imcVal,
            'alerta_emitida' => $alertaEmitida,
            'confirmado_por_usuario' => $confirmado,
            'registrado_por_usuario_id' => (int)($_SESSION['user_id'] ?? 0)
        ];

        $registroId = $this->saludModel->agregar($registroData);

        // Registro en Logs de Auditoría si hay alerta anómala o crítica
        if ($evaluacion['estado'] !== SaludEvaluatorService::STATUS_NORMAL || !empty($evaluacion['alertas'])) {
            $detalles = json_encode([
                'registro_id' => $registroId,
                'estado' => $evaluacion['estado'],
                'alertas' => $evaluacion['alertas'],
                'presion' => "{$sistolica}/{$diastolica}",
                'glucosa' => $glucosaVal,
                'frecuencia_cardiaca' => $ritmoVal,
                'imc' => $imcVal
            ], JSON_UNESCAPED_UNICODE);

            AuthMiddleware::logAudit($pacienteId, 'registro_signos_anomalos', $detalles);
        } else {
            AuthMiddleware::logAudit($pacienteId, 'registro_signos_vitales', "Registro rutinario de signos vitales (ID: {$registroId})");
        }

        // Notificación al usuario
        $mensaje = 'Signos vitales registrados correctamente.';
        $flashType = 'success';

        if (!empty($evaluacion['alertas'])) {
            $mensaje .= ' ' . implode(' | ', $evaluacion['alertas']);
            $flashType = ($evaluacion['estado'] === SaludEvaluatorService::STATUS_CRITICAL) ? 'danger' : 'warning';
        }

        $this->setFlash($flashType, $mensaje);
        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }

    /**
     * Muestra la pantalla de confirmación para valores atípicos
     */
    public function mostrarConfirmacion(): void {
        AuthMiddleware::handle();
        $datos = $_SESSION['salud_datos_pendientes'] ?? [];

        if (empty($datos)) {
            header('Location: ' . APP_URL . '/salud');
            exit;
        }

        $pacienteId = (int)($_SESSION['paciente_activo_id'] ?? $datos['paciente_id'] ?? 0);
        AuthMiddleware::requirePatientAccess($pacienteId);

        $paciente = $this->pacienteModel->find($pacienteId);
        $mensajeAviso = $_SESSION['salud_evaluacion_mensaje'] ?? 'El valor ingresado parece inusual o fuera del rango habitual.';

        require_once dirname(__DIR__) . '/Views/salud/confirmar.php';
    }
}

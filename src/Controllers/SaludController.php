<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\RegistroSaludDiario;
use App\Middlewares\AuthMiddleware;
use App\Services\SaludEvaluatorService;

class SaludController extends Controller {
    private RegistroSaludDiario $saludModel;
    private SaludEvaluatorService $evaluatorService;

    public function __construct() {
        $this->saludModel = new RegistroSaludDiario();
        $this->evaluatorService = new SaludEvaluatorService();
    }

    public function registrar(): void {
        AuthMiddleware::handle();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlash('Método no permitido.', 'danger');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('Token de seguridad inválido.', 'danger');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // El paciente_activo_id lo tomamos de la sesión si es cuidador múltiple, si no, del paciente del usuario.
        // Asumimos que AuthController/DashboardController establece $_SESSION['paciente_activo_id']
        $pacienteId = (int)($_SESSION['paciente_activo_id'] ?? 0);
        
        if ($pacienteId === 0) {
            $this->setFlash('No hay paciente activo seleccionado.', 'danger');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $presionRaw = trim($_POST['presion'] ?? '');
        $glucosa = trim($_POST['glucosa'] ?? '');
        $momento_glucosa = $_POST['momento_glucosa'] ?? null;
        $ritmo_cardiaco = trim($_POST['ritmo_cardiaco'] ?? '');
        $horas_sueno = trim($_POST['horas_sueno'] ?? '');
        $nivel_dolor = trim($_POST['nivel_dolor'] ?? '');
        $confirmado = isset($_POST['confirmado_por_usuario']) && $_POST['confirmado_por_usuario'] == '1';

        // Procesar datos
        $datosEval = [];
        $sistolica = null; $diastolica = null;

        if (!empty($presionRaw)) {
            $partes = explode('/', $presionRaw);
            if (count($partes) === 2) {
                $sis = (int)$partes[0];
                $dia = (int)$partes[1];
                if ($sis > 0 && $dia > 0) {
                    $sistolica = $sis;
                    $diastolica = $dia;
                    $datosEval['presion_sistolica'] = $sistolica;
                    $datosEval['presion_diastolica'] = $diastolica;
                }
            }
        }

        $glucosaVal = null;
        if (!empty($glucosa)) {
            $glucosaVal = (float)$glucosa;
            $datosEval['glucosa'] = $glucosaVal;
            $datosEval['momento_glucosa'] = $momento_glucosa;
        }

        $ritmoVal = null;
        if (!empty($ritmo_cardiaco)) {
            $ritmoVal = (int)$ritmo_cardiaco;
            $datosEval['ritmo_cardiaco'] = $ritmoVal;
        }

        $evaluacion = $this->evaluatorService->evaluar($datosEval, $confirmado);

        if ($evaluacion['requiere_confirmacion']) {
            $_SESSION['salud_datos_pendientes'] = $_POST;
            $this->setFlash($evaluacion['mensaje_confirmacion'], 'warning');
            header('Location: ' . APP_URL . '/salud/confirmar');
            exit;
        }

        $horasVal = !empty($horas_sueno) ? (float)$horas_sueno : null;
        $dolorVal = !empty($nivel_dolor) ? (int)$nivel_dolor : null;

        $alertaEmitida = $evaluacion['estado'] === SaludEvaluatorService::STATUS_CRITICAL;

        $this->saludModel->agregar([
            'paciente_id' => $pacienteId,
            'presion_sistolica' => $sistolica,
            'presion_diastolica' => $diastolica,
            'glucosa' => $glucosaVal,
            'momento_glucosa' => $momento_glucosa,
            'ritmo_cardiaco' => $ritmoVal,
            'horas_sueno' => $horasVal,
            'nivel_dolor' => $dolorVal,
            'alerta_emitida' => $alertaEmitida ? 1 : 0,
            'confirmado_por_usuario' => $confirmado ? 1 : 0,
            'registrado_por' => $_SESSION['user_id']
        ]);

        $mensaje = 'Signos vitales registrados correctamente.';
        $flashType = 'success';
        if (!empty($evaluacion['alertas'])) {
            $mensaje .= ' ' . implode(' ', $evaluacion['alertas']);
            $flashType = $evaluacion['estado'] === SaludEvaluatorService::STATUS_CRITICAL ? 'danger' : 'warning';
        }

        $this->setFlash($mensaje, $flashType);
        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }

    public function mostrarConfirmacion(): void {
        AuthMiddleware::handle();
        $datos = $_SESSION['salud_datos_pendientes'] ?? [];
        if (empty($datos)) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
        $paciente = (new \App\Models\Paciente())->find((int)($_SESSION['paciente_activo_id'] ?? 0));
        require_once __DIR__ . '/../Views/salud/confirmar.php';
    }

    public function stepper(): void {
        AuthMiddleware::handle();
        $paciente = (new \App\Models\Paciente())->find((int)($_SESSION['paciente_activo_id'] ?? 0));
        require_once __DIR__ . '/../Views/salud/stepper.php';
    }
}

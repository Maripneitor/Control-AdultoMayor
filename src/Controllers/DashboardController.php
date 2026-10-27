<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\CitaLaboratorio;
use App\Models\Doctor;
use App\Models\ContactoEmergencia;
use App\Models\Cuidador;
use App\Core\Controller;
use App\Middlewares\AuthMiddleware;

class DashboardController extends Controller {
    private Paciente $pacienteModel;
    private Medicamento $medicamentoModel;
    private CitaLaboratorio $citaModel;
    private Doctor $doctorModel;
    private ContactoEmergencia $contactoModel;
    private Cuidador $cuidadorModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
        $this->medicamentoModel = new Medicamento();
        $this->citaModel = new CitaLaboratorio();
        $this->doctorModel = new Doctor();
        $this->contactoModel = new ContactoEmergencia();
        $this->cuidadorModel = new Cuidador();
    }

    public function index(): void {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $userRole = $_SESSION['user_role'] ?? '';

        // Si el usuario es Médico o Doctor, redirigir a su panel clínico especializado
        if (in_array($userRole, ['medico', 'doctor'], true)) {
            header('Location: ' . APP_URL . '/doctor/dashboard');
            exit;
        }

        // Si se solicita cambio de paciente
        if (isset($_GET['seleccionar_paciente'])) {
            $nuevoId = (int)$_GET['seleccionar_paciente'];
            if (AuthMiddleware::autorizarAccesoPaciente($nuevoId, $userId)) {
                $_SESSION['paciente_activo_id'] = $nuevoId;
                header('Location: ' . APP_URL . '/dashboard');
                exit;
            }
        }

        // Obtener todos los pacientes vinculados (propios y asignados como cuidador)
        $pacientePropio = $this->pacienteModel->findByUserId($userId);
        $pacientesComoCuidador = $this->cuidadorModel->obtenerPacientesPorCuidador($userId);
        
        $todosLosPacientes = [];
        if ($pacientePropio) {
            $todosLosPacientes[] = $pacientePropio;
        }
        foreach ($pacientesComoCuidador as $pc) {
            // Prevenir duplicados si se da el caso
            if (!in_array($pc['id'], array_column($todosLosPacientes, 'id'))) {
                $todosLosPacientes[] = $pc;
            }
        }

        if (empty($todosLosPacientes)) {
            $this->setFlash('info', 'Bienvenido(a) a VitalSenior. Para comenzar, registra el perfil del adulto mayor a tu cuidado o vincula un expediente existente mediante su código NIP.');
            header('Location: ' . APP_URL . '/pacientes/crear');
            exit;
        }

        // Almacenar lista de pacientes en sesión para acceso global
        $_SESSION['pacientes_asignados'] = $todosLosPacientes;

        // Determinar paciente activo
        $pacienteActivoId = (int)($_SESSION['paciente_activo_id'] ?? $todosLosPacientes[0]['id']);
        $_SESSION['paciente_activo_id'] = $pacienteActivoId;

        $paciente = null;
        foreach ($todosLosPacientes as $p) {
            if ($p['id'] == $pacienteActivoId) {
                $paciente = $p;
                break;
            }
        }

        if (!$paciente) {
            // Si por alguna razón el activo no está en la lista (ej. se quitó permiso), resetear
            $paciente = $todosLosPacientes[0];
            $pacienteActivoId = (int)$paciente['id'];
            $_SESSION['paciente_activo_id'] = $pacienteActivoId;
        }

        $_SESSION['paciente_activo_nombre'] = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? ''));
        $_SESSION['paciente_qr_token'] = $paciente['qr_token'] ?? '';

        $pacienteId = $pacienteActivoId;
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        $puedeEditar = $acceso['puede_editar'] ?? false;

        // 1.5. Generar tomas dinámicamente si no existen hoy
        $this->medicamentoModel->generarTomasDiarias($pacienteId);

        // 2. Consultar información médica real del paciente
        $medicamentosActivos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);
        $tomasDelDia = $this->medicamentoModel->getTodayDoses($pacienteId);
        $adherenciaSemanal = $this->medicamentoModel->calcularAdherenciaSemanal($pacienteId);
        $proximasCitas = $this->citaModel->getUpcomingByPacienteId($pacienteId, 5);
        $doctores = $this->doctorModel->getByPacienteId($pacienteId);
        $contactosEmergencia = $this->contactoModel->getByPacienteId($pacienteId);
        $alertasDosis = $this->medicamentoModel->getOmitidasHoy($pacienteId);
        $alertasInventario = $this->medicamentoModel->getBajoInventario($pacienteId);
        $alertaClinicaUrgente = $_SESSION['alerta_clinica_urgente'] ?? null;
        if ($alertaClinicaUrgente) {
            unset($_SESSION['alerta_clinica_urgente']);
        }

        // 3. Renderizar la vista pasando los datos según el rol
        $userRole = $_SESSION['user_role'] ?? '';
        
        if (in_array($userRole, ['paciente', 'adulto_mayor'], true)) {
            require_once __DIR__ . '/../Views/dashboard/index.php';
        } else {
            require_once __DIR__ . '/../Views/dashboard/cuidador.php';
        }
    }
}

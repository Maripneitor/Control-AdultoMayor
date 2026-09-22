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
            $userRole = $_SESSION['user_role'] ?? 'familiar';
            if ($userRole === 'familiar' || $userRole === 'cuidador') {
                // Redirección obligatoria a la Vista 5 (Vinculación) para Familiares sin pacientes vinculados
                header('Location: ' . APP_URL . '/vincular');
                exit;
            }

            $this->setFlash('warning', 'Aún no tienes un perfil de salud asociado. Por favor vincúlalo.');
            header('Location: ' . APP_URL . '/vincular');
            exit;
        }

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

        $pacienteId = $pacienteActivoId;

        // 1.5. Generar tomas dinámicamente si no existen hoy
        $this->medicamentoModel->generarTomasDiarias($pacienteId);

        // 2. Consultar información médica real del paciente
        $medicamentosActivos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);
        $tomasDelDia = $this->medicamentoModel->getTodayDoses($pacienteId);
        $proximasCitas = $this->citaModel->getUpcomingByPacienteId($pacienteId, 5);
        $doctores = $this->doctorModel->getByPacienteId($pacienteId);
        $contactosEmergencia = $this->contactoModel->getByPacienteId($pacienteId);

        // 3. Renderizar la vista pasando los datos
        $userRole = $_SESSION['user_role'] ?? '';
        
        if ($userRole === 'adulto_mayor') {
            require_once __DIR__ . '/../Views/dashboard/index.php';
        } else {
            require_once __DIR__ . '/../Views/dashboard/cuidador.php';
        }
    }
}

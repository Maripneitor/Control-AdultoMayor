<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\CitaLaboratorio;
use App\Models\Doctor;
use App\Models\ContactoEmergencia;

class DashboardController {
    private Paciente $pacienteModel;
    private Medicamento $medicamentoModel;
    private CitaLaboratorio $citaModel;
    private Doctor $doctorModel;
    private ContactoEmergencia $contactoModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
        $this->medicamentoModel = new Medicamento();
        $this->citaModel = new CitaLaboratorio();
        $this->doctorModel = new Doctor();
        $this->contactoModel = new ContactoEmergencia();
    }

    public function index(): void {
        $userId = (int)($_SESSION['user_id'] ?? 0);

        // 1. Obtener datos del paciente vinculado al usuario
        $paciente = $this->pacienteModel->findByUserId($userId);

        if (!$paciente) {
            $_SESSION['error'] = 'No se encontró un expediente asociado a esta cuenta.';
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $pacienteId = (int)$paciente['id'];

        // 1.5. Generar tomas dinámicamente si no existen hoy
        $this->medicamentoModel->generarTomasDiarias($pacienteId);

        // 2. Consultar información médica real del paciente
        $medicamentosActivos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);
        $tomasDelDia = $this->medicamentoModel->getTodayDoses($pacienteId);
        $proximasCitas = $this->citaModel->getUpcomingByPacienteId($pacienteId, 5);
        $doctores = $this->doctorModel->getByPacienteId($pacienteId);
        $contactosEmergencia = $this->contactoModel->getByPacienteId($pacienteId);

        // 3. Renderizar la vista pasando los datos
        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}

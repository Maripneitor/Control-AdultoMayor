<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\ContactoEmergencia;
use App\Models\Doctor;

class EmergenciaController {
    private Paciente $pacienteModel;
    private Medicamento $medicamentoModel;
    private ContactoEmergencia $contactoModel;
    private Doctor $doctorModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
        $this->medicamentoModel = new Medicamento();
        $this->contactoModel = new ContactoEmergencia();
        $this->doctorModel = new Doctor();
    }

    public function showPublicCard(): void {
        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            http_response_code(404);
            die("Ficha médica no encontrada o código inválido.");
        }

        $paciente = $this->pacienteModel->findByQrToken($token);

        if (!$paciente) {
            http_response_code(404);
            die("La ficha de emergencia solicitada no existe.");
        }

        $pacienteId = (int)$paciente['id'];
        $contactos = $this->contactoModel->getByPacienteId($pacienteId);
        $medicamentosActivos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);
        $doctores = $this->doctorModel->getByPacienteId($pacienteId);

        require_once __DIR__ . '/../Views/emergencia/ficha_publica.php';
    }
}

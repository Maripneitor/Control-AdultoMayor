<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Medicamento;
use App\Models\Paciente;

class MedicamentoController {
    private Medicamento $medicamentoModel;
    private Paciente $pacienteModel;

    public function __construct() {
        $this->medicamentoModel = new Medicamento();
        $this->pacienteModel = new Paciente();
    }

    public function index(): void {
        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);
        
        // Use getAllByPacienteId if it exists, otherwise active ones
        $medicamentos = $this->medicamentoModel->getAllByPacienteId((int)$paciente['id']);
        require_once __DIR__ . '/../Views/medicamentos/index.php';
    }

    public function store(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);

        $nombre = trim($_POST['nombre_comercial'] ?? '');
        $dosis = trim($_POST['dosis'] ?? '');
        $frecuenciaHoras = (int)($_POST['frecuencia_horas'] ?? 8);
        $instrucciones = trim($_POST['instrucciones'] ?? '');

        if (!empty($nombre) && !empty($dosis) && $frecuenciaHoras > 0) {
            $this->medicamentoModel->agregar([
                'paciente_id' => (int)$paciente['id'],
                'nombre_comercial' => $nombre,
                'dosis' => $dosis,
                'frecuencia_horas' => $frecuenciaHoras,
                'instrucciones' => $instrucciones
            ]);
        }

        header('Location: ' . APP_URL . '/medicamentos');
        exit;
    }

    public function registrarToma(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);

        $registroId = (int)($_POST['registro_id'] ?? 0);
        if ($registroId > 0 && $paciente) {
            $this->medicamentoModel->marcarTomaComoRealizada($registroId, (int)$paciente['id']);
        }

        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }
}

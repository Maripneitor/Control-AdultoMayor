<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\CitaLaboratorio;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Core\Controller;

class CitaController extends Controller {
    private CitaLaboratorio $citaModel;
    private Doctor $doctorModel;
    private Paciente $pacienteModel;

    public function __construct() {
        $this->citaModel = new CitaLaboratorio();
        $this->doctorModel = new Doctor();
        $this->pacienteModel = new Paciente();
    }

    public function index(): void {
        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);
        $pacienteId = (int)$paciente['id'];

        $citas = $this->citaModel->getAllByPacienteId($pacienteId);
        $doctores = $this->doctorModel->getByPacienteId($pacienteId);

        $this->render('citas/index', [
            'paciente' => $paciente,
            'citas' => $citas,
            'doctores' => $doctores
        ], false);
    }

    public function store(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);

        $tipo = $_POST['tipo_evento'] ?? 'cita_medica';
        $titulo = trim($_POST['titulo'] ?? '');
        $lugar = trim($_POST['lugar'] ?? '');
        $fechaHora = $_POST['fecha_hora'] ?? '';
        $doctorId = !empty($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : null;
        $notas = trim($_POST['notas'] ?? '');

        if (!empty($titulo) && !empty($lugar) && !empty($fechaHora)) {
            // Validar que la cita no esté en el pasado
            if (strtotime($fechaHora) < time()) {
                $this->setFlash('warning', "No puedes agendar citas en el pasado.");
                header('Location: ' . APP_URL . '/citas');
                exit;
            }

            // Validar solapamiento (± 60 minutos)
            if ($this->citaModel->checkOverlap((int)$paciente['id'], $fechaHora, 60)) {
                $this->setFlash('danger', "Ya existe una cita programada muy cerca de este horario.");
                header('Location: ' . APP_URL . '/citas');
                exit;
            }

            $this->citaModel->agregar([
                'paciente_id' => (int)$paciente['id'],
                'tipo_evento' => $tipo,
                'titulo' => $titulo,
                'lugar' => $lugar,
                'fecha_hora' => $fechaHora,
                'doctor_id' => $doctorId,
                'notas' => $notas
            ]);
            
            $this->setFlash('success', 'La cita fue programada correctamente.');
        }

        header('Location: ' . APP_URL . '/citas');
        exit;
    }
}

<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\ContactoEmergencia;
use App\Models\Doctor;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Middlewares\AuthMiddleware;

class PacienteController extends Controller {

    public function expediente(): void {
        AuthMiddleware::handle();
        $userId = $_SESSION['user_id'] ?? null;
        $pacienteModel = new Paciente();
        
        $pacienteId = $_GET['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? null);
        if (!$pacienteId) {
            $paciente = $pacienteModel->findByUserId($userId);
            $pacienteId = $paciente['id'] ?? null;
        } else {
            $paciente = $pacienteModel->find($pacienteId, [
                'id', 'usuario_id', 'uuid_publico', 'nombre', 'apellidos', 
                'fecha_nacimiento', 'genero', 'tipo_sangre', 'peso', 'altura', 
                'alergias', 'enfermedades_cronicas', 'codigo_vinculacion', 'zona_horaria'
            ]);
        }

        // Aislamiento Multitenant con ofuscación 404
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        $puedeEditar = $acceso['puede_editar'];

        $doctorModel = new Doctor();
        $doctores = $doctorModel->getByPacienteId($pacienteId);

        $this->render('pacientes/expediente', [
            'paciente' => $paciente,
            'doctores' => $doctores,
            'puedeEditar' => $puedeEditar,
            'nivelAcceso' => $acceso['nivel_acceso']
        ], false);
    }

    public function actualizarExpediente(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Error de seguridad: Token CSRF inválido o expirado.');
            header("Location: " . APP_URL . "/expediente");
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        if ($pacienteId === 0) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId((int)($_SESSION['user_id'] ?? 0));
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        // Validación de permisos multitenant
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (!$acceso['puede_editar']) {
            http_response_code(403);
            die('Error 403: Tu cuenta tiene permisos de solo lectura sobre este expediente.');
        }

        $seccion = trim($_POST['seccion'] ?? '');
        $usuarioId = (int)($_SESSION['user_id'] ?? 0);

        $db = \Config\Database::getInstance()->getConnection();
        
        try {
            // Iniciar Transacción ACID estricta para garantizar atomicidad
            $db->beginTransaction();

            $pacienteModel = new Paciente();
            $usuarioModel = new \App\Models\Usuario();

            if ($seccion === 'datos') {
                $tiposValidos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'N/D'];
                $tipoSangre = $_POST['tipo_sangre'] ?? 'N/D';
                if (!in_array($tipoSangre, $tiposValidos, true)) {
                    $tipoSangre = 'N/D';
                }

                $pacienteModel->updateDatosBasicos($pacienteId, [
                    'tipo_sangre' => $tipoSangre,
                    'peso' => $_POST['peso'] ?? '',
                    'altura' => $_POST['altura'] ?? ''
                ]);

                // Registro de auditoría clínica dentro de la misma transacción
                $usuarioModel->registrarAuditoria(
                    'EXPEDIENTE_DATOS_ACTUALIZADOS',
                    "Datos clínicos básicos actualizados para el paciente ID {$pacienteId} por usuario ID {$usuarioId}."
                );

                $this->setFlash('success', 'Datos básicos del expediente actualizados correctamente.');

            } elseif ($seccion === 'padecimientos') {
                $pacienteModel->updatePadecimientos($pacienteId, [
                    'alergias' => trim($_POST['alergias'] ?? ''),
                    'enfermedades_cronicas' => trim($_POST['enfermedades_cronicas'] ?? '')
                ]);

                // Registro de auditoría clínica dentro de la misma transacción
                $usuarioModel->registrarAuditoria(
                    'EXPEDIENTE_PADECIMIENTOS_ACTUALIZADOS',
                    "Alergias y padecimientos actualizados para el paciente ID {$pacienteId} por usuario ID {$usuarioId}."
                );

                $this->setFlash('success', 'Alergias y padecimientos actualizados correctamente.');

            } else {
                throw new \InvalidArgumentException("Sección del expediente médica no válida: '{$seccion}'");
            }

            // Confirmar transacción únicamente si ambas operaciones tuvieron éxito
            $db->commit();

        } catch (\Throwable $e) {
            // Revertir cualquier mutación previa en caso de fallo
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log("[ExpedienteController::actualizar Error] Transacción abortada: " . $e->getMessage());
            $this->setFlash('danger', 'Error crítico al procesar la actualización del expediente. Los cambios fueron revertidos.');
        }

        header("Location: " . APP_URL . "/expediente");
        exit;
    }

    public function anadirDoctor(): void {
        AuthMiddleware::requirePermission('gestionar_expediente');
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $pacienteId = (int)($_SESSION['paciente_activo_id'] ?? 0);
        if ($pacienteId === 0) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId((int)$_SESSION['user_id']);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        if ($pacienteId > 0) {
            $doctorModel = new Doctor();
            $doctorModel->agregar([
                'paciente_id' => $pacienteId,
                'nombre' => trim($_POST['nombre'] ?? ''),
                'especialidad' => trim($_POST['especialidad'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? '')
            ]);
            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('DOCTOR_AÑADIDO', "Médico añadido al expediente del paciente $pacienteId.");
            $this->setFlash('success', 'Médico añadido correctamente.');
        }

        header("Location: " . APP_URL . "/expediente");
        exit;
    }

    public function eliminarDoctor(): void {
        AuthMiddleware::requirePermission('gestionar_expediente');
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $pacienteId = (int)($_SESSION['paciente_activo_id'] ?? 0);
        if ($pacienteId === 0) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId((int)$_SESSION['user_id']);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);

        if ($pacienteId > 0 && $doctorId > 0) {
            $doctorModel = new Doctor();
            $doctorModel->eliminar($doctorId, $pacienteId);
            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('DOCTOR_ELIMINADO', "Médico ID $doctorId eliminado del expediente del paciente $pacienteId.");
            $this->setFlash('success', 'Médico eliminado correctamente.');
        }

        header("Location: " . APP_URL . "/expediente");
        exit;
    }

    public function descargarExpediente() {
        if (!isset($_GET['id'])) {
            $this->setFlash('danger', 'ID de paciente no válido');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        $pacienteId = (int)$_GET['id'];
        $usuarioId = $_SESSION['user_id'] ?? null;

        if (!AuthMiddleware::autorizarAccesoPaciente($pacienteId, $usuarioId)) {
            $this->setFlash('danger', 'No tienes permiso para acceder a este expediente');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($pacienteId);

        if (!$paciente) {
            $this->setFlash('danger', 'Paciente no encontrado');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        $medicamentoModel = new Medicamento();
        $medicamentos = $medicamentoModel->getAllByPacienteId($pacienteId);

        $contactoModel = new ContactoEmergencia();
        $contactos = $contactoModel->getByPacienteId($pacienteId);

        $saludModel = new \App\Models\RegistroSaludDiario();
        $registrosSalud = $saludModel->getAllByPacienteId($pacienteId, 7); // Últimos 7 días

        // Generar URL para QuickChart
        $fechas = [];
        $sisData = [];
        $diaData = [];
        $glucosaData = [];
        
        // Invertimos para que el gráfico vaya cronológicamente de izquierda a derecha
        foreach (array_reverse($registrosSalud) as $reg) {
            $fechas[] = date('d/m', strtotime($reg['created_at']));
            $sisData[] = $reg['presion_sistolica'] ?? 'null';
            $diaData[] = $reg['presion_diastolica'] ?? 'null';
            $glucosaData[] = $reg['glucosa'] ?? 'null';
        }

        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $fechas,
                'datasets' => [
                    ['label' => 'Sistólica', 'data' => $sisData, 'borderColor' => 'red', 'fill' => false],
                    ['label' => 'Diastólica', 'data' => $diaData, 'borderColor' => 'blue', 'fill' => false],
                    ['label' => 'Glucosa', 'data' => $glucosaData, 'borderColor' => 'green', 'fill' => false]
                ]
            ],
            'options' => [
                'title' => ['display' => true, 'text' => 'Evolución últimos 7 registros']
            ]
        ];

        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig)) . '&w=600&h=300';

        // Promedios
        $avgSis = !empty($sisData) && max($sisData) !== 'null' ? array_sum(array_filter($sisData, 'is_numeric')) / max(1, count(array_filter($sisData, 'is_numeric'))) : 0;
        $avgDia = !empty($diaData) && max($diaData) !== 'null' ? array_sum(array_filter($diaData, 'is_numeric')) / max(1, count(array_filter($diaData, 'is_numeric'))) : 0;
        $avgGluc = !empty($glucosaData) && max($glucosaData) !== 'null' ? array_sum(array_filter($glucosaData, 'is_numeric')) / max(1, count(array_filter($glucosaData, 'is_numeric'))) : 0;

        // Si dompdf no está instalado (el autoload fallará si no está). 
        // Verificamos si la clase existe antes de instanciar.
        if (!class_exists('Dompdf\Dompdf')) {
            $this->setFlash('danger', 'El motor PDF (dompdf) no está instalado. Ejecute "composer install".');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        try {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            ob_start();
            require __DIR__ . '/../Views/pacientes/pdf_resumen.php';
            $html = ob_get_clean();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("expediente_" . ($paciente['nombre'] ?? 'paciente') . ".pdf", ["Attachment" => true]);
        } catch (\Exception $e) {
            $this->setFlash('danger', 'Error al generar el PDF: ' . $e->getMessage());
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }
    }
}

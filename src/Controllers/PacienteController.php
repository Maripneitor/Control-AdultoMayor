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
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pacienteModel = new Paciente();
        
        $pacienteId = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : (int)($_SESSION['paciente_activo_id'] ?? 0);
        
        if ($pacienteId === 0) {
            $paciente = $pacienteModel->findByUserId($userId);
            if (!$paciente) {
                $cuidadorModel = new \App\Models\Cuidador();
                $pacientesAsignados = $cuidadorModel->obtenerPacientesPorCuidador($userId);
                if (!empty($pacientesAsignados)) {
                    $paciente = $pacientesAsignados[0];
                }
            }
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        } else {
            $paciente = $pacienteModel->getExpedienteById($pacienteId);
        }

        if (!$paciente || $pacienteId === 0) {
            $this->setFlash('warning', 'No se encontró el expediente solicitado o no tienes pacientes vinculados.');
            header('Location: ' . APP_URL . '/vincular');
            exit;
        }

        // Aislamiento Multitenant con ofuscación 404
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        $puedeEditar = $acceso['puede_editar'];

        // Sincronización estricta de la sesión activa del paciente
        $_SESSION['paciente_activo_id'] = $pacienteId;

        $doctorModel = new Doctor();
        $doctores = $doctorModel->getByPacienteId($pacienteId);

        $contactoModel = new ContactoEmergencia();
        $contactosEmergencia = $contactoModel->getByPacienteId($pacienteId);

        $saludModel = new \App\Models\RegistroSaludDiario();
        $ultimoSalud = $saludModel->getTodayByPacienteId($pacienteId);

        $visitaModel = new \App\Models\VisitaMedica();
        $visitasMedicas = $visitaModel->getByPacienteId($pacienteId);

        $todosLosPacientes = $pacienteModel->obtenerTodosVinculados($userId);

        $this->render('pacientes/expediente', [
            'paciente' => $paciente,
            'todosLosPacientes' => $todosLosPacientes,
            'doctores' => $doctores,
            'contactosEmergencia' => $contactosEmergencia,
            'ultimoSalud' => $ultimoSalud,
            'visitasMedicas' => $visitasMedicas,
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

        $_SESSION['paciente_activo_id'] = $pacienteId;
        $seccion = trim($_POST['seccion'] ?? '');
        $usuarioId = (int)($_SESSION['user_id'] ?? 0);

        $db = \Config\Database::getInstance()->getConnection();
        
        try {
            // Iniciar Transacción ACID estricta para garantizar atomicidad
            $db->beginTransaction();

            $pacienteModel = new Paciente();
            $usuarioModel = new \App\Models\Usuario();

            if ($seccion === 'datos') {
                $nombre = trim($_POST['nombre'] ?? '');
                $apellidos = trim($_POST['apellidos'] ?? '');
                $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? date('Y-m-d'));
                
                if (empty($nombre)) {
                    throw new \InvalidArgumentException('El nombre del paciente no puede estar vacío.');
                }

                $tiposValidos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'N/D'];
                $tipoSangre = $_POST['tipo_sangre'] ?? 'N/D';
                if (!in_array($tipoSangre, $tiposValidos, true)) {
                    $tipoSangre = 'N/D';
                }

                $pacienteModel->updateDatosBasicos($pacienteId, [
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'fecha_nacimiento' => $fechaNacimiento,
                    'tipo_sangre' => $tipoSangre
                ]);

                // Registro de auditoría clínica dentro de la misma transacción
                $usuarioModel->registrarAuditoria(
                    'EXPEDIENTE_DATOS_ACTUALIZADOS',
                    "Datos clínicos y demográficos actualizados para el paciente ID {$pacienteId} por usuario ID {$usuarioId}."
                );

                $this->setFlash('success', 'Ficha general y datos demográficos actualizados correctamente.');

            } elseif ($seccion === 'padecimientos') {
                $alergias = trim($_POST['alergias'] ?? '');
                $enfermedadesCronicas = trim($_POST['enfermedades_cronicas'] ?? '');
                $notasCognitivas = trim($_POST['notas_cognitivas'] ?? '');

                $pacienteModel->updatePadecimientos($pacienteId, [
                    'alergias' => $alergias,
                    'enfermedades_cronicas' => $enfermedadesCronicas,
                    'notas_cognitivas' => $notasCognitivas
                ]);

                // Registro de auditoría clínica dentro de la misma transacción
                $usuarioModel->registrarAuditoria(
                    'EXPEDIENTE_PADECIMIENTOS_ACTUALIZADOS',
                    "Alergias, padecimientos crónicos y estado funcional actualizados para el paciente ID {$pacienteId} por usuario ID {$usuarioId}."
                );

                $this->setFlash('success', 'Antecedentes clínicos y estado funcional actualizados correctamente.');

            } else {
                throw new \InvalidArgumentException("Sección del expediente médica no válida: '{$seccion}'");
            }

            // Confirmar transacción únicamente si ambas operaciones tuvieron éxito
            $db->commit();

        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log("[ExpedienteController::actualizar Error] Transacción abortada: " . $e->getMessage());
            $this->setFlash('danger', 'Error al actualizar el expediente: ' . $e->getMessage());
        }

        header("Location: " . APP_URL . "/expediente?paciente_id=" . $pacienteId);
        exit;
    }

    public function anadirDoctor(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad CSRF inválido.');
            header("Location: " . APP_URL . "/expediente");
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        if ($pacienteId === 0) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId((int)$_SESSION['user_id']);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        // Validación Multitenant y control de permisos
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (!$acceso['puede_editar']) {
            http_response_code(403);
            die('Error 403: No tienes permisos para añadir médicos a este expediente.');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if ($pacienteId > 0 && !empty($nombre)) {
            $doctorModel = new Doctor();
            $doctorModel->agregar([
                'paciente_id' => $pacienteId,
                'nombre' => $nombre,
                'especialidad' => trim($_POST['especialidad'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'hospital_clinica' => trim($_POST['hospital_clinica'] ?? ''),
                'horario_atencion' => trim($_POST['horario_atencion'] ?? '')
            ]);
            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('DOCTOR_AÑADIDO', "Médico $nombre añadido al expediente del paciente $pacienteId.");
            $this->setFlash('success', 'Médico especialista añadido correctamente.');
        } else {
            $this->setFlash('warning', 'El nombre del médico es obligatorio.');
        }

        header("Location: " . APP_URL . "/expediente?paciente_id=" . $pacienteId . "#tab-doctores");
        exit;
    }

    public function eliminarDoctor(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad CSRF inválido.');
            header("Location: " . APP_URL . "/expediente");
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        if ($pacienteId === 0) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId((int)$_SESSION['user_id']);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        // Validación Multitenant y control de permisos
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (!$acceso['puede_editar']) {
            http_response_code(403);
            die('Error 403: No tienes permisos para eliminar médicos de este expediente.');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);

        if ($pacienteId > 0 && $doctorId > 0) {
            $doctorModel = new Doctor();
            $doctorModel->eliminar($doctorId, $pacienteId);
            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('DOCTOR_ELIMINADO', "Médico ID $doctorId eliminado del expediente del paciente $pacienteId.");
            $this->setFlash('success', 'Médico eliminado correctamente.');
        }

        header("Location: " . APP_URL . "/expediente?paciente_id=" . $pacienteId . "#tab-doctores");
        exit;
    }

    public function anadirContacto(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad CSRF inválido.');
            header("Location: " . APP_URL . "/expediente");
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        if ($pacienteId === 0) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId((int)$_SESSION['user_id']);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        // Validación Multitenant y control de permisos
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (!$acceso['puede_editar']) {
            http_response_code(403);
            die('Error 403: No tienes permisos para añadir contactos de auxilio a este expediente.');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $parentesco = trim($_POST['parentesco'] ?? 'Familiar');
        $esPrioritario = isset($_POST['es_prioritario']) && $_POST['es_prioritario'] == '1';

        if ($pacienteId > 0 && !empty($nombre) && !empty($telefono)) {
            $contactoModel = new ContactoEmergencia();
            $contactoModel->agregar([
                'paciente_id' => $pacienteId,
                'nombre' => $nombre,
                'parentesco' => $parentesco,
                'telefono' => $telefono,
                'es_prioritario' => $esPrioritario
            ]);

            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('CONTACTO_AÑADIDO', "Contacto $nombre añadido al expediente $pacienteId.");
            $this->setFlash('success', 'Contacto de emergencia añadido exitosamente.');
        } else {
            $this->setFlash('warning', 'El nombre y teléfono del contacto son obligatorios.');
        }

        header("Location: " . APP_URL . "/expediente?paciente_id=" . $pacienteId . "#tab-contactos");
        exit;
    }

    public function eliminarContacto(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad CSRF inválido.');
            header("Location: " . APP_URL . "/expediente");
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        if ($pacienteId === 0) {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId((int)$_SESSION['user_id']);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        // Validación Multitenant y control de permisos
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (!$acceso['puede_editar']) {
            http_response_code(403);
            die('Error 403: No tienes permisos para eliminar contactos de este expediente.');
        }

        $contactoId = (int)($_POST['contacto_id'] ?? 0);

        if ($pacienteId > 0 && $contactoId > 0) {
            $contactoModel = new ContactoEmergencia();
            $contactoModel->eliminar($contactoId, $pacienteId);
            $usuarioModel = new \App\Models\Usuario();
            $usuarioModel->registrarAuditoria('CONTACTO_ELIMINADO', "Contacto ID $contactoId eliminado del expediente $pacienteId.");
            $this->setFlash('success', 'Contacto de emergencia eliminado.');
        }

        header("Location: " . APP_URL . "/expediente?paciente_id=" . $pacienteId . "#tab-contactos");
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

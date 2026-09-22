<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\CitaLaboratorio;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\Cuidador;
use App\Models\Usuario;
use App\Core\Controller;
use App\Middlewares\AuthMiddleware;

class CitaController extends Controller {
    private CitaLaboratorio $citaModel;
    private Doctor $doctorModel;
    private Paciente $pacienteModel;
    private Cuidador $cuidadorModel;
    private Usuario $usuarioModel;

    public function __construct() {
        $this->citaModel = new CitaLaboratorio();
        $this->doctorModel = new Doctor();
        $this->pacienteModel = new Paciente();
        $this->cuidadorModel = new Cuidador();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Muestra la agenda y timeline de citas médicas y estudios
     */
    public function index(): void {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user_id'] ?? 0);
        
        // 1. Obtener ID del paciente activo
        $pacienteId = (int)($_GET['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        
        if ($pacienteId > 0) {
            $paciente = $this->pacienteModel->find($pacienteId);
        } else {
            $paciente = $this->pacienteModel->findByUserId($userId);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        if (!$paciente || $pacienteId === 0) {
            $this->setFlash('warning', 'Debes seleccionar o vincular un paciente primero.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        // 2. Validación Multitenant y control de permisos
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        $puedeEditar = $acceso['puede_editar'] ?? false;
        $_SESSION['paciente_activo_id'] = $pacienteId;

        // 3. Filtros de visualización
        $filtro = $_GET['filtro'] ?? 'proximas';
        if (!in_array($filtro, ['proximas', 'pasadas', 'estudios', 'todos'], true)) {
            $filtro = 'proximas';
        }

        $citas = $this->citaModel->getAllByPacienteId($pacienteId, $filtro);
        $doctores = $this->doctorModel->getByPacienteId($pacienteId);
        
        // Cuidadores / familiares disponibles para asignar responsabilidad
        $cuidadores = $this->cuidadorModel->obtenerCuidadoresPorPaciente($pacienteId);
        
        // Asegurar que el usuario actual aparezca en la lista de responsables si no está ya
        $usuarioActual = $this->usuarioModel->find($userId);
        if ($usuarioActual && !in_array($userId, array_column($cuidadores, 'id'), true)) {
            $cuidadores[] = [
                'id' => $usuarioActual['id'],
                'nombre' => $usuarioActual['nombre'] . ' (Tú)',
                'email' => $usuarioActual['email'],
                'parentesco' => 'Titular/Cuidador'
            ];
        }

        $this->render('citas/index', [
            'paciente' => $paciente,
            'citas' => $citas,
            'doctores' => $doctores,
            'cuidadores' => $cuidadores,
            'filtro' => $filtro,
            'puedeEditar' => $puedeEditar,
            'nivelAcceso' => $acceso['nivel_acceso'] ?? 'lectura'
        ], 'main');
    }

    /**
     * Registra un nuevo evento/cita/laboratorio
     */
    public function store(): void {
        AuthMiddleware::handle();
        AuthMiddleware::requirePermission('gestionar_citas');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o expirado.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));

        if ($pacienteId <= 0) {
            $paciente = $this->pacienteModel->findByUserId($userId);
            $pacienteId = $paciente ? (int)$paciente['id'] : 0;
        }

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (empty($acceso['puede_editar'])) {
            $this->setFlash('danger', 'No tienes permisos para agendar citas para este paciente.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $tipoRaw = trim($_POST['tipo_evento'] ?? 'consulta');
        $tiposValidos = ['consulta', 'laboratorio_estudio', 'terapia', 'resurtido_receta', 'cita_medica', 'laboratorio'];
        $tipo = in_array($tipoRaw, $tiposValidos, true) ? $tipoRaw : 'consulta';
        // Normalizar tipos legacy si aplican
        if ($tipo === 'cita_medica') $tipo = 'consulta';
        if ($tipo === 'laboratorio') $tipo = 'laboratorio_estudio';

        $titulo = trim($_POST['titulo'] ?? '');
        $lugar = trim($_POST['lugar'] ?? '');
        $fechaHora = trim($_POST['fecha_hora'] ?? '');
        $doctorId = !empty($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : null;
        $familiarId = !empty($_POST['familiar_responsable_id']) ? (int)$_POST['familiar_responsable_id'] : null;
        $preparacionPrevia = trim($_POST['preparacion_previa'] ?? '');
        $ubicacionMapa = trim($_POST['ubicacion_mapa'] ?? '');
        $notas = trim($_POST['notas'] ?? '');

        if (empty($titulo) || empty($lugar) || empty($fechaHora)) {
            $this->setFlash('danger', 'Por favor completa todos los campos requeridos (Título, Lugar y Fecha).');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        // Validar formato fecha
        $timestamp = strtotime($fechaHora);
        if ($timestamp === false) {
            $this->setFlash('danger', 'Formato de fecha y hora no válido.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        // Validar que la cita no esté en el pasado
        if ($timestamp < time()) {
            $this->setFlash('warning', 'No puedes agendar citas en fechas pasadas.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        // Validar solapamiento (± 45 minutos)
        if ($this->citaModel->checkOverlap($pacienteId, $fechaHora, 45)) {
            $this->setFlash('warning', 'Aviso: Ya existe un evento programado en un horario muy cercano (±45 min).');
        }

        $citaId = $this->citaModel->agregar([
            'paciente_id' => $pacienteId,
            'tipo_evento' => $tipo,
            'titulo' => $titulo,
            'lugar' => $lugar,
            'fecha_hora' => date('Y-m-d H:i:s', $timestamp),
            'doctor_id' => $doctorId,
            'familiar_responsable_id' => $familiarId,
            'preparacion_previa' => $preparacionPrevia ?: null,
            'ubicacion_mapa' => $ubicacionMapa ?: null,
            'estado' => 'programada',
            'notas' => $notas ?: null
        ]);

        AuthMiddleware::logAudit(
            $pacienteId,
            'crear_cita',
            "Cita '{$titulo}' ({$tipo}) agendada para el " . date('d/m/Y H:i', $timestamp) . " (ID: {$citaId})"
        );

        $this->setFlash('success', '¡Cita médica agendada exitosamente en el calendario!');
        header('Location: ' . APP_URL . '/citas');
        exit;
    }

    /**
     * Marca una cita como completada
     */
    public function completar(): void {
        AuthMiddleware::handle();
        AuthMiddleware::requirePermission('gestionar_citas');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad inválido.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $citaId = (int)($_POST['cita_id'] ?? 0);
        $cita = $this->citaModel->findById($citaId);

        if (!$cita) {
            $this->setFlash('danger', 'Cita médica no encontrada.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $acceso = AuthMiddleware::requirePatientAccess((int)$cita['paciente_id']);
        if (empty($acceso['puede_editar'])) {
            $this->setFlash('danger', 'No tienes permisos para modificar esta cita.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $this->citaModel->cambiarEstado($citaId, 'completada');

        AuthMiddleware::logAudit(
            (int)$cita['paciente_id'],
            'completar_cita',
            "Cita '{$cita['titulo']}' (ID: {$citaId}) marcada como completada/realizada."
        );

        $this->setFlash('success', 'Cita marcada como completada con éxito.');
        header('Location: ' . APP_URL . '/citas');
        exit;
    }

    /**
     * Cancela una cita programada
     */
    public function cancelar(): void {
        AuthMiddleware::handle();
        AuthMiddleware::requirePermission('gestionar_citas');

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad inválido.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $citaId = (int)($_POST['cita_id'] ?? 0);
        $cita = $this->citaModel->findById($citaId);

        if (!$cita) {
            $this->setFlash('danger', 'Cita médica no encontrada.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $acceso = AuthMiddleware::requirePatientAccess((int)$cita['paciente_id']);
        if (empty($acceso['puede_editar'])) {
            $this->setFlash('danger', 'No tienes permisos para cancelar esta cita.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        $this->citaModel->cambiarEstado($citaId, 'cancelada');

        AuthMiddleware::logAudit(
            (int)$cita['paciente_id'],
            'cancelar_cita',
            "Cita '{$cita['titulo']}' (ID: {$citaId}) cancelada."
        );

        $this->setFlash('warning', 'La cita médica ha sido cancelada.');
        header('Location: ' . APP_URL . '/citas');
        exit;
    }

    /**
     * Genera y descarga el archivo iCalendar (.ics) estándar
     */
    public function exportarIcs(): void {
        AuthMiddleware::handle();
        $citaId = (int)($_GET['id'] ?? 0);
        $cita = $this->citaModel->findById($citaId);

        if (!$cita) {
            $this->setFlash('danger', 'Cita no encontrada para exportar.');
            header('Location: ' . APP_URL . '/citas');
            exit;
        }

        AuthMiddleware::requirePatientAccess((int)$cita['paciente_id']);

        $timestamp = strtotime($cita['fecha_hora']);
        $dtStart = gmdate('Ymd\THis\Z', $timestamp);
        $dtEnd = gmdate('Ymd\THis\Z', $timestamp + 3600); // 1 hora de duración estimada
        $dtStamp = gmdate('Ymd\THis\Z');
        $uid = 'vitalsenior-cita-' . $cita['id'] . '@' . parse_url(APP_URL, PHP_URL_HOST);

        $tipoLegible = [
            'consulta' => 'Consulta Médica',
            'laboratorio_estudio' => 'Laboratorio / Estudio',
            'terapia' => 'Sesión de Terapia',
            'resurtido_receta' => 'Resurtido de Receta',
            'cita_medica' => 'Consulta Médica',
            'laboratorio' => 'Laboratorio / Estudio'
        ][$cita['tipo_evento']] ?? 'Evento Médico';

        $summary = $this->escapeIcsString("{$tipoLegible}: {$cita['titulo']}");
        $location = $this->escapeIcsString($cita['lugar']);
        
        $descParts = [];
        $descParts[] = "Tipo: {$tipoLegible}";
        if (!empty($cita['doctor_nombre'])) {
            $descParts[] = "Médico Tratante: Dr(a). {$cita['doctor_nombre']}";
        }
        if (!empty($cita['preparacion_previa'])) {
            $descParts[] = "Preparación / Indicaciones: {$cita['preparacion_previa']}";
        }
        if (!empty($cita['notas'])) {
            $descParts[] = "Notas: {$cita['notas']}";
        }
        $descParts[] = "Gestionado a través de VitalSenior.";
        $description = $this->escapeIcsString(implode("\n", $descParts));

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//VitalSenior//Agenda Medica//ES\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:{$dtStamp}\r\n";
        $ics .= "DTSTART:{$dtStart}\r\n";
        $ics .= "DTEND:{$dtEnd}\r\n";
        $ics .= "SUMMARY:{$summary}\r\n";
        $ics .= "DESCRIPTION:{$description}\r\n";
        $ics .= "LOCATION:{$location}\r\n";
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "BEGIN:VALARM\r\n";
        $ics .= "TRIGGER:-PT2H\r\n"; // Recordatorio 2 horas antes
        $ics .= "ACTION:DISPLAY\r\n";
        $ics .= "DESCRIPTION:Recordatorio de Cita VitalSenior: {$summary}\r\n";
        $ics .= "END:VALARM\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        $filename = 'cita_vitalsenior_' . $cita['id'] . '.ics';
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($ics));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $ics;
        exit;
    }

    private function escapeIcsString(string $string): string {
        $string = str_replace('\\', '\\\\', $string);
        $string = str_replace(';', '\;', $string);
        $string = str_replace(',', '\,', $string);
        $string = str_replace(["\r\n", "\n", "\r"], "\\n", $string);
        return $string;
    }
}

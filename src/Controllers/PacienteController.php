<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\ContactoEmergencia;
use App\Models\Doctor;
use App\Services\PdfReportService;
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
        $userRole = $_SESSION['user_role'] ?? 'familiar';
        $esDoctor = in_array($userRole, ['doctor', 'medico'], true) || ($acceso['nivel_acceso'] ?? '') === 'doctor';
        $nivelAcceso = $acceso['nivel_acceso'] ?? ($esDoctor ? 'doctor' : 'solo_lectura');
        $puedeEditar = in_array($nivelAcceso, ['principal', 'completo'], true) && !$esDoctor;
        $puedeSuministrar = in_array($nivelAcceso, ['principal', 'completo'], true) || in_array($userRole, ['paciente', 'adulto_mayor'], true);

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

        $recetas = $doctorModel->obtenerRecetasPorPaciente($pacienteId);

        $todosLosPacientes = $pacienteModel->obtenerTodosVinculados($userId);

        $this->render('pacientes/expediente', [
            'paciente' => $paciente,
            'todosLosPacientes' => $todosLosPacientes,
            'doctores' => $doctores,
            'contactosEmergencia' => $contactosEmergencia,
            'ultimoSalud' => $ultimoSalud,
            'visitasMedicas' => $visitasMedicas,
            'recetas' => $recetas,
            'puedeEditar' => $puedeEditar,
            'puedeSuministrar' => $puedeSuministrar,
            'esDoctor' => $esDoctor,
            'nivelAcceso' => $nivelAcceso
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

    /**
     * Exporta el resumen clínico y expediente del paciente en formato PDF.
     * Valida permisos multitenant (paciente o cuidador vinculado con acceso).
     */
    public function descargarReporte(): void {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pacienteModel = new Paciente();

        $pacienteId = isset($_GET['paciente_id']) ? (int)$_GET['paciente_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : (int)($_SESSION['paciente_activo_id'] ?? 0));

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
            $paciente = $pacienteModel->find($pacienteId);
        }

        if (!$paciente || $pacienteId === 0) {
            $this->setFlash('danger', 'Paciente o expediente clínico no encontrado.');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        // Aislamiento Multitenant con ofuscación 404
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);

        // Modelos de consulta de datos clínicos
        $medicamentoModel = new Medicamento();
        $medicamentos = $medicamentoModel->getAllByPacienteId($pacienteId);
        $adherencia = $medicamentoModel->calcularAdherenciaSemanal($pacienteId);

        $contactoModel = new ContactoEmergencia();
        $contactos = $contactoModel->getByPacienteId($pacienteId);

        $saludModel = new \App\Models\RegistroSaludDiario();
        $registrosSalud = $saludModel->getAllByPacienteId($pacienteId, 30);

        $doctorModel = new Doctor();
        $recetas = $doctorModel->obtenerRecetasPorPaciente($pacienteId);

        $visitaModel = new \App\Models\VisitaMedica();
        $visitas = $visitaModel->getByPacienteId($pacienteId);

        // Registro de auditoría
        AuthMiddleware::logAudit(
            $pacienteId,
            'DESCARGA_REPORTE_PDF',
            "Reporte clínico en PDF descargado por el usuario ID {$userId}."
        );

        $pdfService = new PdfReportService();
        $pdfService->generarResumenPaciente([
            'paciente' => $paciente,
            'signos' => $registrosSalud,
            'medicamentos' => $medicamentos,
            'adherencia' => $adherencia,
            'contactos' => $contactos,
            'registrosSalud' => $registrosSalud,
            'recetas' => $recetas,
            'visitas' => $visitas,
            'nivelAcceso' => $acceso['nivel_acceso'] ?? 'lectura',
            'emisor' => $_SESSION['user_nombre'] ?? 'Cuidador / Familiar Autorizado'
        ]);
    }

    /**
     * Muestra el formulario para registrar un nuevo adulto mayor administrado
     */
    public function crear(): void {
        AuthMiddleware::handle();
        $userRole = $_SESSION['user_role'] ?? '';

        if (in_array($userRole, ['paciente', 'adulto_mayor'], true)) {
            $this->setFlash('warning', 'Tu cuenta de paciente no permite registrar perfiles adicionales.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $this->render('pacientes/crear', [], 'main');
    }

    /**
     * Procesa el guardado de un nuevo adulto mayor administrado por el cuidador
     */
    public function guardar(): void {
        AuthMiddleware::handle();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF de seguridad inválido o expirado.');
            header('Location: ' . APP_URL . '/pacientes/crear');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
        $genero = trim($_POST['genero'] ?? 'No especificado');
        $tipoSangre = trim($_POST['tipo_sangre'] ?? 'N/D');
        $alergias = trim($_POST['alergias'] ?? 'Ninguna');
        $enfermedades = trim($_POST['enfermedades_cronicas'] ?? 'Ninguna');
        $notas = trim($_POST['notas_cognitivas'] ?? '');
        $parentesco = trim($_POST['parentesco'] ?? 'Familiar / Cuidador Principal');

        if (empty($nombre)) {
            $this->setFlash('warning', 'El nombre del adulto mayor es obligatorio.');
            header('Location: ' . APP_URL . '/pacientes/crear');
            exit;
        }

        $pacienteModel = new Paciente();

        try {
            $nuevoId = $pacienteModel->crearPerfilAdministrado($userId, [
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'fecha_nacimiento' => $fechaNacimiento,
                'genero' => $genero,
                'tipo_sangre' => $tipoSangre,
                'alergias' => $alergias,
                'enfermedades_cronicas' => $enfermedades,
                'notas_cognitivas' => $notas,
                'parentesco' => $parentesco
            ]);

            // Sincronizar en la sesión activa como el paciente seleccionado
            $_SESSION['paciente_activo_id'] = $nuevoId;
            $_SESSION['paciente_activo_nombre'] = trim("{$nombre} {$apellidos}");

            AuthMiddleware::logAudit(
                $nuevoId,
                'PERFIL_ADULTO_MAYOR_CREADO',
                "Nuevo perfil administrado creado para {$nombre} {$apellidos} por usuario ID {$userId}."
            );

            $this->setFlash('success', "¡Perfil de {$nombre} creado y activado correctamente!");
            header('Location: ' . APP_URL . '/dashboard');
            exit;

        } catch (\Throwable $e) {
            error_log("[PacienteController::guardar Error] " . $e->getMessage());
            $this->setFlash('danger', 'Error al guardar el nuevo perfil: ' . $e->getMessage());
            header('Location: ' . APP_URL . '/pacientes/crear');
            exit;
        }
    }

    /**
     * Vincula a un cuidador con un adulto mayor existente utilizando su código/NIP familiar
     */
    public function unirsePorNip(): void {
        AuthMiddleware::handle();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o expirado.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $nip = strtoupper(trim($_POST['codigo_vinculacion'] ?? ($_POST['nip'] ?? '')));
        $parentesco = trim($_POST['parentesco'] ?? 'Familiar / Cuidador');

        if (empty($nip)) {
            $this->setFlash('warning', 'Debes ingresar el NIP o código de vinculación familiar.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $pacienteModel = new Paciente();

        try {
            $paciente = $pacienteModel->vincularPorNip($userId, $nip, $parentesco);
            $pacienteId = (int)$paciente['id'];

            $_SESSION['paciente_activo_id'] = $pacienteId;
            $_SESSION['paciente_activo_nombre'] = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? ''));

            AuthMiddleware::logAudit(
                $pacienteId,
                'VINCULACION_POR_NIP_EXITOSA',
                "Usuario ID {$userId} vinculado al paciente '{$paciente['nombre']}' mediante NIP {$nip}."
            );

            $this->setFlash('success', "¡Vinculado exitosamente con {$paciente['nombre']}!");
            header('Location: ' . APP_URL . '/dashboard');
            exit;

        } catch (\Throwable $e) {
            error_log("[PacienteController::unirsePorNip Error] " . $e->getMessage());
            $this->setFlash('danger', $e->getMessage());
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }

    /**
     * Cambia fluidamente el paciente activo en sesión
     */
    public function seleccionar(): void {
        AuthMiddleware::handle();
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pacienteId = (int)($_POST['paciente_id'] ?? ($_GET['paciente_id'] ?? 0));
        $returnUrl = $_POST['return_url'] ?? ($_GET['return_url'] ?? APP_URL . '/dashboard');

        if ($pacienteId > 0 && AuthMiddleware::autorizarAccesoPaciente($pacienteId, $userId)) {
            $_SESSION['paciente_activo_id'] = $pacienteId;
            
            $pacienteModel = new Paciente();
            $p = $pacienteModel->find($pacienteId);
            if ($p) {
                $_SESSION['paciente_activo_nombre'] = trim(($p['nombre'] ?? '') . ' ' . ($p['apellidos'] ?? ''));
                $_SESSION['paciente_qr_token'] = $p['qr_token'] ?? '';
            }
            $this->setFlash('info', 'Has cambiado al perfil de ' . ($p['nombre'] ?? 'Adulto Mayor'));
        } else {
            $this->setFlash('danger', 'No tienes autorización para acceder al paciente seleccionado.');
        }

        // Sanitizar URL de retorno para evitar Open Redirects
        if (!str_starts_with($returnUrl, APP_URL) && !str_starts_with($returnUrl, '/')) {
            $returnUrl = APP_URL . '/dashboard';
        }

        header('Location: ' . $returnUrl);
        exit;
    }

    /**
     * Regenera el código / NIP de vinculación del adulto mayor
     */
    public function regenerarNip(): void {
        AuthMiddleware::handle();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o expirado.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);

        if (!$acceso['puede_editar']) {
            $this->setFlash('danger', 'No tienes permisos para regenerar el NIP de este expediente.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $pacienteModel = new Paciente();
        try {
            $nuevoNip = $pacienteModel->regenerarCodigoNip($pacienteId);
            AuthMiddleware::logAudit(
                $pacienteId,
                'NIP_REGENERADO',
                "Nuevo NIP '{$nuevoNip}' generado para el paciente ID {$pacienteId}."
            );
            $this->setFlash('success', "¡Nuevo código de vinculación generado: {$nuevoNip}!");
        } catch (\Throwable $e) {
            $this->setFlash('danger', 'Error al regenerar código: ' . $e->getMessage());
        }

        header('Location: ' . ($_POST['return_url'] ?? APP_URL . '/dashboard'));
        exit;
    }

    /**
     * Desvincula a un familiar o cuidador secundario
     */
    public function desvincularCuidador(): void {
        AuthMiddleware::handle();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token CSRF inválido o expirado.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $pacienteId = (int)($_POST['paciente_id'] ?? ($_SESSION['paciente_activo_id'] ?? 0));
        $cuidadorId = (int)($_POST['cuidador_id'] ?? 0);

        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);
        if (!$acceso['puede_editar']) {
            $this->setFlash('danger', 'No tienes permisos para desvincular cuidadores de este expediente.');
            header('Location: ' . APP_URL . '/expediente#tab-contactos');
            exit;
        }

        if ($cuidadorId > 0 && $pacienteId > 0) {
            $pacienteModel = new Paciente();
            $pacienteModel->desvincularFamiliar($pacienteId, $cuidadorId);
            AuthMiddleware::logAudit(
                $pacienteId,
                'FAMILIAR_DESVINCULADO',
                "Usuario cuidador ID {$cuidadorId} desvinculado del paciente ID {$pacienteId}."
            );
            $this->setFlash('success', 'Familiar desvinculado correctamente.');
        }

        header('Location: ' . APP_URL . '/expediente#tab-contactos');
        exit;
    }



    /**
     * Descarga de una receta médica individual en PDF
     */
    public function descargarRecetaPdf(): void {
        AuthMiddleware::handle();
        $recetaId = (int)($_GET['id'] ?? ($_GET['receta_id'] ?? 0));

        if ($recetaId === 0) {
            http_response_code(404);
            die('Error 404: Receta no especificada.');
        }

        $doctorModel = new Doctor();
        $receta = $doctorModel->obtenerRecetaPorId($recetaId);

        if (!$receta) {
            http_response_code(404);
            die('Error 404: Receta no encontrada.');
        }

        $pacienteId = (int)$receta['paciente_id'];
        $acceso = AuthMiddleware::requirePatientAccess($pacienteId);

        $db = \Config\Database::getInstance()->getConnection();
        $stmtMed = $db->prepare("SELECT * FROM medicamentos WHERE receta_id = :rid ORDER BY id ASC");
        $stmtMed->execute(['rid' => $recetaId]);
        $medicamentosRecetados = $stmtMed->fetchAll(\PDO::FETCH_ASSOC);

        $pdfService = new PdfReportService();
        $pdfService->generarRecetaPdf([
            'receta' => $receta,
            'medicamentosRecetados' => $medicamentosRecetados
        ]);
    }

    /**
     * Alias compatible con rutas anteriores
     */
    public function descargarExpediente(): void {
        $this->descargarReporte();
    }
}

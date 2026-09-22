<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Cuidador;
use App\Models\Usuario;

class VinculacionController extends Controller {
    private Paciente $pacienteModel;
    private Cuidador $cuidadorModel;
    private Usuario $usuarioModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
        $this->cuidadorModel = new Cuidador();
        $this->usuarioModel = new Usuario();
    }

    public function showForm(): void {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['user_role'] ?? '';

        if ($role !== 'cuidador_principal' && $role !== 'cuidador_secundario') {
            $this->setFlash('danger', 'Solo los cuidadores/familiares pueden vincular pacientes.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $this->render('pacientes/vincular');
    }

    public function processVinculacion(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['user_role'] ?? '';

        if ($role !== 'cuidador_principal' && $role !== 'cuidador_secundario') {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'codigo') {
            $codigo = trim($_POST['codigo_vinculacion'] ?? '');
            $parentesco = trim($_POST['parentesco_codigo'] ?? 'Familiar');
            
            if (empty($codigo)) {
                $this->setFlash('warning', 'Debes ingresar un código de vinculación válido.');
                header('Location: ' . APP_URL . '/vincular');
                exit;
            }

            $paciente = $this->pacienteModel->findByCodigoVinculacion($codigo);
            if (!$paciente) {
                $this->setFlash('danger', 'Código inválido o paciente no encontrado.');
                header('Location: ' . APP_URL . '/vincular');
                exit;
            }

            // Evitar duplicados
            $pacientesActuales = $this->cuidadorModel->obtenerPacientesPorCuidador($userId);
            foreach ($pacientesActuales as $p) {
                if ($p['id'] == $paciente['id']) {
                    $this->setFlash('info', 'Este paciente ya está vinculado a tu cuenta.');
                    header('Location: ' . APP_URL . '/dashboard');
                    exit;
                }
            }

            $this->cuidadorModel->agregarCuidador((int)$paciente['id'], $userId, $parentesco);
            $this->setFlash('success', '¡Paciente vinculado con éxito!');
            header('Location: ' . APP_URL . '/dashboard');
            exit;

        } elseif ($action === 'nuevo') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? 'Por completar');
            $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? date('Y-m-d'));
            $parentesco = trim($_POST['parentesco_nuevo'] ?? 'Familiar');

            if (empty($nombre)) {
                $this->setFlash('warning', 'El nombre del paciente es obligatorio.');
                header('Location: ' . APP_URL . '/vincular');
                exit;
            }

            // Para perfiles administrados, no creamos usuario real, usamos el usuario del cuidador para el campo usuario_id en pacientes, o un usuario dummy
            // Por el modelo actual, cada paciente tiene usuario_id
            $pacienteId = $this->pacienteModel->createInitialProfile($userId, $nombre);
            
            // Actualizar apellidos y fecha de nacimiento manualmente
            $stmt = $this->pacienteModel->getConnection()->prepare("UPDATE pacientes SET apellidos = :apellidos, fecha_nacimiento = :fecha_nacimiento WHERE id = :id");
            $stmt->execute(['apellidos' => $apellidos, 'fecha_nacimiento' => $fecha_nacimiento, 'id' => $pacienteId]);

            $this->cuidadorModel->agregarCuidador($pacienteId, $userId, $parentesco);
            
            $this->setFlash('success', 'Perfil administrado creado y vinculado con éxito.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        header('Location: ' . APP_URL . '/vincular');
        exit;
    }

    /**
     * POST /vincular/promover
     * Promueve un perfil administrado a cuenta independiente:
     * Otorga credenciales propias al Adulto Mayor y MANTIENE automáticamente al familiar
     * tutor vinculado con permisos de lectura/edición.
     */
    public function promoverAIndependiente(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $familiarId = $_SESSION['user_id'] ?? null;
        if (!$familiarId) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $pacienteId = trim($_POST['paciente_id'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($pacienteId) || empty($telefono) || empty($password)) {
            $this->setFlash('danger', 'Teléfono, contraseña e identificador de paciente son obligatorios.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        try {
            $db = \Config\Database::getInstance()->getConnection();
            $db->beginTransaction();

            // 1. Validar que el familiar actual tenga tutela sobre este paciente
            $stmtCheck = $db->prepare(
                "SELECT v.id FROM vinculaciones v WHERE v.familiar_id = :fid AND v.paciente_id = :pid"
            );
            $stmtCheck->execute([':fid' => $familiarId, ':pid' => $pacienteId]);
            if (!$stmtCheck->fetch()) {
                $db->rollBack();
                $this->setFlash('danger', 'No cuentas con autorización sobre este perfil de paciente.');
                header('Location: ' . APP_URL . '/dashboard');
                exit;
            }

            // 2. Obtener datos del paciente
            $stmtPac = $db->prepare("SELECT nombre_completo FROM pacientes WHERE id = :pid");
            $stmtPac->execute([':pid' => $pacienteId]);
            $paciente = $stmtPac->fetch(\PDO::FETCH_ASSOC);

            // 3. Crear el nuevo usuario para el Adulto Mayor
            $passHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmtUser = $db->prepare(
                "INSERT INTO usuarios (nombre_completo, email, telefono, password_hash, rol, activo, acepta_whatsapp, whatsapp_verificado) 
                 VALUES (:nombre, :email, :tel, :pass, 'adulto_mayor', TRUE, TRUE, FALSE) 
                 RETURNING id"
            );
            $stmtUser->execute([
                ':nombre' => $paciente['nombre_completo'] ?? 'Adulto Mayor',
                ':email' => !empty($email) ? $email : null,
                ':tel' => $telefono,
                ':pass' => $passHash
            ]);
            $nuevoUsuarioAdultoId = $stmtUser->fetchColumn();

            // 4. Asignar el nuevo usuario al paciente
            $stmtUpdatePac = $db->prepare("UPDATE pacientes SET usuario_id = :uid WHERE id = :pid");
            $stmtUpdatePac->execute([':uid' => $nuevoUsuarioAdultoId, ':pid' => $pacienteId]);

            // 5. REGLA CLÍNICA OBLIGATORIA: Mantener al familiar que creó la cuenta vinculado con permisos de lectura/edición
            $stmtUpdateVinc = $db->prepare(
                "UPDATE vinculaciones 
                 SET tipo_gestion = 'independiente_vinculado', 
                     permiso_edicion = TRUE, 
                     es_tutor_principal = TRUE 
                 WHERE familiar_id = :fid AND paciente_id = :pid"
            );
            $stmtUpdateVinc->execute([':fid' => $familiarId, ':pid' => $pacienteId]);

            // 6. Auditoría
            $stmtAudit = $db->prepare(
                "INSERT INTO logs_auditoria (usuario_id, paciente_id, evento, descripcion, ip_origen) 
                 VALUES (:uid, :pid, 'PERFIL_PROMOVIDO_A_INDEPENDIENTE', 'Perfil administrado promovido a cuenta independiente. El familiar tutor se mantiene vinculado con permisos.', :ip)"
            );
            $stmtAudit->execute([
                ':uid' => $familiarId,
                ':pid' => $pacienteId,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $db->commit();
            $this->setFlash('success', '¡Perfil promovido con éxito! El adulto mayor ya tiene sus credenciales y tu cuenta se mantiene vinculada como tutor.');
        } catch (\Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log('[VinculacionController::promoverAIndependiente] ' . $e->getMessage());
            $this->setFlash('danger', 'Error al promover perfil a cuenta independiente. Verifica que el teléfono no esté ya en uso.');
        }

        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }
}

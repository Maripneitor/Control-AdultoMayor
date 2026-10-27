<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Paciente;
use App\Middlewares\AuthMiddleware;

class PerfilController extends Controller {

    public function index(): void {
        AuthMiddleware::handle();
        
        $userId = (int)$_SESSION['user_id'];
        $usuarioModel = new Usuario();
        
        // Asumiendo que find o findById existen, usamos fetch manual si no existe
        $db = \Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, nombre, email, rol, activo, created_at FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);


        if (!$usuario) {
            $this->setFlash('Usuario no encontrado.', 'danger');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        // Si es adulto mayor, mostrar opción de vinculación
        $paciente = null;
        if ($usuario['rol'] === 'adulto_mayor') {
            $pacienteModel = new Paciente();
            $paciente = $pacienteModel->findByUserId($userId);
        }

        $this->render('perfil/index', [
            'usuario' => $usuario,
            'paciente' => $paciente
        ], false);
    }

    public function actualizar(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $usuarioModel = new Usuario();

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $alertas = isset($_POST['alertas_whatsapp']) && $_POST['alertas_whatsapp'] == '1';

        if (!empty($nombre) && !empty($email)) {
            $usuarioModel->updateProfile($userId, $nombre, $email, $telefono, $alertas);
            
            // Actualizar nombre en sesión
            $_SESSION['user_name'] = $nombre;
            
            $this->setFlash('Perfil actualizado correctamente.', 'success');
        } else {
            $this->setFlash('El nombre y correo son obligatorios.', 'danger');
        }

        header("Location: " . APP_URL . "/perfil");
        exit;
    }

    public function actualizarPassword(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $usuarioModel = new Usuario();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $this->setFlash('Las contraseñas nuevas no coinciden.', 'danger');
            header("Location: " . APP_URL . "/perfil");
            exit;
        }

        if (strlen($newPassword) < 6) {
            $this->setFlash('La nueva contraseña debe tener al menos 6 caracteres.', 'danger');
            header("Location: " . APP_URL . "/perfil");
            exit;
        }

        $db = \Config\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT password_hash FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($currentPassword, $user['password_hash'])) {
            $usuarioModel->updatePassword($userId, $newPassword);
            $this->setFlash('Contraseña actualizada correctamente.', 'success');
        } else {
            $this->setFlash('La contraseña actual es incorrecta.', 'danger');
        }

        header("Location: " . APP_URL . "/perfil");
        exit;
    }

    /**
     * POST /perfil/eliminar
     * Garantiza un borrado lógico (soft delete) en PostgreSQL para preservar la integridad referencial y auditoría médica.
     */
    public function eliminarCuenta(): void {
        AuthMiddleware::handle();
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            header("Location: " . APP_URL . "/login");
            exit;
        }
        
        $db = \Config\Database::getInstance()->getConnection();

        // Validar si el usuario administra adultos mayores o es tutor principal
        try {
            $stmtTutor = $db->prepare("
                SELECT COUNT(*) FROM pacientes 
                WHERE (usuario_tutor_id = :uid OR (usuario_id = :uid2 AND usuario_tutor_id IS NOT NULL)) 
                  AND (activo = TRUE OR activo IS NULL)
            ");
            $stmtTutor->execute([':uid' => $userId, ':uid2' => $userId]);
            $countTutelasDirectas = (int)$stmtTutor->fetchColumn();

            $stmtCuidadorPrinc = $db->prepare("
                SELECT COUNT(*) FROM paciente_cuidador 
                WHERE usuario_cuidador_id = :uid AND nivel_acceso = 'principal'
            ");
            $stmtCuidadorPrinc->execute([':uid' => $userId]);
            $countCuidadorPrincipal = (int)$stmtCuidadorPrinc->fetchColumn();

            if ($countTutelasDirectas > 0 || $countCuidadorPrincipal > 0) {
                $this->setFlash('Debes transferir la tutela de tus adultos mayores administrados o desvincularlos antes de eliminar tu cuenta.', 'danger');
                header("Location: " . APP_URL . "/perfil");
                exit;
            }
        } catch (\Throwable $e) {
            error_log('[PerfilController::eliminarCuenta Validation Error] ' . $e->getMessage());
        }
        
        try {
            $db->beginTransaction();

            // 1. Aplicar borrado lógico (Soft Delete)
            $stmt = $db->prepare(
                "UPDATE usuarios 
                 SET activo = FALSE, 
                     eliminado_en = CURRENT_TIMESTAMP 
                 WHERE id = :id"
            );
            $stmt->execute([':id' => $userId]);

            // 2. Registrar en auditoría clínica
            $stmtAudit = $db->prepare(
                "INSERT INTO logs_auditoria (usuario_id, evento, descripcion, ip_origen) 
                 VALUES (:uid, 'USUARIO_SOFT_DELETE', 'El usuario solicitó la eliminación de su cuenta. Se aplicó baja lógica.', :ip)"
            );
            $stmtAudit->execute([
                ':uid' => $userId,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $db->commit();
            
            // 3. Destruir sesión segura y redirigir
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION = [];
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }
                session_destroy();
            }

            header("Location: " . APP_URL . "/?cuenta_eliminada=1");
            exit;
        } catch (\Throwable $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            error_log('[PerfilController::eliminarCuenta SoftDelete Error] ' . $e->getMessage());
            $this->setFlash('No se pudo procesar la baja de la cuenta. Contacta a soporte.', 'danger');
            header("Location: " . APP_URL . "/perfil");
            exit;
        }
    }
}

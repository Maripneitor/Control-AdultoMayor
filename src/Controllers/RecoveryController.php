<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\PasswordReset;
use App\Core\Controller;

class RecoveryController extends Controller {
    private Usuario $usuarioModel;
    private PasswordReset $resetModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->resetModel = new PasswordReset();
    }

    public function showRecoveryForm(): void {
        $this->render('auth/recovery', [], false);
    }

    public function sendToken(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        // Rate Limiting (Máx 3 intentos por sesión en ventanas de 15 min)
        if (!isset($_SESSION['recovery_attempts'])) $_SESSION['recovery_attempts'] = 0;
        if (!isset($_SESSION['recovery_last_attempt'])) $_SESSION['recovery_last_attempt'] = time();

        if (time() - $_SESSION['recovery_last_attempt'] > 900) {
            $_SESSION['recovery_attempts'] = 0; // Reset
        }

        if ($_SESSION['recovery_attempts'] >= 3) {
            $_SESSION['info'] = "Demasiados intentos. Por favor, espera 15 minutos.";
            header('Location: ' . APP_URL . '/recovery');
            exit;
        }
        
        $_SESSION['recovery_attempts']++;
        $_SESSION['recovery_last_attempt'] = time();

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $user = $email ? $this->usuarioModel->findByEmail($email) : null;

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $this->resetModel->createToken((int)$user['id'], $token, $expiresAt);

            // Simulación / Registro para testing local
            error_log("Enlace de recuperación: " . APP_URL . "/new-password?token=" . $token);
        }

        $_SESSION['info'] = "Si el correo existe en nuestra base de datos, se ha enviado un enlace de recuperación.";
        header('Location: ' . APP_URL . '/recovery');
        exit;
    }

    public function resetPassword(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $token = trim($_POST['token'] ?? '');
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($newPassword) < 8 || $newPassword !== $confirmPassword) {
            $_SESSION['error'] = "Las contraseñas no coinciden o tienen menos de 8 caracteres.";
            header("Location: " . APP_URL . "/new-password?token=" . urlencode($token));
            exit;
        }

        $reset = $this->resetModel->verifyToken($token);
        if (!$reset) {
            $_SESSION['error'] = "El token es inválido o ha expirado.";
            header('Location: ' . APP_URL . '/recovery');
            exit;
        }

        $this->usuarioModel->updatePassword((int)$reset['usuario_id'], $newPassword);
        $this->resetModel->markAsUsed((int)$reset['id']);

        // Invalidar sesiones activas tras cambio de password crítico
        session_regenerate_id(true);
        session_destroy();

        header('Location: ' . APP_URL . '/password-updated');
        exit;
    }
}

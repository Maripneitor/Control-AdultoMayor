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

        // Rate Limiting Recovery (DB-based)
        $ip = getRealIp();
        if (!\App\Core\RateLimiter::check($ip, 'recovery', 3, 60)) {
            $this->setFlash('warning', "Demasiados intentos. Por favor, espera una hora.");
            header('Location: ' . APP_URL . '/recovery');
            exit;
        }
        
        \App\Core\RateLimiter::hit($ip, 'recovery');

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $user = $email ? $this->usuarioModel->findByEmail($email) : null;

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $this->resetModel->crearToken((int)$user['id'], $token, $expiresAt);

            // Simulación / Registro para testing local
            error_log("Enlace de recuperación: " . APP_URL . "/new-password?token=" . $token);
            
            \App\Services\EmailNotificationService::enviarCorreoRecuperacion($email, $user['nombre'], $token);
        }

        $this->setFlash('info', "Si el correo existe en nuestra base de datos, se ha enviado un enlace de recuperación.");
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
            $this->setFlash('danger', "Las contraseñas no coinciden o tienen menos de 8 caracteres.");
            header("Location: " . APP_URL . "/new-password?token=" . urlencode($token));
            exit;
        }

        $reset = $this->resetModel->validarToken($token);
        if (!$reset) {
            $this->setFlash('danger', "El token es inválido o ha expirado.");
            header('Location: ' . APP_URL . '/recovery');
            exit;
        }

        $this->usuarioModel->updatePassword((int)$reset['usuario_id'], $newPassword);
        $this->resetModel->marcarComoUsado((int)$reset['id']);

        // Invalidar sesiones activas tras cambio de password crítico
        session_regenerate_id(true);
        session_destroy();

        header('Location: ' . APP_URL . '/password-updated');
        exit;
    }
    public function showNewPasswordForm(): void {
        $token = $_GET['token'] ?? '';
        $this->render('auth/new_password', ['token' => $token], false);
    }

    public function showPasswordUpdated(): void {
        $this->render('auth/password_updated', [], false);
    }
}

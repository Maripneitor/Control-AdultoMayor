<?php
declare(strict_types=1);

namespace App\Middlewares;

class AuthMiddleware {
    public static function handle(): void {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta sección.';
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '/dashboard';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function guestOnly(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }
    }
}

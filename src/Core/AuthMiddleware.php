<?php
namespace Core;

class AuthMiddleware {
    public static function handle() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . APP_URL . "/login");
            exit;
        }
    }
}

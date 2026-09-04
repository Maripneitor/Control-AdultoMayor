<?php
declare(strict_types=1);

namespace App\Middlewares;

use Config\Database;
use PDO;

class AuthMiddleware {
    public static function handle(): void {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['flash_notification'] = [
                'type' => 'danger',
                'message' => 'Debes iniciar sesión para acceder a esta sección.'
            ];
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

    public static function autorizarAccesoPaciente(int $pacienteId, int $usuarioId): bool {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT 1 FROM paciente_cuidador 
            WHERE paciente_id = :p_id AND usuario_cuidador_id = :u_id
            UNION
            SELECT 1 FROM pacientes 
            WHERE id = :p_id AND usuario_id = :u_id
        ");
        $stmt->execute([':p_id' => $pacienteId, ':u_id' => $usuarioId]);
        return (bool) $stmt->fetch();
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class PasswordReset extends Model {
    protected $table = 'tokens_recuperacion';

    public function crearToken(int $userId, string $token, string $expiresAt): bool {
        $this->db->prepare("UPDATE tokens_recuperacion SET usado = TRUE WHERE usuario_id = :uid")->execute(['uid' => $userId]);
        $stmt = $this->db->prepare("
            INSERT INTO tokens_recuperacion (usuario_id, token, expira_en) 
            VALUES (:usuario_id, :token, :expira_en)
        ");
        return $stmt->execute([
            'usuario_id' => $userId,
            'token' => $token,
            'expira_en' => $expiresAt
        ]);
    }

    public function validarToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, token, expira_en, usado, created_at 
            FROM tokens_recuperacion 
            WHERE token = :token AND usado = FALSE AND expira_en > NOW() 
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }


    public function marcarComoUsado(int $tokenId): bool {
        $stmt = $this->db->prepare("UPDATE tokens_recuperacion SET usado = TRUE WHERE id = :id");
        return $stmt->execute(['id' => $tokenId]);
    }
}

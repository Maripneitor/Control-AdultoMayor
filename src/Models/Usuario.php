<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Usuario extends Model {
    protected $table = 'usuarios';
    
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function register(string $nombre, string $email, string $telefono, string $password): int {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, email, password_hash, rol) 
            VALUES (:nombre, :email, :password_hash, 'administrador') RETURNING id
        ");
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'password_hash' => $hash
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare("UPDATE usuarios SET password_hash = :hash WHERE id = :id");
        return $stmt->execute(['hash' => $hash, 'id' => $userId]);
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Paciente extends Model {
    protected $table = 'pacientes';

    public function createInitialProfile(int $userId, string $nombrePaciente): int {
        $stmt = $this->db->prepare("
            INSERT INTO pacientes (usuario_id, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas) 
            VALUES (:usuario_id, :nombre, 'Por completar', CURRENT_DATE, 'N/D', 'Ninguna', 'Ninguna', '') RETURNING id
        ");
        $stmt->execute([
            'usuario_id' => $userId,
            'nombre' => $nombrePaciente
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pacientes WHERE usuario_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function findByQrToken(string $token): ?array {
        $stmt = $this->db->prepare("SELECT * FROM pacientes WHERE qr_token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }
}

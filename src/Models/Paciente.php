<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Paciente extends Model {
    protected $table = 'pacientes';

    public function createInitialProfile(int $userId, string $nombrePaciente): int {
        $tokenSos = bin2hex(random_bytes(16));
        $codigoVinculacion = strtoupper(substr(md5(uniqid((string)rand(), true)), 0, 8)); // Generate 8 char code
        $codigoVinculacion = substr($codigoVinculacion, 0, 4) . '-' . substr($codigoVinculacion, 4, 4);

        $stmt = $this->db->prepare("
            INSERT INTO pacientes (usuario_id, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, token_sos, codigo_vinculacion) 
            VALUES (:usuario_id, :nombre, 'Por completar', CURRENT_DATE, 'N/D', 'Ninguna', 'Ninguna', '', :token_sos, :codigo_vinculacion) RETURNING id
        ");
        $stmt->execute([
            'usuario_id' => $userId,
            'nombre' => $nombrePaciente,
            'token_sos' => $tokenSos,
            'codigo_vinculacion' => $codigoVinculacion
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function findByCodigoVinculacion(string $codigo): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, qr_token, qr_token AS uuid_publico, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, codigo_vinculacion, token_sos, created_at, updated_at 
            FROM pacientes 
            WHERE codigo_vinculacion = :codigo LIMIT 1
        ");
        $stmt->execute(['codigo' => $codigo]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, qr_token, qr_token AS uuid_publico, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, codigo_vinculacion, token_sos, created_at, updated_at 
            FROM pacientes 
            WHERE usuario_id = :user_id LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function findByQrToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, qr_token, qr_token AS uuid_publico, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, codigo_vinculacion, token_sos, created_at, updated_at 
            FROM pacientes 
            WHERE (qr_token::text = :token OR token_sos = :token2) LIMIT 1
        ");
        $stmt->execute(['token' => $token, 'token2' => $token]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function updateDatosBasicos(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE pacientes SET tipo_sangre = :tipo_sangre, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([
            'tipo_sangre' => $data['tipo_sangre'] ?? 'N/D',
            'id' => $id
        ]);
    }

    public function updatePadecimientos(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE pacientes SET alergias = :alergias, enfermedades_cronicas = :enfermedades_cronicas, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([
            'alergias' => $data['alergias'] ?? 'Ninguna',
            'enfermedades_cronicas' => $data['enfermedades_cronicas'] ?? 'Ninguna',
            'id' => $id
        ]);
    }
}


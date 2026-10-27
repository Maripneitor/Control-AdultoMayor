<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class ContactoEmergencia extends Model {
    protected $table = 'contactos_emergencia';

    public function getByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, paciente_id, nombre, parentesco, telefono, es_prioritario, es_prioritario AS es_principal 
            FROM contactos_emergencia 
            WHERE paciente_id = :pid 
            ORDER BY es_prioritario DESC, nombre ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO contactos_emergencia (paciente_id, nombre, parentesco, telefono, es_prioritario) 
            VALUES (:paciente_id, :nombre, :parentesco, :telefono, :es_prioritario)
        ");
        $stmt->bindValue(':paciente_id', (int)$data['paciente_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nombre', (string)$data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(':parentesco', (string)($data['parentesco'] ?? 'Familiar'), PDO::PARAM_STR);
        $stmt->bindValue(':telefono', (string)$data['telefono'], PDO::PARAM_STR);
        $stmt->bindValue(':es_prioritario', !empty($data['es_prioritario']), PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function eliminar(int $id, int $pacienteId): bool {
        $stmt = $this->db->prepare("DELETE FROM contactos_emergencia WHERE id = :id AND paciente_id = :paciente_id");
        return $stmt->execute([
            'id' => $id,
            'paciente_id' => $pacienteId
        ]);
    }
}

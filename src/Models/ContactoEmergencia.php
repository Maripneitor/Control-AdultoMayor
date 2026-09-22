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
}

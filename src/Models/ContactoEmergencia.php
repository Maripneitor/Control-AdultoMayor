<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class ContactoEmergencia extends Model {
    protected $table = 'contactos_emergencia';

    public function getByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("SELECT * FROM contactos_emergencia WHERE paciente_id = :pid");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Doctor extends Model {
    protected $table = 'doctores';

    public function getByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("SELECT * FROM doctores WHERE paciente_id = :pid");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class RegistroSaludDiario extends Model {
    protected $table = 'registro_salud_diario';

    public function agregar(array $data): int {
        return $this->create($data);
    }

    public function getTodayByPacienteId(int $pacienteId): ?array {
        $stmt = $this->db->prepare("
            SELECT id, paciente_id, presion_sistolica, presion_diastolica, glucosa, momento_glucosa, ritmo_cardiaco, horas_sueno, nivel_dolor, alerta_emitida, confirmado_por_usuario, created_at 
            FROM registro_salud_diario 
            WHERE paciente_id = :pid 
            AND DATE(created_at) = CURRENT_DATE 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute(['pid' => $pacienteId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function getAllByPacienteId(int $pacienteId, int $limit = 30): array {
        $stmt = $this->db->prepare("
            SELECT id, paciente_id, presion_sistolica, presion_diastolica, glucosa, momento_glucosa, ritmo_cardiaco, horas_sueno, nivel_dolor, alerta_emitida, confirmado_por_usuario, created_at 
            FROM registro_salud_diario 
            WHERE paciente_id = :pid 
            ORDER BY created_at DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class CitaLaboratorio extends Model {
    protected $table = 'citas_laboratorios';

    public function getUpcomingByPacienteId(int $pacienteId, int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT cl.*, d.nombre AS doctor_nombre 
            FROM citas_laboratorios cl
            LEFT JOIN doctores d ON cl.doctor_id = d.id
            WHERE cl.paciente_id = :pid AND cl.fecha_hora >= NOW() AND cl.realizado = FALSE
            ORDER BY cl.fecha_hora ASC
            LIMIT :lim
        ");
        $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT cl.*, d.nombre AS doctor_nombre 
            FROM citas_laboratorios cl
            LEFT JOIN doctores d ON cl.doctor_id = d.id
            WHERE cl.paciente_id = :pid
            ORDER BY cl.fecha_hora DESC
        ");
        $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO citas_laboratorios (paciente_id, tipo_evento, titulo, lugar, fecha_hora, doctor_id, notas)
            VALUES (:paciente_id, :tipo_evento, :titulo, :lugar, :fecha_hora, :doctor_id, :notas)
            RETURNING id
        ");
        $stmt->execute($data);
        return (int)$stmt->fetchColumn();
    }

    public function checkOverlap(int $pacienteId, string $fechaHora, int $intervalMinutes = 60): bool {
        $timestamp = strtotime($fechaHora);
        $inicio = date('Y-m-d H:i:s', $timestamp - ($intervalMinutes * 60));
        $fin = date('Y-m-d H:i:s', $timestamp + ($intervalMinutes * 60));

        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM citas_laboratorios 
            WHERE paciente_id = :pid 
            AND fecha_hora BETWEEN :inicio AND :fin
        ");
        $stmt->execute([
            'pid' => $pacienteId,
            'inicio' => $inicio,
            'fin' => $fin
        ]);
        
        return ((int)$stmt->fetchColumn()) > 0;
    }
}

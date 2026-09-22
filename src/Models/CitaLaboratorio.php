<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class CitaLaboratorio extends Model {
    protected $table = 'citas_laboratorios';

    /**
     * Obtiene las próximas citas programadas no vencidas
     */
    public function getUpcomingByPacienteId(int $pacienteId, int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT cl.id, cl.paciente_id, cl.tipo_evento, cl.titulo, cl.lugar, 
                   cl.fecha_hora, cl.doctor_id, cl.notas, cl.realizado, 
                   cl.preparacion_previa, cl.familiar_responsable_id, cl.ubicacion_mapa, 
                   cl.estado, d.nombre AS doctor_nombre, u.nombre AS familiar_nombre
            FROM citas_laboratorios cl
            LEFT JOIN doctores d ON cl.doctor_id = d.id
            LEFT JOIN usuarios u ON cl.familiar_responsable_id = u.id
            WHERE cl.paciente_id = :pid 
              AND cl.estado = 'programada' 
              AND cl.fecha_hora >= NOW()
            ORDER BY cl.fecha_hora ASC
            LIMIT :lim
        ");
        $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene citas con filtros flexibles ('proximas', 'pasadas', 'estudios', 'todos')
     */
    public function getAllByPacienteId(int $pacienteId, ?string $filtro = null): array {
        $sql = "
            SELECT cl.id, cl.paciente_id, cl.tipo_evento, cl.titulo, cl.lugar, 
                   cl.fecha_hora, cl.doctor_id, cl.notas, cl.realizado, 
                   cl.preparacion_previa, cl.familiar_responsable_id, cl.ubicacion_mapa, 
                   cl.estado, d.nombre AS doctor_nombre, u.nombre AS familiar_nombre
            FROM citas_laboratorios cl
            LEFT JOIN doctores d ON cl.doctor_id = d.id
            LEFT JOIN usuarios u ON cl.familiar_responsable_id = u.id
            WHERE cl.paciente_id = :pid
        ";

        if ($filtro === 'proximas') {
            $sql .= " AND cl.estado = 'programada' AND cl.fecha_hora >= (NOW() - interval '2 hours') ORDER BY cl.fecha_hora ASC";
        } elseif ($filtro === 'pasadas') {
            $sql .= " AND (cl.estado = 'completada' OR cl.fecha_hora < (NOW() - interval '2 hours') OR cl.estado = 'cancelada') ORDER BY cl.fecha_hora DESC";
        } elseif ($filtro === 'estudios') {
            $sql .= " AND cl.tipo_evento IN ('laboratorio_estudio', 'laboratorio') ORDER BY cl.fecha_hora DESC";
        } else {
            $sql .= " ORDER BY (CASE WHEN cl.estado = 'programada' AND cl.fecha_hora >= NOW() THEN 0 ELSE 1 END), cl.fecha_hora ASC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT cl.id, cl.paciente_id, cl.tipo_evento, cl.titulo, cl.lugar, 
                   cl.fecha_hora, cl.doctor_id, cl.notas, cl.realizado, 
                   cl.preparacion_previa, cl.familiar_responsable_id, cl.ubicacion_mapa, 
                   cl.estado, d.nombre AS doctor_nombre, d.telefono_contacto AS doctor_telefono,
                   u.nombre AS familiar_nombre
            FROM citas_laboratorios cl
            LEFT JOIN doctores d ON cl.doctor_id = d.id
            LEFT JOIN usuarios u ON cl.familiar_responsable_id = u.id
            WHERE cl.id = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function agregar(array $data): int {
        return $this->create([
            'paciente_id' => (int)$data['paciente_id'],
            'tipo_evento' => (string)($data['tipo_evento'] ?? 'consulta'),
            'titulo' => (string)$data['titulo'],
            'lugar' => (string)$data['lugar'],
            'fecha_hora' => (string)$data['fecha_hora'],
            'doctor_id' => !empty($data['doctor_id']) ? (int)$data['doctor_id'] : null,
            'familiar_responsable_id' => !empty($data['familiar_responsable_id']) ? (int)$data['familiar_responsable_id'] : null,
            'preparacion_previa' => !empty($data['preparacion_previa']) ? (string)$data['preparacion_previa'] : null,
            'ubicacion_mapa' => !empty($data['ubicacion_mapa']) ? (string)$data['ubicacion_mapa'] : null,
            'estado' => (string)($data['estado'] ?? 'programada'),
            'notas' => !empty($data['notas']) ? (string)$data['notas'] : null
        ]);
    }

    public function cambiarEstado(int $id, string $nuevoEstado): bool {
        $realizadoClause = ($nuevoEstado === 'completada') ? ", realizado = TRUE" : "";
        $stmt = $this->db->prepare("
            UPDATE citas_laboratorios 
            SET estado = :estado {$realizadoClause}
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':estado' => $nuevoEstado
        ]);
    }

    public function checkOverlap(int $pacienteId, string $fechaHora, int $intervalMinutes = 60, ?int $excludeId = null): bool {
        $timestamp = strtotime($fechaHora);
        $inicio = date('Y-m-d H:i:s', $timestamp - ($intervalMinutes * 60));
        $fin = date('Y-m-d H:i:s', $timestamp + ($intervalMinutes * 60));

        $sql = "
            SELECT COUNT(*) 
            FROM citas_laboratorios 
            WHERE paciente_id = :pid 
              AND estado = 'programada'
              AND fecha_hora BETWEEN :inicio AND :fin
        ";
        if ($excludeId !== null) {
            $sql .= " AND id != :exId";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
        $stmt->bindValue(':inicio', $inicio);
        $stmt->bindValue(':fin', $fin);
        if ($excludeId !== null) {
            $stmt->bindValue(':exId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return ((int)$stmt->fetchColumn()) > 0;
    }
}

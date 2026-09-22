<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Medicamento extends Model {
    protected $table = 'medicamentos';

    public function getActiveByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, paciente_id, nombre_comercial, dosis, dosis_por_toma, forma, color_distintivo, frecuencia_horas, instrucciones, activo 
            FROM medicamentos 
            WHERE paciente_id = :pid AND activo = TRUE 
            ORDER BY nombre_comercial ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodayDoses(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT rt.id AS registro_id, m.nombre_comercial, m.nombre_comercial AS medicamento_nombre, m.dosis, m.dosis_por_toma, m.color_distintivo, m.forma, rt.momento_dia, rt.estado, rt.fecha_programada, rt.hora_programada
            FROM tomas_historial rt
            JOIN medicamentos m ON rt.medicamento_id = m.id
            WHERE m.paciente_id = :pid 
              AND rt.fecha_programada = CURRENT_DATE
            ORDER BY rt.hora_programada ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, paciente_id, nombre_comercial, dosis, dosis_por_toma, forma, color_distintivo, frecuencia_horas, instrucciones, activo 
            FROM medicamentos 
            WHERE paciente_id = :pid 
            ORDER BY nombre_comercial ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(array $data): int {
        // La validación de permisos ($usuarioId y roles) ya se realizó en el Middleware/Controller

        $stmt = $this->db->prepare("
            INSERT INTO medicamentos (paciente_id, nombre_comercial, dosis, frecuencia_horas, instrucciones)
            VALUES (:paciente_id, :nombre_comercial, :dosis, :frecuencia_horas, :instrucciones)
            RETURNING id
        ");
        $stmt->execute($data);
        return (int)$stmt->fetchColumn();
    }

    public function actualizarEstadoToma(int $registroId, int $pacienteId, string $estado, int $usuarioId, ?string $nota = null): bool {
        $stmt = $this->db->prepare("
            UPDATE tomas_historial 
            SET estado = :estado, 
                tomado_at = CASE WHEN :estado IN ('tomada', 'pospuesto') THEN NOW() ELSE tomado_at END,
                registrado_por_usuario_id = :usuario_id,
                nota_incidencia = :nota
            WHERE id = :id AND medicamento_id IN (SELECT id FROM medicamentos WHERE paciente_id = :paciente_id)
        ");
        return $stmt->execute([
            'estado' => $estado,
            'usuario_id' => $usuarioId,
            'nota' => $nota,
            'id' => $registroId,
            'paciente_id' => $pacienteId
        ]);
    }

    public function generarTomasDiarias(int $pacienteId): void {
        $medicamentos = $this->getActiveByPacienteId($pacienteId);
        if (empty($medicamentos)) {
            return;
        }

        $hoy = date('Y-m-d');
        
        $horasMomento = [
            'manana' => '08:00:00',
            'tarde'  => '14:00:00',
            'noche'  => '20:00:00'
        ];

        $valores = [];
        $parametros = [];
        $contador = 0;

        foreach ($medicamentos as $med) {
            $mid = (int)$med['id'];
            $frecuencia = (int)$med['frecuencia_horas'];
            
            $momentos = [];
            if ($frecuencia <= 8) {
                $momentos = ['manana', 'tarde', 'noche'];
            } elseif ($frecuencia <= 12) {
                $momentos = ['manana', 'noche'];
            } else {
                $momentos = ['manana']; 
            }

            foreach ($momentos as $momento) {
                $horaProg = $horasMomento[$momento] ?? '08:00:00';
                
                $c = $contador * 5;
                $valores[] = "(:p{$c}, :p" . ($c+1) . ", :p" . ($c+2) . ", :p" . ($c+3) . ", 'pendiente', :p" . ($c+4) . ")";
                
                $parametros["p{$c}"] = $mid;
                $parametros["p" . ($c+1)] = $pacienteId;
                $parametros["p" . ($c+2)] = $hoy;
                $parametros["p" . ($c+3)] = $momento;
                $parametros["p" . ($c+4)] = $horaProg;
                
                $contador++;
            }
        }

        if (count($valores) > 0) {
            $sql = "
                INSERT INTO tomas_historial (medicamento_id, paciente_id, fecha_programada, momento_dia, estado, hora_programada)
                VALUES " . implode(', ', $valores) . "
                ON CONFLICT (medicamento_id, fecha_programada, momento_dia) DO NOTHING
            ";
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($parametros);
            } catch (\PDOException $e) {
                error_log("Error bulk insert tomas: " . $e->getMessage());
            }
        }
    }
}

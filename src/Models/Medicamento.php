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

    public function getOmitidasHoy(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT th.id, m.nombre_comercial, th.hora_programada, th.fecha_programada, th.momento_dia, th.nota_incidencia
            FROM tomas_historial th
            JOIN medicamentos m ON th.medicamento_id = m.id
            WHERE th.paciente_id = :pid 
              AND th.fecha_programada = CURRENT_DATE 
              AND th.estado = 'omitido'
            ORDER BY th.hora_programada ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBajoInventario(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, nombre_comercial, stock_actual, stock_minimo_alerta, dosis_por_toma, frecuencia_horas,
                   FLOOR(stock_actual / NULLIF(FLOOR(24 / GREATEST(frecuencia_horas, 1)) * GREATEST(dosis_por_toma, 1), 0)) AS dias_restantes
            FROM medicamentos
            WHERE paciente_id = :pid AND activo = TRUE 
              AND (
                  stock_actual <= stock_minimo_alerta 
                  OR stock_actual / NULLIF(FLOOR(24 / GREATEST(frecuencia_horas, 1)) * GREATEST(dosis_por_toma, 1), 0) < 4
              )
            ORDER BY dias_restantes ASC, stock_actual ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO medicamentos (
                paciente_id, nombre_comercial, dosis, frecuencia_horas, 
                stock_actual, dosis_por_toma, stock_minimo_alerta, instrucciones, activo
            )
            VALUES (
                :paciente_id, :nombre_comercial, :dosis, :frecuencia_horas, 
                :stock_actual, :dosis_por_toma, :stock_minimo_alerta, :instrucciones, TRUE
            )
            RETURNING id
        ");
        $stmt->execute([
            'paciente_id' => $data['paciente_id'],
            'nombre_comercial' => $data['nombre_comercial'],
            'dosis' => $data['dosis'],
            'frecuencia_horas' => $data['frecuencia_horas'],
            'stock_actual' => $data['stock_actual'] ?? 30,
            'dosis_por_toma' => $data['dosis_por_toma'] ?? 1,
            'stock_minimo_alerta' => $data['stock_minimo_alerta'] ?? 5,
            'instrucciones' => $data['instrucciones'] ?? ''
        ]);
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

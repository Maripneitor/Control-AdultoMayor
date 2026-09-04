<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Medicamento extends Model {
    protected $table = 'medicamentos';

    public function getActiveByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("SELECT * FROM medicamentos WHERE paciente_id = :pid AND activo = TRUE ORDER BY nombre_comercial ASC");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodayDoses(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT rt.id AS registro_id, m.nombre_comercial, m.dosis, m.dosis_por_toma, m.color_distintivo, m.forma, rt.momento_dia, rt.estado, rt.fecha_programada
            FROM tomas_historial rt
            JOIN medicamentos m ON rt.medicamento_id = m.id
            WHERE m.paciente_id = :pid 
              AND rt.fecha_programada = CURRENT_DATE
            ORDER BY 
              CASE rt.momento_dia 
                WHEN 'manana' THEN 1 
                WHEN 'tarde' THEN 2 
                WHEN 'noche' THEN 3 
                ELSE 4 
              END ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("SELECT * FROM medicamentos WHERE paciente_id = :pid ORDER BY nombre_comercial ASC");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO medicamentos (paciente_id, nombre_comercial, dosis, frecuencia_horas, instrucciones)
            VALUES (:paciente_id, :nombre_comercial, :dosis, :frecuencia_horas, :instrucciones)
            RETURNING id
        ");
        $stmt->execute($data);
        return (int)$stmt->fetchColumn();
    }

    public function marcarTomaComoRealizada(int $registroId, int $pacienteId): bool {
        $stmt = $this->db->prepare("
            UPDATE registro_tomas 
            SET estado = 'tomada', fecha_hora_tomada = NOW() 
            WHERE id = :id AND medicamento_id IN (SELECT id FROM medicamentos WHERE paciente_id = :paciente_id)
        ");
        return $stmt->execute(['id' => $registroId, 'paciente_id' => $pacienteId]);
    }

    public function generarTomasDiarias(int $pacienteId): void {
        $medicamentos = $this->getActiveByPacienteId($pacienteId);
        if (empty($medicamentos)) {
            return;
        }

        $hoy = date('Y-m-d');
        
        foreach ($medicamentos as $med) {
            $mid = (int)$med['id'];
            $frecuencia = (int)$med['frecuencia_horas'];
            
            // Determinar momentos basados en frecuencia
            $momentos = [];
            if ($frecuencia <= 8) {
                $momentos = ['manana', 'tarde', 'noche'];
            } elseif ($frecuencia <= 12) {
                $momentos = ['manana', 'noche'];
            } else {
                $momentos = ['manana']; // Por defecto 1 al día
            }

            foreach ($momentos as $momento) {
                // Upsert para ignorar duplicados gracias al UNIQUE constraint
                $stmt = $this->db->prepare("
                    INSERT INTO tomas_historial (medicamento_id, paciente_id, fecha_programada, momento_dia, estado)
                    VALUES (:mid, :pid, :fecha, :momento, 'pendiente')
                    ON CONFLICT (medicamento_id, fecha_programada, momento_dia) DO NOTHING
                ");
                try {
                    $stmt->execute([
                        'mid' => $mid,
                        'pid' => $pacienteId,
                        'fecha' => $hoy,
                        'momento' => $momento
                    ]);
                } catch (\PDOException $e) {
                    // Ignorar si el driver no soporta ON CONFLICT (MySQL -> INSERT IGNORE, pero Postgres usa ON CONFLICT)
                }
            }
        }
    }
}

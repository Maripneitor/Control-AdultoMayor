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
            SELECT rt.id AS registro_id, m.nombre_comercial, m.dosis, rt.fecha_hora_programada, rt.estado
            FROM registro_tomas rt
            JOIN medicamentos m ON rt.medicamento_id = m.id
            WHERE m.paciente_id = :pid 
              AND DATE(rt.fecha_hora_programada) = CURRENT_DATE
            ORDER BY rt.fecha_hora_programada ASC
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
        
        // Verifica tomas ya generadas hoy
        $stmtCheck = $this->db->prepare("
            SELECT COUNT(*) FROM registro_tomas 
            WHERE medicamento_id = :mid AND DATE(fecha_hora_programada) = CURRENT_DATE
        ");
        
        $stmtInsert = $this->db->prepare("
            INSERT INTO registro_tomas (medicamento_id, fecha_hora_programada, estado)
            VALUES (:mid, :fecha_hora, 'pendiente')
        ");

        $hoy = date('Y-m-d');

        foreach ($medicamentos as $med) {
            $stmtCheck->execute(['mid' => $med['id']]);
            $count = (int)$stmtCheck->fetchColumn();

            // Si no hay tomas programadas para hoy de este fármaco
            if ($count === 0) {
                $frecuencia = (int)$med['frecuencia_horas'];
                if ($frecuencia <= 0) $frecuencia = 24;
                
                $tomasAlDia = floor(24 / $frecuencia);
                if ($tomasAlDia < 1) $tomasAlDia = 1;

                // Empezamos a las 08:00 AM (configurable)
                $horaInicio = 8;
                
                for ($i = 0; $i < $tomasAlDia; $i++) {
                    $horaSiguiente = $horaInicio + ($i * $frecuencia);
                    if ($horaSiguiente > 23) break; 
                    
                    $horaStr = str_pad((string)$horaSiguiente, 2, '0', STR_PAD_LEFT) . ':00:00';
                    $fechaHoraProg = $hoy . ' ' . $horaStr;
                    
                    $stmtInsert->execute([
                        'mid' => $med['id'],
                        'fecha_hora' => $fechaHoraProg
                    ]);
                }
            }
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Medicamento extends Model {
    protected $table = 'medicamentos';

    public function getActiveByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, paciente_id, nombre_comercial, dosis, dosis_por_toma, forma, color_distintivo, frecuencia_horas, stock_actual, dosis_restantes, instrucciones, activo 
            FROM medicamentos 
            WHERE paciente_id = :pid AND activo = TRUE 
            ORDER BY nombre_comercial ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodayDoses(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT rt.id AS registro_id, rt.id, m.id AS medicamento_id, m.nombre_comercial, m.nombre_comercial AS medicamento_nombre, 
                   m.dosis, m.dosis_por_toma, m.color_distintivo, m.forma, m.stock_actual,
                   rt.momento_dia, rt.estado, rt.fecha_programada, rt.hora_programada, rt.tomado_at
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
            SELECT id, paciente_id, nombre_comercial, dosis, dosis_por_toma, forma, color_distintivo, frecuencia_horas, stock_actual, stock_minimo_alerta, instrucciones, activo 
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
              AND th.fecha_programada >= (CURRENT_DATE - INTERVAL '1 day')
              AND th.estado = 'omitido'
            ORDER BY th.fecha_programada DESC, th.hora_programada ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBajoInventario(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, nombre_comercial, stock_actual, stock_minimo_alerta, dosis_por_toma, frecuencia_horas,
                   FLOOR(stock_actual / NULLIF((24.0 / GREATEST(frecuencia_horas, 1)) * GREATEST(dosis_por_toma, 1), 0)) AS dias_restantes
            FROM medicamentos
            WHERE paciente_id = :pid AND activo = TRUE 
              AND (
                  stock_actual <= 3
                  OR stock_actual <= stock_minimo_alerta 
                  OR (stock_actual / NULLIF((24.0 / GREATEST(frecuencia_horas, 1)) * GREATEST(dosis_por_toma, 1), 0)) < 4
              )
            ORDER BY stock_actual ASC, dias_restantes ASC
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

    public function togglePausa(int $id, int $pacienteId): bool {
        $stmt = $this->db->prepare("
            UPDATE medicamentos 
            SET activo = NOT activo 
            WHERE id = :id AND paciente_id = :pid
        ");
        return $stmt->execute(['id' => $id, 'pid' => $pacienteId]);
    }

    public function eliminar(int $id, int $pacienteId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM medicamentos 
            WHERE id = :id AND paciente_id = :pid
        ");
        return $stmt->execute(['id' => $id, 'pid' => $pacienteId]);
    }

    public function actualizarEstadoToma(int $registroId, int $pacienteId, string $estado, int $usuarioId, ?string $nota = null): bool {
        $esTomado = in_array($estado, ['tomado', 'pospuesto'], true) ? 1 : 0;
        
        // Obtener el medicamento_id antes de actualizar
        $stmtMed = $this->db->prepare("
            SELECT th.medicamento_id, m.dosis_por_toma, th.estado AS estado_anterior
            FROM tomas_historial th
            JOIN medicamentos m ON th.medicamento_id = m.id
            WHERE th.id = :id AND th.paciente_id = :pid
            LIMIT 1
        ");
        $stmtMed->execute(['id' => $registroId, 'pid' => $pacienteId]);
        $info = $stmtMed->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("
            UPDATE tomas_historial 
            SET estado = :estado, 
                tomado_at = CASE WHEN :es_tomado = 1 THEN NOW() ELSE tomado_at END,
                registrado_por_usuario_id = :usuario_id,
                nota_incidencia = :nota
            WHERE id = :id AND medicamento_id IN (SELECT id FROM medicamentos WHERE paciente_id = :paciente_id)
        ");
        $ok = $stmt->execute([
            'estado' => $estado,
            'es_tomado' => $esTomado,
            'usuario_id' => $usuarioId,
            'nota' => $nota,
            'id' => $registroId,
            'paciente_id' => $pacienteId
        ]);

        // Si se marca como tomado y antes no lo estaba, descontar stock del medicamento
        if ($ok && $estado === 'tomado' && $info && ($info['estado_anterior'] ?? '') !== 'tomado') {
            $dosis = (int)($info['dosis_por_toma'] ?? 1);
            $medId = (int)$info['medicamento_id'];
            $this->db->prepare("
                UPDATE medicamentos 
                SET stock_actual = GREATEST(0, stock_actual - :dosis) 
                WHERE id = :mid
            ")->execute(['dosis' => $dosis, 'mid' => $medId]);

            // Registrar en registro_tomas para trazabilidad
            $this->db->prepare("
                INSERT INTO registro_tomas (medicamento_id, paciente_id, usuario_registro_id, fecha_hora_programada, fecha_hora_tomada, estado, observaciones)
                VALUES (:mid, :pid, :uid, NOW(), NOW(), 'tomado', :obs)
            ")->execute([
                'mid' => $medId,
                'pid' => $pacienteId,
                'uid' => $usuarioId,
                'obs' => $nota ?? 'Toma registrada en tiempo'
            ]);
        }

        return $ok;
    }

    public function calcularAdherenciaSemanal(int $pacienteId): float {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) AS total_programadas,
                COUNT(CASE WHEN estado = 'tomado' THEN 1 END) AS total_tomadas
            FROM tomas_historial
            WHERE paciente_id = :pid 
              AND fecha_programada >= (CURRENT_DATE - INTERVAL '7 days')
              AND fecha_programada <= CURRENT_DATE
        ");
        $stmt->execute(['pid' => $pacienteId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = (int)($row['total_programadas'] ?? 0);
        $tomadas = (int)($row['total_tomadas'] ?? 0);

        if ($total === 0) {
            return 100.0;
        }

        return round(($tomadas / $total) * 100, 1);
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

        foreach ($medicamentos as $med) {
            $frecuencia = (int)($med['frecuencia_horas'] ?? 8);
            
            // Determinar momentos del día según frecuencia
            $momentos = match(true) {
                $frecuencia <= 6 => ['manana', 'tarde', 'noche'],
                $frecuencia <= 8 => ['manana', 'tarde', 'noche'],
                $frecuencia <= 12 => ['manana', 'noche'],
                default => ['manana']
            };

            foreach ($momentos as $momento) {
                $hora = $horasMomento[$momento] ?? '08:00:00';
                
                // Verificar si ya existe la toma para hoy
                $stmtCheck = $this->db->prepare("
                    SELECT id FROM tomas_historial 
                    WHERE medicamento_id = :mid AND fecha_programada = :fecha AND momento_dia::text = :momento 
                    LIMIT 1
                ");
                $stmtCheck->execute([
                    'mid' => $med['id'],
                    'fecha' => $hoy,
                    'momento' => $momento
                ]);

                if (!$stmtCheck->fetch()) {
                    $stmtIns = $this->db->prepare("
                        INSERT INTO tomas_historial (
                            medicamento_id, paciente_id, fecha_programada, hora_programada, momento_dia, estado
                        ) VALUES (
                            :mid, :pid, :fecha, :hora, :momento::momento_dia_tipo, 'pendiente'
                        )
                    ");
                    try {
                        $stmtIns->execute([
                            'mid' => $med['id'],
                            'pid' => $pacienteId,
                            'fecha' => $hoy,
                            'hora' => $hora,
                            'momento' => $momento
                        ]);
                    } catch (\Throwable $e) {
                        // Fallback si momento_dia no es enum
                        $stmtInsFallback = $this->db->prepare("
                            INSERT INTO tomas_historial (
                                medicamento_id, paciente_id, fecha_programada, hora_programada, momento_dia, estado
                            ) VALUES (
                                :mid, :pid, :fecha, :hora, :momento, 'pendiente'
                            )
                        ");
                        $stmtInsFallback->execute([
                            'mid' => $med['id'],
                            'pid' => $pacienteId,
                            'fecha' => $hoy,
                            'hora' => $hora,
                            'momento' => $momento
                        ]);
                    }
                }
            }
        }
    }
}

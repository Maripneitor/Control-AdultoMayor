<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class VisitaMedica extends Model {
    protected $table = 'visitas_medicas';

    /**
     * Registra una nueva visita médica / consulta clínica
     */
    public function crear(array $data): string {
        $stmt = $this->db->prepare("
            INSERT INTO visitas_medicas (
                paciente_id,
                usuario_registra_id,
                fecha_visita,
                lugar_atencion,
                motivo_consulta,
                diagnostico_notas,
                doctor_nombre,
                requiere_seguimiento,
                fecha_seguimiento,
                instrucciones_seguimiento
            ) VALUES (
                :paciente_id,
                :usuario_registra_id,
                :fecha_visita,
                :lugar_atencion,
                :motivo_consulta,
                :diagnostico_notas,
                :doctor_nombre,
                :requiere_seguimiento,
                :fecha_seguimiento,
                :instrucciones_seguimiento
            ) RETURNING id
        ");

        $stmt->bindValue(':paciente_id', (int)$data['paciente_id'], PDO::PARAM_INT);
        $stmt->bindValue(':usuario_registra_id', !empty($data['usuario_registra_id']) ? (int)$data['usuario_registra_id'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':fecha_visita', $data['fecha_visita'] ?? date('Y-m-d H:i:s'));
        $stmt->bindValue(':lugar_atencion', !empty($data['lugar_atencion']) ? (string)$data['lugar_atencion'] : null);
        $stmt->bindValue(':motivo_consulta', (string)$data['motivo_consulta']);
        $stmt->bindValue(':diagnostico_notas', !empty($data['diagnostico_notas']) ? (string)$data['diagnostico_notas'] : null);
        $stmt->bindValue(':doctor_nombre', !empty($data['doctor_nombre']) ? (string)$data['doctor_nombre'] : null);
        $stmt->bindValue(':requiere_seguimiento', !empty($data['requiere_seguimiento']), PDO::PARAM_BOOL);
        $stmt->bindValue(':fecha_seguimiento', !empty($data['fecha_seguimiento']) ? (string)$data['fecha_seguimiento'] : null);
        $stmt->bindValue(':instrucciones_seguimiento', !empty($data['instrucciones_seguimiento']) ? (string)$data['instrucciones_seguimiento'] : null);

        $stmt->execute();
        return (string)$stmt->fetchColumn();
    }

    /**
     * Obtiene el historial de visitas de un paciente ordenado cronológicamente con sus archivos adjuntos
     */
    public function getByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT v.id, v.paciente_id, v.usuario_registra_id, v.fecha_visita, 
                   v.lugar_atencion, v.motivo_consulta, v.diagnostico_notas, 
                   v.doctor_nombre, v.requiere_seguimiento, v.fecha_seguimiento, 
                   v.instrucciones_seguimiento, v.created_at,
                   u.nombre AS registrado_por_nombre
            FROM visitas_medicas v
            LEFT JOIN usuarios u ON v.usuario_registra_id = u.id
            WHERE v.paciente_id = :pid
            ORDER BY v.fecha_visita DESC
        ");
        $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
        $stmt->execute();
        $visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($visitas)) {
            return [];
        }

        // Obtener archivos adjuntos asociados a las visitas
        $visitasIds = array_column($visitas, 'id');
        $placeholders = implode(',', array_fill(0, count($visitasIds), '?'));
        
        $stmtFiles = $this->db->prepare("
            SELECT id, paciente_id, visita_medica_id, nombre_original, 
                   nombre_archivo_almacenado, mime_type, tamano_bytes, tipo_documento, created_at
            FROM expediente_archivos
            WHERE visita_medica_id IN ($placeholders)
            ORDER BY created_at ASC
        ");
        $stmtFiles->execute($visitasIds);
        $archivos = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar archivos por visita_medica_id
        $archivosPorVisita = [];
        foreach ($archivos as $arch) {
            $vid = $arch['visita_medica_id'];
            if (!isset($archivosPorVisita[$vid])) {
                $archivosPorVisita[$vid] = [];
            }
            $archivosPorVisita[$vid][] = $arch;
        }

        foreach ($visitas as &$v) {
            $v['archivos'] = $archivosPorVisita[$v['id']] ?? [];
        }

        return $visitas;
    }

    /**
     * Busca una visita médica por su UUID
     */
    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("
            SELECT v.id, v.paciente_id, v.usuario_registra_id, v.fecha_visita, 
                   v.lugar_atencion, v.motivo_consulta, v.diagnostico_notas, 
                   v.doctor_nombre, v.requiere_seguimiento, v.fecha_seguimiento, 
                   v.instrucciones_seguimiento, v.created_at,
                   u.nombre AS registrado_por_nombre
            FROM visitas_medicas v
            LEFT JOIN usuarios u ON v.usuario_registra_id = u.id
            WHERE v.id = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $visita = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$visita) {
            return null;
        }

        // Cargar archivos
        $stmtFiles = $this->db->prepare("
            SELECT id, paciente_id, visita_medica_id, nombre_original, 
                   nombre_archivo_almacenado, mime_type, tamano_bytes, tipo_documento, created_at
            FROM expediente_archivos
            WHERE visita_medica_id = :vid
            ORDER BY created_at ASC
        ");
        $stmtFiles->bindValue(':vid', $id);
        $stmtFiles->execute();
        $visita['archivos'] = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);

        return $visita;
    }

    /**
     * Registra y vincula un archivo subido a una visita médica
     */
    public function registrarArchivo(array $data): string {
        $stmt = $this->db->prepare("
            INSERT INTO expediente_archivos (
                paciente_id,
                visita_medica_id,
                nombre_original,
                nombre_archivo_almacenado,
                ruta_almacenamiento,
                mime_type,
                tamano_bytes,
                tipo_documento,
                subido_por_usuario_id
            ) VALUES (
                :paciente_id,
                :visita_medica_id,
                :nombre_original,
                :nombre_archivo_almacenado,
                :ruta_almacenamiento,
                :mime_type,
                :tamano_bytes,
                :tipo_documento,
                :subido_por_usuario_id
            ) RETURNING id
        ");

        $stmt->bindValue(':paciente_id', (int)$data['paciente_id'], PDO::PARAM_INT);
        $stmt->bindValue(':visita_medica_id', !empty($data['visita_medica_id']) ? (string)$data['visita_medica_id'] : null);
        $stmt->bindValue(':nombre_original', (string)$data['nombre_original']);
        $stmt->bindValue(':nombre_archivo_almacenado', (string)$data['nombre_archivo_almacenado']);
        $stmt->bindValue(':ruta_almacenamiento', (string)$data['ruta_almacenamiento']);
        $stmt->bindValue(':mime_type', (string)$data['mime_type']);
        $stmt->bindValue(':tamano_bytes', (int)$data['tamano_bytes'], PDO::PARAM_INT);
        $stmt->bindValue(':tipo_documento', (string)($data['tipo_documento'] ?? 'receta'));
        $stmt->bindValue(':subido_por_usuario_id', !empty($data['subido_por_usuario_id']) ? (int)$data['subido_por_usuario_id'] : null, PDO::PARAM_INT);

        $stmt->execute();
        return (string)$stmt->fetchColumn();
    }
}

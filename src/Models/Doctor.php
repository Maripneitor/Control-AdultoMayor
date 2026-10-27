<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Doctor extends Model {
    protected $table = 'doctores';

    public function getByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT d.id, d.usuario_id, d.paciente_id, d.nombre, d.cedula_profesional, 
                   d.especialidad, d.hospital_clinica, d.telefono_contacto, 
                   COALESCE(d.telefono_consultorio, d.telefono_contacto) AS telefono, 
                   d.direccion_consultorio, d.horario_atencion,
                   pd.estado, pd.fecha_vinculacion, pd.notas_medicas
            FROM doctores d
            LEFT JOIN paciente_doctor pd ON pd.doctor_id = d.id AND pd.paciente_id = :pid
            WHERE d.paciente_id = :pid2 OR pd.paciente_id = :pid3
            ORDER BY d.nombre ASC
        ");
        $stmt->execute(['pid' => $pacienteId, 'pid2' => $pacienteId, 'pid3' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT d.*, u.nombre AS usuario_nombre, u.email AS usuario_email
            FROM doctores d
            JOIN usuarios u ON d.usuario_id = u.id
            WHERE d.usuario_id = :uid
            LIMIT 1
        ");
        $stmt->execute(['uid' => $userId]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            // Si el usuario tiene rol médico pero no tiene registro en tabla doctores, crearlo automáticamente
            $stmtUser = $this->db->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE id = :uid LIMIT 1");
            $stmtUser->execute(['uid' => $userId]);
            $u = $stmtUser->fetch(PDO::FETCH_ASSOC);
            if ($u && in_array($u['rol'], ['medico', 'doctor'], true)) {
                $this->crearPerfilDoctor($userId, [
                    'nombre' => $u['nombre'],
                    'especialidad' => 'Medicina General / Geriatría',
                    'telefono_contacto' => '',
                    'cedula_profesional' => 'EN TRAMITE'
                ]);
                // Re-ejecutar consulta principal
                $stmt->execute(['uid' => $userId]);
                return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            }
            return null;
        }
        return $doctor;
    }

    public function crearPerfilDoctor(int $userId, array $datos): int {
        $stmt = $this->db->prepare("
            INSERT INTO doctores (usuario_id, nombre, cedula_profesional, especialidad, hospital_clinica, telefono_contacto, telefono_consultorio, direccion_consultorio, horario_atencion)
            VALUES (:uid, :nombre, :cedula, :especialidad, :hospital, :tel_contacto, :tel_consultorio, :direccion, :horario)
            RETURNING id
        ");
        $stmt->execute([
            'uid' => $userId,
            'nombre' => $datos['nombre'] ?? 'Dr(a). Médico',
            'cedula' => $datos['cedula_profesional'] ?? null,
            'especialidad' => $datos['especialidad'] ?? 'Geriatría',
            'hospital' => $datos['hospital_clinica'] ?? null,
            'tel_contacto' => $datos['telefono_contacto'] ?? null,
            'tel_consultorio' => $datos['telefono_consultorio'] ?? ($datos['telefono_contacto'] ?? null),
            'direccion' => $datos['direccion_consultorio'] ?? null,
            'horario' => $datos['horario_atencion'] ?? 'Lunes a Viernes 09:00 - 18:00'
        ]);
        return (int)$stmt->fetchColumn();
    }

    public function actualizarPerfilDoctor(int $doctorId, array $datos): bool {
        $stmt = $this->db->prepare("
            UPDATE doctores
            SET nombre = :nombre,
                cedula_profesional = :cedula,
                especialidad = :especialidad,
                hospital_clinica = :hospital,
                telefono_consultorio = :tel_consultorio,
                direccion_consultorio = :direccion,
                horario_atencion = :horario
            WHERE id = :id
        ");
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'cedula' => $datos['cedula_profesional'] ?? null,
            'especialidad' => $datos['especialidad'] ?? '',
            'hospital' => $datos['hospital_clinica'] ?? null,
            'tel_consultorio' => $datos['telefono_consultorio'] ?? null,
            'direccion' => $datos['direccion_consultorio'] ?? null,
            'horario' => $datos['horario_atencion'] ?? null,
            'id' => $doctorId
        ]);
    }

    public function obtenerPacientesAsignados(int $doctorId): array {
        $stmt = $this->db->prepare("
            SELECT p.id, p.nombre, p.apellidos, p.fecha_nacimiento, p.tipo_sangre, 
                   p.alergias, p.enfermedades_cronicas, p.foto_perfil, p.codigo_vinculacion,
                   p.token_emergencia_qr, p.qr_token,
                   pd.fecha_vinculacion, pd.estado AS estado_vinculo, pd.notas_medicas,
                   -- Últimos signos vitales
                   rs.presion_sistolica, rs.presion_diastolica, rs.ritmo_cardiaco, 
                   rs.glucosa, rs.momento_glucosa, rs.created_at AS fecha_ultimos_signos,
                   rs.alerta_emitida
            FROM paciente_doctor pd
            JOIN pacientes p ON pd.paciente_id = p.id
            LEFT JOIN LATERAL (
                SELECT presion_sistolica, presion_diastolica, ritmo_cardiaco, glucosa, momento_glucosa, created_at, alerta_emitida
                FROM registro_salud_diario
                WHERE paciente_id = p.id
                ORDER BY created_at DESC
                LIMIT 1
            ) rs ON true
            WHERE pd.doctor_id = :did AND pd.estado = 'activo'
            ORDER BY p.nombre ASC
        ");
        $stmt->execute(['did' => $doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function vincularConPacientePorNip(int $doctorId, string $codigoVinculacion): array {
        $codigo = strtoupper(trim($codigoVinculacion));
        if (empty($codigo)) {
            throw new \InvalidArgumentException('El código de vinculación o NIP no puede estar vacío.');
        }

        $stmtP = $this->db->prepare("
            SELECT id, nombre, apellidos, codigo_vinculacion, codigo_activo, codigo_expira_en 
            FROM pacientes 
            WHERE UPPER(codigo_vinculacion) = :cod LIMIT 1
        ");
        $stmtP->execute(['cod' => $codigo]);
        $paciente = $stmtP->fetch(PDO::FETCH_ASSOC);

        if (!$paciente) {
            throw new \RuntimeException("No se encontró ningún adulto mayor registrado con el código '{$codigo}'.");
        }

        if (isset($paciente['codigo_activo']) && $paciente['codigo_activo'] === false) {
            throw new \RuntimeException("El código de vinculación '{$codigo}' se encuentra inactivo.");
        }

        $pacienteId = (int)$paciente['id'];

        // Verificar si ya existe vínculo
        $stmtCheck = $this->db->prepare("
            SELECT id, estado FROM paciente_doctor 
            WHERE doctor_id = :did AND paciente_id = :pid 
            LIMIT 1
        ");
        $stmtCheck->execute(['did' => $doctorId, 'pid' => $pacienteId]);
        $existente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            if ($existente['estado'] !== 'activo') {
                $this->db->prepare("UPDATE paciente_doctor SET estado = 'activo', fecha_vinculacion = NOW() WHERE id = :id")
                         ->execute(['id' => $existente['id']]);
            }
        } else {
            $stmtIns = $this->db->prepare("
                INSERT INTO paciente_doctor (doctor_id, paciente_id, fecha_vinculacion, estado)
                VALUES (:did, :pid, NOW(), 'activo')
            ");
            $stmtIns->execute(['did' => $doctorId, 'pid' => $pacienteId]);
        }

        return $paciente;
    }

    public function guardarReceta(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO recetas_medicas (doctor_id, paciente_id, diagnostico, indicaciones_generales, fecha_emision, archivo_receta_url)
            VALUES (:did, :pid, :diagnostico, :indicaciones, :fecha_emision, :archivo_url)
            RETURNING id
        ");
        $stmt->execute([
            'did' => $data['doctor_id'] ?? null,
            'pid' => $data['paciente_id'],
            'diagnostico' => $data['diagnostico'] ?? '',
            'indicaciones' => $data['indicaciones_generales'] ?? '',
            'fecha_emision' => $data['fecha_emision'] ?? date('Y-m-d'),
            'archivo_url' => $data['archivo_receta_url'] ?? null
        ]);
        return (int)$stmt->fetchColumn();
    }

    public function obtenerRecetasPorDoctor(int $doctorId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, p.nombre AS paciente_nombre, p.apellidos AS paciente_apellidos
            FROM recetas_medicas r
            JOIN pacientes p ON r.paciente_id = p.id
            WHERE r.doctor_id = :did
            ORDER BY r.fecha_emision DESC, r.created_at DESC
        ");
        $stmt->execute(['did' => $doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRecetasPorPaciente(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, d.nombre AS doctor_nombre, d.cedula_profesional, d.especialidad
            FROM recetas_medicas r
            LEFT JOIN doctores d ON r.doctor_id = d.id
            WHERE r.paciente_id = :pid
            ORDER BY r.fecha_emision DESC, r.created_at DESC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerRecetaPorId(int $recetaId): ?array {
        $stmt = $this->db->prepare("
            SELECT r.*, d.nombre AS doctor_nombre, d.cedula_profesional, d.especialidad, d.hospital_clinica, d.telefono_consultorio,
                   p.nombre AS paciente_nombre, p.apellidos AS paciente_apellidos, p.fecha_nacimiento, p.tipo_sangre, p.alergias
            FROM recetas_medicas r
            LEFT JOIN doctores d ON r.doctor_id = d.id
            JOIN pacientes p ON r.paciente_id = p.id
            WHERE r.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $recetaId]);
        $receta = $stmt->fetch(PDO::FETCH_ASSOC);
        return $receta ?: null;
    }

    public function actualizarNotasEvolucion(int $pacienteId, int $doctorId, string $notas): bool {
        $stmt = $this->db->prepare("
            UPDATE paciente_doctor
            SET notas_medicas = :notas
            WHERE paciente_id = :pid AND doctor_id = :did
        ");
        return $stmt->execute([
            'notas' => $notas,
            'pid' => $pacienteId,
            'did' => $doctorId
        ]);
    }

    public function agregar(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO doctores (paciente_id, nombre, especialidad, telefono_contacto, hospital_clinica, horario_atencion) 
            VALUES (:paciente_id, :nombre, :especialidad, :telefono, :hospital_clinica, :horario_atencion)
        ");
        return $stmt->execute([
            'paciente_id' => $data['paciente_id'] ?? null,
            'nombre' => $data['nombre'],
            'especialidad' => $data['especialidad'] ?? '',
            'telefono' => $data['telefono'] ?? ($data['telefono_contacto'] ?? ''),
            'hospital_clinica' => $data['hospital_clinica'] ?? null,
            'horario_atencion' => $data['horario_atencion'] ?? null
        ]);
    }

    public function eliminar(int $id, int $pacienteId): bool {
        $stmt = $this->db->prepare("DELETE FROM doctores WHERE id = :id AND (paciente_id = :paciente_id OR id IN (SELECT doctor_id FROM paciente_doctor WHERE paciente_id = :pid2))");
        return $stmt->execute([
            'id' => $id,
            'paciente_id' => $pacienteId,
            'pid2' => $pacienteId
        ]);
    }
}

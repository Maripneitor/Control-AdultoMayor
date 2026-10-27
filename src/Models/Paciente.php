<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Paciente extends Model {
    protected $table = 'pacientes';

    public static function generarCodigoMnemotecnico(string $nombre): string {
        $limpio = strtoupper(preg_replace('/[^A-Za-z]/', '', $nombre));
        $prefijo = substr($limpio, 0, 3);
        if (strlen($prefijo) < 3) {
            $prefijo = str_pad($prefijo, 3, 'SEN');
        }
        $numero = random_int(1000, 9999);
        return "{$prefijo}-{$numero}";
    }

    public function createInitialProfile(int $userId, string $nombrePaciente): int {
        $tokenSos = bin2hex(random_bytes(16));
        $codigoVinculacion = self::generarCodigoMnemotecnico($nombrePaciente);

        $stmt = $this->db->prepare("
            INSERT INTO pacientes (usuario_id, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, token_sos, token_emergencia_qr, codigo_vinculacion, codigo_activo) 
            VALUES (:usuario_id, :nombre, 'Por completar', CURRENT_DATE, 'N/D', 'Ninguna', 'Ninguna', '', :token_sos, :token_qr, :codigo_vinculacion, TRUE) RETURNING id
        ");
        $stmt->execute([
            'usuario_id' => $userId,
            'nombre' => $nombrePaciente,
            'token_sos' => $tokenSos,
            'token_qr' => $tokenSos,
            'codigo_vinculacion' => $codigoVinculacion
        ]);
        return (int) $stmt->fetchColumn();
    }

    public function findByCodigoVinculacion(string $codigo): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, qr_token, qr_token AS uuid_publico, token_emergencia_qr,
                   nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, 
                   enfermedades_cronicas, notas_cognitivas, foto_perfil,
                   codigo_vinculacion, codigo_activo, codigo_expira_en, token_sos, 
                   created_at, updated_at 
            FROM pacientes 
            WHERE UPPER(codigo_vinculacion) = UPPER(:codigo) LIMIT 1
        ");
        $stmt->execute(['codigo' => trim($codigo)]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, qr_token, qr_token AS uuid_publico, token_emergencia_qr,
                   nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, 
                   enfermedades_cronicas, notas_cognitivas, foto_perfil,
                   codigo_vinculacion, codigo_activo, codigo_expira_en, token_sos, 
                   created_at, updated_at 
            FROM pacientes 
            WHERE usuario_id = :user_id LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function findByQrToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, qr_token, qr_token AS uuid_publico, token_emergencia_qr,
                   nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, 
                   enfermedades_cronicas, notas_cognitivas, foto_perfil,
                   codigo_vinculacion, codigo_activo, token_sos, created_at, updated_at 
            FROM pacientes 
            WHERE (qr_token::text = :token OR token_sos = :token2 OR token_emergencia_qr = :token3) LIMIT 1
        ");
        $stmt->execute(['token' => $token, 'token2' => $token, 'token3' => $token]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function getExpedienteById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT id, usuario_id, qr_token, qr_token AS uuid_publico, token_emergencia_qr,
                   nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, 
                   enfermedades_cronicas, notas_cognitivas, foto_perfil,
                   codigo_vinculacion, codigo_activo, codigo_expira_en, token_sos, 
                   created_at, updated_at 
            FROM pacientes 
            WHERE id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $paciente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $paciente ?: null;
    }

    public function updateDatosBasicos(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE pacientes 
            SET nombre = :nombre,
                apellidos = :apellidos,
                fecha_nacimiento = :fecha_nacimiento,
                tipo_sangre = :tipo_sangre,
                foto_perfil = COALESCE(:foto_perfil, foto_perfil),
                updated_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'tipo_sangre' => $data['tipo_sangre'] ?? 'N/D',
            'foto_perfil' => $data['foto_perfil'] ?? null,
            'id' => $id
        ]);
    }

    public function updatePadecimientos(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE pacientes 
            SET alergias = :alergias, 
                enfermedades_cronicas = :enfermedades_cronicas, 
                notas_cognitivas = :notas_cognitivas,
                updated_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute([
            'alergias' => $data['alergias'] ?? 'Ninguna',
            'enfermedades_cronicas' => $data['enfermedades_cronicas'] ?? 'Ninguna',
            'notas_cognitivas' => $data['notas_cognitivas'] ?? '',
            'id' => $id
        ]);
    }

    public function crearPerfilAdministrado(int $usuarioCuidadorId, array $datos): int {
        $nombre = trim($datos['nombre'] ?? '');
        $apellidos = trim($datos['apellidos'] ?? 'Por completar');
        $fechaNacimiento = !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : date('Y-m-d');
        $genero = $datos['genero'] ?? 'No especificado';
        $tipoSangre = $datos['tipo_sangre'] ?? 'N/D';
        $alergias = $datos['alergias'] ?? 'Ninguna';
        $enfermedades = $datos['enfermedades_cronicas'] ?? 'Ninguna';
        $notas = $datos['notas_cognitivas'] ?? '';
        $parentesco = $datos['parentesco'] ?? 'Familiar / Cuidador Principal';
        $fotoPerfil = $datos['foto_perfil'] ?? null;

        $tokenSos = bin2hex(random_bytes(16));
        $codigoVinculacion = self::generarCodigoMnemotecnico($nombre);

        // Comprobar si usuario creador no tiene usuario_id asignado en pacientes
        $stmtCheck = $this->db->prepare("SELECT id FROM pacientes WHERE usuario_id = :uid LIMIT 1");
        $stmtCheck->execute([':uid' => $usuarioCuidadorId]);
        $tienePropio = (bool)$stmtCheck->fetch();

        $usuarioIdParaInsertar = $tienePropio ? null : $usuarioCuidadorId;

        $stmt = $this->db->prepare("
            INSERT INTO pacientes (
                usuario_id, nombre, apellidos, fecha_nacimiento, 
                tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, 
                foto_perfil, token_sos, token_emergencia_qr, codigo_vinculacion, codigo_activo
            ) 
            VALUES (
                :usuario_id, :nombre, :apellidos, :fecha_nacimiento,
                :tipo_sangre, :alergias, :enfermedades_cronicas, :notas_cognitivas,
                :foto_perfil, :token_sos, :token_qr, :codigo_vinculacion, TRUE
            ) RETURNING id
        ");

        $stmt->execute([
            'usuario_id' => $usuarioIdParaInsertar,
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fechaNacimiento,
            'tipo_sangre' => $tipoSangre,
            'alergias' => $alergias,
            'enfermedades_cronicas' => $enfermedades,
            'notas_cognitivas' => $notas,
            'foto_perfil' => $fotoPerfil,
            'token_sos' => $tokenSos,
            'token_qr' => $tokenSos,
            'codigo_vinculacion' => $codigoVinculacion
        ]);

        $pacienteId = (int)$stmt->fetchColumn();

        // Vincular automáticamente en paciente_cuidador
        $cuidadorModel = new Cuidador();
        $cuidadorModel->agregarCuidador($pacienteId, $usuarioCuidadorId, $parentesco, 'familiar_admin');

        return $pacienteId;
    }

    public function regenerarCodigoNip(int $pacienteId): string {
        $paciente = $this->getExpedienteById($pacienteId);
        if (!$paciente) {
            throw new \RuntimeException("Paciente no encontrado.");
        }
        $nuevoCodigo = self::generarCodigoMnemotecnico($paciente['nombre'] ?? 'SEN');
        
        $stmt = $this->db->prepare("
            UPDATE pacientes 
            SET codigo_vinculacion = :codigo, 
                codigo_activo = TRUE,
                codigo_expira_en = NOW() + INTERVAL '30 days',
                updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt->execute(['codigo' => $nuevoCodigo, 'id' => $pacienteId]);
        return $nuevoCodigo;
    }

    public function desvincularFamiliar(int $pacienteId, int $cuidadorId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM paciente_cuidador 
            WHERE paciente_id = :pid AND usuario_cuidador_id = :uid
        ");
        return $stmt->execute(['pid' => $pacienteId, 'uid' => $cuidadorId]);
    }

    public function vincularPorNip(int $usuarioId, string $nip, string $parentesco = 'Familiar'): array {
        $codigo = strtoupper(trim($nip));
        if (empty($codigo)) {
            throw new \InvalidArgumentException('El código o NIP de vinculación no puede estar vacío.');
        }

        $paciente = $this->findByCodigoVinculacion($codigo);
        if (!$paciente) {
            throw new \RuntimeException("No se encontró ningún adulto mayor con el código '{$codigo}'. Verifica que sea correcto.");
        }

        $pacienteId = (int)$paciente['id'];

        // Comprobar si ya está vinculado
        $stmtVinc = $this->db->prepare("
            SELECT 1 FROM paciente_cuidador 
            WHERE usuario_cuidador_id = :uid AND paciente_id = :pid 
            LIMIT 1
        ");
        $stmtVinc->execute([':uid' => $usuarioId, ':pid' => $pacienteId]);
        $yaVinculado = (bool)$stmtVinc->fetch();

        if (!$yaVinculado) {
            $cuidadorModel = new Cuidador();
            $cuidadorModel->agregarCuidador($pacienteId, $usuarioId, $parentesco, 'familiar_admin');
        }

        return $paciente;
    }

    public function obtenerMedicosTratantes(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT d.id, d.nombre, d.especialidad, d.cedula_profesional, d.hospital_clinica, 
                   COALESCE(d.telefono_consultorio, d.telefono_contacto) AS telefono,
                   d.direccion_consultorio, d.horario_atencion,
                   pd.fecha_vinculacion, pd.estado AS estado_vinculo, pd.notas_medicas
            FROM paciente_doctor pd
            JOIN doctores d ON pd.doctor_id = d.id
            WHERE pd.paciente_id = :pid AND pd.estado = 'activo'
            ORDER BY d.nombre ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosVinculados(int $userId): array {
        $cuidadorModel = new Cuidador();
        $pacientePropio = $this->findByUserId($userId);
        $pacientesComoCuidador = $cuidadorModel->obtenerPacientesPorCuidador($userId);

        $todosLosPacientes = [];
        if ($pacientePropio) {
            $todosLosPacientes[] = $pacientePropio;
        }
        foreach ($pacientesComoCuidador as $pc) {
            if (!in_array($pc['id'], array_column($todosLosPacientes, 'id'))) {
                $todosLosPacientes[] = $pc;
            }
        }
        return $todosLosPacientes;
    }
}

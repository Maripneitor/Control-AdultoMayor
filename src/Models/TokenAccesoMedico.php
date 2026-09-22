<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class TokenAccesoMedico extends Model {
    protected $table = 'tokens_acceso_medico';

    /**
     * Genera un PIN temporal de 6 dígitos y token criptográfico para acceso médico
     */
    public function generarPin(
        int $pacienteId, 
        int $usuarioId, 
        string $medicoNombre = '', 
        string $hospital = '', 
        int $duracionHoras = 24
    ): array {
        // Generar PIN de 6 dígitos numéricos legibles
        $pin = (string) random_int(100000, 999999);
        $tokenUrl = bin2hex(random_bytes(24));
        $expiraAt = date('Y-m-d H:i:s', time() + ($duracionHoras * 3600));

        $stmt = $this->db->prepare("
            INSERT INTO tokens_acceso_medico (
                paciente_id,
                generado_por_usuario_id,
                pin_seguridad,
                token_url,
                medico_nombre,
                hospital_institucion,
                duracion_horas,
                expira_at,
                max_usos,
                usos_actuales,
                activo
            ) VALUES (
                :paciente_id,
                :generado_por_usuario_id,
                :pin_seguridad,
                :token_url,
                :medico_nombre,
                :hospital_institucion,
                :duracion_horas,
                :expira_at,
                20,
                0,
                TRUE
            ) RETURNING id, pin_seguridad, token_url, expira_at, duracion_horas
        ");

        $stmt->execute([
            ':paciente_id' => $pacienteId,
            ':generado_por_usuario_id' => $usuarioId,
            ':pin_seguridad' => $pin,
            ':token_url' => $tokenUrl,
            ':medico_nombre' => !empty($medicoNombre) ? $medicoNombre : null,
            ':hospital_institucion' => !empty($hospital) ? $hospital : null,
            ':duracion_horas' => $duracionHoras,
            ':expira_at' => $expiraAt
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Valida si un PIN o token URL es vigente, activo y no ha superado el máximo de usos
     */
    public function validarAcceso(string $pinOrToken, ?string $ip = null): ?array {
        $clean = trim($pinOrToken);
        if (empty($clean)) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT t.*, p.nombre, p.apellidos, p.fecha_nacimiento, p.tipo_sangre, 
                   p.alergias, p.enfermedades_cronicas, p.notas_cognitivas, p.qr_token
            FROM tokens_acceso_medico t
            JOIN pacientes p ON t.paciente_id = p.id
            WHERE (t.pin_seguridad = :val OR t.token_url = :val)
              AND t.activo = TRUE
              AND t.expira_at >= NOW()
              AND t.usos_actuales < t.max_usos
            LIMIT 1
        ");

        $stmt->execute([':val' => $clean]);
        $token = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$token) {
            return null;
        }

        // Registrar uso y actualizar timestamp de acceso
        $updateStmt = $this->db->prepare("
            UPDATE tokens_acceso_medico 
            SET usos_actuales = usos_actuales + 1,
                ultimo_acceso_at = NOW(),
                ip_ultimo_acceso = :ip
            WHERE id = :id
        ");
        $updateStmt->execute([
            ':id' => $token['id'],
            ':ip' => $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')
        ]);

        return $token;
    }

    /**
     * Revoca un token / PIN manualmente
     */
    public function revocar(string $tokenId): bool {
        $stmt = $this->db->prepare("UPDATE tokens_acceso_medico SET activo = FALSE WHERE id = :id");
        return $stmt->execute([':id' => $tokenId]);
    }
}

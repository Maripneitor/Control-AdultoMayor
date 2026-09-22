<?php
declare(strict_types=1);

namespace App\Security;

use Config\Database;
use PDO;

class AuditLogger {
    /**
     * Registra un evento de auditoría clínica en PostgreSQL de forma estructurada e inmutable
     *
     * @param string $accion Clave semántica del evento (ej. EXPEDIENTE_ALERGIAS_MODIFICADAS)
     * @param int|string|null $pacienteId ID del paciente involucrado
     * @param string|array $detalles Descripción o payload con los cambios realizados
     * @param int|null $usuarioId ID del usuario ejecutor (si es null se toma de sesión)
     */
    public static function log(string $accion, $pacienteId = null, $detalles = '', ?int $usuarioId = null): void {
        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            $uid = $usuarioId ?? ($_SESSION['user_id'] ?? null);
            $pid = $pacienteId !== null && (int)$pacienteId > 0 ? (int)$pacienteId : null;
            $ip = RateLimiter::getClientIp();
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

            $detallesStr = is_array($detalles) 
                ? json_encode($detalles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) 
                : (string)$detalles;

            $stmt = $db->prepare("
                INSERT INTO logs_auditoria (usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc)
                VALUES (:uid, :pid, :accion, :detalles, :ip, :ua, NOW() AT TIME ZONE 'UTC')
            ");

            $stmt->execute([
                ':uid' => $uid,
                ':pid' => $pid,
                ':accion' => $accion,
                ':detalles' => $detallesStr,
                ':ip' => $ip,
                ':ua' => mb_substr($ua, 0, 255)
            ]);

            error_log("[AUDIT CLINICO] UID: " . ($uid ?? 'Anónimo') . " | PID: " . ($pid ?? 'N/A') . " | Acción: $accion | $detallesStr");

        } catch (\Throwable $e) {
            error_log('[AuditLogger::log Error] ' . $e->getMessage());
        }
    }

    /**
     * Consulta el historial de auditoría de un paciente específico
     */
    public static function getHistorialPaciente(int $pacienteId, int $limite = 50): array {
        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            $stmt = $db->prepare("
                SELECT la.id, la.usuario_id, u.nombre as usuario_nombre, u.rol as usuario_rol,
                       la.accion, la.detalles, la.ip_origen, la.fecha_utc
                FROM logs_auditoria la
                LEFT JOIN usuarios u ON u.id = la.usuario_id
                WHERE la.paciente_id = :pid
                ORDER BY la.fecha_utc DESC
                LIMIT :limite
            ");
            $stmt->bindValue(':pid', $pacienteId, PDO::PARAM_INT);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            error_log('[AuditLogger::getHistorialPaciente Error] ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Garantiza la existencia de la tabla e índices en PostgreSQL
     */
    private static function ensureTableExists(PDO $db): void {
        static $checked = false;
        if ($checked) return;

        $sql = "
            CREATE TABLE IF NOT EXISTS logs_auditoria (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
                paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
                accion VARCHAR(100) NOT NULL,
                detalles TEXT,
                ip_origen VARCHAR(45),
                user_agent VARCHAR(255),
                fecha_utc TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL
            );
            CREATE INDEX IF NOT EXISTS idx_logs_auditoria_paciente ON logs_auditoria(paciente_id, fecha_utc DESC);
            CREATE INDEX IF NOT EXISTS idx_logs_auditoria_usuario ON logs_auditoria(usuario_id, fecha_utc DESC);
            CREATE INDEX IF NOT EXISTS idx_logs_auditoria_accion ON logs_auditoria(accion, fecha_utc DESC);
        ";
        $db->exec($sql);
        $checked = true;
    }
}

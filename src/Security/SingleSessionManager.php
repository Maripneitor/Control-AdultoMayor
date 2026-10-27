<?php
declare(strict_types=1);

namespace App\Security;

use Config\Database;
use PDO;

class SingleSessionManager {
    private const SESSION_TOKEN_KEY = 'auth_session_token';
    private const SESSION_SYNC_INTERVAL_SEC = 60;

    /**
     * Asegura la existencia de la tabla en PostgreSQL
     */
    public static function ensureTableExists(?PDO $db = null): void {
        static $checked = false;
        if ($checked) return;

        try {
            $db = $db ?? Database::getInstance()->getConnection();
            $sql = "
                CREATE TABLE IF NOT EXISTS sesiones_activas (
                    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                    usuario_id INT NOT NULL,
                    session_token VARCHAR(128) NOT NULL,
                    session_id VARCHAR(128) NOT NULL,
                    ip_origen VARCHAR(45) NOT NULL,
                    user_agent VARCHAR(500) NOT NULL,
                    dispositivo VARCHAR(150) NOT NULL,
                    ultimo_acceso TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    CONSTRAINT uq_sesiones_activas_usuario UNIQUE (usuario_id),
                    CONSTRAINT fk_sesiones_activas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
                );
                CREATE INDEX IF NOT EXISTS idx_sesiones_activas_usuario ON sesiones_activas(usuario_id);
                CREATE INDEX IF NOT EXISTS idx_sesiones_activas_token ON sesiones_activas(session_token);
            ";
            $db->exec($sql);
            $checked = true;
        } catch (\Throwable $e) {
            error_log('[SingleSessionManager::ensureTableExists Error] ' . $e->getMessage());
        }
    }

    /**
     * Registra una nueva sesión activa para el usuario, invalidando cualquier sesión previa
     *
     * @param int $userId ID del usuario que inicia sesión
     * @return string Token criptográfico generado para la sesión
     */
    public static function register(int $userId): string {
        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            $token = bin2hex(random_bytes(32));
            $phpSessionId = session_id() ?: 'cli_' . bin2hex(random_bytes(8));
            $ip = self::getClientIp();
            $ua = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido', 0, 500);
            $dispositivo = self::parseUserAgent($ua);

            // Verificar si existía una sesión previa activa para auditar el desplazamiento
            $stmtPrev = $db->prepare("
                SELECT session_token, ip_origen, dispositivo, creado_en 
                FROM sesiones_activas 
                WHERE usuario_id = :uid 
                LIMIT 1
            ");
            $stmtPrev->execute([':uid' => $userId]);
            $prevSession = $stmtPrev->fetch(PDO::FETCH_ASSOC);

            if ($prevSession) {
                $prevDevice = $prevSession['dispositivo'] ?? 'Dispositivo previo';
                $prevIp = $prevSession['ip_origen'] ?? 'IP previa';

                AuditLogger::log(
                    'SESION_CONCURRENTE_DESPLAZADA',
                    null,
                    "Sesión previa cerrada desde {$prevDevice} (IP: {$prevIp}) por nuevo inicio de sesión desde {$dispositivo} (IP: {$ip}).",
                    $userId
                );
            } else {
                AuditLogger::log(
                    'SESION_INICIADA',
                    null,
                    "Inicio de sesión seguro registrado en {$dispositivo} (IP: {$ip}).",
                    $userId
                );
            }

            // Registrar o actualizar sesión única activa (UPSERT atómico)
            $stmt = $db->prepare("
                INSERT INTO sesiones_activas (
                    usuario_id, session_token, session_id, ip_origen, user_agent, dispositivo, ultimo_acceso, creado_en
                ) VALUES (
                    :uid, :token, :sess_id, :ip, :ua, :dispositivo, NOW() AT TIME ZONE 'UTC', NOW() AT TIME ZONE 'UTC'
                )
                ON CONFLICT (usuario_id) DO UPDATE SET
                    session_token = EXCLUDED.session_token,
                    session_id = EXCLUDED.session_id,
                    ip_origen = EXCLUDED.ip_origen,
                    user_agent = EXCLUDED.user_agent,
                    dispositivo = EXCLUDED.dispositivo,
                    ultimo_acceso = NOW() AT TIME ZONE 'UTC',
                    creado_en = NOW() AT TIME ZONE 'UTC'
            ");

            $stmt->execute([
                ':uid' => $userId,
                ':token' => $token,
                ':sess_id' => $phpSessionId,
                ':ip' => $ip,
                ':ua' => $ua,
                ':dispositivo' => $dispositivo
            ]);

            // Guardar en la sesión PHP local
            $_SESSION[self::SESSION_TOKEN_KEY] = $token;
            $_SESSION['auth_session_ip'] = $ip;
            $_SESSION['auth_session_device'] = $dispositivo;
            $_SESSION['auth_session_last_sync'] = time();

            return $token;

        } catch (\Throwable $e) {
            error_log('[SingleSessionManager::register Error] ' . $e->getMessage());
            // Token de fallback en caso de error transitorio de BD
            $fallback = bin2hex(random_bytes(32));
            $_SESSION[self::SESSION_TOKEN_KEY] = $fallback;
            return $fallback;
        }
    }

    /**
     * Valida si la sesión actual en el navegador sigue siendo la única sesión activa autorizada.
     * Si fue desplazada por otro dispositivo o navegador, termina la sesión local y devuelve false.
     *
     * @return bool True si la sesión es válida y activa; False si fue invalidada o no autenticado.
     */
    public static function validate(): bool {
        if (empty($_SESSION['user_id'])) {
            return false;
        }

        $userId = (int)$_SESSION['user_id'];
        $currentSessionToken = (string)($_SESSION[self::SESSION_TOKEN_KEY] ?? '');

        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            $stmt = $db->prepare("
                SELECT session_token, session_id, ip_origen, dispositivo, creado_en, ultimo_acceso
                FROM sesiones_activas
                WHERE usuario_id = :uid
                LIMIT 1
            ");
            $stmt->execute([':uid' => $userId]);
            $activeRecord = $stmt->fetch(PDO::FETCH_ASSOC);

            // Caso A: No hay registro de sesión activa en la base de datos (fue cerrada o revocada)
            if (!$activeRecord) {
                AuditLogger::log(
                    'SESION_REVOCADA_TERMINADA',
                    null,
                    "Acceso rechazado: la sesión activa para el usuario fue revocada o cerrada remotamente.",
                    $userId
                );

                self::terminateLocalSession(
                    'Tu sesión ha expirado o fue cerrada desde otro lugar. Por favor inicia sesión nuevamente.',
                    'warning',
                    'Sesión Finalizada'
                );
                return false;
            }

            // Caso B: Si la sesión PHP actual no tiene token guardado aún (migración transparente en caliente)
            if ($currentSessionToken === '') {
                // Si el registro activo coincide con el ID de sesión PHP actual
                if (session_id() && session_id() === $activeRecord['session_id']) {
                    $_SESSION[self::SESSION_TOKEN_KEY] = $activeRecord['session_token'];
                    $_SESSION['auth_session_device'] = $activeRecord['dispositivo'];
                    $_SESSION['auth_session_last_sync'] = time();
                    return true;
                }

                // Si no coincide, esta sesión no es la activa
                self::terminateLocalSession(
                    'Se detectó un inicio de sesión en otro navegador o dispositivo (' . htmlspecialchars($activeRecord['dispositivo']) . '). Solo se permite una sesión activa a la vez.',
                    'warning',
                    'Sesión Cerrada por Seguridad'
                );
                return false;
            }

            // Caso C: Comparación criptográfica segura de tokens
            if (!hash_equals($activeRecord['session_token'], $currentSessionToken)) {
                $newDevice = $activeRecord['dispositivo'] ?? 'Otro dispositivo/navegador';
                $newIp = $activeRecord['ip_origen'] ?? 'IP remota';

                AuditLogger::log(
                    'SESION_CONCURRENTE_BLOQUEADA',
                    null,
                    "Petición bloqueada en sesión obsoleta. La sesión activa actual reside en {$newDevice} (IP: {$newIp}).",
                    $userId
                );

                self::terminateLocalSession(
                    'Tu sesión ha sido cerrada automáticamente porque se inició sesión desde otro navegador o dispositivo (' . htmlspecialchars($newDevice) . '). Por protección y seguridad médica, solo se permite una sesión activa.',
                    'warning',
                    'Sesión Cerrada por Seguridad'
                );
                return false;
            }

            // Sesión válida: sincronizar última actividad periódicamente (cada 60 segundos)
            $lastSync = (int)($_SESSION['auth_session_last_sync'] ?? 0);
            if ((time() - $lastSync) >= self::SESSION_SYNC_INTERVAL_SEC) {
                $stmtUpdate = $db->prepare("
                    UPDATE sesiones_activas 
                    SET ultimo_acceso = NOW() AT TIME ZONE 'UTC' 
                    WHERE usuario_id = :uid
                ");
                $stmtUpdate->execute([':uid' => $userId]);
                $_SESSION['auth_session_last_sync'] = time();
            }

            return true;

        } catch (\Throwable $e) {
            error_log('[SingleSessionManager::validate Error] ' . $e->getMessage());
            // En caso de fallo transitorio en base de datos, no expulsar abruptamente al usuario médico
            return true;
        }
    }

    /**
     * Destruye y desregistra la sesión activa de la base de datos (usado en Logout y eliminación de cuenta)
     */
    public static function destroy(int $userId): void {
        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            $stmt = $db->prepare("DELETE FROM sesiones_activas WHERE usuario_id = :uid");
            $stmt->execute([':uid' => $userId]);

            AuditLogger::log(
                'SESION_CERRADA',
                null,
                "Sesión cerrada voluntariamente por el usuario.",
                $userId
            );
        } catch (\Throwable $e) {
            error_log('[SingleSessionManager::destroy Error] ' . $e->getMessage());
        }

        self::clearSessionAuthVariables();
    }

    /**
     * Consulta información de la sesión activa de un usuario para mostrar en Mi Perfil
     */
    public static function getActiveSessionInfo(int $userId): ?array {
        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            $stmt = $db->prepare("
                SELECT session_token, ip_origen, user_agent, dispositivo, ultimo_acceso, creado_en
                FROM sesiones_activas
                WHERE usuario_id = :uid
                LIMIT 1
            ");
            $stmt->execute([':uid' => $userId]);
            $session = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($session) {
                $currentToken = $_SESSION[self::SESSION_TOKEN_KEY] ?? '';
                $session['es_sesion_actual'] = !empty($currentToken) && hash_equals($session['session_token'], (string)$currentToken);
            }

            return $session ?: null;
        } catch (\Throwable $e) {
            error_log('[SingleSessionManager::getActiveSessionInfo Error] ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Termina la sesión local en este navegador, regenera el ID de sesión para evitar fijación
     * y deja preparado el mensaje Flash para la vista de login.
     */
    public static function terminateLocalSession(
        string $message,
        string $type = 'warning',
        string $title = 'Sesión Finalizada'
    ): void {
        self::clearSessionAuthVariables();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION['flash_notification'] = [
            'type' => $type,
            'title' => $title,
            'message' => $message
        ];
    }

    /**
     * Limpia todas las variables de autenticación de la sesión PHP
     */
    private static function clearSessionAuthVariables(): void {
        unset(
            $_SESSION['user_id'],
            $_SESSION['user_name'],
            $_SESSION['user_role'],
            $_SESSION['user_permissions'],
            $_SESSION['paciente_activo_id'],
            $_SESSION['paciente_activo_nombre'],
            $_SESSION['paciente_qr_token'],
            $_SESSION[self::SESSION_TOKEN_KEY],
            $_SESSION['auth_session_ip'],
            $_SESSION['auth_session_device'],
            $_SESSION['auth_session_last_sync']
        );
    }

    /**
     * Obtiene la IP del cliente considerando proxies y Cloudflare
     */
    public static function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($parts[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Traduce el User Agent a una descripción amigable (Navegador y Sistema Operativo)
     */
    public static function parseUserAgent(string $ua): string {
        $browser = 'Navegador Web';
        $os = 'Dispositivo';

        // Detección de Sistema Operativo
        if (preg_match('/windows nt 10/i', $ua)) {
            $os = 'Windows 10/11';
        } elseif (preg_match('/windows nt 6\.3/i', $ua)) {
            $os = 'Windows 8.1';
        } elseif (preg_match('/windows nt 6\.1/i', $ua)) {
            $os = 'Windows 7';
        } elseif (preg_match('/windows nt/i', $ua)) {
            $os = 'Windows';
        } elseif (preg_match('/iphone/i', $ua)) {
            $os = 'iPhone';
        } elseif (preg_match('/ipad/i', $ua)) {
            $os = 'iPad';
        } elseif (preg_match('/android/i', $ua)) {
            $os = 'Android';
        } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $ua)) {
            $os = 'Linux';
        }

        // Detección de Navegador (Orden cuidadoso por superposición de tokens)
        if (preg_match('/edg\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/opr\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Opera';
        } elseif (preg_match('/chrome\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/firefox\/([0-9\.]+)/i', $ua, $matches)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/version\/([0-9\.]+).*safari/i', $ua, $matches)) {
            $browser = 'Safari';
        }

        return "{$browser} en {$os}";
    }
}

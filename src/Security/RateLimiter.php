<?php
declare(strict_types=1);

namespace App\Security;

use Config\Database;
use PDO;

class RateLimiter {
    public const DEFAULT_MAX_ATTEMPTS = 3;
    public const DEFAULT_WINDOW_MINUTES = 15;

    /**
     * Aplica la regla estricta de Rate Limiting (Máx 3 peticiones cada 15 min).
     * Si se supera el límite, aborta con código HTTP 429 (Too Many Requests).
     */
    public static function enforce(string $accion, int $maxAttempts = self::DEFAULT_MAX_ATTEMPTS, int $windowMinutes = self::DEFAULT_WINDOW_MINUTES): void {
        $ip = self::getClientIp();

        if (!self::check($ip, $accion, $maxAttempts, $windowMinutes)) {
            self::abortTooManyRequests($windowMinutes);
        }

        // Registrar intento
        self::hit($ip, $accion, $windowMinutes);
    }

    /**
     * Comprueba si la IP ha superado el número de intentos permitidos en la ventana de tiempo.
     */
    public static function check(string $ip, string $accion, int $maxAttempts = self::DEFAULT_MAX_ATTEMPTS, int $windowMinutes = self::DEFAULT_WINDOW_MINUTES): bool {
        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            // 1. Limpiar intentos que ya hayan expirado fuera de la ventana de tiempo
            $stmtClean = $db->prepare(
                "DELETE FROM rate_limits 
                 WHERE ip_address = :ip AND accion = :accion AND primer_intento < NOW() - INTERVAL '" . (int)$windowMinutes . " minutes'"
            );
            $stmtClean->execute([':ip' => $ip, ':accion' => $accion]);

            // 2. Consultar intentos en la ventana activa
            $stmt = $db->prepare(
                "SELECT intentos, primer_intento FROM rate_limits 
                 WHERE ip_address = :ip AND accion = :accion LIMIT 1"
            );
            $stmt->execute([':ip' => $ip, ':accion' => $accion]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record && (int)$record['intentos'] >= $maxAttempts) {
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            error_log('[RateLimiter::check Error] ' . $e->getMessage());
            // En caso de fallo de infraestructura no bloquear el acceso legítimo, pero registrar error
            return true;
        }
    }

    /**
     * Registra un nuevo intento o incrementa el contador
     */
    public static function hit(string $ip, string $accion, int $windowMinutes = self::DEFAULT_WINDOW_MINUTES): void {
        try {
            $db = Database::getInstance()->getConnection();
            self::ensureTableExists($db);

            $stmt = $db->prepare("
                INSERT INTO rate_limits (ip_address, accion, intentos, primer_intento, ultimo_intento)
                VALUES (:ip, :accion, 1, NOW(), NOW())
                ON CONFLICT (ip_address, accion) DO UPDATE 
                SET intentos = rate_limits.intentos + 1,
                    ultimo_intento = NOW()
            ");
            $stmt->execute([':ip' => $ip, ':accion' => $accion]);
        } catch (\Throwable $e) {
            error_log('[RateLimiter::hit Error] ' . $e->getMessage());
        }
    }

    /**
     * Limpia los registros de rate limit tras un inicio de sesión exitoso
     */
    public static function clear(string $ip, string $accion): void {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM rate_limits WHERE ip_address = :ip AND accion = :accion");
            $stmt->execute([':ip' => $ip, ':accion' => $accion]);
        } catch (\Throwable $e) {
            error_log('[RateLimiter::clear Error] ' . $e->getMessage());
        }
    }

    /**
     * Emite la respuesta HTTP 429 Too Many Requests formateada según el tipo de cliente
     */
    private static function abortTooManyRequests(int $windowMinutes): void {
        $retrySeconds = $windowMinutes * 60;
        http_response_code(429);
        header('HTTP/1.1 429 Too Many Requests');
        header("Retry-After: {$retrySeconds}");

        $mensaje = "Demasiadas solicitudes. Límite de seguridad alcanzado (máximo 3 peticiones por cada {$windowMinutes} minutos). Por favor espera antes de reintentar.";

        if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => $mensaje,
                'status' => 429,
                'retry_after_seconds' => $retrySeconds
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Vista HTML accesible con Tailwind CSS
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>429 - Límite Excedido | VitalSenior</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans overflow-x-hidden">
            <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4 text-3xl font-extrabold">⏳</div>
                <h1 class="text-2xl font-extrabold text-slate-800 mb-2">Límite de Solicitudes Excedido</h1>
                <p class="text-slate-600 text-sm mb-6 leading-relaxed">{$mensaje}</p>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 mb-6 text-xs text-slate-500">
                    Tu IP ha sido pausada temporalmente por protección de costos y prevención de fuerza bruta.
                </div>
                <a href="/Control-AdultoMayor/public/login" class="inline-block w-full py-3 px-6 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition shadow">
                    Regresar al Inicio de Sesión
                </a>
            </div>
        </body>
        </html>
HTML;
        exit;
    }

    /**
     * Asegura la creación de la tabla rate_limits en PostgreSQL
     */
    private static function ensureTableExists(PDO $db): void {
        static $checked = false;
        if ($checked) return;

        $sql = "
            CREATE TABLE IF NOT EXISTS rate_limits (
                id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                ip_address VARCHAR(45) NOT NULL,
                accion VARCHAR(100) NOT NULL,
                intentos INT DEFAULT 1,
                primer_intento TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                ultimo_intento TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                CONSTRAINT uq_ip_accion UNIQUE (ip_address, accion)
            );
            CREATE INDEX IF NOT EXISTS idx_rate_limits_busqueda ON rate_limits(ip_address, accion, primer_intento);
        ";
        $db->exec($sql);
        $checked = true;
    }

    /**
     * Obtiene la IP real del cliente considerando headers de proxy seguros
     */
    public static function getClientIp(): string {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

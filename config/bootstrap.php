<?php
declare(strict_types=1);

/**
 * ==============================================================================
 * VitalSenior - Bootstrap de Inicialización y Gestión Segura de Errores
 * DevSecOps: Prevención de Fugas de Información y Trazas de Pila (Anti-Data Leak)
 * ==============================================================================
 */

// 1. Carga Segura del Archivo de Entorno (.env)
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }
        if (strpos($trimmed, '=') !== false) {
            [$key, $value] = explode('=', $trimmed, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// 2. Detección Estricta del Entorno de Ejecución (Por defecto: 'production' para máxima seguridad)
$appEnv = strtolower(trim((string)(getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'production'))));
$isProduction = ($appEnv === 'production');

if (!defined('APP_ENV')) {
    define('APP_ENV', $appEnv);
}

// 3. Configuración de Directivas PHP y Destino Privado de Logs
$logsDirectory = dirname(__DIR__) . '/logs';
if (!is_dir($logsDirectory)) {
    @mkdir($logsDirectory, 0750, true);
}
$privateLogFile = $logsDirectory . '/error.log';

ini_set('log_errors', '1');
ini_set('error_log', $privateLogFile);

if ($isProduction) {
    // EN PRODUCCIÓN: Prohibir estrictamente display_errors para evitar exponer Stack Traces al navegador
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
} else {
    // EN DESARROLLO: Habilitar diagnóstico detallado para el desarrollador local
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

// 4. Manejador Global de Errores PHP (Warnings, Notices, etc.)
set_error_handler(function (int $severity, string $message, string $file, int $line) use ($isProduction): bool {
    // Si el operador de supresión de errores (@) fue utilizado, ignorar
    if (!(error_reporting() & $severity)) {
        return false;
    }

    $logEntry = sprintf(
        "[%s] PHP Error (Nivel %d): %s en %s:%d",
        gmdate('Y-m-d H:i:s \U\T\C'),
        $severity,
        $message,
        $file,
        $line
    );
    error_log($logEntry);

    // Si es un error fatal o crítico, convertirlo en excepción para manejo uniforme
    if ($severity === E_USER_ERROR || $severity === E_RECOVERABLE_ERROR) {
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    return true; // Error no crítico procesado y registrado
});

// 5. Manejador Global de Excepciones No Capturadas (Anti-Data Leak)
set_exception_handler(function (\Throwable $exception) use ($isProduction): void {
    // Registrar detalle completo, archivo, línea y traza de pila en el archivo privado
    $logEntry = sprintf(
        "[%s] CRITICAL UNHANDLED EXCEPTION: %s (Código %d) en %s:%d\nStack Trace:\n%s\n",
        gmdate('Y-m-d H:i:s \U\T\C'),
        $exception->getMessage(),
        $exception->getCode(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    error_log($logEntry);

    // Responder al cliente de forma segura sin filtrar estructura de carpetas ni consultas SQL
    if (!headers_sent()) {
        http_response_code(500);
        header('HTTP/1.1 500 Internal Server Error');
    }

    $isJsonExpected = (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
        || (!empty($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))
        || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    if ($isJsonExpected) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error'   => 'Ocurrió un problema interno en el servidor. Por favor comuníquese con soporte.',
            'code'    => 500,
            'utc'     => gmdate('Y-m-d H:i:s \U\T\C')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($isProduction) {
        // Pantalla amigable y accesible con Tailwind CSS sin fugas de datos
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error 500 - Problema Interno | VitalSenior</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans">
            <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-3xl font-extrabold">⚠️</div>
                <h1 class="text-2xl font-extrabold text-slate-800 mb-2">Ocurrió un problema</h1>
                <p class="text-slate-600 text-sm mb-6 leading-relaxed">
                    No pudimos procesar tu solicitud médica en este momento. El incidente ha sido registrado de forma segura para su pronta resolución.
                </p>
                <div class="flex flex-col gap-2">
                    <a href="/Control-AdultoMayor/public/dashboard" class="w-full py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow">
                        Regresar al Dashboard
                    </a>
                    <a href="/Control-AdultoMayor/public/expediente#contactos" class="w-full py-3 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition text-xs">
                        Ver Red de Contactos de Emergencia
                    </a>
                </div>
            </div>
        </body>
        </html>
HTML;
    } else {
        // Modo Debug local: muestra detalles técnicos exclusivamente en desarrollo
        echo "<div style='font-family: monospace; background:#fee2e2; color:#991b1b; padding:20px; border:2px solid #ef4444; border-radius:8px;'>";
        echo "<h2>[DEVELOPMENT DEBUG] Unhandled Exception</h2>";
        echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><strong>Archivo:</strong> " . htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8') . ":" . $exception->getLine() . "</p>";
        echo "<pre style='background:#fff; padding:10px; overflow-x:auto; border-radius:4px;'>" . htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "</div>";
    }
    exit;
});

// 6. Manejador de Cierre (Shutdown Function para atrapar Fatal Errors de compilación/parseo)
register_shutdown_function(function () use ($isProduction): void {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $logEntry = sprintf(
            "[%s] FATAL SHUTDOWN ERROR: %s en %s:%d",
            gmdate('Y-m-d H:i:s \U\T\C'),
            $error['message'],
            $error['file'],
            $error['line']
        );
        error_log($logEntry);

        if (!headers_sent()) {
            http_response_code(500);
        }

        if ($isProduction) {
            echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'/><title>Error del Servidor</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans'><div class='bg-white p-8 rounded-3xl shadow-xl max-w-sm text-center border border-slate-200'><h1 class='text-xl font-bold text-slate-800 mb-2'>Servicio no disponible</h1><p class='text-slate-600 text-sm mb-4'>Estamos solucionando un problema técnico. Intenta nuevamente en unos minutos.</p><a href='/Control-AdultoMayor/public/' class='inline-block w-full py-2.5 bg-blue-600 text-white font-bold rounded-xl'>Ir al Inicio</a></div></body></html>";
        }
    }
});

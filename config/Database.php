<?php
declare(strict_types=1);

namespace Config;

use PDO;
use PDOException;
use Throwable;

require_once __DIR__ . '/config.php';

class Database {
    private static ?Database $instance = null;
    private ?PDO $conn = null;

    /**
     * Constructor privado bajo patrón Singleton
     * Conexión estricta a PostgreSQL mediante PDO
     */
    private function __construct() {
        $host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: '127.0.0.1');
        $port = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: '5432');
        $dbname = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'VitalSenior');
        $user = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'postgres');
        $pass = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: '');

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;options=\'--client_encoding=UTF8\'', $host, $port, $dbname);

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5
        ];

        try {
            $this->conn = new PDO($dsn, $user, $pass, $options);
            // Asegurar que las fechas y zonas horarias de sesión se traten en UTC
            $this->conn->exec("SET TIME ZONE 'UTC'");
        } catch (PDOException $e) {
            // Manejo estricto de excepciones: ocultar contraseñas/DSN al usuario y registrar en log
            error_log('[DB_ERROR] Fallo de conexión PDO a PostgreSQL: ' . $e->getMessage());
            
            if (!headers_sent()) {
                http_response_code(500);
            }
            
            // Si es petición API / JSON
            if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Error de conexión con la base de datos segura.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Vista de contingencia amigable y segura
            echo <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <title>VitalSenior - Servicio No Disponible</title>
                <script src="https://cdn.tailwindcss.com"></script>
            </head>
            <body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 overflow-x-hidden font-sans">
                <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center border border-slate-200">
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">!</div>
                    <h1 class="text-2xl font-bold text-slate-800 mb-2">Servicio Temporalmente Interrumpido</h1>
                    <p class="text-slate-600 text-sm mb-6 leading-relaxed">No se pudo establecer conexión con el repositorio clínico seguro. Nuestro equipo técnico ha sido notificado.</p>
                    <button onclick="window.location.reload();" class="w-full bg-teal-700 hover:bg-teal-800 text-white font-semibold py-3 px-6 rounded-xl transition duration-200 shadow-md">
                        Reintentar Conexión
                    </button>
                </div>
            </body>
            </html>
HTML;
            exit;
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->conn;
    }

    // Evitar clonación o deserialización
    private function __clone() {}
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}

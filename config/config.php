<?php
// config/config.php

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        if ($trimmedLine === '' || strpos($trimmedLine, '#') === 0) continue;
        list($name, $value) = explode('=', $trimmedLine, 2);
        $trimmedName = trim($name);
        // Respetar variables inyectadas por el entorno/Docker
        if (getenv($trimmedName) === false) {
            putenv($trimmedName . '=' . trim($value));
        }
    }
}

// Módulo 12: Extraer contraseñas hardcodeadas.
// Configuración de la Base de Datos PostgreSQL (ahora leyendo estrictamente desde el Entorno)
$dbHost = getenv('DB_HOST');
$dbPort = getenv('DB_PORT');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');

if (!$dbHost || !$dbName || !$dbUser || !$dbPass) {
    die('Error de Configuración (Módulo 12): Faltan variables críticas en el archivo .env.');
}

define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort ?: '5432');
define('DB_NAME', $dbName); 
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass); 

// Otras configuraciones de la aplicación
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/Control-AdultoMayor/public');

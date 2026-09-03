<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';

use App\Config\Database;

try {
    $db = Database::getInstance()->getConnection();

    $sql = "
    -- 1. Vincular pacientes con usuarios
    ALTER TABLE pacientes 
    ADD COLUMN IF NOT EXISTS usuario_id INT REFERENCES usuarios(id) ON DELETE CASCADE;

    -- 2. Tabla para tokens de recuperación de contraseña con restricciones completas
    CREATE TABLE IF NOT EXISTS tokens_recuperacion (
        id SERIAL PRIMARY KEY,
        usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
        token VARCHAR(64) NOT NULL UNIQUE,
        expira_en TIMESTAMP NOT NULL,
        usado BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE INDEX IF NOT EXISTS idx_tokens_lookup ON tokens_recuperacion(token, usado, expira_en);
    ";

    $db->exec($sql);
    echo "<h2 style='color: green;'>Refactorización de base de datos completada con éxito.</h2>";
} catch (\Throwable $e) {
    echo "<h2 style='color: red;'>Error en la migración:</h2> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
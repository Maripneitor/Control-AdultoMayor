<?php
require_once 'config/config.php';
require_once 'config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    $sql = "CREATE TABLE IF NOT EXISTS tokens_recuperacion (
        id SERIAL PRIMARY KEY,
        usuario_id INT REFERENCES usuarios(id) ON DELETE CASCADE,
        token VARCHAR(64) NOT NULL,
        expira_en TIMESTAMP NOT NULL,
        usado BOOLEAN DEFAULT FALSE
    );";
    $db->exec($sql);
    echo "Refactorizacion completada con exito.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

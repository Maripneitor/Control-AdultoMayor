<?php
declare(strict_types=1);

require_once __DIR__ . '/config/Database.php';

use Config\Database;

$db = Database::getInstance()->getConnection();

$sql = "
-- 1. Tablero Salud Diario
CREATE TABLE IF NOT EXISTS registro_salud_diario (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL,
    presion_sistolica INT NULL,
    presion_diastolica INT NULL,
    glucosa DECIMAL(5,2) NULL,
    peso DECIMAL(5,2) NULL,
    estado_animo VARCHAR(50) DEFAULT 'bien' CHECK (estado_animo IN ('muy_bien', 'bien', 'regular', 'mal', 'con_dolor')),
    notas TEXT NULL,
    registrado_por INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
);

-- 2. Pistas visuales en medicamentos
-- Agregar columnas si no existen
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS color_distintivo VARCHAR(20) DEFAULT 'Blanco';
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS forma VARCHAR(20) DEFAULT 'tableta' CHECK (forma IN ('tableta', 'capsula', 'jarabe', 'gotas', 'inyeccion'));
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS momento_dia VARCHAR(50) NULL;

-- 3. Tabla Cuidadores (Modo Familiar)
CREATE TABLE IF NOT EXISTS paciente_cuidador (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL,
    usuario_cuidador_id INT NOT NULL,
    parentesco VARCHAR(50) NOT NULL,
    nivel_acceso VARCHAR(50) DEFAULT 'tomas_y_citas' CHECK (nivel_acceso IN ('lectura', 'tomas_y_citas', 'administrador')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_cuidador_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
";

try {
    $db->exec($sql);
    echo "Migración Fase 5 completada exitosamente.\n";
} catch (PDOException $e) {
    echo "Error en la migración: " . $e->getMessage() . "\n";
}

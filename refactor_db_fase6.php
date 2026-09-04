<?php
declare(strict_types=1);

require_once __DIR__ . '/config/Database.php';

use Config\Database;

$db = Database::getInstance()->getConnection();

$sql = "
-- 1. Ampliar registro_salud_diario
ALTER TABLE registro_salud_diario ADD COLUMN IF NOT EXISTS momento_glucosa VARCHAR(20) NULL;
ALTER TABLE registro_salud_diario ADD COLUMN IF NOT EXISTS ritmo_cardiaco SMALLINT NULL;
ALTER TABLE registro_salud_diario ADD COLUMN IF NOT EXISTS horas_sueno DECIMAL(3,1) NULL;
ALTER TABLE registro_salud_diario ADD COLUMN IF NOT EXISTS nivel_dolor SMALLINT NULL;
ALTER TABLE registro_salud_diario ADD COLUMN IF NOT EXISTS alerta_emitida BOOLEAN DEFAULT FALSE;
ALTER TABLE registro_salud_diario ADD COLUMN IF NOT EXISTS confirmado_por_usuario BOOLEAN DEFAULT FALSE;

-- 2. Ampliar tabla medicamentos
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS stock_actual INT DEFAULT 0;
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS stock_minimo_alerta INT DEFAULT 5;
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS dosis_por_toma INT DEFAULT 1;

-- 3. Crear tomas_historial
CREATE TABLE IF NOT EXISTS tomas_historial (
    id SERIAL PRIMARY KEY,
    medicamento_id INT NOT NULL,
    paciente_id INT NOT NULL,
    fecha_programada DATE NOT NULL,
    momento_dia VARCHAR(20) NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    tomado_at TIMESTAMP NULL,
    FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE CASCADE,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    UNIQUE (medicamento_id, fecha_programada, momento_dia)
);

-- 4. Crear codigos_vinculacion
CREATE TABLE IF NOT EXISTS codigos_vinculacion (
    codigo VARCHAR(6) PRIMARY KEY,
    paciente_id INT NOT NULL,
    expira_at TIMESTAMP NOT NULL,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE
);

-- 5. Añadir token_sos a pacientes si no existe
ALTER TABLE pacientes ADD COLUMN IF NOT EXISTS token_sos VARCHAR(64) UNIQUE;
";

try {
    $db->exec($sql);
    echo "Migración Fase 6 completada exitosamente.\n";
} catch (PDOException $e) {
    echo "Error en la migración: " . $e->getMessage() . "\n";
}

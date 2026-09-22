-- ==============================================================================
-- Migración Incremental: Roles Clínicos, Códigos Mnemotécnicos y Portal Médico
-- Archivo: 2026_09_22_migracion_roles_portal_medico.sql
-- DBMS: PostgreSQL 13+
-- ==============================================================================

-- 1. Habilitar extensión pgcrypto si no estuviera activa
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- 2. Convertir columnas enum a VARCHAR(50) para máxima flexibilidad
ALTER TABLE usuarios ALTER COLUMN rol TYPE VARCHAR(50);
ALTER TABLE usuarios DROP CONSTRAINT IF EXISTS usuarios_rol_check;
ALTER TABLE usuarios ADD CONSTRAINT usuarios_rol_check 
CHECK (rol IN ('administrador', 'familiar_admin', 'cuidador_auxiliar', 'paciente', 'medico', 'cuidador', 'adulto_mayor', 'familiar', 'emergencia'));

-- Normalizar roles legacy si existen en la BD
UPDATE usuarios SET rol = 'familiar_admin' WHERE rol IN ('cuidador', 'familiar');
UPDATE usuarios SET rol = 'paciente' WHERE rol = 'adulto_mayor';

-- 3. Actualización de niveles de acceso en tabla paciente_cuidador
ALTER TABLE paciente_cuidador ALTER COLUMN nivel_acceso TYPE VARCHAR(50);
ALTER TABLE paciente_cuidador DROP CONSTRAINT IF EXISTS paciente_cuidador_nivel_acceso_check;
ALTER TABLE paciente_cuidador ADD CONSTRAINT paciente_cuidador_nivel_acceso_check 
CHECK (nivel_acceso IN ('lectura', 'tomas_y_signos', 'familiar_admin', 'admin', 'tomas_y_citas', 'edicion_completa'));

-- Normalizar niveles legacy
UPDATE paciente_cuidador SET nivel_acceso = 'familiar_admin' WHERE nivel_acceso IN ('edicion_completa');
UPDATE paciente_cuidador SET nivel_acceso = 'tomas_y_signos' WHERE nivel_acceso IN ('tomas_y_citas');

-- 4. Tabla de Tokens y PINs de Acceso Médico Temporal
CREATE TABLE IF NOT EXISTS tokens_acceso_medico (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    generado_por_usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    pin_seguridad VARCHAR(8) NOT NULL,
    token_url VARCHAR(64) UNIQUE NOT NULL,
    medico_nombre VARCHAR(150),
    hospital_institucion VARCHAR(150),
    duracion_horas INT DEFAULT 24,
    expira_at TIMESTAMP WITH TIME ZONE NOT NULL,
    max_usos INT DEFAULT 20,
    usos_actuales INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    ultimo_acceso_at TIMESTAMP WITH TIME ZONE,
    ip_ultimo_acceso VARCHAR(45),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_tokens_medico_pin ON tokens_acceso_medico(pin_seguridad, activo, expira_at);
CREATE INDEX IF NOT EXISTS idx_tokens_medico_token ON tokens_acceso_medico(token_url, activo, expira_at);
CREATE INDEX IF NOT EXISTS idx_tokens_medico_paciente ON tokens_acceso_medico(paciente_id, expira_at DESC);

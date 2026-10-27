-- ==============================================================================
-- Migración: Ecosistema Médico, Doctores, Recetas y Trazabilidad de Tomas
-- ==============================================================================

-- 1. Ampliar roles de usuarios para aceptar 'medico' y 'doctor'
ALTER TABLE usuarios DROP CONSTRAINT IF EXISTS usuarios_rol_check;
ALTER TABLE usuarios ADD CONSTRAINT usuarios_rol_check CHECK (rol IN ('administrador', 'familiar_admin', 'cuidador_auxiliar', 'paciente', 'medico', 'doctor', 'cuidador', 'adulto_mayor', 'familiar', 'emergencia'));

-- 2. Extensión de pacientes
ALTER TABLE pacientes ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(255) NULL;
ALTER TABLE pacientes ADD COLUMN IF NOT EXISTS token_emergencia_qr VARCHAR(64) NULL;
ALTER TABLE pacientes ADD COLUMN IF NOT EXISTS codigo_expira_en TIMESTAMP WITH TIME ZONE NULL;
ALTER TABLE pacientes ADD COLUMN IF NOT EXISTS codigo_activo BOOLEAN DEFAULT TRUE;

-- Sincronizar token_emergencia_qr con qr_token si no está definido
UPDATE pacientes SET token_emergencia_qr = CAST(qr_token AS VARCHAR) WHERE token_emergencia_qr IS NULL AND qr_token IS NOT NULL;

-- 3. Extensión de paciente_cuidador
ALTER TABLE paciente_cuidador ADD COLUMN IF NOT EXISTS nivel_acceso VARCHAR(50) DEFAULT 'completo';
ALTER TABLE paciente_cuidador ADD COLUMN IF NOT EXISTS parentesco VARCHAR(50) DEFAULT 'Familiar';

-- 4. Extensión de doctores para soporte de usuario_id y datos de consultorio
ALTER TABLE doctores ADD COLUMN IF NOT EXISTS usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL;
ALTER TABLE doctores ADD COLUMN IF NOT EXISTS cedula_profesional VARCHAR(50) NULL;
ALTER TABLE doctores ADD COLUMN IF NOT EXISTS telefono_consultorio VARCHAR(25) NULL;
ALTER TABLE doctores ADD COLUMN IF NOT EXISTS direccion_consultorio TEXT NULL;
ALTER TABLE doctores ALTER COLUMN paciente_id DROP NOT NULL;

-- 5. Tabla relacional paciente_doctor
CREATE TABLE IF NOT EXISTS paciente_doctor (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    doctor_id INT NOT NULL REFERENCES doctores(id) ON DELETE CASCADE,
    fecha_vinculacion TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'activo',
    notas_medicas TEXT NULL,
    CONSTRAINT uniq_paciente_doctor UNIQUE (paciente_id, doctor_id)
);

CREATE INDEX IF NOT EXISTS idx_paciente_doctor_p ON paciente_doctor(paciente_id);
CREATE INDEX IF NOT EXISTS idx_paciente_doctor_d ON paciente_doctor(doctor_id);

-- 6. Tabla recetas_medicas
CREATE TABLE IF NOT EXISTS recetas_medicas (
    id SERIAL PRIMARY KEY,
    doctor_id INT REFERENCES doctores(id) ON DELETE SET NULL,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    diagnostico TEXT,
    indicaciones_generales TEXT,
    fecha_emision DATE DEFAULT CURRENT_DATE,
    archivo_receta_url VARCHAR(255) NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_recetas_paciente ON recetas_medicas(paciente_id);
CREATE INDEX IF NOT EXISTS idx_recetas_doctor ON recetas_medicas(doctor_id);

-- 7. Trazabilidad de medicamentos y tomas
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS stock_actual INT DEFAULT 0;
ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS dosis_restantes INT DEFAULT 0;

ALTER TABLE registro_tomas ADD COLUMN IF NOT EXISTS paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE;
ALTER TABLE registro_tomas ADD COLUMN IF NOT EXISTS usuario_registro_id INT REFERENCES usuarios(id) ON DELETE SET NULL;
ALTER TABLE registro_tomas ADD COLUMN IF NOT EXISTS fecha_hora_programada TIMESTAMP WITH TIME ZONE;
ALTER TABLE registro_tomas ADD COLUMN IF NOT EXISTS fecha_hora_tomada TIMESTAMP WITH TIME ZONE;
ALTER TABLE registro_tomas ADD COLUMN IF NOT EXISTS observaciones VARCHAR(255);

-- 8. Tabla notificaciones_alertas
CREATE TABLE IF NOT EXISTS notificaciones_alertas (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    tipo VARCHAR(50) NOT NULL,
    mensaje TEXT NOT NULL,
    destinatario_telefono VARCHAR(20),
    estado_envio VARCHAR(20) DEFAULT 'pendiente',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_notificaciones_paciente ON notificaciones_alertas(paciente_id, created_at DESC);

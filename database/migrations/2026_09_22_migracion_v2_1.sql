-- ==============================================================================
-- Migración Incremental VitalSenior v2.1
-- Fecha: 2026-09-22
-- DBMS: PostgreSQL 13+
-- Descripción:
--   1. Tabla inmutable logs_auditoria con UUID PK e índices de fecha UTC.
--   2. Tabla rate_limits para protección anti-fuerza bruta y anti-scraping.
--   3. Columnas de inventario en tabla medicamentos (stock_actual, dosis_por_toma, stock_minimo_alerta).
--   4. Columnas clínicas y QR en tabla pacientes.
--   5. Índices de alta velocidad para getOmitidasHoy() y getBajoInventario().
-- ==============================================================================

-- 1. Habilitar extensión pgcrypto si no existe
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- 2. Tabla de Auditoría Clínica Inmutable
CREATE TABLE IF NOT EXISTS logs_auditoria (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    accion VARCHAR(100) NOT NULL,
    detalles TEXT,
    ip_origen VARCHAR(45),
    user_agent VARCHAR(255),
    fecha_utc TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_logs_auditoria_paciente ON logs_auditoria(paciente_id, fecha_utc DESC);
CREATE INDEX IF NOT EXISTS idx_logs_auditoria_usuario ON logs_auditoria(usuario_id, fecha_utc DESC);
CREATE INDEX IF NOT EXISTS idx_logs_auditoria_accion ON logs_auditoria(accion, fecha_utc DESC);

-- 3. Tabla de Rate Limiting
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

-- 4. Actualización de Columnas en Tabla Pacientes
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'pacientes' AND column_name = 'nombre') THEN
        ALTER TABLE pacientes ADD COLUMN nombre VARCHAR(100) DEFAULT 'Por completar';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'pacientes' AND column_name = 'apellidos') THEN
        ALTER TABLE pacientes ADD COLUMN apellidos VARCHAR(100) DEFAULT 'Por completar';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'pacientes' AND column_name = 'alergias') THEN
        ALTER TABLE pacientes ADD COLUMN alergias TEXT;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'pacientes' AND column_name = 'enfermedades_cronicas') THEN
        ALTER TABLE pacientes ADD COLUMN enfermedades_cronicas TEXT;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'pacientes' AND column_name = 'notas_cognitivas') THEN
        ALTER TABLE pacientes ADD COLUMN notas_cognitivas TEXT;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'pacientes' AND column_name = 'qr_token') THEN
        ALTER TABLE pacientes ADD COLUMN qr_token UUID UNIQUE DEFAULT gen_random_uuid();
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'pacientes' AND column_name = 'codigo_vinculacion') THEN
        ALTER TABLE pacientes ADD COLUMN codigo_vinculacion VARCHAR(10) UNIQUE;
    END IF;
END $$;

-- 5. Actualización de Columnas de Inventario en Tabla Medicamentos
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'medicamentos' AND column_name = 'stock_actual') THEN
        ALTER TABLE medicamentos ADD COLUMN stock_actual INT DEFAULT 0;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'medicamentos' AND column_name = 'dosis_por_toma') THEN
        ALTER TABLE medicamentos ADD COLUMN dosis_por_toma INT DEFAULT 1;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name = 'medicamentos' AND column_name = 'stock_minimo_alerta') THEN
        ALTER TABLE medicamentos ADD COLUMN stock_minimo_alerta INT DEFAULT 5;
    END IF;
END $$;

-- 6. Índices de Optimización de Consultas Críticas
-- Aceleración getOmitidasHoy()
CREATE INDEX IF NOT EXISTS idx_tomas_historial_omitidas 
ON tomas_historial(paciente_id, fecha_programada, estado, hora_programada);

-- Aceleración getBajoInventario()
CREATE INDEX IF NOT EXISTS idx_medicamentos_inventario 
ON medicamentos(paciente_id, activo, stock_actual, stock_minimo_alerta);

-- Aceleración getTodayDoses()
CREATE INDEX IF NOT EXISTS idx_tomas_historial_hoy 
ON tomas_historial(paciente_id, fecha_programada, hora_programada ASC);

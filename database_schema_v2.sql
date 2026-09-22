-- ==============================================================================
-- VitalSenior Database Schema V2 (Producción PostgreSQL)
-- Características: UUIDs nativos, Auditoría Clínica UTC, Rate Limiting, Control de Inventario
-- DBMS: PostgreSQL 13+
-- ==============================================================================

-- Habilitar extensión pgcrypto para UUIDs
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ==========================================
-- 1. Usuarios y Control de Acceso
-- ==========================================
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL DEFAULT 'cuidador' CHECK (rol IN ('administrador', 'cuidador', 'emergencia', 'adulto_mayor')),
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. Pacientes (Adultos Mayores) & Ficha QR
-- ==========================================
CREATE TABLE IF NOT EXISTS pacientes (
    id SERIAL PRIMARY KEY,
    usuario_id INT UNIQUE REFERENCES usuarios(id) ON DELETE CASCADE,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    genero VARCHAR(20),
    tipo_sangre VARCHAR(5) DEFAULT 'N/D',
    alergias TEXT,
    enfermedades_cronicas TEXT,
    notas_cognitivas TEXT,
    qr_token UUID UNIQUE DEFAULT gen_random_uuid(),
    codigo_vinculacion VARCHAR(10) UNIQUE,
    zona_horaria VARCHAR(50) DEFAULT 'America/Mexico_City',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 3. Vínculos Paciente - Cuidador / Familiar
-- ==========================================
CREATE TABLE IF NOT EXISTS paciente_cuidador (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    usuario_cuidador_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    parentesco VARCHAR(50) DEFAULT 'Familiar',
    nivel_acceso VARCHAR(50) DEFAULT 'edicion_completa' CHECK (nivel_acceso IN ('lectura', 'tomas_y_citas', 'edicion_completa', 'admin')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_paciente_cuidador UNIQUE (paciente_id, usuario_cuidador_id)
);

-- ==========================================
-- 4. Directorio Médico y Contactos SOS
-- ==========================================
CREATE TABLE IF NOT EXISTS contactos_emergencia (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre VARCHAR(150) NOT NULL,
    parentesco VARCHAR(50) DEFAULT 'Familiar',
    telefono VARCHAR(25) NOT NULL,
    email VARCHAR(255),
    es_prioritario BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS doctores (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre VARCHAR(150) NOT NULL,
    especialidad VARCHAR(100),
    telefono_contacto VARCHAR(25),
    hospital_clinica VARCHAR(150),
    horario_atencion VARCHAR(100),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 5. Medicamentos & Control de Inventario
-- ==========================================
CREATE TABLE IF NOT EXISTS medicamentos (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre_comercial VARCHAR(255) NOT NULL,
    dosis VARCHAR(100) NOT NULL,
    frecuencia_horas INT NOT NULL DEFAULT 8,
    instrucciones TEXT,
    stock_actual INT DEFAULT 0,
    dosis_por_toma INT DEFAULT 1,
    stock_minimo_alerta INT DEFAULT 5,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tomas_historial (
    id SERIAL PRIMARY KEY,
    medicamento_id INT NOT NULL REFERENCES medicamentos(id) ON DELETE CASCADE,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    fecha_programada DATE NOT NULL,
    hora_programada TIME NOT NULL,
    fecha_programada_utc TIMESTAMP WITH TIME ZONE,
    pospuesto_hasta TIMESTAMP WITH TIME ZONE,
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'tomada', 'omitido', 'pospuesta', 'deshecha')),
    tomado_at TIMESTAMP WITH TIME ZONE,
    deshecho_at TIMESTAMP WITH TIME ZONE,
    recordatorio_enviado BOOLEAN DEFAULT FALSE,
    nota_incidencia TEXT,
    notificacion_enviada_at TIMESTAMP WITH TIME ZONE,
    registrado_por_usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 6. Registro de Salud Diario & Citas
-- ==========================================
CREATE TABLE IF NOT EXISTS registro_salud_diario (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    presion_sistolica INT,
    presion_diastolica INT,
    ritmo_cardiaco INT,
    glucosa DECIMAL(5,2),
    momento_glucosa VARCHAR(50) DEFAULT 'ayunas',
    horas_sueno DECIMAL(4,2),
    nivel_dolor INT CHECK (nivel_dolor BETWEEN 0 AND 10),
    peso DECIMAL(5,2),
    altura DECIMAL(5,2),
    imc DECIMAL(4,2),
    alerta_emitida BOOLEAN DEFAULT FALSE,
    confirmado_por_usuario BOOLEAN DEFAULT FALSE,
    registrado_por_usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
    notas TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS citas_laboratorios (
    id SERIAL PRIMARY KEY,
    paciente_id INT NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    doctor_id INT REFERENCES doctores(id) ON DELETE SET NULL,
    tipo_evento VARCHAR(50) DEFAULT 'cita_medica',
    titulo VARCHAR(255) NOT NULL,
    lugar VARCHAR(255),
    fecha_hora TIMESTAMP WITH TIME ZONE NOT NULL,
    notas TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 7. Seguridad: Rate Limiting & Auditoría Clínica
-- ==========================================
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


-- ==============================================================================
-- Vitalsenior Database Schema V2 (Módulos 1-12 Integrados)
-- Características añadidas: UUIDs nativos, Auditoría UTC, Manejo OTP, y Archivos Médicos
-- DBMS: PostgreSQL 13+
-- ==============================================================================

-- Habilitar extensión pgcrypto para UUIDs
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ==========================================
-- 1. Usuarios, Autenticación y OTP
-- ==========================================
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    telefono VARCHAR(20) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL CHECK (rol IN ('adulto_mayor', 'familiar', 'admin')),
    acepta_whatsapp BOOLEAN DEFAULT FALSE,
    telefono_verificado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE otp_verifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    usuario_id INT REFERENCES usuarios(id) ON DELETE CASCADE,
    codigo_otp VARCHAR(6) NOT NULL,
    proposito VARCHAR(50) NOT NULL CHECK (proposito IN ('recuperacion_password', 'validacion_whatsapp')),
    metodo VARCHAR(20) NOT NULL CHECK (metodo IN ('sms', 'whatsapp', 'email')),
    expira_en TIMESTAMP WITH TIME ZONE NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. Pacientes (Adultos Mayores) con UUID Público
-- ==========================================
CREATE TABLE pacientes (
    id SERIAL PRIMARY KEY,
    usuario_id INT UNIQUE REFERENCES usuarios(id) ON DELETE CASCADE,
    -- Identificador público utilizado para generar la URL del QR de Emergencia
    uuid_publico UUID UNIQUE DEFAULT gen_random_uuid(),
    fecha_nacimiento DATE,
    genero VARCHAR(20),
    tipo_sangre VARCHAR(5) DEFAULT 'N/D',
    peso DECIMAL(5,2),
    altura DECIMAL(5,2),
    codigo_vinculacion VARCHAR(10) UNIQUE,
    -- Configuración Zona Horaria del Paciente
    zona_horaria VARCHAR(50) DEFAULT 'UTC',
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 3. Vinculaciones Familiares (Cuentas Administradas vs Independientes)
-- ==========================================
CREATE TABLE vinculaciones (
    familiar_id INT REFERENCES usuarios(id) ON DELETE CASCADE,
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    es_administrador_principal BOOLEAN DEFAULT FALSE,
    -- Indica si la cuenta es totalmente administrada por el familiar o si tiene credenciales independientes
    tipo_gestion VARCHAR(20) DEFAULT 'compartido' CHECK (tipo_gestion IN ('compartido', 'administrado')),
    vinculado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (familiar_id, paciente_id)
);

-- ==========================================
-- 4. Expediente Médico (Archivos y Diagnósticos)
-- ==========================================
CREATE TABLE padecimientos (
    id SERIAL PRIMARY KEY,
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) CHECK (tipo IN ('alergia', 'enfermedad', 'cirugia', 'dispositivo_medico')),
    severidad VARCHAR(20) DEFAULT 'moderada',
    notas TEXT,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE doctores (
    id SERIAL PRIMARY KEY,
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre_completo VARCHAR(255) NOT NULL,
    especialidad VARCHAR(150),
    telefono VARCHAR(20),
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para almacenar PDFs o Imágenes seguras (Archivos Médicos)
CREATE TABLE archivos_medicos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_hash VARCHAR(255) NOT NULL, -- Nombre ofuscado en el bucket
    mime_type VARCHAR(100) NOT NULL,
    size_bytes INT NOT NULL,
    tipo_documento VARCHAR(50) CHECK (tipo_documento IN ('receta', 'estudio_laboratorio', 'imagen_medica', 'otro')),
    subido_por_usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 5. Módulo de Medicamentos (Fechas en UTC estrictas)
-- ==========================================
CREATE TABLE medicamentos (
    id SERIAL PRIMARY KEY,
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre_comercial VARCHAR(255) NOT NULL,
    dosis VARCHAR(100) NOT NULL,
    frecuencia_horas INT NOT NULL,
    instrucciones TEXT,
    fecha_inicio DATE,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT TRUE,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Historial de tomas con ventana para "Deshacer"
CREATE TABLE tomas_historial (
    id SERIAL PRIMARY KEY,
    medicamento_id INT REFERENCES medicamentos(id) ON DELETE CASCADE,
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    fecha_programada DATE NOT NULL, 
    hora_programada TIME NOT NULL,  
    momento_dia VARCHAR(20),
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'tomada', 'omitido', 'pospuesto')),
    
    -- Tiempos UTC para cálculos rigurosos
    tomado_at TIMESTAMP WITH TIME ZONE,
    
    nota_incidencia TEXT,
    registrado_por_usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 6. Citas y Eventos Médicos
-- ==========================================
CREATE TABLE citas (
    id SERIAL PRIMARY KEY,
    paciente_id INT REFERENCES pacientes(id) ON DELETE CASCADE,
    doctor_id INT REFERENCES doctores(id) ON DELETE SET NULL,
    tipo_evento VARCHAR(50) DEFAULT 'cita_medica',
    titulo VARCHAR(255) NOT NULL,
    lugar VARCHAR(255) NOT NULL,
    
    -- Fecha absoluta guardada en UTC (Calculada desde el frontend)
    fecha_hora_utc TIMESTAMP WITH TIME ZONE NOT NULL,
    
    notas TEXT,
    estado VARCHAR(20) DEFAULT 'programada' CHECK (estado IN ('programada', 'completada', 'cancelada')),
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 7. Auditoría General (Requisito Seguridad y Logueo)
-- ==========================================
CREATE TABLE logs_auditoria (
    id SERIAL PRIMARY KEY,
    usuario_id INT REFERENCES usuarios(id) ON DELETE SET NULL,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    direccion_ip VARCHAR(45),
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

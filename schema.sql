-- ==============================================================================
-- VitalSenior - Esquema de Base de Datos PostgreSQL
-- Arquitectura: PHP Puro (MVC) + PostgreSQL mediante PDO
-- ==============================================================================

-- Habilitar extensión para UUIDs v4
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ==============================================================================
-- 1. Tabla de Usuarios (Autenticación, Roles y Soft Delete)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    telefono VARCHAR(25) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(30) NOT NULL CHECK (rol IN ('familiar', 'adulto_mayor', 'admin')),
    acepta_whatsapp BOOLEAN DEFAULT FALSE,
    whatsapp_verificado BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    eliminado_en TIMESTAMP WITH TIME ZONE DEFAULT NULL, -- Soft Delete para integridad referencial
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- 2. Tabla de Verificaciones OTP (WhatsApp / SMS)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS otp_verificaciones (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    usuario_id UUID REFERENCES usuarios(id) ON DELETE CASCADE,
    telefono VARCHAR(25) NOT NULL,
    codigo_otp VARCHAR(6) NOT NULL,
    proposito VARCHAR(50) NOT NULL CHECK (proposito IN ('recuperacion_password', 'validacion_whatsapp')),
    canal VARCHAR(20) NOT NULL DEFAULT 'whatsapp' CHECK (canal IN ('whatsapp', 'sms', 'email')),
    expira_en TIMESTAMP WITH TIME ZONE NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    intentos INT DEFAULT 0,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- 3. Tabla de Pacientes (Adultos Mayores y Ficha Pública Segura)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS pacientes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    -- Si el paciente tiene cuenta propia (Adulto Mayor independiente)
    usuario_id UUID UNIQUE REFERENCES usuarios(id) ON DELETE SET NULL,
    -- Identificador público UUIDv4 para URL del QR de Emergencia (Vista 6)
    uuid_publico UUID UNIQUE NOT NULL DEFAULT gen_random_uuid(),
    nombre_completo VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE,
    genero VARCHAR(20),
    tipo_sangre VARCHAR(10) DEFAULT 'N/D',
    peso DECIMAL(5,2),
    altura DECIMAL(5,2),
    codigo_vinculacion VARCHAR(12) UNIQUE,
    zona_horaria VARCHAR(60) DEFAULT 'UTC',
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- 4. Vinculación Familiar - Paciente (Cuentas Administradas e Independientes)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS vinculaciones (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    familiar_id UUID NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    es_tutor_principal BOOLEAN DEFAULT TRUE,
    permiso_edicion BOOLEAN DEFAULT TRUE,
    -- nivel_acceso: 'edicion_completa' (por defecto en perfil administrado) o 'lectura' (si el adulto mayor revoca permisos)
    nivel_acceso VARCHAR(20) DEFAULT 'edicion_completa' CHECK (nivel_acceso IN ('lectura', 'edicion_completa')),
    -- 'administrado': perfil gestionado exclusivamente por el familiar
    -- 'independiente_vinculado': promovido a credenciales propias manteniendo al familiar tutor
    tipo_gestion VARCHAR(30) DEFAULT 'administrado' CHECK (tipo_gestion IN ('administrado', 'compartido', 'independiente_vinculado')),
    vinculado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT uq_familiar_paciente UNIQUE (familiar_id, paciente_id)
);

-- ==============================================================================
-- 5. Expediente Médico: Padecimientos, Alergias y Contactos Médicos
-- ==============================================================================
CREATE TABLE IF NOT EXISTS padecimientos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) NOT NULL CHECK (tipo IN ('alergia', 'enfermedad_cronica', 'cirugia', 'dispositivo_medico', 'otro')),
    severidad VARCHAR(30) DEFAULT 'moderada',
    notas TEXT,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS doctores (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre_completo VARCHAR(255) NOT NULL,
    especialidad VARCHAR(150),
    telefono VARCHAR(25),
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- 6. Archivos Médicos y Estudios (Subida Segura multipart/form-data)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS archivos_medicos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_archivo_almacenado VARCHAR(255) NOT NULL, -- Nombre aleatorio protegido
    ruta_almacenamiento TEXT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    tamano_bytes BIGINT NOT NULL,
    tipo_documento VARCHAR(50) NOT NULL CHECK (tipo_documento IN ('receta', 'estudio_laboratorio', 'imagen_medica', 'otro')),
    subido_por_usuario_id UUID REFERENCES usuarios(id) ON DELETE SET NULL,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- 7. Medicamentos y Tomas (Soporte Estricto para Deshacer y Recordatorios Cron)
-- ==============================================================================
CREATE TABLE IF NOT EXISTS medicamentos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    nombre_comercial VARCHAR(255) NOT NULL,
    dosis VARCHAR(100) NOT NULL,
    frecuencia_horas INT NOT NULL,
    stock_actual INT DEFAULT 30,
    dosis_por_toma INT DEFAULT 1,
    instrucciones TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT TRUE,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tomas_historial (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    medicamento_id UUID NOT NULL REFERENCES medicamentos(id) ON DELETE CASCADE,
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    fecha_programada_utc TIMESTAMP WITH TIME ZONE NOT NULL,
    -- Estados soportados
    estado VARCHAR(20) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'tomada', 'deshecha', 'omitida', 'pospuesta')),
    tomado_at TIMESTAMP WITH TIME ZONE, -- UTC estricto
    deshecho_at TIMESTAMP WITH TIME ZONE, -- UTC estricto
    pospuesto_hasta TIMESTAMP WITH TIME ZONE, -- UTC estricto
    recordatorio_enviado BOOLEAN DEFAULT FALSE,
    notificado_at TIMESTAMP WITH TIME ZONE,
    registrado_por_usuario_id UUID REFERENCES usuarios(id) ON DELETE SET NULL,
    nota_incidencia TEXT,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- 8. Citas y Agenda Médica
-- ==============================================================================
CREATE TABLE IF NOT EXISTS citas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE,
    doctor_id UUID REFERENCES doctores(id) ON DELETE SET NULL,
    titulo VARCHAR(255) NOT NULL,
    lugar VARCHAR(255) NOT NULL,
    fecha_hora_utc TIMESTAMP WITH TIME ZONE NOT NULL,
    notas TEXT,
    estado VARCHAR(20) DEFAULT 'programada' CHECK (estado IN ('programada', 'completada', 'cancelada')),
    recordatorio_enviado BOOLEAN DEFAULT FALSE,
    notificado_at TIMESTAMP WITH TIME ZONE,
    creado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- ==============================================================================
-- 9. Logs de Auditoría y Eventos SOS
-- ==============================================================================
CREATE TABLE IF NOT EXISTS logs_auditoria (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    usuario_id UUID NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE, -- Quién ejecutó la acción
    paciente_id UUID NOT NULL REFERENCES pacientes(id) ON DELETE CASCADE, -- Sobre quién se ejecutó
    accion VARCHAR(100) NOT NULL, -- Ej: 'ELIMINAR_MEDICAMENTO', 'REGISTRAR_TOMA', 'DESHACER_TOMA'
    detalles TEXT, -- Información clínica o payload del cambio
    ip_origen VARCHAR(45),
    user_agent TEXT,
    fecha_utc TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL
);

-- Índices de alto rendimiento
CREATE INDEX IF NOT EXISTS idx_pacientes_uuid_publico ON pacientes(uuid_publico);
CREATE INDEX IF NOT EXISTS idx_otp_telefono_valido ON otp_verificaciones(telefono, codigo_otp, expira_en, usado);
CREATE INDEX IF NOT EXISTS idx_tomas_medicamento_fecha ON tomas_historial(medicamento_id, fecha_programada_utc);
CREATE INDEX IF NOT EXISTS idx_tomas_cron_pendientes ON tomas_historial(estado, fecha_programada_utc, recordatorio_enviado);
CREATE INDEX IF NOT EXISTS idx_citas_cron_pendientes ON citas(estado, fecha_hora_utc, recordatorio_enviado);
CREATE INDEX IF NOT EXISTS idx_archivos_paciente ON archivos_medicos(paciente_id);

-- ==============================================================================
-- VitalSenior - Base de Datos Completa (Estructura DDL + Datos / Inserts)
-- ==============================================================================
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- Sistema: Plataforma de Monitoreo y Cuidado del Adulto Mayor (VitalSenior)
-- Base de Datos: bd-vitalsenior
-- Motor DBMS: PostgreSQL 13+ (Validado en PostgreSQL 18)
-- Fecha de Generación: 2026-10-13
-- Formato: Sentencias SQL estándar (DROP IF EXISTS, CREATE TABLE, INSERT INTO explícito, Secuencias, Índices y Llaves Foráneas)
-- Compatibilidad: psql CLI, pgAdmin 4, DBeaver, DataGrip, PHP PDO, Adminer.
--
-- ==============================================================================
-- INSTRUCCIONES DE RESTAURACIÓN / IMPORTACIÓN:
-- ==============================================================================
--
-- Opción 1: Desde Terminal / Consola de Comandos (psql):
--    psql -h localhost -p 5433 -U postgres -d "bd-vitalsenior" -f bd-vitalsenior.sql
--
--    (Si la base de datos no existe previamente, crearla primero con:)
--    CREATE DATABASE "bd-vitalsenior" WITH ENCODING 'UTF8';
--
-- Opción 2: Desde pgAdmin 4 / DBeaver:
--    1. Conectarse al servidor PostgreSQL en el puerto 5433.
--    2. Abrir la base de datos 'bd-vitalsenior' (o crearla si es una instalación limpia).
--    3. Abrir este archivo en la herramienta de consulta (Query Tool).
--    4. Ejecutar todo el script (F5 o botón Execute Script).
--
-- ==============================================================================
-- RESUMEN DE TABLAS INCLUIDAS (20 Tablas / Vistas del Ecosistema):
-- ==============================================================================
--  1. usuarios               : Autenticación, roles (admin, familiar_admin, paciente, doctor, etc.), hashes bcrypt.
--  2. pacientes              : Adultos mayores, perfiles clínicos, datos QR, vínculos SOS.
--  3. paciente_cuidador      : Relación M:N cuidadores/familiares con niveles de acceso.
--  4. doctores               : Directorio médico, especialidades, cédulas y consultorios.
--  5. paciente_doctor        : Vinculación activa y notas clínicas entre médico y paciente.
--  6. contactos_emergencia   : Agenda prioritaria SOS para llamadas inmediatas.
--  7. codigos_vinculacion    : Códigos temporales mnemotécnicos de vinculación.
--  8. medicamentos           : Catálogo farmacológico, stock, dosis por toma, alertas.
--  9. tomas_historial        : Trazabilidad horaria de tomas (pendiente, tomado, omitido, pospuesto).
-- 10. registro_tomas         : Bitácora complementaria de administración de fármacos.
-- 11. recetas_medicas        : Registro y archivo digital de prescripciones médicas.
-- 12. citas_laboratorios     : Agenda médica, consultas, estudios y preparaciones previas.
-- 13. visitas_medicas        : Bitácora de consultas clínicas con UUIDs nativos.
-- 14. expediente_archivos    : Almacén de documentos médicos, recetas y análisis (UUIDs).
-- 15. archivos_medicos       : Vista de compatibilidad para acceso al expediente.
-- 16. registro_salud_diario  : Monitoreo de signos vitales (presión, glucosa, ritmo, peso, IMC).
-- 17. tokens_acceso_medico   : PINs y URLs efímeras con caducidad para el Portal Médico.
-- 18. tokens_recuperacion    : Recuperación segura de contraseña para usuarios.
-- 19. notificaciones_alertas : Registro de alertas emitidas (SMS/WhatsApp/Push).
-- 20. rate_limits            : Protección de seguridad contra fuerza bruta y scrapers.
-- 21. logs_auditoria         : Pista inmutable de auditoría clínica con fecha UTC e IP.
-- ==============================================================================

--
-- PostgreSQL database dump
--


-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.visitas_medicas DROP CONSTRAINT IF EXISTS visitas_medicas_usuario_registra_id_fkey;
ALTER TABLE IF EXISTS ONLY public.visitas_medicas DROP CONSTRAINT IF EXISTS visitas_medicas_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.tomas_historial DROP CONSTRAINT IF EXISTS tomas_historial_registrado_por_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.tokens_recuperacion DROP CONSTRAINT IF EXISTS tokens_recuperacion_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.tokens_acceso_medico DROP CONSTRAINT IF EXISTS tokens_acceso_medico_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.tokens_acceso_medico DROP CONSTRAINT IF EXISTS tokens_acceso_medico_generado_por_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.registro_tomas DROP CONSTRAINT IF EXISTS registro_tomas_usuario_registro_id_fkey;
ALTER TABLE IF EXISTS ONLY public.registro_tomas DROP CONSTRAINT IF EXISTS registro_tomas_registrado_por_fkey;
ALTER TABLE IF EXISTS ONLY public.registro_tomas DROP CONSTRAINT IF EXISTS registro_tomas_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.registro_tomas DROP CONSTRAINT IF EXISTS registro_tomas_medicamento_id_fkey;
ALTER TABLE IF EXISTS ONLY public.registro_salud_diario DROP CONSTRAINT IF EXISTS registro_salud_diario_registrado_por_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.recetas_medicas DROP CONSTRAINT IF EXISTS recetas_medicas_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.recetas_medicas DROP CONSTRAINT IF EXISTS recetas_medicas_doctor_id_fkey;
ALTER TABLE IF EXISTS ONLY public.pacientes DROP CONSTRAINT IF EXISTS pacientes_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.paciente_doctor DROP CONSTRAINT IF EXISTS paciente_doctor_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.paciente_doctor DROP CONSTRAINT IF EXISTS paciente_doctor_doctor_id_fkey;
ALTER TABLE IF EXISTS ONLY public.paciente_cuidador DROP CONSTRAINT IF EXISTS paciente_cuidador_usuario_cuidador_id_fkey;
ALTER TABLE IF EXISTS ONLY public.paciente_cuidador DROP CONSTRAINT IF EXISTS paciente_cuidador_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.notificaciones_alertas DROP CONSTRAINT IF EXISTS notificaciones_alertas_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.medicamentos DROP CONSTRAINT IF EXISTS medicamentos_receta_id_fkey;
ALTER TABLE IF EXISTS ONLY public.medicamentos DROP CONSTRAINT IF EXISTS medicamentos_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.logs_auditoria DROP CONSTRAINT IF EXISTS logs_auditoria_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.logs_auditoria DROP CONSTRAINT IF EXISTS logs_auditoria_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.tomas_historial DROP CONSTRAINT IF EXISTS fk_th_paciente;
ALTER TABLE IF EXISTS ONLY public.tomas_historial DROP CONSTRAINT IF EXISTS fk_th_medicamento;
ALTER TABLE IF EXISTS ONLY public.registro_salud_diario DROP CONSTRAINT IF EXISTS fk_rsd_paciente;
ALTER TABLE IF EXISTS ONLY public.codigos_vinculacion DROP CONSTRAINT IF EXISTS fk_cv_paciente;
ALTER TABLE IF EXISTS ONLY public.expediente_archivos DROP CONSTRAINT IF EXISTS expediente_archivos_visita_medica_id_fkey;
ALTER TABLE IF EXISTS ONLY public.expediente_archivos DROP CONSTRAINT IF EXISTS expediente_archivos_subido_por_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.expediente_archivos DROP CONSTRAINT IF EXISTS expediente_archivos_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.doctores DROP CONSTRAINT IF EXISTS doctores_usuario_id_fkey;
ALTER TABLE IF EXISTS ONLY public.doctores DROP CONSTRAINT IF EXISTS doctores_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.contactos_emergencia DROP CONSTRAINT IF EXISTS contactos_emergencia_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.citas_laboratorios DROP CONSTRAINT IF EXISTS citas_laboratorios_paciente_id_fkey;
ALTER TABLE IF EXISTS ONLY public.citas_laboratorios DROP CONSTRAINT IF EXISTS citas_laboratorios_familiar_responsable_id_fkey;
ALTER TABLE IF EXISTS ONLY public.citas_laboratorios DROP CONSTRAINT IF EXISTS citas_laboratorios_doctor_id_fkey;
DROP INDEX IF EXISTS public.uq_pacientes_token_sos;
DROP INDEX IF EXISTS public.idx_visitas_medicas_paciente_fecha;
DROP INDEX IF EXISTS public.idx_tomas_historial_omitidas;
DROP INDEX IF EXISTS public.idx_tomas_historial_hoy;
DROP INDEX IF EXISTS public.idx_tokens_medico_token;
DROP INDEX IF EXISTS public.idx_tokens_medico_pin;
DROP INDEX IF EXISTS public.idx_tokens_medico_paciente;
DROP INDEX IF EXISTS public.idx_tokens_lookup;
DROP INDEX IF EXISTS public.idx_th_paciente_fecha_estado;
DROP INDEX IF EXISTS public.idx_rsd_paciente_fecha;
DROP INDEX IF EXISTS public.idx_registro_tomas_fecha;
DROP INDEX IF EXISTS public.idx_recetas_paciente;
DROP INDEX IF EXISTS public.idx_recetas_doctor;
DROP INDEX IF EXISTS public.idx_rate_limits_busqueda;
DROP INDEX IF EXISTS public.idx_pacientes_qr_token;
DROP INDEX IF EXISTS public.idx_paciente_doctor_p;
DROP INDEX IF EXISTS public.idx_paciente_doctor_d;
DROP INDEX IF EXISTS public.idx_notificaciones_paciente;
DROP INDEX IF EXISTS public.idx_medicamentos_paciente;
DROP INDEX IF EXISTS public.idx_medicamentos_inventario;
DROP INDEX IF EXISTS public.idx_logs_auditoria_usuario;
DROP INDEX IF EXISTS public.idx_logs_auditoria_paciente;
DROP INDEX IF EXISTS public.idx_logs_auditoria_accion;
DROP INDEX IF EXISTS public.idx_expediente_archivos_visita;
DROP INDEX IF EXISTS public.idx_expediente_archivos_paciente;
DROP INDEX IF EXISTS public.idx_cv_expira;
DROP INDEX IF EXISTS public.idx_contactos_paciente;
DROP INDEX IF EXISTS public.idx_citas_laboratorios_paciente_fecha;
DROP INDEX IF EXISTS public.idx_citas_fecha;
ALTER TABLE IF EXISTS ONLY public.visitas_medicas DROP CONSTRAINT IF EXISTS visitas_medicas_pkey;
ALTER TABLE IF EXISTS ONLY public.usuarios DROP CONSTRAINT IF EXISTS usuarios_pkey;
ALTER TABLE IF EXISTS ONLY public.usuarios DROP CONSTRAINT IF EXISTS usuarios_email_key;
ALTER TABLE IF EXISTS ONLY public.tomas_historial DROP CONSTRAINT IF EXISTS uq_toma;
ALTER TABLE IF EXISTS ONLY public.rate_limits DROP CONSTRAINT IF EXISTS uq_ip_accion;
ALTER TABLE IF EXISTS ONLY public.paciente_doctor DROP CONSTRAINT IF EXISTS uniq_paciente_doctor;
ALTER TABLE IF EXISTS ONLY public.tomas_historial DROP CONSTRAINT IF EXISTS tomas_historial_pkey;
ALTER TABLE IF EXISTS ONLY public.tokens_recuperacion DROP CONSTRAINT IF EXISTS tokens_recuperacion_token_key;
ALTER TABLE IF EXISTS ONLY public.tokens_recuperacion DROP CONSTRAINT IF EXISTS tokens_recuperacion_pkey;
ALTER TABLE IF EXISTS ONLY public.tokens_acceso_medico DROP CONSTRAINT IF EXISTS tokens_acceso_medico_token_url_key;
ALTER TABLE IF EXISTS ONLY public.tokens_acceso_medico DROP CONSTRAINT IF EXISTS tokens_acceso_medico_pkey;
ALTER TABLE IF EXISTS ONLY public.registro_tomas DROP CONSTRAINT IF EXISTS registro_tomas_pkey;
ALTER TABLE IF EXISTS ONLY public.registro_salud_diario DROP CONSTRAINT IF EXISTS registro_salud_diario_pkey;
ALTER TABLE IF EXISTS ONLY public.recetas_medicas DROP CONSTRAINT IF EXISTS recetas_medicas_pkey;
ALTER TABLE IF EXISTS ONLY public.rate_limits DROP CONSTRAINT IF EXISTS rate_limits_pkey;
ALTER TABLE IF EXISTS ONLY public.pacientes DROP CONSTRAINT IF EXISTS pacientes_qr_token_key;
ALTER TABLE IF EXISTS ONLY public.pacientes DROP CONSTRAINT IF EXISTS pacientes_pkey;
ALTER TABLE IF EXISTS ONLY public.pacientes DROP CONSTRAINT IF EXISTS pacientes_codigo_vinculacion_key;
ALTER TABLE IF EXISTS ONLY public.paciente_doctor DROP CONSTRAINT IF EXISTS paciente_doctor_pkey;
ALTER TABLE IF EXISTS ONLY public.paciente_cuidador DROP CONSTRAINT IF EXISTS paciente_cuidador_pkey;
ALTER TABLE IF EXISTS ONLY public.notificaciones_alertas DROP CONSTRAINT IF EXISTS notificaciones_alertas_pkey;
ALTER TABLE IF EXISTS ONLY public.medicamentos DROP CONSTRAINT IF EXISTS medicamentos_pkey;
ALTER TABLE IF EXISTS ONLY public.logs_auditoria DROP CONSTRAINT IF EXISTS logs_auditoria_pkey;
ALTER TABLE IF EXISTS ONLY public.expediente_archivos DROP CONSTRAINT IF EXISTS expediente_archivos_pkey;
ALTER TABLE IF EXISTS ONLY public.doctores DROP CONSTRAINT IF EXISTS doctores_pkey;
ALTER TABLE IF EXISTS ONLY public.contactos_emergencia DROP CONSTRAINT IF EXISTS contactos_emergencia_pkey;
ALTER TABLE IF EXISTS ONLY public.codigos_vinculacion DROP CONSTRAINT IF EXISTS codigos_vinculacion_pkey;
ALTER TABLE IF EXISTS ONLY public.citas_laboratorios DROP CONSTRAINT IF EXISTS citas_laboratorios_pkey;
ALTER TABLE IF EXISTS public.usuarios ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.tomas_historial ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.tokens_recuperacion ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.registro_tomas ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.registro_salud_diario ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.recetas_medicas ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.pacientes ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.paciente_doctor ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.paciente_cuidador ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.notificaciones_alertas ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.medicamentos ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.doctores ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.contactos_emergencia ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.citas_laboratorios ALTER COLUMN id DROP DEFAULT;
DROP TABLE IF EXISTS public.visitas_medicas;
DROP SEQUENCE IF EXISTS public.usuarios_id_seq;
DROP TABLE IF EXISTS public.usuarios;
DROP SEQUENCE IF EXISTS public.tomas_historial_id_seq;
DROP TABLE IF EXISTS public.tomas_historial;
DROP SEQUENCE IF EXISTS public.tokens_recuperacion_id_seq;
DROP TABLE IF EXISTS public.tokens_recuperacion;
DROP TABLE IF EXISTS public.tokens_acceso_medico;
DROP SEQUENCE IF EXISTS public.registro_tomas_id_seq;
DROP TABLE IF EXISTS public.registro_tomas;
DROP SEQUENCE IF EXISTS public.registro_salud_diario_id_seq;
DROP TABLE IF EXISTS public.registro_salud_diario;
DROP SEQUENCE IF EXISTS public.recetas_medicas_id_seq;
DROP TABLE IF EXISTS public.recetas_medicas;
DROP TABLE IF EXISTS public.rate_limits;
DROP SEQUENCE IF EXISTS public.pacientes_id_seq;
DROP TABLE IF EXISTS public.pacientes;
DROP SEQUENCE IF EXISTS public.paciente_doctor_id_seq;
DROP TABLE IF EXISTS public.paciente_doctor;
DROP SEQUENCE IF EXISTS public.paciente_cuidador_id_seq;
DROP TABLE IF EXISTS public.paciente_cuidador;
DROP SEQUENCE IF EXISTS public.notificaciones_alertas_id_seq;
DROP TABLE IF EXISTS public.notificaciones_alertas;
DROP SEQUENCE IF EXISTS public.medicamentos_id_seq;
DROP TABLE IF EXISTS public.medicamentos;
DROP TABLE IF EXISTS public.logs_auditoria;
DROP SEQUENCE IF EXISTS public.doctores_id_seq;
DROP TABLE IF EXISTS public.doctores;
DROP SEQUENCE IF EXISTS public.contactos_emergencia_id_seq;
DROP TABLE IF EXISTS public.contactos_emergencia;
DROP TABLE IF EXISTS public.codigos_vinculacion;
DROP SEQUENCE IF EXISTS public.citas_laboratorios_id_seq;
DROP TABLE IF EXISTS public.citas_laboratorios;
DROP VIEW IF EXISTS public.archivos_medicos;
DROP TABLE IF EXISTS public.expediente_archivos;
DROP TYPE IF EXISTS public.tipo_evento_cita;
DROP TYPE IF EXISTS public.rol_usuario;
DROP TYPE IF EXISTS public.instrucciones_ingesta_enum;
DROP TYPE IF EXISTS public.estado_toma;
DROP TYPE IF EXISTS public.enum_momento_glucosa;
DROP TYPE IF EXISTS public.enum_momento_dia;
DROP TYPE IF EXISTS public.enum_estado_toma;
DROP EXTENSION IF EXISTS pgcrypto;
--
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- Name: enum_estado_toma; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.enum_estado_toma AS ENUM (
    'pendiente',
    'tomado',
    'omitido',
    'pospuesto'
);


--
-- Name: enum_momento_dia; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.enum_momento_dia AS ENUM (
    'manana',
    'tarde',
    'noche'
);


--
-- Name: enum_momento_glucosa; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.enum_momento_glucosa AS ENUM (
    'ayunas',
    'antes_comer',
    'despues_comer'
);


--
-- Name: estado_toma; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.estado_toma AS ENUM (
    'tomada',
    'pendiente',
    'omitida'
);


--
-- Name: instrucciones_ingesta_enum; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.instrucciones_ingesta_enum AS ENUM (
    'con_alimentos',
    'en_ayunas',
    'indiferente'
);


--
-- Name: rol_usuario; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.rol_usuario AS ENUM (
    'administrador',
    'cuidador',
    'emergencia'
);


--
-- Name: tipo_evento_cita; Type: TYPE; Schema: public; Owner: -
--

CREATE TYPE public.tipo_evento_cita AS ENUM (
    'cita_medica',
    'laboratorio'
);


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: expediente_archivos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.expediente_archivos (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    paciente_id integer NOT NULL,
    visita_medica_id uuid,
    nombre_original character varying(255) NOT NULL,
    nombre_archivo_almacenado character varying(255) NOT NULL,
    ruta_almacenamiento character varying(500) NOT NULL,
    mime_type character varying(100) NOT NULL,
    tamano_bytes bigint NOT NULL,
    tipo_documento character varying(50) DEFAULT 'receta'::character varying,
    subido_por_usuario_id integer,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: archivos_medicos; Type: VIEW; Schema: public; Owner: -
--

CREATE VIEW public.archivos_medicos AS
 SELECT id,
    paciente_id,
    visita_medica_id,
    nombre_original,
    nombre_archivo_almacenado,
    ruta_almacenamiento,
    mime_type,
    tamano_bytes,
    tipo_documento,
    subido_por_usuario_id,
    created_at
   FROM public.expediente_archivos;


--
-- Name: citas_laboratorios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.citas_laboratorios (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    doctor_id integer,
    tipo_evento character varying(50) NOT NULL,
    titulo character varying(150) NOT NULL,
    lugar character varying(150) NOT NULL,
    fecha_hora timestamp with time zone NOT NULL,
    archivo_resultado_url character varying(255),
    notas text,
    realizado boolean DEFAULT false,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    preparacion_previa text,
    familiar_responsable_id integer,
    ubicacion_mapa character varying(500),
    estado character varying(20) DEFAULT 'programada'::character varying
);


--
-- Name: citas_laboratorios_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.citas_laboratorios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: citas_laboratorios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.citas_laboratorios_id_seq OWNED BY public.citas_laboratorios.id;


--
-- Name: codigos_vinculacion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.codigos_vinculacion (
    codigo character varying(6) NOT NULL,
    paciente_id integer NOT NULL,
    expira_at timestamp with time zone NOT NULL
);


--
-- Name: contactos_emergencia; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.contactos_emergencia (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    parentesco character varying(50) NOT NULL,
    telefono character varying(20) NOT NULL,
    es_prioritario boolean DEFAULT false,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    email character varying(255)
);


--
-- Name: contactos_emergencia_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.contactos_emergencia_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: contactos_emergencia_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.contactos_emergencia_id_seq OWNED BY public.contactos_emergencia.id;


--
-- Name: doctores; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.doctores (
    id integer NOT NULL,
    paciente_id integer,
    nombre character varying(100) NOT NULL,
    especialidad character varying(100),
    hospital_clinica character varying(150),
    telefono_contacto character varying(20),
    horario_atencion character varying(100),
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    usuario_id integer,
    cedula_profesional character varying(50),
    telefono_consultorio character varying(25),
    direccion_consultorio text
);


--
-- Name: doctores_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.doctores_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: doctores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.doctores_id_seq OWNED BY public.doctores.id;


--
-- Name: logs_auditoria; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.logs_auditoria (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    usuario_id integer,
    paciente_id integer,
    accion character varying(100) NOT NULL,
    detalles text,
    ip_origen character varying(45),
    user_agent character varying(255),
    fecha_utc timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: medicamentos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.medicamentos (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    nombre_comercial character varying(100) NOT NULL,
    dosis character varying(50) NOT NULL,
    frecuencia_horas integer NOT NULL,
    stock_actual integer DEFAULT 0,
    stock_minimo integer DEFAULT 5,
    instrucciones text,
    activo boolean DEFAULT true,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    stock_minimo_alerta integer DEFAULT 5 NOT NULL,
    dosis_por_toma integer DEFAULT 1 NOT NULL,
    color_distintivo character varying(20) DEFAULT 'Blanco'::character varying,
    forma character varying(20) DEFAULT 'tableta'::character varying,
    momento_dia character varying(50),
    instrucciones_ingesta public.instrucciones_ingesta_enum DEFAULT 'indiferente'::public.instrucciones_ingesta_enum,
    requiere_confirmacion_visual boolean DEFAULT true,
    ultima_recarga_stock date,
    dosis_restantes integer DEFAULT 0,
    receta_id integer,
    CONSTRAINT medicamentos_forma_check CHECK (((forma)::text = ANY ((ARRAY['tableta'::character varying, 'capsula'::character varying, 'jarabe'::character varying, 'gotas'::character varying, 'inyeccion'::character varying])::text[])))
);


--
-- Name: medicamentos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.medicamentos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: medicamentos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.medicamentos_id_seq OWNED BY public.medicamentos.id;


--
-- Name: notificaciones_alertas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.notificaciones_alertas (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    tipo character varying(50) NOT NULL,
    mensaje text NOT NULL,
    destinatario_telefono character varying(20),
    estado_envio character varying(20) DEFAULT 'pendiente'::character varying,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: notificaciones_alertas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.notificaciones_alertas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: notificaciones_alertas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.notificaciones_alertas_id_seq OWNED BY public.notificaciones_alertas.id;


--
-- Name: paciente_cuidador; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.paciente_cuidador (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    usuario_cuidador_id integer NOT NULL,
    parentesco character varying(50) NOT NULL,
    nivel_acceso character varying(50) DEFAULT 'tomas_y_citas'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT paciente_cuidador_nivel_acceso_check CHECK (((nivel_acceso)::text = ANY ((ARRAY['solo_lectura'::character varying, 'completo'::character varying, 'lectura'::character varying, 'tomas_y_signos'::character varying, 'familiar_admin'::character varying, 'admin'::character varying, 'tomas_y_citas'::character varying, 'edicion_completa'::character varying])::text[])))
);


--
-- Name: paciente_cuidador_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.paciente_cuidador_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: paciente_cuidador_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.paciente_cuidador_id_seq OWNED BY public.paciente_cuidador.id;


--
-- Name: paciente_doctor; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.paciente_doctor (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    doctor_id integer NOT NULL,
    fecha_vinculacion timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    estado character varying(20) DEFAULT 'activo'::character varying,
    notas_medicas text
);


--
-- Name: paciente_doctor_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.paciente_doctor_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: paciente_doctor_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.paciente_doctor_id_seq OWNED BY public.paciente_doctor.id;


--
-- Name: pacientes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.pacientes (
    id integer NOT NULL,
    usuario_id integer,
    nombre character varying(100) NOT NULL,
    apellidos character varying(100) NOT NULL,
    fecha_nacimiento date NOT NULL,
    tipo_sangre character varying(5) NOT NULL,
    alergias text,
    enfermedades_cronicas text,
    notas_cognitivas text,
    qr_token uuid DEFAULT gen_random_uuid(),
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    token_sos character varying(64),
    codigo_vinculacion character varying(20),
    foto_perfil character varying(255),
    token_emergencia_qr character varying(64),
    codigo_expira_en timestamp with time zone,
    codigo_activo boolean DEFAULT true
);


--
-- Name: pacientes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.pacientes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: pacientes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.pacientes_id_seq OWNED BY public.pacientes.id;


--
-- Name: rate_limits; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rate_limits (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    ip_address character varying(45) NOT NULL,
    accion character varying(100) NOT NULL,
    intentos integer DEFAULT 1,
    primer_intento timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    ultimo_intento timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: recetas_medicas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.recetas_medicas (
    id integer NOT NULL,
    doctor_id integer,
    paciente_id integer NOT NULL,
    diagnostico text,
    indicaciones_generales text,
    fecha_emision date DEFAULT CURRENT_DATE,
    archivo_receta_url character varying(255),
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: recetas_medicas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.recetas_medicas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: recetas_medicas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.recetas_medicas_id_seq OWNED BY public.recetas_medicas.id;


--
-- Name: registro_salud_diario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.registro_salud_diario (
    id integer NOT NULL,
    paciente_id integer NOT NULL,
    presion_sistolica smallint,
    presion_diastolica smallint,
    glucosa numeric(5,2),
    momento_glucosa public.enum_momento_glucosa,
    ritmo_cardiaco smallint,
    horas_sueno numeric(3,1),
    nivel_dolor smallint,
    alerta_emitida boolean DEFAULT false NOT NULL,
    confirmado_por_usuario boolean DEFAULT false NOT NULL,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    peso numeric(5,2),
    altura numeric(5,2),
    imc numeric(4,2),
    registrado_por_usuario_id integer,
    CONSTRAINT registro_salud_diario_nivel_dolor_check CHECK (((nivel_dolor >= 0) AND (nivel_dolor <= 4)))
);


--
-- Name: COLUMN registro_salud_diario.nivel_dolor; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.registro_salud_diario.nivel_dolor IS 'Rango 0 (sin dolor) a 4 (dolor severo)';


--
-- Name: registro_salud_diario_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.registro_salud_diario_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: registro_salud_diario_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.registro_salud_diario_id_seq OWNED BY public.registro_salud_diario.id;


--
-- Name: registro_tomas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.registro_tomas (
    id integer NOT NULL,
    medicamento_id integer NOT NULL,
    fecha_hora_programada timestamp with time zone NOT NULL,
    fecha_hora_tomada timestamp with time zone,
    estado public.estado_toma DEFAULT 'pendiente'::public.estado_toma,
    observaciones text,
    registrado_por integer,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    paciente_id integer,
    usuario_registro_id integer
);


--
-- Name: registro_tomas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.registro_tomas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: registro_tomas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.registro_tomas_id_seq OWNED BY public.registro_tomas.id;


--
-- Name: tokens_acceso_medico; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tokens_acceso_medico (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    paciente_id integer NOT NULL,
    generado_por_usuario_id integer NOT NULL,
    pin_seguridad character varying(8) NOT NULL,
    token_url character varying(64) NOT NULL,
    medico_nombre character varying(150),
    hospital_institucion character varying(150),
    duracion_horas integer DEFAULT 24,
    expira_at timestamp with time zone NOT NULL,
    max_usos integer DEFAULT 20,
    usos_actuales integer DEFAULT 0,
    activo boolean DEFAULT true,
    ultimo_acceso_at timestamp with time zone,
    ip_ultimo_acceso character varying(45),
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: tokens_recuperacion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tokens_recuperacion (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    token character varying(64) NOT NULL,
    expira_en timestamp without time zone NOT NULL,
    usado boolean DEFAULT false,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: tokens_recuperacion_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tokens_recuperacion_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tokens_recuperacion_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tokens_recuperacion_id_seq OWNED BY public.tokens_recuperacion.id;


--
-- Name: tomas_historial; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tomas_historial (
    id integer NOT NULL,
    medicamento_id integer NOT NULL,
    paciente_id integer NOT NULL,
    fecha_programada date NOT NULL,
    momento_dia public.enum_momento_dia NOT NULL,
    estado character varying(20) DEFAULT 'pendiente'::public.enum_estado_toma NOT NULL,
    tomado_at timestamp with time zone,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    hora_programada time without time zone DEFAULT '08:00:00'::time without time zone,
    registrado_por_usuario_id integer,
    nota_incidencia character varying(255),
    notificacion_enviada_at timestamp with time zone,
    veces_pospuesto integer DEFAULT 0,
    CONSTRAINT tomas_historial_estado_check CHECK (((estado)::text = ANY ((ARRAY['pendiente'::character varying, 'tomado'::character varying, 'omitido'::character varying, 'pospuesto'::character varying, 'deshecho'::character varying])::text[])))
);


--
-- Name: tomas_historial_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tomas_historial_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tomas_historial_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tomas_historial_id_seq OWNED BY public.tomas_historial.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    password_hash character varying(255) NOT NULL,
    rol character varying(50) DEFAULT 'cuidador'::public.rol_usuario NOT NULL,
    activo boolean DEFAULT true,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    telefono character varying(20),
    CONSTRAINT usuarios_rol_check CHECK (((rol)::text = ANY ((ARRAY['administrador'::character varying, 'familiar_admin'::character varying, 'cuidador_auxiliar'::character varying, 'paciente'::character varying, 'medico'::character varying, 'doctor'::character varying, 'cuidador'::character varying, 'adulto_mayor'::character varying, 'familiar'::character varying, 'emergencia'::character varying])::text[])))
);


--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: visitas_medicas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.visitas_medicas (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    paciente_id integer NOT NULL,
    usuario_registra_id integer,
    fecha_visita timestamp with time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    lugar_atencion character varying(150),
    motivo_consulta text NOT NULL,
    diagnostico_notas text,
    doctor_nombre character varying(150),
    requiere_seguimiento boolean DEFAULT false,
    fecha_seguimiento date,
    instrucciones_seguimiento text,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);


--
-- Name: citas_laboratorios id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.citas_laboratorios ALTER COLUMN id SET DEFAULT nextval('public.citas_laboratorios_id_seq'::regclass);


--
-- Name: contactos_emergencia id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contactos_emergencia ALTER COLUMN id SET DEFAULT nextval('public.contactos_emergencia_id_seq'::regclass);


--
-- Name: doctores id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.doctores ALTER COLUMN id SET DEFAULT nextval('public.doctores_id_seq'::regclass);


--
-- Name: medicamentos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medicamentos ALTER COLUMN id SET DEFAULT nextval('public.medicamentos_id_seq'::regclass);


--
-- Name: notificaciones_alertas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones_alertas ALTER COLUMN id SET DEFAULT nextval('public.notificaciones_alertas_id_seq'::regclass);


--
-- Name: paciente_cuidador id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_cuidador ALTER COLUMN id SET DEFAULT nextval('public.paciente_cuidador_id_seq'::regclass);


--
-- Name: paciente_doctor id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_doctor ALTER COLUMN id SET DEFAULT nextval('public.paciente_doctor_id_seq'::regclass);


--
-- Name: pacientes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pacientes ALTER COLUMN id SET DEFAULT nextval('public.pacientes_id_seq'::regclass);


--
-- Name: recetas_medicas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.recetas_medicas ALTER COLUMN id SET DEFAULT nextval('public.recetas_medicas_id_seq'::regclass);


--
-- Name: registro_salud_diario id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_salud_diario ALTER COLUMN id SET DEFAULT nextval('public.registro_salud_diario_id_seq'::regclass);


--
-- Name: registro_tomas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_tomas ALTER COLUMN id SET DEFAULT nextval('public.registro_tomas_id_seq'::regclass);


--
-- Name: tokens_recuperacion id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_recuperacion ALTER COLUMN id SET DEFAULT nextval('public.tokens_recuperacion_id_seq'::regclass);


--
-- Name: tomas_historial id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tomas_historial ALTER COLUMN id SET DEFAULT nextval('public.tomas_historial_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Data for Name: citas_laboratorios; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.citas_laboratorios (id, paciente_id, doctor_id, tipo_evento, titulo, lugar, fecha_hora, archivo_resultado_url, notas, realizado, created_at, preparacion_previa, familiar_responsable_id, ubicacion_mapa, estado) VALUES (1, 1, NULL, 'consulta', 'Analisis de sangre', 'Hospital regional', '2026-09-29 19:44:00-06', NULL, NULL, false, '2026-09-25 01:44:39.188522-06', NULL, 4, NULL, 'programada');


--
-- Data for Name: codigos_vinculacion; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: contactos_emergencia; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.contactos_emergencia (id, paciente_id, nombre, parentesco, telefono, es_prioritario, created_at, email) VALUES (1, 1, 'Mario Efrain', 'Nieto', '9613002013', false, '2026-10-07 18:14:26.617722-06', NULL);


--
-- Data for Name: doctores; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.doctores (id, paciente_id, nombre, especialidad, hospital_clinica, telefono_contacto, horario_atencion, created_at, usuario_id, cedula_profesional, telefono_consultorio, direccion_consultorio) VALUES (1, NULL, 'Dra. Especialista Test', 'Medicina General / Geriatría', NULL, '', 'Lunes a Viernes 09:00 - 18:00', '2026-10-07 18:13:10.018312-06', 16, 'EN TRAMITE', '', NULL);
INSERT INTO public.doctores (id, paciente_id, nombre, especialidad, hospital_clinica, telefono_contacto, horario_atencion, created_at, usuario_id, cedula_profesional, telefono_consultorio, direccion_consultorio) VALUES (2, 1, 'Dr. Carlos Mendoza', 'Geriatría y Medicina Interna', 'Hospital Ángeles / VitalSenior Clinic', '9998889900', 'Lun - Vie 09:00 - 18:00', '2026-10-07 18:23:40.283904-06', 20, 'MED-849201', '9993334444', 'Av. Principal 123, Consultorio 402');


--
-- Data for Name: expediente_archivos; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: logs_auditoria; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('152782d0-83f1-448a-97db-12b1e0afb2a6', 4, 1, 'PERFIL_ADULTO_MAYOR_CREADO', 'Nuevo perfil administrado creado para Esperanza Ruiz por usuario ID 4.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 01:43:27.273345-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('dbf7822b-9686-48c4-baa5-af7f4deaae12', 4, 1, 'crear_cita', 'Cita ''Analisis de sangre'' (consulta) agendada para el 30/09/2026 01:44 (ID: 1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-09-25 01:44:39.197637-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('c8b0b7ff-e6dc-48ab-88bc-930be146b62a', 4, 1, 'DESCARGA_REPORTE_PDF', 'Reporte clínico en PDF descargado por el usuario ID 4.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 18:05:32.086711-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('9514a528-6f47-4831-bd0a-bc03267a2b60', 4, 1, 'EXPEDIENTE_DATOS_ACTUALIZADOS', 'Datos clínicos y demográficos actualizados para el paciente ID 1 por usuario ID 4.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 18:09:35.616376-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('08734802-0216-4d47-bbf8-71e8dfadb394', 4, 1, 'DESCARGA_REPORTE_PDF', 'Reporte clínico en PDF descargado por el usuario ID 4.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 18:09:39.713179-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('78353727-8ee7-4253-99a3-57fd0d6e6077', 4, 1, 'CONTACTO_AÑADIDO', 'Contacto Mario Efrain añadido al expediente 1.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 18:14:26.660532-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('29037e16-a99b-41bf-a5c4-80301729f986', 4, 1, 'DESCARGA_REPORTE_PDF', 'Reporte clínico en PDF descargado por el usuario ID 4.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', '2026-10-07 18:14:31.274825-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('3eb2cf13-ec5b-44b2-9104-62b3846c5064', 4, 1, 'DESCARGA_REPORTE_PDF', 'Reporte clínico en PDF descargado por el usuario ID 4.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0', '2026-10-09 17:46:48.175744-06');
INSERT INTO public.logs_auditoria (id, usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) VALUES ('eb433af2-5c68-4d4a-9618-ac6a2db2bb4c', 21, 4, 'DESCARGA_REPORTE_PDF', 'Reporte clínico en PDF descargado por el usuario ID 21.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 18:49:10.162659-06');


--
-- Data for Name: medicamentos; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.medicamentos (id, paciente_id, nombre_comercial, dosis, frecuencia_horas, stock_actual, stock_minimo, instrucciones, activo, created_at, stock_minimo_alerta, dosis_por_toma, color_distintivo, forma, momento_dia, instrucciones_ingesta, requiere_confirmacion_visual, ultima_recarga_stock, dosis_restantes, receta_id) VALUES (1, 2, 'Losartán Test 50mg', '1 tableta', 12, 20, 5, 'Tomar por la mañana', true, '2026-10-07 18:13:10.045729-06', 5, 1, 'Blanco', 'tableta', NULL, 'indiferente', true, NULL, 0, NULL);


--
-- Data for Name: notificaciones_alertas; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: paciente_cuidador; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.paciente_cuidador (id, paciente_id, usuario_cuidador_id, parentesco, nivel_acceso, created_at) VALUES (1, 1, 4, 'Nieto/a', 'familiar_admin', '2026-09-25 07:43:27.269665');
INSERT INTO public.paciente_cuidador (id, paciente_id, usuario_cuidador_id, parentesco, nivel_acceso, created_at) VALUES (2, 2, 13, 'Hijo Cuidador', 'familiar_admin', '2026-10-08 00:13:10.036284');
INSERT INTO public.paciente_cuidador (id, paciente_id, usuario_cuidador_id, parentesco, nivel_acceso, created_at) VALUES (3, 2, 15, 'Hija', 'solo_lectura', '2026-10-08 00:13:10.039485');
INSERT INTO public.paciente_cuidador (id, paciente_id, usuario_cuidador_id, parentesco, nivel_acceso, created_at) VALUES (5, 1, 17, 'Tutor Principal', 'completo', '2026-10-08 00:23:40.27714');
INSERT INTO public.paciente_cuidador (id, paciente_id, usuario_cuidador_id, parentesco, nivel_acceso, created_at) VALUES (6, 1, 18, 'Hijo / Familiar', 'solo_lectura', '2026-10-08 00:23:40.27974');
INSERT INTO public.paciente_cuidador (id, paciente_id, usuario_cuidador_id, parentesco, nivel_acceso, created_at) VALUES (7, 1, 19, 'Titular', 'completo', '2026-10-08 00:23:40.281031');


--
-- Data for Name: paciente_doctor; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.paciente_doctor (id, paciente_id, doctor_id, fecha_vinculacion, estado, notas_medicas) VALUES (1, 2, 1, '2026-10-07 18:13:10.042657-06', 'activo', 'Paciente geriátrico en control mensual');
INSERT INTO public.paciente_doctor (id, paciente_id, doctor_id, fecha_vinculacion, estado, notas_medicas) VALUES (2, 1, 2, '2026-10-07 18:23:40.288205-06', 'activo', 'Médico tratante principal de Esperanza Ruiz');


--
-- Data for Name: pacientes; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.pacientes (id, usuario_id, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, qr_token, created_at, updated_at, token_sos, codigo_vinculacion, foto_perfil, token_emergencia_qr, codigo_expira_en, codigo_activo) VALUES (2, 13, 'Roberto', 'Gómez Test', '1948-06-15', 'O+', 'Penicilina', 'Hipertensión', 'Autónomo', '82b914aa-3112-4fad-a7d3-64145146a6c7', '2026-10-07 18:13:10.02371-06', '2026-10-07 18:13:10.02371-06', 'bdc47ff9183377360322f974e408c7ba', 'ROB-4232', NULL, 'bdc47ff9183377360322f974e408c7ba', NULL, true);
INSERT INTO public.pacientes (id, usuario_id, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, qr_token, created_at, updated_at, token_sos, codigo_vinculacion, foto_perfil, token_emergencia_qr, codigo_expira_en, codigo_activo) VALUES (1, 17, 'Esperanza', 'Ruiz', '1965-02-25', 'O-', 'Azucar', 'diabetes y hipertencion', '', '6dc93735-a4b2-4faa-8e07-df75806cf081', '2026-09-25 01:43:27.2661-06', '2026-10-07 18:09:35.616376-06', 'ef5cdfe272a9909a0646a6f3ca4ff67f', 'ESP-6056', NULL, 'ef5cdfe272a9909a0646a6f3ca4ff67f', NULL, true);
INSERT INTO public.pacientes (id, usuario_id, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, qr_token, created_at, updated_at, token_sos, codigo_vinculacion, foto_perfil, token_emergencia_qr, codigo_expira_en, codigo_activo) VALUES (3, 14, 'Roberto', 'Gómez Titular', '1948-06-15', 'O+', 'Ninguna', NULL, NULL, 'e905549a-ae9a-42cb-8ba6-64bd5352b4f2', '2026-10-07 18:13:10.054556-06', '2026-10-07 18:13:10.054556-06', 'sos_token_pac_test', 'VITA-PAC-01', NULL, 'e905549a-ae9a-42cb-8ba6-64bd5352b4f2', NULL, true);
INSERT INTO public.pacientes (id, usuario_id, nombre, apellidos, fecha_nacimiento, tipo_sangre, alergias, enfermedades_cronicas, notas_cognitivas, qr_token, created_at, updated_at, token_sos, codigo_vinculacion, foto_perfil, token_emergencia_qr, codigo_expira_en, codigo_activo) VALUES (4, 21, 'Expediente de MARIO EFRAIN Moguel Hernandez', 'Por completar', '2026-10-14', 'N/D', 'Ninguna', 'Ninguna', '', 'ec6e1bd6-3019-454b-97c0-fcbbf9932af5', '2026-10-13 18:49:00.593845-06', '2026-10-13 18:49:00.593845-06', 'b7e8548cb6b734324ce6ad262cddc02c', 'EXP-6906', NULL, 'b7e8548cb6b734324ce6ad262cddc02c', NULL, true);


--
-- Data for Name: rate_limits; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: recetas_medicas; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: registro_salud_diario; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: registro_tomas; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: tokens_acceso_medico; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: tokens_recuperacion; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Data for Name: tomas_historial; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.tomas_historial (id, medicamento_id, paciente_id, fecha_programada, momento_dia, estado, tomado_at, created_at, hora_programada, registrado_por_usuario_id, nota_incidencia, notificacion_enviada_at, veces_pospuesto) VALUES (1, 1, 2, '2026-10-08', 'manana', 'pendiente', NULL, '2026-10-07 18:13:10.051316-06', '08:00:00', NULL, NULL, NULL, 0);
INSERT INTO public.tomas_historial (id, medicamento_id, paciente_id, fecha_programada, momento_dia, estado, tomado_at, created_at, hora_programada, registrado_por_usuario_id, nota_incidencia, notificacion_enviada_at, veces_pospuesto) VALUES (2, 1, 2, '2026-10-08', 'noche', 'pendiente', NULL, '2026-10-07 18:13:10.053202-06', '20:00:00', NULL, NULL, NULL, 0);


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (4, 'MARIO EFRAIN Moguel Hernandez', 'mariomoguel05@gmail.com', '$2y$12$Z.cb2AZxFQsTfNqSMmYbNe9U18o/U7dyaxcU2MpC8dybnl2nP3VwW', 'administrador', true, '2026-09-02 16:54:58.190994-06', '2026-09-25 01:42:30.130166-06', NULL);
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (13, 'Cuidador Principal Test', 'cuidador.principal.test@vitalsenior.com', '$2y$12$GrrGLS6jRRQeHJIjD5NiLOtKQwA4hw.Fevsh4qhXLOJPBXg7ps6tq', 'familiar_admin', true, '2026-10-07 18:13:09.360876-06', '2026-10-07 18:13:09.360876-06', '9991112233');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (14, 'Don Roberto Gómez Test', 'paciente.titular.test@vitalsenior.com', '$2y$12$zlVBkkM5XewD1pJm5IpcP.Gigkrux/iWASzD19FUcbhgUQFOK3IBC', 'paciente', true, '2026-10-07 18:13:09.580053-06', '2026-10-07 18:13:09.580053-06', '9992223344');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (15, 'Hija Solo Lectura Test', 'familiar.secundario.test@vitalsenior.com', '$2y$12$nO2nuCaLdIEQbExn.dAa/eIY17OW54Sa1.Un4DMQy3TYYJBnhxGsq', 'familiar', true, '2026-10-07 18:13:09.798329-06', '2026-10-07 18:13:09.798329-06', '9993334455');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (16, 'Dra. Especialista Test', 'dr.vinculado.test@vitalsenior.com', '$2y$12$a3Dp6fp2t7bm301ZoKH/zul7/4549Jp2aLxg.i/8hiUI4ioxdJf1e', 'doctor', true, '2026-10-07 18:13:10.014232-06', '2026-10-07 18:13:10.014232-06', '9994445566');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (17, 'Cuidador Principal de Esperanza', 'cuidador.esperanza@vitalsenior.com', '$2y$10$ZwIM5mplAeowqyJQSh4ETOEpigF2WE30P9RMQpSWQMaxbVU8dbwby', 'familiar_admin', true, '2026-10-07 18:22:52.6389-06', '2026-10-07 18:22:52.6389-06', '9991112233');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (18, 'Gael Eleazar', 'gael.eleazar@vitalsenior.com', '$2y$10$ZwIM5mplAeowqyJQSh4ETOEpigF2WE30P9RMQpSWQMaxbVU8dbwby', 'familiar', true, '2026-10-07 18:23:40.278857-06', '2026-10-07 18:23:40.278857-06', '9994445566');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (19, 'Esperanza Ruiz', 'esperanza.ruiz@vitalsenior.com', '$2y$10$ZwIM5mplAeowqyJQSh4ETOEpigF2WE30P9RMQpSWQMaxbVU8dbwby', 'paciente', true, '2026-10-07 18:23:40.280506-06', '2026-10-07 18:23:40.280506-06', '9997778899');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (20, 'Dr. Carlos Mendoza', 'doctor.esperanza@vitalsenior.com', '$2y$10$ZwIM5mplAeowqyJQSh4ETOEpigF2WE30P9RMQpSWQMaxbVU8dbwby', 'doctor', true, '2026-10-07 18:23:40.281617-06', '2026-10-07 18:23:40.281617-06', '9998889900');
INSERT INTO public.usuarios (id, nombre, email, password_hash, rol, activo, created_at, updated_at, telefono) VALUES (21, 'MARIO EFRAIN Moguel Hernandez', 'mariomoguel11@gmail.com', '$2y$12$2Kf7x5w8h78M3UkUO/Qzye5YUYL8goQhmla0kbFkB0L9Oh2wFPs7G', 'cuidador', true, '2026-10-13 18:49:00.593845-06', '2026-10-13 18:49:00.593845-06', '+529613002013');


--
-- Data for Name: visitas_medicas; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- Name: citas_laboratorios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.citas_laboratorios_id_seq', 1, true);


--
-- Name: contactos_emergencia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.contactos_emergencia_id_seq', 1, true);


--
-- Name: doctores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.doctores_id_seq', 2, true);


--
-- Name: medicamentos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.medicamentos_id_seq', 1, true);


--
-- Name: notificaciones_alertas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.notificaciones_alertas_id_seq', 1, false);


--
-- Name: paciente_cuidador_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.paciente_cuidador_id_seq', 7, true);


--
-- Name: paciente_doctor_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.paciente_doctor_id_seq', 2, true);


--
-- Name: pacientes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.pacientes_id_seq', 4, true);


--
-- Name: recetas_medicas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.recetas_medicas_id_seq', 1, false);


--
-- Name: registro_salud_diario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.registro_salud_diario_id_seq', 1, false);


--
-- Name: registro_tomas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.registro_tomas_id_seq', 1, false);


--
-- Name: tokens_recuperacion_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tokens_recuperacion_id_seq', 1, false);


--
-- Name: tomas_historial_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.tomas_historial_id_seq', 2, true);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 21, true);


--
-- Name: citas_laboratorios citas_laboratorios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.citas_laboratorios
    ADD CONSTRAINT citas_laboratorios_pkey PRIMARY KEY (id);


--
-- Name: codigos_vinculacion codigos_vinculacion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.codigos_vinculacion
    ADD CONSTRAINT codigos_vinculacion_pkey PRIMARY KEY (codigo);


--
-- Name: contactos_emergencia contactos_emergencia_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contactos_emergencia
    ADD CONSTRAINT contactos_emergencia_pkey PRIMARY KEY (id);


--
-- Name: doctores doctores_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.doctores
    ADD CONSTRAINT doctores_pkey PRIMARY KEY (id);


--
-- Name: expediente_archivos expediente_archivos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.expediente_archivos
    ADD CONSTRAINT expediente_archivos_pkey PRIMARY KEY (id);


--
-- Name: logs_auditoria logs_auditoria_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logs_auditoria
    ADD CONSTRAINT logs_auditoria_pkey PRIMARY KEY (id);


--
-- Name: medicamentos medicamentos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medicamentos
    ADD CONSTRAINT medicamentos_pkey PRIMARY KEY (id);


--
-- Name: notificaciones_alertas notificaciones_alertas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones_alertas
    ADD CONSTRAINT notificaciones_alertas_pkey PRIMARY KEY (id);


--
-- Name: paciente_cuidador paciente_cuidador_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_cuidador
    ADD CONSTRAINT paciente_cuidador_pkey PRIMARY KEY (id);


--
-- Name: paciente_doctor paciente_doctor_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_doctor
    ADD CONSTRAINT paciente_doctor_pkey PRIMARY KEY (id);


--
-- Name: pacientes pacientes_codigo_vinculacion_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pacientes
    ADD CONSTRAINT pacientes_codigo_vinculacion_key UNIQUE (codigo_vinculacion);


--
-- Name: pacientes pacientes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pacientes
    ADD CONSTRAINT pacientes_pkey PRIMARY KEY (id);


--
-- Name: pacientes pacientes_qr_token_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pacientes
    ADD CONSTRAINT pacientes_qr_token_key UNIQUE (qr_token);


--
-- Name: rate_limits rate_limits_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rate_limits
    ADD CONSTRAINT rate_limits_pkey PRIMARY KEY (id);


--
-- Name: recetas_medicas recetas_medicas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.recetas_medicas
    ADD CONSTRAINT recetas_medicas_pkey PRIMARY KEY (id);


--
-- Name: registro_salud_diario registro_salud_diario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_salud_diario
    ADD CONSTRAINT registro_salud_diario_pkey PRIMARY KEY (id);


--
-- Name: registro_tomas registro_tomas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_tomas
    ADD CONSTRAINT registro_tomas_pkey PRIMARY KEY (id);


--
-- Name: tokens_acceso_medico tokens_acceso_medico_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_acceso_medico
    ADD CONSTRAINT tokens_acceso_medico_pkey PRIMARY KEY (id);


--
-- Name: tokens_acceso_medico tokens_acceso_medico_token_url_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_acceso_medico
    ADD CONSTRAINT tokens_acceso_medico_token_url_key UNIQUE (token_url);


--
-- Name: tokens_recuperacion tokens_recuperacion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_recuperacion
    ADD CONSTRAINT tokens_recuperacion_pkey PRIMARY KEY (id);


--
-- Name: tokens_recuperacion tokens_recuperacion_token_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_recuperacion
    ADD CONSTRAINT tokens_recuperacion_token_key UNIQUE (token);


--
-- Name: tomas_historial tomas_historial_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tomas_historial
    ADD CONSTRAINT tomas_historial_pkey PRIMARY KEY (id);


--
-- Name: paciente_doctor uniq_paciente_doctor; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_doctor
    ADD CONSTRAINT uniq_paciente_doctor UNIQUE (paciente_id, doctor_id);


--
-- Name: rate_limits uq_ip_accion; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rate_limits
    ADD CONSTRAINT uq_ip_accion UNIQUE (ip_address, accion);


--
-- Name: tomas_historial uq_toma; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tomas_historial
    ADD CONSTRAINT uq_toma UNIQUE (medicamento_id, fecha_programada, momento_dia);


--
-- Name: usuarios usuarios_email_key; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_email_key UNIQUE (email);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: visitas_medicas visitas_medicas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.visitas_medicas
    ADD CONSTRAINT visitas_medicas_pkey PRIMARY KEY (id);


--
-- Name: idx_citas_fecha; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_citas_fecha ON public.citas_laboratorios USING btree (fecha_hora, realizado);


--
-- Name: idx_citas_laboratorios_paciente_fecha; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_citas_laboratorios_paciente_fecha ON public.citas_laboratorios USING btree (paciente_id, estado, fecha_hora);


--
-- Name: idx_contactos_paciente; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_contactos_paciente ON public.contactos_emergencia USING btree (paciente_id);


--
-- Name: idx_cv_expira; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_cv_expira ON public.codigos_vinculacion USING btree (expira_at);


--
-- Name: idx_expediente_archivos_paciente; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_expediente_archivos_paciente ON public.expediente_archivos USING btree (paciente_id);


--
-- Name: idx_expediente_archivos_visita; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_expediente_archivos_visita ON public.expediente_archivos USING btree (visita_medica_id);


--
-- Name: idx_logs_auditoria_accion; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_logs_auditoria_accion ON public.logs_auditoria USING btree (accion, fecha_utc DESC);


--
-- Name: idx_logs_auditoria_paciente; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_logs_auditoria_paciente ON public.logs_auditoria USING btree (paciente_id, fecha_utc DESC);


--
-- Name: idx_logs_auditoria_usuario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_logs_auditoria_usuario ON public.logs_auditoria USING btree (usuario_id, fecha_utc DESC);


--
-- Name: idx_medicamentos_inventario; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_medicamentos_inventario ON public.medicamentos USING btree (paciente_id, activo, stock_actual, stock_minimo_alerta);


--
-- Name: idx_medicamentos_paciente; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_medicamentos_paciente ON public.medicamentos USING btree (paciente_id);


--
-- Name: idx_notificaciones_paciente; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_notificaciones_paciente ON public.notificaciones_alertas USING btree (paciente_id, created_at DESC);


--
-- Name: idx_paciente_doctor_d; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_paciente_doctor_d ON public.paciente_doctor USING btree (doctor_id);


--
-- Name: idx_paciente_doctor_p; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_paciente_doctor_p ON public.paciente_doctor USING btree (paciente_id);


--
-- Name: idx_pacientes_qr_token; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_pacientes_qr_token ON public.pacientes USING btree (qr_token);


--
-- Name: idx_rate_limits_busqueda; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_rate_limits_busqueda ON public.rate_limits USING btree (ip_address, accion, primer_intento);


--
-- Name: idx_recetas_doctor; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_recetas_doctor ON public.recetas_medicas USING btree (doctor_id);


--
-- Name: idx_recetas_paciente; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_recetas_paciente ON public.recetas_medicas USING btree (paciente_id);


--
-- Name: idx_registro_tomas_fecha; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_registro_tomas_fecha ON public.registro_tomas USING btree (fecha_hora_programada, estado);


--
-- Name: idx_rsd_paciente_fecha; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_rsd_paciente_fecha ON public.registro_salud_diario USING btree (paciente_id, created_at);


--
-- Name: idx_th_paciente_fecha_estado; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_th_paciente_fecha_estado ON public.tomas_historial USING btree (paciente_id, fecha_programada, estado);


--
-- Name: idx_tokens_lookup; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_tokens_lookup ON public.tokens_recuperacion USING btree (token, usado, expira_en);


--
-- Name: idx_tokens_medico_paciente; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_tokens_medico_paciente ON public.tokens_acceso_medico USING btree (paciente_id, expira_at DESC);


--
-- Name: idx_tokens_medico_pin; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_tokens_medico_pin ON public.tokens_acceso_medico USING btree (pin_seguridad, activo, expira_at);


--
-- Name: idx_tokens_medico_token; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_tokens_medico_token ON public.tokens_acceso_medico USING btree (token_url, activo, expira_at);


--
-- Name: idx_tomas_historial_hoy; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_tomas_historial_hoy ON public.tomas_historial USING btree (paciente_id, fecha_programada, hora_programada);


--
-- Name: idx_tomas_historial_omitidas; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_tomas_historial_omitidas ON public.tomas_historial USING btree (paciente_id, fecha_programada, estado, hora_programada);


--
-- Name: idx_visitas_medicas_paciente_fecha; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_visitas_medicas_paciente_fecha ON public.visitas_medicas USING btree (paciente_id, fecha_visita DESC);


--
-- Name: uq_pacientes_token_sos; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX uq_pacientes_token_sos ON public.pacientes USING btree (token_sos);


--
-- Name: citas_laboratorios citas_laboratorios_doctor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.citas_laboratorios
    ADD CONSTRAINT citas_laboratorios_doctor_id_fkey FOREIGN KEY (doctor_id) REFERENCES public.doctores(id) ON DELETE SET NULL;


--
-- Name: citas_laboratorios citas_laboratorios_familiar_responsable_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.citas_laboratorios
    ADD CONSTRAINT citas_laboratorios_familiar_responsable_id_fkey FOREIGN KEY (familiar_responsable_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: citas_laboratorios citas_laboratorios_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.citas_laboratorios
    ADD CONSTRAINT citas_laboratorios_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: contactos_emergencia contactos_emergencia_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contactos_emergencia
    ADD CONSTRAINT contactos_emergencia_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: doctores doctores_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.doctores
    ADD CONSTRAINT doctores_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: doctores doctores_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.doctores
    ADD CONSTRAINT doctores_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: expediente_archivos expediente_archivos_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.expediente_archivos
    ADD CONSTRAINT expediente_archivos_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: expediente_archivos expediente_archivos_subido_por_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.expediente_archivos
    ADD CONSTRAINT expediente_archivos_subido_por_usuario_id_fkey FOREIGN KEY (subido_por_usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: expediente_archivos expediente_archivos_visita_medica_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.expediente_archivos
    ADD CONSTRAINT expediente_archivos_visita_medica_id_fkey FOREIGN KEY (visita_medica_id) REFERENCES public.visitas_medicas(id) ON DELETE SET NULL;


--
-- Name: codigos_vinculacion fk_cv_paciente; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.codigos_vinculacion
    ADD CONSTRAINT fk_cv_paciente FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: registro_salud_diario fk_rsd_paciente; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_salud_diario
    ADD CONSTRAINT fk_rsd_paciente FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: tomas_historial fk_th_medicamento; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tomas_historial
    ADD CONSTRAINT fk_th_medicamento FOREIGN KEY (medicamento_id) REFERENCES public.medicamentos(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: tomas_historial fk_th_paciente; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tomas_historial
    ADD CONSTRAINT fk_th_paciente FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: logs_auditoria logs_auditoria_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logs_auditoria
    ADD CONSTRAINT logs_auditoria_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: logs_auditoria logs_auditoria_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.logs_auditoria
    ADD CONSTRAINT logs_auditoria_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: medicamentos medicamentos_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medicamentos
    ADD CONSTRAINT medicamentos_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: medicamentos medicamentos_receta_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medicamentos
    ADD CONSTRAINT medicamentos_receta_id_fkey FOREIGN KEY (receta_id) REFERENCES public.recetas_medicas(id) ON DELETE SET NULL;


--
-- Name: notificaciones_alertas notificaciones_alertas_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notificaciones_alertas
    ADD CONSTRAINT notificaciones_alertas_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: paciente_cuidador paciente_cuidador_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_cuidador
    ADD CONSTRAINT paciente_cuidador_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: paciente_cuidador paciente_cuidador_usuario_cuidador_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_cuidador
    ADD CONSTRAINT paciente_cuidador_usuario_cuidador_id_fkey FOREIGN KEY (usuario_cuidador_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: paciente_doctor paciente_doctor_doctor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_doctor
    ADD CONSTRAINT paciente_doctor_doctor_id_fkey FOREIGN KEY (doctor_id) REFERENCES public.doctores(id) ON DELETE CASCADE;


--
-- Name: paciente_doctor paciente_doctor_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.paciente_doctor
    ADD CONSTRAINT paciente_doctor_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: pacientes pacientes_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.pacientes
    ADD CONSTRAINT pacientes_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: recetas_medicas recetas_medicas_doctor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.recetas_medicas
    ADD CONSTRAINT recetas_medicas_doctor_id_fkey FOREIGN KEY (doctor_id) REFERENCES public.doctores(id) ON DELETE SET NULL;


--
-- Name: recetas_medicas recetas_medicas_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.recetas_medicas
    ADD CONSTRAINT recetas_medicas_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: registro_salud_diario registro_salud_diario_registrado_por_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_salud_diario
    ADD CONSTRAINT registro_salud_diario_registrado_por_usuario_id_fkey FOREIGN KEY (registrado_por_usuario_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: registro_tomas registro_tomas_medicamento_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_tomas
    ADD CONSTRAINT registro_tomas_medicamento_id_fkey FOREIGN KEY (medicamento_id) REFERENCES public.medicamentos(id) ON DELETE CASCADE;


--
-- Name: registro_tomas registro_tomas_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_tomas
    ADD CONSTRAINT registro_tomas_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: registro_tomas registro_tomas_registrado_por_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_tomas
    ADD CONSTRAINT registro_tomas_registrado_por_fkey FOREIGN KEY (registrado_por) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: registro_tomas registro_tomas_usuario_registro_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.registro_tomas
    ADD CONSTRAINT registro_tomas_usuario_registro_id_fkey FOREIGN KEY (usuario_registro_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- Name: tokens_acceso_medico tokens_acceso_medico_generado_por_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_acceso_medico
    ADD CONSTRAINT tokens_acceso_medico_generado_por_usuario_id_fkey FOREIGN KEY (generado_por_usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: tokens_acceso_medico tokens_acceso_medico_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_acceso_medico
    ADD CONSTRAINT tokens_acceso_medico_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: tokens_recuperacion tokens_recuperacion_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tokens_recuperacion
    ADD CONSTRAINT tokens_recuperacion_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: tomas_historial tomas_historial_registrado_por_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tomas_historial
    ADD CONSTRAINT tomas_historial_registrado_por_usuario_id_fkey FOREIGN KEY (registrado_por_usuario_id) REFERENCES public.usuarios(id);


--
-- Name: visitas_medicas visitas_medicas_paciente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.visitas_medicas
    ADD CONSTRAINT visitas_medicas_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES public.pacientes(id) ON DELETE CASCADE;


--
-- Name: visitas_medicas visitas_medicas_usuario_registra_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.visitas_medicas
    ADD CONSTRAINT visitas_medicas_usuario_registra_id_fkey FOREIGN KEY (usuario_registra_id) REFERENCES public.usuarios(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--



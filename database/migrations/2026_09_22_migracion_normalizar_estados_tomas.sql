-- ==============================================================================
-- Migración: Normalización de Estados de Tomas y Constraints
-- Archivo: 2026_09_22_migracion_normalizar_estados_tomas.sql
-- DBMS: PostgreSQL 13+
-- ==============================================================================

-- 1. Normalizar constraint de estados en tomas_historial
ALTER TABLE tomas_historial ALTER COLUMN estado TYPE VARCHAR(20);
ALTER TABLE tomas_historial DROP CONSTRAINT IF EXISTS tomas_historial_estado_check;
ALTER TABLE tomas_historial ADD CONSTRAINT tomas_historial_estado_check 
CHECK (estado IN ('pendiente', 'tomado', 'omitido', 'pospuesto', 'deshecho'));

-- 2. Normalizar registros existentes con valores legacy
UPDATE tomas_historial SET estado = 'tomado' WHERE estado = 'tomada';
UPDATE tomas_historial SET estado = 'pospuesto' WHERE estado = 'pospuesta';
UPDATE tomas_historial SET estado = 'deshecho' WHERE estado = 'deshecha';

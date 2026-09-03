<?php
require_once 'config/config.php';
require_once 'config/Database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Agregar columna 'activo' a medicamentos si no existe
    $db->exec("ALTER TABLE medicamentos ADD COLUMN IF NOT EXISTS activo BOOLEAN DEFAULT TRUE;");
    
    // 2. Crear índices de optimización para el Dashboard (Fase 4)
    $db->exec("CREATE INDEX IF NOT EXISTS idx_medicamentos_paciente ON medicamentos(paciente_id, activo);");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_tomas_fecha ON registro_tomas(medicamento_id, fecha_hora_programada, estado);");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_citas_paciente_fecha ON citas_laboratorios(paciente_id, fecha_hora, realizado);");
    
    echo "Refactorización de Fase 4 completada con éxito. (Índices y columnas añadidas).";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

try {
    $db = \Config\Database::getInstance()->getConnection();

    // 1. Inactividad: Marcar tomas no confirmadas como omitidas
    $db->beginTransaction();
    
    // Buscar tomas pendientes cuya hora programada ya pasó por más de 60 min
    $stmt = $db->prepare("
        SELECT th.id, th.paciente_id, m.nombre_comercial, p.nombre, p.apellidos, th.fecha_programada, th.hora_programada
        FROM tomas_historial th
        JOIN medicamentos m ON th.medicamento_id = m.id
        JOIN pacientes p ON th.paciente_id = p.id
        WHERE th.estado = 'pendiente' 
          AND th.fecha_programada = CURRENT_DATE
          AND (th.hora_programada + interval '60 minutes') < CURRENT_TIME
        FOR UPDATE SKIP LOCKED
    ");
    $stmt->execute();
    $omitidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($omitidas) > 0) {
        $updateStmt = $db->prepare("UPDATE tomas_historial SET estado = 'omitido', nota_incidencia = 'Marcado automáticamente por inactividad' WHERE id = :id");
        
        foreach ($omitidas as $toma) {
            $updateStmt->execute(['id' => $toma['id']]);
            // Simular notificación
            $msg = "[CRON] ALERTA: Toma omitida de {$toma['nombre_comercial']} para el paciente {$toma['nombre']} {$toma['apellidos']} programada a las {$toma['hora_programada']}.";
            error_log($msg);
            echo $msg . "\n";
        }
    }
    
    $db->commit();

    // 2. Desabastecimiento Predictivo
    // (stock_actual / (24 / frecuencia_horas * dosis_por_toma)) < 4
    $stmtStock = $db->query("
        SELECT m.id, m.nombre_comercial, m.stock_actual, m.dosis_por_toma, m.frecuencia_horas, p.nombre, p.apellidos
        FROM medicamentos m
        JOIN pacientes p ON m.paciente_id = p.id
        WHERE m.activo = TRUE AND m.stock_actual IS NOT NULL AND m.dosis_por_toma > 0 AND m.frecuencia_horas > 0
    ");
    
    $medicamentos = $stmtStock->fetchAll(PDO::FETCH_ASSOC);
    foreach ($medicamentos as $med) {
        // dosis diarias estimadas = (24 / frecuencia) * dosis_por_toma
        // Si frecuencia es 8 -> 3 tomas al día
        $tomasAlDia = floor(24 / $med['frecuencia_horas']);
        $consumoDiario = $tomasAlDia * $med['dosis_por_toma'];
        
        if ($consumoDiario > 0) {
            $diasRestantes = $med['stock_actual'] / $consumoDiario;
            if ($diasRestantes < 4) {
                // Simular notificación
                $msg = "[CRON] ALERTA DE STOCK: Quedan " . round($diasRestantes, 1) . " días de {$med['nombre_comercial']} para {$med['nombre']} {$med['apellidos']}. Stock actual: {$med['stock_actual']}.";
                error_log($msg);
                echo $msg . "\n";
            }
        }
    }

    echo "Cron ejecutado correctamente a las " . date('Y-m-d H:i:s') . "\n";

} catch (\Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "Error en cron: " . $e->getMessage() . "\n";
}

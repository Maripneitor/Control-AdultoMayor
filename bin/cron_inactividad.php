<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

try {
    $db = Database::getInstance()->getConnection();

    // 1. Buscar tomas vencidas por más de 60 minutos
    $stmt = $db->prepare("
        SELECT rt.id as toma_id, rt.paciente_id, m.nombre_comercial, p.nombre as paciente_nombre, rt.fecha_hora_programada
        FROM registro_tomas rt
        JOIN medicamentos m ON rt.medicamento_id = m.id
        JOIN pacientes p ON rt.paciente_id = p.id
        WHERE rt.estado = 'pendiente'
          AND rt.fecha_hora_programada < (NOW() - INTERVAL '60 minutes')
          AND DATE(rt.fecha_hora_programada) = CURRENT_DATE
    ");
    $stmt->execute();
    $tomasVencidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Buscar pacientes sin registro de constantes vitales en las últimas 24 horas
    $stmtVitales = $db->prepare("
        SELECT p.id as paciente_id, p.nombre as paciente_nombre
        FROM pacientes p
        WHERE p.id NOT IN (
            SELECT paciente_id 
            FROM registro_salud_diario 
            WHERE created_at >= (NOW() - INTERVAL '24 hours')
        )
    ");
    $stmtVitales->execute();
    $pacientesSinVitales = $stmtVitales->fetchAll(PDO::FETCH_ASSOC);

    // Procesar alertas por paciente
    $alertasPorPaciente = [];

    foreach ($tomasVencidas as $toma) {
        $pid = $toma['paciente_id'];
        if (!isset($alertasPorPaciente[$pid])) {
            $alertasPorPaciente[$pid] = [
                'nombre' => $toma['paciente_nombre'],
                'tomas' => [],
                'vitales_faltantes' => false
            ];
        }
        $alertasPorPaciente[$pid]['tomas'][] = "Medicamento: {$toma['nombre_comercial']} (Programado: {$toma['fecha_hora_programada']})";
    }

    foreach ($pacientesSinVitales as $paciente) {
        $pid = $paciente['paciente_id'];
        if (!isset($alertasPorPaciente[$pid])) {
            $alertasPorPaciente[$pid] = [
                'nombre' => $paciente['paciente_nombre'],
                'tomas' => [],
                'vitales_faltantes' => true
            ];
        } else {
            $alertasPorPaciente[$pid]['vitales_faltantes'] = true;
        }
    }

    // 3. Notificar a Cuidadores
    if (!empty($alertasPorPaciente)) {
        foreach ($alertasPorPaciente as $pid => $data) {
            // Obtener cuidadores del paciente
            $stmtCuidadores = $db->prepare("
                SELECT u.email, u.nombre
                FROM paciente_cuidador pc
                JOIN usuarios u ON pc.usuario_cuidador_id = u.id
                WHERE pc.paciente_id = :pid
            ");
            $stmtCuidadores->execute(['pid' => $pid]);
            $cuidadores = $stmtCuidadores->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cuidadores as $cuidador) {
                $to = $cuidador['email'];
                $subject = "Alerta de Inactividad - VitalSenior: " . $data['nombre'];
                $message = "Hola {$cuidador['nombre']},\n\n";
                $message .= "Hemos detectado inactividad reciente para el paciente: {$data['nombre']}.\n\n";
                
                if (!empty($data['tomas'])) {
                    $message .= "Las siguientes tomas de medicamento están retrasadas:\n";
                    foreach ($data['tomas'] as $t) {
                        $message .= "- $t\n";
                    }
                    $message .= "\n";
                }

                if ($data['vitales_faltantes']) {
                    $message .= "No se han registrado signos vitales en las últimas 24 horas.\n\n";
                }

                $message .= "Por favor, póngase en contacto con el paciente para verificar su bienestar.\n\n";
                $message .= "Saludos,\nEl equipo de VitalSenior";

                $headers = "From: noreply@vitalsenior.local\r\n";
                $headers .= "Reply-To: noreply@vitalsenior.local\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();

                // Usamos mail() como fallback al no tener confirmación de PHPMailer
                mail($to, $subject, $message, $headers);
            }
        }
    }

    echo "Cron ejecutado correctamente. Se verificaron inactividades.\n";

} catch (Exception $e) {
    echo "Error en cron_inactividad: " . $e->getMessage() . "\n";
}

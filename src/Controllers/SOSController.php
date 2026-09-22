<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class SOSController extends Controller {

    /**
     * POST /sos/trigger
     * Desata una alerta de emergencia desde el backend hacia el familiar responsable
     */
    public function triggerSOS(): void {
        // Se espera que el usuario esté autenticado (Adulto Mayor)
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId === 0) {
            $this->jsonResponse(['error' => 'No autorizado'], 401);
            return;
        }

        // Límite estricto para evitar spam de alertas (1 alerta cada 3 minutos)
        $ip = getRealIp();
        if (!\App\Core\RateLimiter::check($ip, 'sos_trigger', 1, 3)) {
            $this->jsonResponse(['error' => 'Alerta ya enviada recientemente.'], 429);
            return;
        }

        // Buscar al familiar vinculado
        // $db = \Config\Database::getInstance()->getConnection();
        // $stmt = $db->prepare("SELECT f.telefono, p.nombre_completo as paciente_nombre FROM vinculaciones v JOIN usuarios f ON v.familiar_id = f.id JOIN pacientes p ON v.paciente_id = p.id WHERE p.usuario_id = ?");
        // $familiar = $stmt->fetch();
        
        $familiarTelefono = "+1234567890"; // Mock
        $pacienteNombre = "Don Juan"; // Mock

        if ($familiarTelefono) {
            $mensaje = "🚨 ALERTA VITAL: $pacienteNombre ha presionado el Botón SOS. Por favor, comunícate de inmediato o revisa su ficha médica.";
            $this->dispararAlertaWhatsApp($familiarTelefono, $mensaje);
        }

        $this->jsonResponse(['success' => true, 'message' => 'Alerta SOS enviada al familiar.']);
    }

    private function dispararAlertaWhatsApp(string $numero, string $mensaje): void {
        // Llamada a Twilio / Meta API desde el servidor asegurando entrega
        // ... (Similar a enviarMensajeTwilio en OTPController) ...
        error_log("Mock SOS a $numero: $mensaje");
    }

    private function jsonResponse(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

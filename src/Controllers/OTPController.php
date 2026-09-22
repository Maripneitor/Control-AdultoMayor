<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class OTPController extends Controller {

    /**
     * POST /otp/request
     * Solicita el envío de un código OTP vía WhatsApp/SMS
     */
    public function requestOTP(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->jsonResponse(['error' => 'CSRF Token inválido'], 403);
            return;
        }

        $telefono = trim($_POST['telefono'] ?? '');
        if (empty($telefono)) {
            $this->jsonResponse(['error' => 'El teléfono es requerido'], 400);
            return;
        }

        // Rate limiting de OTP (Ej. Máximo 3 solicitudes por hora)
        $ip = getRealIp();
        if (!\App\Core\RateLimiter::check($ip, 'otp_request', 3, 60)) {
            $this->jsonResponse(['error' => 'Demasiados intentos. Espera una hora.'], 429);
            return;
        }

        // TODO: Buscar usuario por teléfono en la base de datos
        // $usuario = Usuario::findByTelefono($telefono);
        // if (!$usuario) ...

        // Generar PIN de 6 dígitos
        $codigoOtp = (string) random_int(100000, 999999);

        // Guardar en la DB: otp_verifications
        // $db = \Config\Database::getInstance()->getConnection();
        // $stmt = $db->prepare("INSERT INTO otp_verifications (usuario_id, codigo_otp, proposito, metodo, expira_en) VALUES (...)");

        // Integración con Twilio/Meta API (Simulada)
        $this->enviarMensajeTwilio($telefono, "Tu código de recuperación para VitalSenior es: $codigoOtp. Expira en 15 minutos.");

        $this->jsonResponse(['success' => true, 'message' => 'Código OTP enviado con éxito.']);
    }

    /**
     * POST /otp/verify
     * Verifica el código introducido por el usuario
     */
    public function verifyOTP(): void {
        $telefono = trim($_POST['telefono'] ?? '');
        $codigo = trim($_POST['codigo_otp'] ?? '');

        if (empty($telefono) || empty($codigo)) {
            $this->jsonResponse(['error' => 'Faltan parámetros'], 400);
            return;
        }

        // Lógica de verificación en DB
        // $stmt = $db->prepare("SELECT * FROM otp_verifications WHERE usuario_id = ? AND codigo_otp = ? AND expira_en > NOW() AND usado = FALSE");
        // if ($stmt->rowCount() > 0) { ... marcar como usado y crear sesión ... }

        $this->jsonResponse(['success' => true, 'message' => 'OTP Validado. Puedes cambiar tu contraseña.']);
    }

    private function enviarMensajeTwilio(string $numero, string $mensaje): void {
        // Ejemplo de implementación de Twilio API
        /*
        $sid = getenv('TWILIO_SID');
        $token = getenv('TWILIO_TOKEN');
        $twilio = new \Twilio\Rest\Client($sid, $token);

        $twilio->messages->create(
            $numero, // To
            [
                "from" => getenv('TWILIO_WHATSAPP_NUMBER'),
                "body" => $mensaje
            ]
        );
        */
        error_log("Mock Twilio Message a $numero: $mensaje");
    }

    private function jsonResponse(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

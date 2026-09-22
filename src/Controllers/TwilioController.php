<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\TwilioService;
use Config\Database;
use PDO;

class TwilioController extends Controller {
    private TwilioService $twilioService;
    private PDO $db;

    public function __construct() {
        $this->twilioService = new TwilioService();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * POST /api/otp/send
     * Genera y envía un código OTP de 6 dígitos por WhatsApp
     */
    public function sendWhatsAppOTP(): void {
        // Rate Limiting Estricto: máximo 3 solicitudes de OTP por IP en 15 minutos (Control de costos Twilio)
        \App\Security\RateLimiter::enforce('otp_send', 3, 15);

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $telefono = trim($input['telefono'] ?? '');
        $proposito = trim($input['proposito'] ?? 'validacion_whatsapp');

        if (empty($telefono) || !preg_match('/^\+?[0-9]{10,15}$/', $telefono)) {
            $this->jsonResponse(['error' => 'Número de teléfono inválido o formato internacional incorrecto.'], 400);
            return;
        }

        try {
            // Generar código numérico criptográficamente seguro
            $codigoOtp = (string) random_int(100000, 999999);
            // Expira en 15 minutos (UTC)
            $expiraEn = (new \DateTime('now', new \DateTimeZone('UTC')))->modify('+15 minutes')->format('Y-m-d H:i:sP');

            // Invalidar OTPs previos pendientes para este teléfono
            $stmtCancel = $this->db->prepare(
                "UPDATE otp_verificaciones SET usado = TRUE WHERE telefono = :tel AND usado = FALSE"
            );
            $stmtCancel->execute([':tel' => $telefono]);

            // Insertar nuevo registro OTP
            $stmt = $this->db->prepare(
                "INSERT INTO otp_verificaciones (telefono, codigo_otp, proposito, canal, expira_en, usado) 
                 VALUES (:telefono, :codigo, :proposito, 'whatsapp', :expira, FALSE)"
            );
            $stmt->execute([
                ':telefono' => $telefono,
                ':codigo' => $codigoOtp,
                ':proposito' => $proposito,
                ':expira' => $expiraEn
            ]);

            // Mensaje para el usuario mayor o familiar
            $mensaje = "🔐 VitalSenior: Tu código de verificación es *{$codigoOtp}*. Expira en 15 minutos. No lo compartas con nadie.";

            // Disparo vía Twilio WhatsApp
            $envio = $this->twilioService->sendWhatsAppMessage($telefono, $mensaje);

            if (!$envio['success']) {
                $this->jsonResponse(['error' => $envio['error'] ?? 'No se pudo enviar el código por WhatsApp.'], 502);
                return;
            }

            $this->jsonResponse([
                'success' => true,
                'message' => 'Código de verificación enviado vía WhatsApp.',
                'simulated' => $envio['simulated'] ?? false
            ]);
        } catch (\Throwable $e) {
            error_log('[TwilioController::sendWhatsAppOTP] ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Error interno al procesar la verificación OTP.'], 500);
        }
    }

    /**
     * POST /api/otp/verify
     * Valida el OTP ingresado por el usuario
     */
    public function verifyWhatsAppOTP(): void {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $telefono = trim($input['telefono'] ?? '');
        $codigo = trim($input['codigo_otp'] ?? '');

        if (empty($telefono) || empty($codigo)) {
            $this->jsonResponse(['error' => 'Teléfono y código son requeridos.'], 400);
            return;
        }

        try {
            // Buscar el código vigente en UTC
            $stmt = $this->db->prepare(
                "SELECT id, expira_en, intentos FROM otp_verificaciones 
                 WHERE telefono = :telefono AND codigo_otp = :codigo AND usado = FALSE AND expira_en > NOW()
                 ORDER BY creado_en DESC LIMIT 1"
            );
            $stmt->execute([':telefono' => $telefono, ':codigo' => $codigo]);
            $otp = $stmt->fetch();

            if (!$otp) {
                // Registrar intento fallido
                $stmtInc = $this->db->prepare(
                    "UPDATE otp_verificaciones SET intentos = intentos + 1 WHERE telefono = :telefono AND usado = FALSE"
                );
                $stmtInc->execute([':telefono' => $telefono]);

                $this->jsonResponse(['error' => 'Código OTP inválido o expirado.'], 400);
                return;
            }

            // Marcar OTP como utilizado
            $stmtUsed = $this->db->prepare("UPDATE otp_verificaciones SET usado = TRUE WHERE id = :id");
            $stmtUsed->execute([':id' => $otp['id']]);

            // Si hay un usuario registrado con este teléfono, marcar WhatsApp como verificado
            $stmtUser = $this->db->prepare("UPDATE usuarios SET whatsapp_verificado = TRUE WHERE telefono = :telefono");
            $stmtUser->execute([':telefono' => $telefono]);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Número de WhatsApp verificado exitosamente.'
            ]);
        } catch (\Throwable $e) {
            error_log('[TwilioController::verifyWhatsAppOTP] ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Error al verificar el código.'], 500);
        }
    }

    /**
     * POST /api/sos
     * Botón SOS (Vista 4): Recibe petición POST y dispara la alerta de emergencia vía Twilio
     */
    public function triggerSOS(): void {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $pacienteId = trim($input['paciente_id'] ?? '');

        // Obtener ID del usuario logueado o validar el paciente solicitado
        $usuarioSesionId = $_SESSION['user_id'] ?? null;
        if (!$usuarioSesionId && empty($pacienteId)) {
            $this->jsonResponse(['error' => 'Petición no autorizada.'], 401);
            return;
        }

        try {
            // Resolver paciente: si no viene paciente_id, buscar el paciente asociado al usuario en sesión
            if (empty($pacienteId)) {
                $stmtPac = $this->db->prepare("SELECT id, nombre, apellidos, qr_token FROM pacientes WHERE usuario_id = :uid LIMIT 1");
                $stmtPac->execute([':uid' => $usuarioSesionId]);
                $paciente = $stmtPac->fetch(PDO::FETCH_ASSOC);
            } else {
                $stmtPac = $this->db->prepare("SELECT id, nombre, apellidos, qr_token FROM pacientes WHERE id = :pid LIMIT 1");
                $stmtPac->execute([':pid' => $pacienteId]);
                $paciente = $stmtPac->fetch(PDO::FETCH_ASSOC);
            }

            if (!$paciente) {
                $this->jsonResponse(['error' => 'Paciente no localizado para emitir alerta.'], 404);
                return;
            }

            $pacienteNombre = trim("{$paciente['nombre']} {$paciente['apellidos']}");
            $pacienteToken = (string)($paciente['qr_token'] ?? '');

            // Obtener contactos de emergencia prioritarios
            $stmtFam = $this->db->prepare(
                "SELECT nombre, telefono 
                 FROM contactos_emergencia 
                 WHERE paciente_id = :pid AND telefono IS NOT NULL AND telefono != ''
                 ORDER BY es_prioritario DESC, id ASC"
            );
            $stmtFam->execute([':pid' => $paciente['id']]);
            $contactos = $stmtFam->fetchAll(PDO::FETCH_ASSOC);

            // Si no hay en contactos_emergencia, buscar en paciente_cuidador
            if (empty($contactos)) {
                $stmtCuid = $this->db->prepare(
                    "SELECT u.nombre, NULL as telefono 
                     FROM paciente_cuidador pc 
                     JOIN usuarios u ON pc.usuario_cuidador_id = u.id 
                     WHERE pc.paciente_id = :pid"
                );
                $stmtCuid->execute([':pid' => $paciente['id']]);
                $contactos = $stmtCuid->fetchAll(PDO::FETCH_ASSOC);
            }

            $appUrl = defined('APP_URL') ? APP_URL : (getenv('APP_URL') ?: 'http://localhost/Control-AdultoMayor/public');
            $fichaUrl = "{$appUrl}/emergencia?token={$pacienteToken}";

            $mensajeAlerta = "🚨 *ALERTA DE EMERGENCIA - VITALSENIOR*\n\n"
                           . "El paciente *{$pacienteNombre}* ha presionado el *Botón SOS*.\n"
                           . "Por favor comunícate inmediatamente o consulta su Ficha Médica de Emergencia aquí:\n"
                           . "👉 {$fichaUrl}\n\n"
                           . "Hora de emisión (UTC): " . gmdate('Y-m-d H:i:s');

            $enviados = 0;
            $errores = [];

            if (empty($contactos)) {
                // Fallback a teléfono de emergencia configurado en .env si no hay contacto directo
                $adminPhone = getenv('EMERGENCY_BACKUP_PHONE');
                if ($adminPhone) {
                    $this->twilioService->sendWhatsAppMessage($adminPhone, $mensajeAlerta);
                    $enviados++;
                }
            } else {
                foreach ($contactos as $familiar) {
                    $tel = trim((string)($familiar['telefono'] ?? ''));
                    if (empty($tel)) continue;

                    $res = $this->twilioService->sendWhatsAppMessage($tel, $mensajeAlerta);
                    if ($res['success']) {
                        $enviados++;
                    } else {
                        // Reintento por SMS tradicional
                        $smsRes = $this->twilioService->sendSMSMessage($tel, "ALERTA SOS VitalSenior: $pacienteNombre activo el boton SOS. Ficha: $fichaUrl");
                        if ($smsRes['success']) {
                            $enviados++;
                        } else {
                            $errores[] = "Fallo al notificar a {$familiar['nombre']}";
                        }
                    }
                }
            }

            // Registrar en logs de auditoría
            \App\Security\AuditLogger::log(
                'SOS_TRIGGERED', 
                (int)$paciente['id'], 
                "Alerta SOS detonada. Notificaciones enviadas: {$enviados}."
            );

            $this->jsonResponse([
                'success' => true,
                'message' => 'Alerta SOS despachada exitosamente a los contactos de emergencia.',
                'contactos_notificados' => $enviados,
                'alertas_con_error' => $errores
            ]);
        } catch (\Throwable $e) {
            error_log('[TwilioController::triggerSOS] ' . $e->getMessage());
            $this->jsonResponse(['error' => 'Error del servidor al procesar la alerta SOS.'], 500);
        }
    }

    /**
     * POST /api/twilio/webhook
     * Maneja webhooks entrantes de Twilio con validación estricta de firma X-Twilio-Signature
     */
    public function handleWebhook(): void {
        $signature = $_SERVER['HTTP_X_TWILIO_SIGNATURE'] ?? '';
        $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if (!$this->twilioService->validateTwilioSignature($currentUrl, $_POST, $signature)) {
            error_log('[TwilioController::handleWebhook] Firma X-Twilio-Signature inválida.');
            http_response_code(403);
            header('Content-Type: text/plain');
            echo "Forbidden: Invalid Signature";
            exit;
        }

        // Procesar estado del mensaje (delivered, read, failed, etc.)
        $messageSid = $_POST['MessageSid'] ?? '';
        $messageStatus = $_POST['MessageStatus'] ?? '';

        error_log("[Twilio Webhook] Mensaje SID $messageSid cambio a estado: $messageStatus");

        http_response_code(200);
        header('Content-Type: text/xml');
        echo "<Response></Response>";
        exit;
    }

    private function jsonResponse(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

<?php
declare(strict_types=1);

namespace App\Services;

class TwilioService {
    private string $accountSid;
    private string $authToken;
    private string $whatsappFrom;
    private string $smsFrom;

    public function __construct() {
        $this->accountSid = getenv('TWILIO_ACCOUNT_SID') ?: '';
        $this->authToken = getenv('TWILIO_AUTH_TOKEN') ?: '';
        // Número WhatsApp configurado en Twilio (ej: "whatsapp:+14155238886")
        $this->whatsappFrom = getenv('TWILIO_WHATSAPP_FROM') ?: 'whatsapp:+14155238886';
        $this->smsFrom = getenv('TWILIO_SMS_FROM') ?: '+14155238886';
    }

    /**
     * Valida la firma X-Twilio-Signature en webhooks entrantes para evitar suplantaciones
     */
    public function validateTwilioSignature(string $expectedUrl, array $postData, string $signature): bool {
        if (empty($this->authToken)) {
            return false;
        }

        // Algoritmo oficial de Twilio: URL completa + parámetros POST ordenados alfabéticamente
        ksort($postData);
        $dataString = $expectedUrl;
        foreach ($postData as $key => $value) {
            $dataString .= $key . $value;
        }

        $calculatedSignature = base64_encode(hash_hmac('sha1', $dataString, $this->authToken, true));
        return hash_equals($calculatedSignature, $signature);
    }

    /**
     * Enviar mensaje por WhatsApp vía cURL a la API REST de Twilio
     */
    public function sendWhatsAppMessage(string $toPhone, string $message): array {
        // Asegurar prefijo "whatsapp:" y formato internacional
        $cleanPhone = preg_replace('/[^0-9+]/', '', $toPhone);
        if (strpos($cleanPhone, 'whatsapp:') !== 0) {
            $toFormatted = 'whatsapp:' . $cleanPhone;
        } else {
            $toFormatted = $cleanPhone;
        }

        return $this->dispatchTwilioRequest([
            'From' => $this->whatsappFrom,
            'To' => $toFormatted,
            'Body' => $message
        ]);
    }

    /**
     * Enviar SMS como fallback de emergencia
     */
    public function sendSMSMessage(string $toPhone, string $message): array {
        $cleanPhone = preg_replace('/[^0-9+]/', '', $toPhone);
        return $this->dispatchTwilioRequest([
            'From' => $this->smsFrom,
            'To' => $cleanPhone,
            'Body' => $message
        ]);
    }

    /**
     * Petición HTTP POST autenticada con cURL nativo a Twilio
     */
    private function dispatchTwilioRequest(array $params): array {
        if (empty($this->accountSid) || empty($this->authToken)) {
            error_log('[TWILIO] Claves no configuradas en .env. Modo Simulación activo: ' . json_encode($params));
            return [
                'success' => true,
                'simulated' => true,
                'message' => 'Modo simulación local: mensaje registrado en log.'
            ];
        }

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "{$this->accountSid}:{$this->authToken}",
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            error_log("[TWILIO cURL Error] $curlError");
            return [
                'success' => false,
                'error' => 'Error de conexión con el proveedor de telecomunicaciones.'
            ];
        }

        $result = json_decode((string)$response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'sid' => $result['sid'] ?? null,
                'status' => $result['status'] ?? 'queued'
            ];
        }

        error_log("[TWILIO API Error HTTP $httpCode]: " . json_encode($result));
        return [
            'success' => false,
            'error' => $result['message'] ?? 'Fallo al procesar el envío con Twilio.'
        ];
    }
}

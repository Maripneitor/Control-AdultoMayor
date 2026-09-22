<?php
declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailNotificationService {
    public static function enviarAlertaOmitida(string $emailDestino, string $nombreMedicamento, string $hora, string $pacienteNombre = 'Adulto Mayor'): bool {
        try {
            if (empty($emailDestino) || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            // Si PHPMailer no está instalado o en entorno local sin SMTP configurado, registrar simulación
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                error_log("[EMAIL SIMULADO] Enviando alerta a {$emailDestino}: Dosis omitida de {$nombreMedicamento} para {$pacienteNombre} a las {$hora}.");
                return true;
            }

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.ejemplo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER') ?: 'alertas@vitalsenior.com';
            $mail->Password   = getenv('SMTP_PASS') ?: 'secreto123';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)(getenv('SMTP_PORT') ?: 587);
            $mail->Timeout    = 5; // Timeout corto para evitar bloqueos
            $mail->CharSet    = 'UTF-8';
            
            $mail->setFrom(getenv('SMTP_FROM') ?: 'alertas@vitalsenior.com', 'VitalSenior Notificaciones');
            $mail->addAddress($emailDestino);
            $mail->isHTML(true);
            $mail->Subject = "🚨 ALERTA CRÍTICA: Dosis omitida de {$nombreMedicamento} - {$pacienteNombre}";
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #e2e8f0; rounded: 12px;'>
                    <h2 style='color: #dc2626;'>⚠️ Alerta Médica de Dosis Omitida</h2>
                    <p>Estimado contacto de emergencia / cuidador:</p>
                    <p>Le informamos que se ha detectado una <strong>toma no registrada</strong> para el paciente <strong>{$pacienteNombre}</strong>.</p>
                    <ul>
                        <li><strong>Medicamento:</strong> {$nombreMedicamento}</li>
                        <li><strong>Hora programada:</strong> {$hora}</li>
                    </ul>
                    <p>Por favor, comuníquese con el paciente a la brevedad para verificar su estado de salud.</p>
                    <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                    <p style='font-size: 12px; color: #64748b;'>Sistema VitalSenior • Cuidado y Asistencia Geriátrica</p>
                </div>
            ";
            
            $mail->send();
            error_log("Alerta enviada correctamente a {$emailDestino}");
            return true;
        } catch (\Exception $e) {
            error_log("Fallo en EmailNotificationService: " . $e->getMessage());
            return false;
        } catch (\Throwable $t) {
            error_log("Error fatal en EmailNotificationService: " . $t->getMessage());
            return false;
        }
    }

    public static function enviarCorreoRecuperacion(string $emailDestino, string $nombreUsuario, string $token): void {
        try {
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                error_log("[SIMULACRO CORREO] Recuperación para $emailDestino - Token: $token");
                return;
            }

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.ejemplo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'alertas@vitalsenior.com';
            $mail->Password   = 'secreto123';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            $mail->setFrom('seguridad@vitalsenior.com', 'VitalSenior Soporte');
            $mail->addAddress($emailDestino);
            $mail->isHTML(true);
            $mail->Subject = 'VitalSenior - Recuperación de Contraseña';
            
            $urlRecuperacion = APP_URL . '/new-password?token=' . $token;
            
            ob_start();
            require __DIR__ . '/../Views/emails/recovery_email.php';
            $htmlBody = ob_get_clean();
            
            $mail->Body = $htmlBody;
            
            $mail->send();
        } catch (\Throwable $t) {
            error_log("Error al enviar correo de recuperación: " . $t->getMessage());
        }
    }
}

<?php
declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailNotificationService {
    public static function enviarAlertaOmitida(string $emailDestino, string $nombreMedicamento, string $hora): void {
        try {
            // Simulamos si PHPMailer no está autoloadeado aún o hay un error.
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                error_log("[SIMULACRO CORREO] Enviando correo a $emailDestino: Dosis Omitida de $nombreMedicamento a las $hora");
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
            $mail->Timeout    = 5; // Timeout corto para evitar bloqueos
            
            $mail->setFrom('alertas@vitalsenior.com', 'Sistema VitalSenior');
            $mail->addAddress($emailDestino);
            $mail->isHTML(true);
            $mail->Subject = 'ALERTA CRÍTICA: Dosis omitida de ' . $nombreMedicamento;
            $mail->Body    = "<b>Alerta Crítica:</b> No se ha registrado la toma de $nombreMedicamento programada para las $hora.";
            
            $mail->send();
            error_log("Alerta enviada correctamente a $emailDestino");
        } catch (\Exception $e) {
            error_log("Fallo crítico en EmailNotificationService: " . $e->getMessage());
        } catch (\Throwable $t) {
            error_log("Error fatal en EmailNotificationService: " . $t->getMessage());
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

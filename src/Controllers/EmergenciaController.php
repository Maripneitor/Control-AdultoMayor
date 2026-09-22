<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\ContactoEmergencia;
use App\Models\Doctor;
use App\Security\RateLimiter;

class EmergenciaController {
    private Paciente $pacienteModel;
    private Medicamento $medicamentoModel;
    private ContactoEmergencia $contactoModel;
    private Doctor $doctorModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
        $this->medicamentoModel = new Medicamento();
        $this->contactoModel = new ContactoEmergencia();
        $this->doctorModel = new Doctor();
    }

    public function showPublicCard(): void {
        // 1. Rate Limiting estricto anti-scraping: Máximo 30 solicitudes cada 5 minutos por IP
        RateLimiter::enforce('qr_public_access', 30, 5);

        // 2. Encabezados de privacidad y no-indexación en buscadores
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            http_response_code(404);
            $this->render404('Código de emergencia no especificado.');
            return;
        }

        $paciente = $this->pacienteModel->findByQrToken($token);

        if (!$paciente) {
            http_response_code(404);
            $this->render404('La ficha médica de auxilio solicitada no existe o el enlace ha caducado.');
            return;
        }

        $pacienteId = (int)$paciente['id'];
        $contactos = $this->contactoModel->getByPacienteId($pacienteId);
        $medicamentosActivos = $this->medicamentoModel->getActiveByPacienteId($pacienteId);
        $doctores = $this->doctorModel->getByPacienteId($pacienteId);

        require_once __DIR__ . '/../Views/emergencia/ficha_publica.php';
    }

    private function render404(string $mensaje): void {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Ficha no encontrada | VitalSenior</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-sans">
            <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4 text-3xl font-black">✚</div>
                <h1 class="text-2xl font-black text-slate-800 mb-2">Ficha de Emergencia Inaccesible</h1>
                <p class="text-slate-600 text-sm mb-6 leading-relaxed">{$mensaje}</p>
                <div class="bg-amber-50 rounded-xl p-3 border border-amber-200 text-xs text-amber-800 font-medium">
                    En caso de requerir asistencia urgente, comunícate directamente con la red de cuidadores o familiares del paciente.
                </div>
            </div>
        </body>
        </html>
HTML;
    }
}

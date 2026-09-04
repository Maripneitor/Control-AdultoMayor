<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Medicamento;
use App\Models\ContactoEmergencia;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Middlewares\AuthMiddleware;

class PacienteController extends Controller {

    public function descargarExpediente() {
        if (!isset($_GET['id'])) {
            $this->setFlash('ID de paciente no válido', 'danger');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        $pacienteId = (int)$_GET['id'];
        $usuarioId = $_SESSION['user_id'];

        if (!AuthMiddleware::autorizarAccesoPaciente($pacienteId, $usuarioId)) {
            $this->setFlash('No tienes permiso para acceder a este expediente', 'danger');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($pacienteId);

        if (!$paciente) {
            $this->setFlash('Paciente no encontrado', 'danger');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        $medicamentoModel = new Medicamento();
        $medicamentos = $medicamentoModel->getAllByPaciente($pacienteId);

        $contactoModel = new ContactoEmergencia();
        $contactos = $contactoModel->getByPaciente($pacienteId);

        $saludModel = new \App\Models\RegistroSaludDiario();
        $registrosSalud = $saludModel->getAllByPacienteId($pacienteId, 7); // Últimos 7 días

        // Generar URL para QuickChart
        $fechas = [];
        $sisData = [];
        $diaData = [];
        $glucosaData = [];
        
        // Invertimos para que el gráfico vaya cronológicamente de izquierda a derecha
        foreach (array_reverse($registrosSalud) as $reg) {
            $fechas[] = date('d/m', strtotime($reg['created_at']));
            $sisData[] = $reg['presion_sistolica'] ?? 'null';
            $diaData[] = $reg['presion_diastolica'] ?? 'null';
            $glucosaData[] = $reg['glucosa'] ?? 'null';
        }

        $chartConfig = [
            'type' => 'line',
            'data' => [
                'labels' => $fechas,
                'datasets' => [
                    ['label' => 'Sistólica', 'data' => $sisData, 'borderColor' => 'red', 'fill' => false],
                    ['label' => 'Diastólica', 'data' => $diaData, 'borderColor' => 'blue', 'fill' => false],
                    ['label' => 'Glucosa', 'data' => $glucosaData, 'borderColor' => 'green', 'fill' => false]
                ]
            ],
            'options' => [
                'title' => ['display' => true, 'text' => 'Evolución últimos 7 registros']
            ]
        ];

        $chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig)) . '&w=600&h=300';

        // Promedios
        $avgSis = !empty($sisData) && max($sisData) !== 'null' ? array_sum(array_filter($sisData, 'is_numeric')) / max(1, count(array_filter($sisData, 'is_numeric'))) : 0;
        $avgDia = !empty($diaData) && max($diaData) !== 'null' ? array_sum(array_filter($diaData, 'is_numeric')) / max(1, count(array_filter($diaData, 'is_numeric'))) : 0;
        $avgGluc = !empty($glucosaData) && max($glucosaData) !== 'null' ? array_sum(array_filter($glucosaData, 'is_numeric')) / max(1, count(array_filter($glucosaData, 'is_numeric'))) : 0;

        // Si dompdf no está instalado (el autoload fallará si no está). 
        // Verificamos si la clase existe antes de instanciar.
        if (!class_exists('Dompdf\Dompdf')) {
            $this->setFlash('El motor PDF (dompdf) no está instalado. Ejecute "composer install".', 'danger');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }

        try {
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            ob_start();
            require __DIR__ . '/../Views/pacientes/pdf_resumen.php';
            $html = ob_get_clean();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("expediente_{$paciente['nombre']}.pdf", ["Attachment" => true]);
        } catch (\Exception $e) {
            $this->setFlash('Error al generar el PDF: ' . $e->getMessage(), 'danger');
            header("Location: " . APP_URL . "/dashboard");
            exit;
        }
    }
}

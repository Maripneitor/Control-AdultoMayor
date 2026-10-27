<?php
declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;
use InvalidArgumentException;

/**
 * Servicio centralizado para generación y compilación de reportes PDF en VitalSenior.
 * 
 * Reglas de Arquitectura:
 * - Aislado de layouts web HTML principales (sin Tailwind CDN, scripts JS o navegación).
 * - Renderiza plantillas PHP/HTML puras con estilos CSS embebidos compatibles con Dompdf.
 */
class PdfReportService {

    private Options $options;

    public function __construct() {
        $this->options = new Options();
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('isRemoteEnabled', true);
        $this->options->set('defaultFont', 'DejaVu Sans');
        $this->options->set('chroot', realpath(__DIR__ . '/../../'));
    }

    /**
     * Compila una vista/plantilla PHP aislando las variables en su propio scope.
     *
     * @param string $templatePath Ruta absoluta al archivo de plantilla PHP.
     * @param array<string, mixed> $datos Variables a inyectar en la plantilla.
     * @return string Código HTML resultante.
     * @throws InvalidArgumentException Si la plantilla no existe.
     */
    public function compilarPlantilla(string $templatePath, array $datos = []): string {
        if (!file_exists($templatePath)) {
            throw new InvalidArgumentException("La plantilla de reporte PDF no existe: {$templatePath}");
        }

        extract($datos, EXTR_SKIP);
        ob_start();
        try {
            require $templatePath;
            return (string)ob_get_clean();
        } catch (\Throwable $t) {
            ob_end_clean();
            throw new RuntimeException("Error al compilar la plantilla PDF [{$templatePath}]: " . $t->getMessage(), 0, $t);
        }
    }

    /**
     * Genera y envía el reporte PDF del resumen clínico / expediente del paciente como descarga directa.
     *
     * @param array<string, mixed> $datos Datos del paciente, signos vitales, medicamentos y contactos.
     * @return void
     */
    public function generarResumenPaciente(array $datos): void {
        $templatePath = __DIR__ . '/../Views/pacientes/pdf_resumen.php';
        $html = $this->compilarPlantilla($templatePath, $datos);

        $paciente = $datos['paciente'] ?? [];
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string)($paciente['nombre'] ?? 'Paciente'));
        $apellidoLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string)($paciente['apellidos'] ?? ''));
        $filename = "Expediente_{$nombreLimpio}_{$apellidoLimpio}_" . date('Ymd_His') . ".pdf";

        $this->renderToStream($html, $filename, true, 'A4', 'portrait');
    }

    /**
     * Genera y envía la receta médica oficial en PDF como descarga directa.
     *
     * @param array<string, mixed> $datos Datos de la receta, médico emisor, paciente y fármacos.
     * @return void
     */
    public function generarRecetaPdf(array $datos): void {
        $templatePath = __DIR__ . '/../Views/pacientes/pdf_receta.php';
        $html = $this->compilarPlantilla($templatePath, $datos);

        $receta = $datos['receta'] ?? [];
        $recetaId = $receta['id'] ?? 1;
        $nombreLimpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string)($receta['paciente_nombre'] ?? 'Paciente'));
        $filename = "Receta_Medica_Folio_{$recetaId}_{$nombreLimpio}_" . date('Ymd') . ".pdf";

        $this->renderToStream($html, $filename, true, 'A4', 'portrait');
    }

    /**
     * Convierte HTML a PDF y lo envía directamente al cliente HTTP vía Stream.
     *
     * @param string $html Contenido HTML estructurado con CSS embebido.
     * @param string $filename Nombre sugerido para el archivo PDF.
     * @param bool $descargar Si true, fuerza descarga (attachment); si false, muestra en visor (inline).
     * @param string $paper Tamaño de papel (ej. 'A4', 'letter').
     * @param string $orientation Orientación ('portrait' o 'landscape').
     * @return void
     */
    public function renderToStream(
        string $html,
        string $filename = 'reporte.pdf',
        bool $descargar = true,
        string $paper = 'A4',
        string $orientation = 'portrait'
    ): void {
        if (!class_exists(Dompdf::class)) {
            throw new RuntimeException('El motor Dompdf no está disponible. Ejecute composer install.');
        }

        // Garantizar límites suficientes de memoria y tiempo para compilación de PDFs extensos
        if ((int)ini_get('memory_limit') < 128) {
            @ini_set('memory_limit', '256M');
        }
        @set_time_limit(60);

        // Verificación preventiva contra errores 'Headers already sent'
        $headerFile = '';
        $headerLine = 0;
        if (headers_sent($headerFile, $headerLine)) {
            throw new RuntimeException("No se pueden emitir las cabeceras del PDF porque ya se envió salida previa desde {$headerFile} en la línea {$headerLine}.");
        }

        // Limpiar cualquier buffer previo que pudiera corromper la salida binaria
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $dompdf = new Dompdf($this->options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        $dompdf->stream($filename, [
            'Attachment' => $descargar ? 1 : 0
        ]);
        exit;
    }

    /**
     * Convierte HTML a PDF y retorna el string binario para almacenamiento o adjunto de correo.
     *
     * @param string $html Contenido HTML.
     * @param string $paper Tamaño de papel.
     * @param string $orientation Orientación.
     * @return string Contenido binario del PDF generado.
     */
    public function renderToBinary(
        string $html,
        string $paper = 'A4',
        string $orientation = 'portrait'
    ): string {
        if (!class_exists(Dompdf::class)) {
            throw new RuntimeException('El motor Dompdf no está disponible.');
        }

        $dompdf = new Dompdf($this->options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        $output = $dompdf->output();
        if ($output === null) {
            throw new RuntimeException('No se pudo generar la salida binaria del PDF.');
        }

        return $output;
    }
}

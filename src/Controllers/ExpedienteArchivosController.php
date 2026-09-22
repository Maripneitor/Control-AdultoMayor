<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ImageOptimizerService;
use Config\Database;
use finfo;
use PDO;

class ExpedienteArchivosController extends Controller {
    private PDO $db;
    private ImageOptimizerService $imageOptimizer;
    private string $storageDir;

    // Tipos MIME estrictamente permitidos
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf'
    ];

    // Límite estricto de 5 MB para prevenir crash por Out Of Memory (OOM) en extensión GD
    private const MAX_BYTES = 5 * 1024 * 1024; // 5 MB máximo

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->imageOptimizer = new ImageOptimizerService(1600, 1600, 80);
        $this->storageDir = dirname(__DIR__, 2) . '/storage/uploads/expedientes';

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0750, true);
        }
    }

    /**
     * POST /expediente/archivos/subir
     * Maneja subida multipart/form-data con validación finfo y compresión GD
     */
    public function subirArchivo(): void {
        // Validación de CSRF Token
        if (function_exists('validateCsrfToken') && !validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->jsonResponse(['error' => 'Token de seguridad inválido o expirado.'], 403);
            return;
        }

        $usuarioId = $_SESSION['user_id'] ?? null;
        if (!$usuarioId) {
            $this->jsonResponse(['error' => 'Sesión no iniciada.'], 401);
            return;
        }

        $pacienteId = trim($_POST['paciente_id'] ?? '');
        $tipoDocumento = trim($_POST['tipo_documento'] ?? 'receta');

        $tiposValidos = ['receta', 'estudio_laboratorio', 'imagen_medica', 'otro'];
        if (!in_array($tipoDocumento, $tiposValidos, true)) {
            $tipoDocumento = 'receta';
        }

        // 1. Validar que exista el archivo en $_FILES
        if (!isset($_FILES['archivo']) || is_array($_FILES['archivo']['error'])) {
            $this->jsonResponse(['error' => 'No se recibió ningún archivo.'], 400);
            return;
        }

        $file = $_FILES['archivo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(['error' => 'Error en la subida del archivo. Código: ' . $file['error']], 400);
            return;
        }

        // 2. PREVENCIÓN DE CRASH EN GD: Validación previa estricta a 5 MB
        // Rechazar cualquier archivo > 5MB antes de pasarlo a imagecreatefrom* para prevenir errores de 'Memory Exhausted'
        if ($file['size'] > self::MAX_BYTES) {
            $this->jsonResponse([
                'error' => 'El archivo supera el límite máximo permitido de 5 MB. Se rechaza preventivamente para evitar saturación de memoria en el servidor (Memory Exhaustion).'
            ], 413);
            return;
        }

        // 3. Validación estricta con finfo (MIME real del contenido binario)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($file['tmp_name']);

        if (!array_key_exists($detectedMime, self::ALLOWED_MIMES)) {
            $this->jsonResponse(['error' => "Tipo de archivo no permitido ($detectedMime). Solo imágenes (JPEG, PNG, WEBP) o PDF."], 415);
            return;
        }

        $extension = self::ALLOWED_MIMES[$detectedMime];
        $nombreAleatorio = bin2hex(random_bytes(16)) . '.' . $extension;
        $destinoFinal = $this->storageDir . '/' . $nombreAleatorio;

        try {
            $tamanoFinal = 0;

            // 4. Si es imagen, redimensionar y comprimir usando GD
            if (strpos($detectedMime, 'image/') === 0) {
                $resultadoCompresion = $this->imageOptimizer->optimizeAndSave($file['tmp_name'], $destinoFinal, $detectedMime);
                if (!$resultadoCompresion['success']) {
                    $this->jsonResponse(['error' => $resultadoCompresion['error'] ?? 'Error al procesar la imagen con GD.'], 500);
                    return;
                }
                $tamanoFinal = $resultadoCompresion['size_bytes'];
            } else {
                // Es PDF: mover de forma segura
                if (!move_uploaded_file($file['tmp_name'], $destinoFinal)) {
                    $this->jsonResponse(['error' => 'No se pudo almacenar el documento PDF en el disco seguro.'], 500);
                    return;
                }
                chmod($destinoFinal, 0640);
                $tamanoFinal = filesize($destinoFinal);
            }

            // 5. Registrar en PostgreSQL mediante PDO
            $stmt = $this->db->prepare(
                "INSERT INTO archivos_medicos 
                 (paciente_id, nombre_original, nombre_archivo_almacenado, ruta_almacenamiento, mime_type, tamano_bytes, tipo_documento, subido_por_usuario_id) 
                 VALUES (:paciente_id, :original, :almacenado, :ruta, :mime, :tamano, :tipo_doc, :subido_por)
                 RETURNING id"
            );

            // Sanitizar nombre original
            $nombreOriginal = htmlspecialchars(basename($file['name']), ENT_QUOTES, 'UTF-8');

            $stmt->execute([
                ':paciente_id' => $pacienteId,
                ':original' => $nombreOriginal,
                ':almacenado' => $nombreAleatorio,
                ':ruta' => $destinoFinal,
                ':mime' => $detectedMime,
                ':tamano' => $tamanoFinal,
                ':tipo_doc' => $tipoDocumento,
                ':subido_por' => $usuarioId
            ]);

            $archivoId = $stmt->fetchColumn();

            // Auditoría
            $stmtAudit = $this->db->prepare(
                "INSERT INTO logs_auditoria (usuario_id, paciente_id, evento, descripcion, ip_origen) 
                 VALUES (:uid, :pid, 'ARCHIVO_SUBIDO', :desc, :ip)"
            );
            $stmtAudit->execute([
                ':uid' => $usuarioId,
                ':pid' => $pacienteId,
                ':desc' => "Archivo '{$nombreOriginal}' ({$tipoDocumento}) subido y procesado con GD.",
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Archivo médico subido y optimizado con éxito.',
                'archivo' => [
                    'id' => $archivoId,
                    'nombre' => $nombreOriginal,
                    'tipo' => $tipoDocumento,
                    'tamano_bytes' => $tamanoFinal
                ]
            ]);
        } catch (\Throwable $e) {
            error_log('[ExpedienteArchivosController::subirArchivo] ' . $e->getMessage());
            // Si ocurrió un error y el archivo llegó a escribirse, limpiarlo
            if (file_exists($destinoFinal)) {
                @unlink($destinoFinal);
            }
            $this->jsonResponse(['error' => 'Ocurrió un error en el servidor al procesar el expediente médico.'], 500);
        }
    }

    /**
     * GET /expediente/archivos/descargar?id=UUID
     * Entrega el archivo protegido previa comprobación de permisos
     */
    public function descargarArchivo(): void {
        $usuarioId = $_SESSION['user_id'] ?? null;
        if (!$usuarioId) {
            http_response_code(401);
            die('Acceso denegado.');
        }

        $archivoId = trim($_GET['id'] ?? '');
        if (empty($archivoId)) {
            http_response_code(400);
            die('Identificador de archivo requerido.');
        }

        $stmt = $this->db->prepare(
            "SELECT a.*, p.usuario_id as paciente_usuario_id 
             FROM archivos_medicos a 
             JOIN pacientes p ON a.paciente_id = p.id 
             WHERE a.id = :id"
        );
        $stmt->execute([':id' => $archivoId]);
        $archivo = $stmt->fetch();

        if (!$archivo || !file_exists($archivo['ruta_almacenamiento'])) {
            http_response_code(404);
            die('Archivo no encontrado.');
        }

        // Cabeceras para descarga protegida
        header('Content-Type: ' . $archivo['mime_type']);
        header('Content-Length: ' . $archivo['tamano_bytes']);
        header('Content-Disposition: inline; filename="' . addslashes($archivo['nombre_original']) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=0, must-revalidate');

        readfile($archivo['ruta_almacenamiento']);
        exit;
    }

    private function jsonResponse(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

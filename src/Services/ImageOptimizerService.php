<?php
declare(strict_types=1);

namespace App\Services;

class ImageOptimizerService {
    private int $maxWidth;
    private int $maxHeight;
    private int $quality;

    public function __construct(int $maxWidth = 1600, int $maxHeight = 1600, int $quality = 80) {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
        $this->quality = $quality;
    }

    /**
     * Comprime y redimensiona una imagen médica (receta/laboratorio) usando la extensión nativa GD.
     * Retorna array con éxito, ruta final y bytes resultantes.
     */
    public function optimizeAndSave(string $sourceTmpPath, string $destinationPath, string $mimeType): array {
        // Prevención de OOM (Out Of Memory): validar que el archivo temporal no supere 5MB
        if (file_exists($sourceTmpPath) && filesize($sourceTmpPath) > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'La imagen supera los 5MB permitidos. Procesamiento cancelado para proteger memoria del servidor.'];
        }

        if (!extension_loaded('gd')) {
            // Fallback si GD no estuviera disponible en el entorno
            if (move_uploaded_file($sourceTmpPath, $destinationPath)) {
                chmod($destinationPath, 0640);
                return [
                    'success' => true,
                    'file_path' => $destinationPath,
                    'size_bytes' => filesize($destinationPath)
                ];
            }
            return ['success' => false, 'error' => 'No se pudo mover el archivo.'];
        }

        // 1. Cargar imagen original según MIME
        $srcImage = null;
        switch ($mimeType) {
            case 'image/jpeg':
                $srcImage = @imagecreatefromjpeg($sourceTmpPath);
                break;
            case 'image/png':
                $srcImage = @imagecreatefrompng($sourceTmpPath);
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($sourceTmpPath);
                break;
            default:
                return ['success' => false, 'error' => 'Formato no compatible para optimización GD.'];
        }

        if (!$srcImage) {
            return ['success' => false, 'error' => 'No se pudo decodificar la imagen médica.'];
        }

        // 2. Calcular nuevas dimensiones manteniendo aspect ratio
        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        $newWidth = $origWidth;
        $newHeight = $origHeight;

        if ($origWidth > $this->maxWidth || $origHeight > $this->maxHeight) {
            $ratio = min($this->maxWidth / $origWidth, $this->maxHeight / $origHeight);
            $newWidth = (int) round($origWidth * $ratio);
            $newHeight = (int) round($origHeight * $ratio);
        }

        // 3. Crear canvas redimensionado
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Manejo de transparencias para PNG y WEBP
        if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // 4. Remuestreo de alta calidad
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // 5. Guardar comprimido
        $saved = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $saved = imagejpeg($dstImage, $destinationPath, $this->quality);
                break;
            case 'image/png':
                // Calidad de compresión PNG (0-9). Convertir escala 0-100 a 0-9
                $pngQuality = (int) round((100 - $this->quality) / 10);
                $saved = imagepng($dstImage, $destinationPath, $pngQuality);
                break;
            case 'image/webp':
                $saved = imagewebp($dstImage, $destinationPath, $this->quality);
                break;
        }

        // Liberar memoria
        imagedestroy($srcImage);
        imagedestroy($dstImage);

        if ($saved && file_exists($destinationPath)) {
            chmod($destinationPath, 0640);
            return [
                'success' => true,
                'file_path' => $destinationPath,
                'size_bytes' => filesize($destinationPath),
                'optimized' => true
            ];
        }

        return ['success' => false, 'error' => 'Fallo al escribir la imagen comprimida en disco.'];
    }
}

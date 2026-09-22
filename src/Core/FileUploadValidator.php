<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Módulo 12: Utilidad para carga segura de archivos (Sanitización Profunda).
 */
class FileUploadValidator {
    
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'application/pdf' => 'pdf'
    ];
    
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

    /**
     * Valida y guarda un archivo de forma segura.
     * @param array $file Array $_FILES['input_name']
     * @param string $destinationDir Directorio destino (preferentemente fuera de public/)
     * @return string|false Nombre del archivo final o false si falla.
     */
    public static function processUpload(array $file, string $destinationDir) {
        if (!isset($file['error']) || is_array($file['error'])) {
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!array_key_exists($mimeType, self::ALLOWED_MIME_TYPES)) {
            return false;
        }

        $ext = self::ALLOWED_MIME_TYPES[$mimeType];
        
        // Renombrar el archivo usando un hash criptográfico fuerte
        $newFilename = bin2hex(random_bytes(16)) . '.' . $ext;
        $destinationPath = rtrim($destinationDir, '/') . '/' . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
            // Remover permisos de ejecución
            chmod($destinationPath, 0644);
            return $newFilename;
        }

        return false;
    }
}

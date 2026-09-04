<?php
namespace App\Core;

class Controller {
    /**
     * Renderiza una vista y le pasa datos.
     * @param string $view Ruta de la vista (ej: 'home/index')
     * @param array $data Datos a extraer para la vista
     * @param string $layout Nombre del layout a usar
     */
    protected function render($view, $data = [], $layout = 'main') {
        $flash = $_SESSION['flash_notification'] ?? null;
        unset($_SESSION['flash_notification']);
        
        $data['flash'] = $flash;
        extract($data);
        
        // Iniciar el buffer de salida para capturar la vista
        ob_start();
        require_once "../src/Views/$view.php";
        $content = ob_get_clean();
        
        // Renderizar dentro del layout si está definido
        if ($layout) {
            require_once "../src/Views/layouts/$layout.php";
        } else {
            echo $content;
        }
    }

    protected function jsonResponse(bool $success, string $message, $data = null, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
            'timestamp' => date('c')
        ]);
        exit;
    }

    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash_notification'] = [
            'type'    => $type,
            'message' => $message
        ];
    }
}

<?php
namespace App\Core;

class Controller {
    /**
     * Renderiza una vista y le pasa datos.
     * @param string $view Ruta de la vista (ej: 'home/index')
     * @param array $data Datos a extraer para la vista
     * @param string|false|null $layout Nombre del layout a usar
     */
    protected function render($view, $data = [], $layout = 'main') {
        $flash = $_SESSION['flash_notification'] ?? ($data['flash'] ?? null);
        unset($_SESSION['flash_notification']);
        
        $data['flash'] = $flash;
        if (!empty($flash['message']) && !isset($data['error']) && in_array($flash['type'] ?? '', ['danger', 'error'])) {
            $data['error'] = $flash['message'];
        }
        
        extract($data);
        
        // Iniciar el buffer de salida para capturar la vista
        ob_start();
        $viewPath = dirname(__DIR__) . "/Views/$view.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {

            echo "Vista [{$view}] no encontrada.";
        }
        $content = ob_get_clean();
        
        // Renderizar dentro del layout si está definido
        if ($layout) {
            $layoutPath = dirname(__DIR__) . "/Views/layouts/$layout.php";
            if (file_exists($layoutPath)) {
                require_once $layoutPath;
            } else {
                echo $content;
            }
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

    /**
     * Establece un mensaje flash para ser mostrado en la siguiente vista.
     * Es polimórfico para admitir ($type, $message) o ($message, $type) y un título opcional.
     */
    protected function setFlash(string $typeOrMessage, string $messageOrType, ?string $title = null): void {
        $validTypes = ['success', 'danger', 'error', 'warning', 'info'];
        
        if (in_array(strtolower($typeOrMessage), $validTypes, true)) {
            $type = strtolower($typeOrMessage);
            $message = $messageOrType;
        } elseif (in_array(strtolower($messageOrType), $validTypes, true)) {
            $type = strtolower($messageOrType);
            $message = $typeOrMessage;
        } else {
            $type = 'info';
            $message = $typeOrMessage;
        }
        
        if ($type === 'error') {
            $type = 'danger';
        }
        
        $_SESSION['flash_notification'] = [
            'type'    => $type,
            'message' => $message,
            'title'   => $title
        ];
    }
}


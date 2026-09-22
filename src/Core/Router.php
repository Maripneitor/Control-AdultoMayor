<?php
declare(strict_types=1);

namespace App\Core;

class Router {
    private array $routes = [];
    private $notFoundHandler = null;

    public function get(string $path, string|callable $handler, array $middlewares = []): void {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, string|callable $handler, array $middlewares = []): void {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function setNotFoundHandler(callable $handler): void {
        $this->notFoundHandler = $handler;
    }

    private function addRoute(string $method, string $path, string|callable $handler, array $middlewares): void {
        $this->routes[] = [
            'method' => $method,
            'path' => rtrim($path, '/') === '' ? '/' : rtrim($path, '/'),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        // Ajustamos la ruta para ignorar el subdirectorio de APP_URL si aplica
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $basePath = parse_url(defined('APP_URL') ? APP_URL : '', PHP_URL_PATH) ?: '';
        
        if ($basePath && strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }
        $requestUri = rtrim($requestUri, '/') === '' ? '/' : rtrim($requestUri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestUri) {
                // 1. Ejecutar Middlewares asignados
                foreach ($route['middlewares'] as $middlewareClass) {
                    if (is_callable($middlewareClass)) {
                        call_user_func($middlewareClass);
                    } elseif (is_string($middlewareClass) && class_exists($middlewareClass) && method_exists($middlewareClass, 'handle')) {
                        $middlewareClass::handle();
                    }
                }

                // 2. Despachar Handler (Closure o Controller@action)
                if (is_callable($route['handler'])) {
                    call_user_func($route['handler']);
                    return;
                }

                if (is_string($route['handler']) && strpos($route['handler'], '@') !== false) {
                    [$controllerName, $action] = explode('@', $route['handler']);
                    $fullControllerClass = "App\\Controllers\\{$controllerName}";

                    if (!class_exists($fullControllerClass)) {
                        http_response_code(500);
                        die("Controlador [{$fullControllerClass}] no encontrado.");
                    }

                    $controllerInstance = new $fullControllerClass();

                    if (!method_exists($controllerInstance, $action)) {
                        http_response_code(500);
                        die("Acción [{$action}] no encontrada en {$fullControllerClass}.");
                    }

                    $controllerInstance->$action();
                    return;
                }
            }
        }

        // 3. Captura elegante de ruta no encontrada (404)
        http_response_code(404);

        if ($this->notFoundHandler && is_callable($this->notFoundHandler)) {
            call_user_func($this->notFoundHandler);
            return;
        }

        // Renderizar la vista 404 de VitalSenior
        $view404 = dirname(__DIR__) . '/Views/404.php';
        if (file_exists($view404)) {
            require $view404;
        } else {
            echo "404 - Página no encontrada.";
        }
    }
}

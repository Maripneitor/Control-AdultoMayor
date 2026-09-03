<?php
declare(strict_types=1);

namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, string $handler, array $middlewares = []): void {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, string $handler, array $middlewares = []): void {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, string $handler, array $middlewares): void {
        $this->routes[] = [
            'method' => $method,
            'path' => rtrim($path, '/') === '' ? '/' : rtrim($path, '/'),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        // Ajustamos la ruta para ignorar /Control-AdultoMayor/public
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $basePath = parse_url(APP_URL, PHP_URL_PATH) ?: '';
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
                    } elseif (class_exists($middlewareClass) && method_exists($middlewareClass, 'handle')) {
                        $middlewareClass::handle();
                    }
                }

                // 2. Despachar Controlador y Acción
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

        http_response_code(404);
        echo "404 - Página no encontrada";
    }
}

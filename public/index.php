<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/security.php';

// Forzar la conexión a la base de datos para mostrar la pantalla de error global si está caída
\Config\Database::getInstance();

// Autoloader PSR-4 custom en lugar de Composer para simplificar la compatibilidad local
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) { return; }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Router;
use App\Middlewares\AuthMiddleware;

startSecureSession();

$router = new Router();

// Rutas Públicas e Invitados
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@showLoginForm', [[AuthMiddleware::class, 'guestOnly']]);
$router->post('/login', 'AuthController@login', [[AuthMiddleware::class, 'guestOnly']]);
$router->post('/register', 'AuthController@register', [[AuthMiddleware::class, 'guestOnly']]);
$router->get('/recovery', 'RecoveryController@showRecoveryForm', [[AuthMiddleware::class, 'guestOnly']]);
$router->post('/recovery', 'RecoveryController@sendToken', [[AuthMiddleware::class, 'guestOnly']]);

// Rutas Protegidas
$router->get('/dashboard', 'DashboardController@index', [AuthMiddleware::class]);
$router->get('/logout', 'AuthController@logout', [AuthMiddleware::class]);

// Ficha QR pública (sin AuthMiddleware)
$router->get('/emergencia', 'EmergenciaController@showPublicCard');

// Medicamentos
$router->get('/medicamentos', 'MedicamentoController@index', [AuthMiddleware::class]);
$router->post('/medicamentos/crear', 'MedicamentoController@store', [AuthMiddleware::class]);
$router->post('/medicamentos/toma', 'MedicamentoController@registrarToma', [AuthMiddleware::class]);

// Citas y Laboratorios
$router->get('/citas', 'CitaController@index', [AuthMiddleware::class]);
$router->post('/citas/crear', 'CitaController@store', [AuthMiddleware::class]);

$router->dispatch();

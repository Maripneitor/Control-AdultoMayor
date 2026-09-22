<?php
declare(strict_types=1);

// Cargar inicializador seguro del sistema (DevSecOps: Gestión de Errores y Anti-Fugas)
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/security.php';

// Forzar la conexión a la base de datos para mostrar la pantalla de error global si está caída
\Config\Database::getInstance();

// Cargar autoloader de Composer para librerías de terceros (ej. Dompdf)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

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
$router->get('/register', 'AuthController@showRegisterForm', [[AuthMiddleware::class, 'guestOnly']]);
$router->post('/register', 'AuthController@register', [[AuthMiddleware::class, 'guestOnly']]);
$router->get('/recovery', 'RecoveryController@showRecoveryForm', [[AuthMiddleware::class, 'guestOnly']]);
$router->post('/recovery', 'RecoveryController@sendToken', [[AuthMiddleware::class, 'guestOnly']]);
$router->get('/new-password', 'RecoveryController@showNewPasswordForm', [[AuthMiddleware::class, 'guestOnly']]);
$router->post('/new-password', 'RecoveryController@resetPassword', [[AuthMiddleware::class, 'guestOnly']]);
$router->get('/password-updated', 'RecoveryController@showPasswordUpdated', [[AuthMiddleware::class, 'guestOnly']]);


// Rutas Protegidas
$router->get('/dashboard', 'DashboardController@index', [AuthMiddleware::class]);
$router->get('/logout', 'AuthController@logout', [AuthMiddleware::class]);

// Vinculación
$router->get('/vincular', 'VinculacionController@showForm', [AuthMiddleware::class]);
$router->post('/vincular/procesar', 'VinculacionController@processVinculacion', [AuthMiddleware::class]);
$router->post('/vincular/promover', 'VinculacionController@promoverAIndependiente', [AuthMiddleware::class]);

// Ficha QR pública (sin AuthMiddleware)
$router->get('/emergencia', 'EmergenciaController@showPublicCard');

// Twilio & Notificaciones (SOS y OTP WhatsApp)
$router->post('/api/otp/send', 'TwilioController@sendWhatsAppOTP');
$router->post('/api/otp/verify', 'TwilioController@verifyWhatsAppOTP');
$router->post('/api/sos', 'TwilioController@triggerSOS', [AuthMiddleware::class]);
$router->post('/api/twilio/webhook', 'TwilioController@handleWebhook');

// Medicamentos
$router->get('/medicamentos', 'MedicamentoController@index', [AuthMiddleware::class]);
$router->get('/medicamentos/control', 'MedicamentoController@controlTomas', [AuthMiddleware::class]);
$router->post('/medicamentos/crear', 'MedicamentoController@store', [AuthMiddleware::class]);
$router->post('/medicamentos/toma', 'MedicamentoController@marcarToma', [AuthMiddleware::class]);
$router->post('/medicamentos/posponer', 'MedicamentoController@posponerToma', [AuthMiddleware::class]);
$router->post('/medicamentos/deshacer', 'MedicamentoController@deshacerToma', [AuthMiddleware::class]);

// Citas y Laboratorios
$router->get('/citas', 'CitaController@index', [AuthMiddleware::class]);
$router->post('/citas/crear', 'CitaController@store', [AuthMiddleware::class]);

// Salud y Signos Vitales
$router->get('/salud/stepper', 'SaludController@stepper', [AuthMiddleware::class]);
$router->post('/salud/registrar', 'SaludController@registrar', [AuthMiddleware::class]);
$router->get('/salud/confirmar', 'SaludController@mostrarConfirmacion', [AuthMiddleware::class]);

// Expediente Médico y Archivos (Multipart/form-data con GD)
$router->get('/expediente', 'PacienteController@expediente', [AuthMiddleware::class]);
$router->post('/expediente/actualizar', 'PacienteController@actualizarExpediente', [AuthMiddleware::class]);
$router->post('/expediente/anadir_doctor', 'PacienteController@anadirDoctor', [AuthMiddleware::class]);
$router->post('/expediente/eliminar_doctor', 'PacienteController@eliminarDoctor', [AuthMiddleware::class]);
$router->post('/expediente/archivos/subir', 'ExpedienteArchivosController@subirArchivo', [AuthMiddleware::class]);
$router->get('/expediente/archivos/descargar', 'ExpedienteArchivosController@descargarArchivo', [AuthMiddleware::class]);

// Perfil y Ajustes
$router->get('/perfil', 'PerfilController@index', [AuthMiddleware::class]);
$router->post('/perfil/actualizar', 'PerfilController@actualizar', [AuthMiddleware::class]);
$router->post('/perfil/password', 'PerfilController@actualizarPassword', [AuthMiddleware::class]);
$router->post('/perfil/eliminar', 'PerfilController@eliminarCuenta', [AuthMiddleware::class]);

// PDF
$router->get('/paciente/pdf', 'PacienteController@descargarExpediente', [AuthMiddleware::class]);

// Ruta de prueba para 404
$router->get('/404', function() {
    require __DIR__ . '/../src/Views/404.php';
});

$router->dispatch();


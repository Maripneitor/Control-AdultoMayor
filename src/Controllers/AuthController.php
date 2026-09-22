<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Paciente;
use Config\Database;
use App\Core\Controller;

class AuthController extends Controller {
    private Usuario $usuarioModel;
    private Paciente $pacienteModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->pacienteModel = new Paciente();
    }

    public function showLoginForm(): void {
        $tab = $_GET['tab'] ?? 'login';
        $this->render('auth/login', ['tab' => $tab], false);
    }

    public function showRegisterForm(): void {
        $this->render('auth/login', ['tab' => 'register'], false);
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/register');
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'La sesión ha expirado por inactividad. Por favor intenta registrarte nuevamente.', 'Seguridad');
            header('Location: ' . APP_URL . '/register');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $telefono = trim($_POST['telefono'] ?? '');
        $password = $_POST['password'] ?? '';
        $rolInput = $_POST['rol'] ?? 'adulto_mayor';

        if (empty($nombre)) {
            $this->setFlash('warning', 'Por favor ingresa tu nombre completo para el registro.', 'Nombre requerido');
            header('Location: ' . APP_URL . '/register');
            exit;
        }

        if (!$email) {
            $this->setFlash('warning', 'Por favor ingresa un correo electrónico válido.', 'Correo inválido');
            header('Location: ' . APP_URL . '/register');
            exit;
        }

        if (strlen($password) < 8) {
            $this->setFlash('warning', 'La contraseña debe tener un mínimo de 8 caracteres para garantizar la seguridad de tu información médica.', 'Contraseña muy corta');
            header('Location: ' . APP_URL . '/register');
            exit;
        }

        if ($this->usuarioModel->findByEmail($email)) {
            $this->setFlash('danger', 'El correo ' . htmlspecialchars($email) . ' ya se encuentra registrado. Puedes iniciar sesión o recuperar tu contraseña si la olvidaste.', 'Correo ya registrado');
            header('Location: ' . APP_URL . '/register');
            exit;
        }

        // Determinar rol en la base de datos
        $rolDb = ($rolInput === 'familiar') ? 'cuidador' : 'cuidador';

        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();

            $userId = $this->usuarioModel->register($nombre, $email, $telefono, $password, $rolDb);
            
            // Crear expediente médico inicial
            $this->pacienteModel->createInitialProfile($userId, "Expediente de " . $nombre);

            $db->commit();

            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_role'] = $rolDb;
            $_SESSION['user_permissions'] = $this->usuarioModel->cargarPermisosRol($rolDb);

            $this->setFlash('success', '¡Tu cuenta ha sido creada exitosamente! Bienvenido a VitalSenior.', '¡Registro Exitoso!');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('[AuthController::register Error] ' . $e->getMessage());
            $this->setFlash('danger', 'No se pudo completar el registro debido a un problema técnico. Por favor inténtalo de nuevo.', 'Error en el Registro');
            header('Location: ' . APP_URL . '/register');
            exit;
        }
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'La sesión ha caducado por motivos de seguridad. Por favor intenta iniciar sesión nuevamente.', 'Seguridad');
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || empty($password)) {
            $this->setFlash('warning', 'Por favor ingresa tanto tu correo electrónico como tu contraseña.', 'Datos Incompletos');
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $ip = \App\Security\RateLimiter::getClientIp();

        // Rate Limiting seguro: máximo 5 intentos por cada 10 minutos
        if (!\App\Security\RateLimiter::check($ip, 'login', 5, 10)) {
            $this->setFlash('danger', 'Has superado el límite de intentos de inicio de sesión. Por motivos de seguridad médica, por favor espera 10 minutos antes de reintentar.', 'Demasiados Intentos Fallidos');
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        // Registrar intento
        \App\Security\RateLimiter::hit($ip, 'login', 10);

        try {
            $user = $this->usuarioModel->findByEmail($email);

            if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
                \App\Security\RateLimiter::clear($ip, 'login'); // Restablecer contador al ingresar exitosamente
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_role'] = $user['rol'];
                $_SESSION['user_permissions'] = $this->usuarioModel->cargarPermisosRol($user['rol']);

                $this->setFlash('success', '¡Hola de nuevo, ' . htmlspecialchars($user['nombre']) . '! Has iniciado sesión correctamente.', 'Bienvenido a VitalSenior');
                header('Location: ' . APP_URL . '/dashboard');
                exit;
            }
        } catch (\Throwable $e) {
            error_log('[AuthController::login Error] ' . $e->getMessage());
        }

        $this->setFlash('danger', 'Correo electrónico o contraseña incorrectos. Por favor verifica tus credenciales o utiliza la opción "¿Olvidaste tu contraseña?".', 'Credenciales Incorrectas');
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        session_destroy();
        
        session_start();
        $this->setFlash('info', 'Has cerrado tu sesión de forma segura. ¡Hasta pronto!', 'Sesión Finalizada');
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}


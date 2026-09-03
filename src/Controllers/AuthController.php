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
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        $this->render('auth/login', ['error' => $error], false);
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error: Token de seguridad no válido (CSRF).');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $telefono = trim($_POST['telefono'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || strlen($password) < 8 || empty($nombre)) {
            $_SESSION['error'] = "Datos inválidos o contraseña menor a 8 caracteres.";
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        if ($this->usuarioModel->findByEmail($email)) {
            $_SESSION['error'] = "El correo ingresado ya está registrado.";
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        // Transacción para registrar usuario y crear expediente vinculado
        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();

            $userId = $this->usuarioModel->register($nombre, $email, $telefono, $password);
            $this->pacienteModel->createInitialProfile($userId, "Expediente de " . $nombre);

            $db->commit();

            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $nombre;

            header('Location: ' . APP_URL . '/dashboard');
            exit;
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "Error interno al procesar el registro.";
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        // Rate Limiting Login
        if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;
        if (!isset($_SESSION['login_last_attempt'])) $_SESSION['login_last_attempt'] = time();

        if (time() - $_SESSION['login_last_attempt'] > 900) {
            $_SESSION['login_attempts'] = 0;
        }

        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['error'] = "Demasiados intentos fallidos. Bloqueo temporal de 15 minutos.";
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $_SESSION['login_last_attempt'] = time();

        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        $user = $email ? $this->usuarioModel->findByEmail($email) : null;

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['login_attempts'] = 0; // reset on success
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $_SESSION['login_attempts']++;
        $_SESSION['error'] = "Credenciales incorrectas.";
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
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}

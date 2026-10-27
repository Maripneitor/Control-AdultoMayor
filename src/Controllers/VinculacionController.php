<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Paciente;
use App\Models\Cuidador;
use App\Models\Usuario;
use App\Middlewares\AuthMiddleware;

class VinculacionController extends Controller {
    private Paciente $pacienteModel;
    private Cuidador $cuidadorModel;
    private Usuario $usuarioModel;

    public function __construct() {
        $this->pacienteModel = new Paciente();
        $this->cuidadorModel = new Cuidador();
        $this->usuarioModel = new Usuario();
    }

    public function showForm(): void {
        AuthMiddleware::handle();
        $role = $_SESSION['user_role'] ?? '';

        if (in_array($role, ['paciente', 'adulto_mayor'], true)) {
            $this->setFlash('danger', 'Los pacientes con cuenta propia no pueden vincular otros perfiles.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        header('Location: ' . APP_URL . '/dashboard?abrir_nip=1');
        exit;
    }

    public function processVinculacion(): void {
        AuthMiddleware::handle();

        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->setFlash('danger', 'Token de seguridad inválido o expirado.');
            header('Location: ' . APP_URL . '/vincular');
            exit;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['user_role'] ?? 'familiar_admin';

        if (in_array($role, ['paciente', 'adulto_mayor'], true)) {
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        $action = $_POST['action'] ?? 'codigo';

        if ($action === 'codigo') {
            $codigo = strtoupper(trim($_POST['codigo_vinculacion'] ?? ''));
            $parentesco = trim($_POST['parentesco_codigo'] ?? 'Familiar');
            
            if (empty($codigo)) {
                $this->setFlash('warning', 'Debes ingresar un código de vinculación válido (ej. ESP-8429).');
                header('Location: ' . APP_URL . '/vincular');
                exit;
            }

            $paciente = $this->pacienteModel->findByCodigoVinculacion($codigo);
            if (!$paciente) {
                $this->setFlash('danger', "Código '{$codigo}' no encontrado. Verifica con el familiar titular.");
                header('Location: ' . APP_URL . '/vincular');
                exit;
            }

            $pacienteId = (int)$paciente['id'];

            // Evitar duplicados
            $pacientesActuales = $this->cuidadorModel->obtenerPacientesPorCuidador($userId);
            foreach ($pacientesActuales as $p) {
                if ((int)$p['id'] === $pacienteId) {
                    $_SESSION['paciente_activo_id'] = $pacienteId;
                    $this->setFlash('info', 'Este paciente ya está vinculado a tu cuenta.');
                    header('Location: ' . APP_URL . '/dashboard');
                    exit;
                }
            }

            $nivelAcceso = ($role === 'cuidador_auxiliar') ? 'tomas_y_signos' : 'familiar_admin';
            $this->cuidadorModel->agregarCuidador($pacienteId, $userId, $parentesco, $nivelAcceso);
            
            $_SESSION['paciente_activo_id'] = $pacienteId;

            AuthMiddleware::logAudit(
                $pacienteId,
                'VINCULACION_FAMILIAR_EXITOSA',
                "Usuario ID {$userId} vinculado con éxito con parentesco '{$parentesco}' y nivel de acceso '{$nivelAcceso}'."
            );

            $this->setFlash('success', '¡Paciente ' . $paciente['nombre'] . ' vinculado con éxito!');
            header('Location: ' . APP_URL . '/dashboard');
            exit;

        } elseif ($action === 'nuevo') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? 'Por completar');
            $fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? date('Y-m-d'));
            $parentesco = trim($_POST['parentesco_nuevo'] ?? 'Familiar');

            if (empty($nombre)) {
                $this->setFlash('warning', 'El nombre del paciente es obligatorio.');
                header('Location: ' . APP_URL . '/vincular');
                exit;
            }

            $pacienteId = $this->pacienteModel->createInitialProfile($userId, $nombre);
            
            // Actualizar apellidos y fecha de nacimiento
            $stmt = $this->pacienteModel->getConnection()->prepare("UPDATE pacientes SET apellidos = :apellidos, fecha_nacimiento = :fecha_nacimiento WHERE id = :id");
            $stmt->execute(['apellidos' => $apellidos, 'fecha_nacimiento' => $fecha_nacimiento, 'id' => $pacienteId]);

            $this->cuidadorModel->agregarCuidador($pacienteId, $userId, $parentesco, 'familiar_admin');
            $_SESSION['paciente_activo_id'] = $pacienteId;
            
            AuthMiddleware::logAudit(
                $pacienteId,
                'PERFIL_PACIENTE_CREADO',
                "Perfil de salud para '{$nombre} {$apellidos}' creado y vinculado como familiar titular."
            );

            $this->setFlash('success', 'Perfil de salud creado y vinculado con éxito.');
            header('Location: ' . APP_URL . '/dashboard');
            exit;
        }

        header('Location: ' . APP_URL . '/vincular');
        exit;
    }
}

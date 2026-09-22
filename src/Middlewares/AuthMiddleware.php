<?php
declare(strict_types=1);

namespace App\Middlewares;

use Config\Database;
use PDO;

class AuthMiddleware {
    /**
     * Asegura que exista una sesión activa de usuario
     */
    public static function handle(): void {
        if (empty($_SESSION['user_id'])) {
            if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Autenticación requerida para acceder al recurso.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $_SESSION['flash_notification'] = [
                'type' => 'danger',
                'message' => 'Debes iniciar sesión para acceder a esta sección clínica.'
            ];
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '/dashboard';
            header('Location: ' . (defined('APP_URL') ? APP_URL : '') . '/login');
            exit;
        }
    }

    /**
     * Asegura que el usuario NO esté autenticado (solo para login/registro)
     */
    public static function guestOnly(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . (defined('APP_URL') ? APP_URL : '') . '/dashboard');
            exit;
        }
    }

    /**
     * Bloquea peticiones según el rol del usuario ('familiar', 'adulto_mayor', 'admin')
     *
     * @param array $rolesPermitidos Lista de roles con permiso (ej. ['familiar', 'admin'])
     */
    public static function requireRole(array $rolesPermitidos): void {
        self::handle();

        $userRole = $_SESSION['user_role'] ?? '';

        if (!in_array($userRole, $rolesPermitidos, true)) {
            if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Acceso denegado: tu rol no tiene autorización para esta acción.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            http_response_code(403);
            die('Error 403: No tienes los privilegios necesarios para esta vista.');
        }
    }

    /**
     * Valida permisos requeridos en sesión (o permite por defecto si rol autorizado)
     */
    public static function requirePermission(string $permiso): void {
        self::handle();
        $permissions = $_SESSION['user_permissions'] ?? [];
        $userRole = $_SESSION['user_role'] ?? '';

        // Si es admin o cuidador_principal/familiar tiene acceso por defecto
        if ($userRole === 'admin' || in_array('todas_acciones', $permissions, true)) {
            return;
        }

        if (!empty($permissions) && !in_array($permiso, $permissions, true)) {
            // Verificar si el rol base tiene acceso
            if ($userRole === 'familiar' || $userRole === 'cuidador_principal') {
                return;
            }
            if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['error' => 'Permiso insuficiente para esta operación.'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            http_response_code(403);
            die('Error 403: No tienes los permisos necesarios para realizar esta acción.');
        }
    }

    /**
     * Valida si un usuario tiene acceso a un paciente específico (retorna booleano)
     */
    public static function autorizarAccesoPaciente($pacienteId, $userId): bool {
        if (empty($pacienteId) || empty($userId)) {
            return false;
        }

        $userRole = $_SESSION['user_role'] ?? '';
        if ($userRole === 'admin') {
            return true;
        }

        try {
            $db = Database::getInstance()->getConnection();
            // Comprobar si es paciente propio
            $stmt = $db->prepare("SELECT id FROM pacientes WHERE id = :pid AND usuario_id = :uid LIMIT 1");
            $stmt->execute([':pid' => $pacienteId, ':uid' => $userId]);
            if ($stmt->fetch()) {
                return true;
            }

            // Comprobar en vinculaciones
            $stmtVinc = $db->prepare("SELECT 1 FROM vinculaciones WHERE familiar_id = :fid AND paciente_id = :pid LIMIT 1");
            $stmtVinc->execute([':fid' => $userId, ':pid' => $pacienteId]);
            if ($stmtVinc->fetch()) {
                return true;
            }
        } catch (\Throwable $e) {
            error_log('[AuthMiddleware::autorizarAccesoPaciente] ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Aislamiento de datos Multitenant y Ofuscación de Seguridad.
     * Garantiza que el usuario en sesión es el propio paciente o un familiar con vínculo activo.
     * Si no tiene acceso, devuelve un error 404 (en lugar de 403) para ofuscar la existencia del recurso.
     *
     * @param string|int $pacienteId Identificador del paciente a consultar
     * @return array Metadatos de autorización: ['puede_editar' => bool, 'nivel_acceso' => string, 'es_propio' => bool]
     */
    public static function requirePatientAccess($pacienteId): array {
        self::handle();

        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['user_role'] ?? '';

        if (!$userId || empty($pacienteId)) {
            self::abortWith404();
        }

        $db = Database::getInstance()->getConnection();

        // 1. Rol Administrador: Acceso total de auditoría y soporte
        if ($userRole === 'admin') {
            return [
                'puede_ver' => true,
                'puede_editar' => true,
                'es_propio' => false,
                'nivel_acceso' => 'edicion_completa'
            ];
        }

        // 2. Rol Adulto Mayor: Verificar si es el dueño directo del perfil clínico
        if ($userRole === 'adulto_mayor') {
            $stmt = $db->prepare("SELECT id FROM pacientes WHERE id = :pid AND usuario_id = :uid LIMIT 1");
            $stmt->execute([':pid' => $pacienteId, ':uid' => $userId]);
            $esDuenio = (bool) $stmt->fetch();

            if ($esDuenio) {
                return [
                    'puede_ver' => true,
                    // El adulto mayor puede ver y registrar tomas, pero las modificaciones estructurales de fármacos se restringen
                    'puede_editar' => false,
                    'es_propio' => true,
                    'nivel_acceso' => 'lectura'
                ];
            }

            // Si intenta acceder a un ID ajeno, ofuscar con 404
            self::abortWith404();
        }

        // 3. Rol Familiar: Consultar la tabla vinculaciones y el nivel de acceso granular
        $stmtVinc = $db->prepare(
            "SELECT v.nivel_acceso, v.tipo_gestion, v.permiso_edicion 
             FROM vinculaciones v 
             WHERE v.familiar_id = :fid AND v.paciente_id = :pid 
             LIMIT 1"
        );
        $stmtVinc->execute([':fid' => $userId, ':pid' => $pacienteId]);
        $vinculo = $stmtVinc->fetch(PDO::FETCH_ASSOC);

        if ($vinculo) {
            $nivelAcceso = $vinculo['nivel_acceso'] ?? 'edicion_completa';
            $puedeEditar = ($nivelAcceso === 'edicion_completa');

            return [
                'puede_ver' => true,
                'puede_editar' => $puedeEditar,
                'es_propio' => false,
                'nivel_acceso' => $nivelAcceso,
                'tipo_gestion' => $vinculo['tipo_gestion'] ?? 'administrado'
            ];
        }

        // 4. OFUSCACIÓN DE SEGURIDAD: Si falla la comprobación, devolver 404 (no 403)
        self::abortWith404();
    }

    /**
     * Ofusca la respuesta devolviendo un 404 estricto para evitar ataques de enumeración
     */
    private static function abortWith404(): void {
        http_response_code(404);

        if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Recurso clínico no encontrado.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $view404 = dirname(__DIR__) . '/Views/404.php';
        if (file_exists($view404)) {
            require $view404;
        } else {
            echo "404 - Página no encontrada.";
        }
        exit;
    }

    /**
     * Registro estricto de eventos de auditoría clínica en PostgreSQL
     */
    public static function logAudit(string $pacienteId, string $accion, string $detalles): void {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) return;

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare(
                "INSERT INTO logs_auditoria (usuario_id, paciente_id, accion, detalles, ip_origen, user_agent, fecha_utc) 
                 VALUES (:uid, :pid, :accion, :detalles, :ip, :ua, NOW() AT TIME ZONE 'UTC')"
            );
            $stmt->execute([
                ':uid' => $userId,
                ':pid' => $pacienteId,
                ':accion' => $accion,
                ':detalles' => $detalles,
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'
            ]);
        } catch (\Throwable $e) {
            error_log('[AuthMiddleware::logAudit Error] ' . $e->getMessage());
        }
    }
}

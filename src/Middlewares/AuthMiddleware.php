<?php
declare(strict_types=1);

namespace App\Middlewares;

use Config\Database;
use PDO;

class AuthMiddleware {
    /**
     * Matriz Formal de Permisos por Rol en VitalSenior
     */
    public const ROLES_PERMISSIONS = [
        'paciente' => [
            'ver_dashboard_paciente',
            'marcar_toma_propia',
            'activar_sos'
        ],
        'adulto_mayor' => [
            'ver_dashboard_paciente',
            'marcar_toma_propia',
            'activar_sos'
        ],
        'familiar_admin' => [
            'ver_dashboard_cuidador',
            'gestionar_pacientes',
            'gestionar_medicamentos',
            'gestionar_inventario',
            'gestionar_expediente',
            'gestionar_citas',
            'registrar_signos',
            'generar_pin_medico',
            'vincular_cuidadores',
            'ver_auditoria',
            'marcar_toma',
            'posponer_toma',
            'deshacer_toma',
            'ver_citas',
            'ver_contactos_sos'
        ],
        'cuidador' => [
            'ver_dashboard_cuidador',
            'gestionar_pacientes',
            'gestionar_medicamentos',
            'gestionar_inventario',
            'gestionar_expediente',
            'gestionar_citas',
            'registrar_signos',
            'generar_pin_medico',
            'vincular_cuidadores',
            'ver_auditoria',
            'marcar_toma',
            'posponer_toma',
            'deshacer_toma',
            'ver_citas',
            'ver_contactos_sos'
        ],
        'familiar' => [
            'ver_dashboard_cuidador',
            'gestionar_pacientes',
            'gestionar_medicamentos',
            'gestionar_inventario',
            'gestionar_expediente',
            'gestionar_citas',
            'registrar_signos',
            'generar_pin_medico',
            'vincular_cuidadores',
            'ver_auditoria',
            'marcar_toma',
            'posponer_toma',
            'deshacer_toma',
            'ver_citas',
            'ver_contactos_sos'
        ],
        'cuidador_auxiliar' => [
            'ver_dashboard_cuidador',
            'marcar_toma',
            'posponer_toma',
            'registrar_signos',
            'ver_citas',
            'ver_contactos_sos'
        ],
        'medico' => [
            'ver_expediente_clinico',
            'ver_estudios_laboratorio',
            'exportar_pdf_expediente',
            'registrar_nota_evolucion',
            'emitir_receta'
        ],
        'administrador' => [
            'todas_acciones'
        ],
        'admin' => [
            'todas_acciones'
        ]
    ];

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
     * Bloquea peticiones según el rol del usuario
     */
    public static function requireRole(array $rolesPermitidos): void {
        self::handle();

        $userRole = $_SESSION['user_role'] ?? '';

        if (!in_array($userRole, $rolesPermitidos, true) && !in_array($userRole, ['admin', 'administrador'], true)) {
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
     * Valida permisos requeridos evaluando la matriz ROLES_PERMISSIONS
     */
    public static function requirePermission(string $permiso): void {
        self::handle();
        $userRole = $_SESSION['user_role'] ?? '';

        // Si es administrador tiene acceso irrestricto
        if (in_array($userRole, ['admin', 'administrador'], true)) {
            return;
        }

        $permisosRol = self::ROLES_PERMISSIONS[$userRole] ?? [];

        if (in_array('todas_acciones', $permisosRol, true) || in_array($permiso, $permisosRol, true)) {
            return;
        }

        // Si no tiene el permiso requerido
        if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => 'Permiso insuficiente para esta operación clínica.',
                'permiso_requerido' => $permiso,
                'rol_actual' => $userRole
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $_SESSION['flash_notification'] = [
            'type' => 'danger',
            'message' => 'No tienes los permisos necesarios para realizar esta acción.'
        ];
        header('Location: ' . (defined('APP_URL') ? APP_URL : '') . '/dashboard');
        exit;
    }

    /**
     * Valida si un usuario tiene acceso a un paciente específico (retorna booleano)
     */
    public static function autorizarAccesoPaciente($pacienteId, $userId): bool {
        if (empty($pacienteId) || empty($userId)) {
            return false;
        }

        $userRole = $_SESSION['user_role'] ?? '';
        if (in_array($userRole, ['admin', 'administrador'], true)) {
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

            // Comprobar en paciente_cuidador
            $stmtVinc = $db->prepare("SELECT 1 FROM paciente_cuidador WHERE usuario_cuidador_id = :uid AND paciente_id = :pid LIMIT 1");
            $stmtVinc->execute([':uid' => $userId, ':pid' => $pacienteId]);
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
     * Garantiza que el usuario en sesión es el propio paciente o un familiar/cuidador con vínculo activo.
     * Si no tiene acceso, devuelve un error 404 (en lugar de 403) para ofuscar la existencia del recurso.
     */
    public static function requirePatientAccess($pacienteId): array {
        self::handle();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $userRole = $_SESSION['user_role'] ?? '';

        if (!$userId || empty($pacienteId)) {
            self::abortWith404();
        }

        $db = Database::getInstance()->getConnection();

        // 1. Rol Administrador: Acceso total de auditoría y soporte
        if (in_array($userRole, ['admin', 'administrador'], true)) {
            return [
                'puede_ver' => true,
                'puede_editar' => true,
                'es_propio' => false,
                'nivel_acceso' => 'admin'
            ];
        }

        // 2. Verificar si es el dueño directo del perfil clínico (paciente propio)
        $stmt = $db->prepare("SELECT id FROM pacientes WHERE id = :pid AND usuario_id = :uid LIMIT 1");
        $stmt->execute([':pid' => $pacienteId, ':uid' => $userId]);
        $esDuenio = (bool) $stmt->fetch();

        if ($esDuenio) {
            if (in_array($userRole, ['paciente', 'adulto_mayor'], true)) {
                return [
                    'puede_ver' => true,
                    'puede_editar' => false,
                    'es_propio' => true,
                    'nivel_acceso' => 'lectura'
                ];
            } else {
                return [
                    'puede_ver' => true,
                    'puede_editar' => true,
                    'es_propio' => true,
                    'nivel_acceso' => 'familiar_admin'
                ];
            }
        }

        // 3. Rol Familiar / Cuidador: Consultar paciente_cuidador
        $stmtVinc = $db->prepare(
            "SELECT pc.nivel_acceso, pc.parentesco 
             FROM paciente_cuidador pc 
             WHERE pc.usuario_cuidador_id = :uid AND pc.paciente_id = :pid 
             LIMIT 1"
        );
        $stmtVinc->execute([':uid' => $userId, ':pid' => $pacienteId]);
        $vinculo = $stmtVinc->fetch(PDO::FETCH_ASSOC);

        if ($vinculo) {
            $nivelAcceso = $vinculo['nivel_acceso'] ?? 'tomas_y_signos';
            $puedeEditar = in_array($nivelAcceso, ['familiar_admin', 'edicion_completa', 'admin'], true);

            return [
                'puede_ver' => true,
                'puede_editar' => $puedeEditar,
                'es_propio' => false,
                'nivel_acceso' => $nivelAcceso,
                'parentesco' => $vinculo['parentesco'] ?? 'Familiar / Cuidador'
            ];
        }

        // 4. OFUSCACIÓN DE SEGURIDAD: Si falla la comprobación, devolver 404
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
    public static function logAudit($pacienteId, string $accion, string $detalles): void {
        \App\Security\AuditLogger::log($accion, $pacienteId, $detalles);
    }
}

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
            'emitir_receta',
            'vincular_paciente_nip'
        ],
        'doctor' => [
            'ver_expediente_clinico',
            'ver_estudios_laboratorio',
            'exportar_pdf_expediente',
            'registrar_nota_evolucion',
            'emitir_receta',
            'vincular_paciente_nip'
        ],
        'administrador' => [
            'todas_acciones'
        ],
        'admin' => [
            'todas_acciones'
        ]
    ];

    /**
     * Asegura que el usuario tenga rol de Doctor o Médico
     */
    public static function requireDoctorRole(): void {
        self::requireRole(['doctor', 'medico']);
    }

    /**
     * Asegura que exista una sesión activa de usuario y que sea la única sesión autorizada
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

        // Validación estricta de Sesión Única Activa (Protección contra sesiones simultáneas en otro navegador o dispositivo)
        if (!\App\Security\SingleSessionManager::validate()) {
            if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'error' => 'Tu sesión ha sido cerrada porque se inició sesión en otro navegador o dispositivo.',
                    'code' => 'CONCURRENT_SESSION_DETECTED'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            header('Location: ' . (defined('APP_URL') ? APP_URL : '') . '/login');
            exit;
        }
    }

    /**
     * Asegura que el usuario NO esté autenticado (solo para login/registro)
     */
    public static function guestOnly(): void {
        if (!empty($_SESSION['user_id'])) {
            // Verificar si su sesión sigue siendo la activa antes de asumir que está autenticado
            if (!\App\Security\SingleSessionManager::validate()) {
                // Si fue invalidada por otro dispositivo, la sesión se limpió y puede ver el login
                return;
            }
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
     * Valida si un usuario tiene acceso a un paciente específico y retorna la matriz de privilegios
     * Compatible con firmas booleanas y llamadas detalladas (pacienteId, usuarioId, rol)
     */
    public static function autorizarAccesoPaciente($pacienteId, $userId = null, ?string $rol = null): array|bool {
        $pacienteId = (int)$pacienteId;
        $userId = $userId !== null ? (int)$userId : (int)($_SESSION['user_id'] ?? 0);
        $rol = $rol !== null ? $rol : ($_SESSION['user_role'] ?? '');

        if ($pacienteId <= 0 || $userId <= 0) {
            return false;
        }

        if (in_array($rol, ['admin', 'administrador'], true)) {
            return [
                'autorizado' => true,
                'puede_ver' => true,
                'puede_editar' => true,
                'puede_suministrar' => true,
                'es_doctor' => false,
                'es_propio' => false,
                'nivel_acceso' => 'principal',
                'rol' => $rol
            ];
        }

        try {
            $db = Database::getInstance()->getConnection();

            // 1. Verificar si es Médico / Doctor
            if (in_array($rol, ['doctor', 'medico'], true)) {
                $stmtDoc = $db->prepare("
                    SELECT pd.estado, pd.notas_medicas, d.id AS doctor_id, d.nombre AS doctor_nombre 
                    FROM paciente_doctor pd 
                    JOIN doctores d ON pd.doctor_id = d.id 
                    WHERE d.usuario_id = :uid AND pd.paciente_id = :pid AND pd.estado = 'activo' 
                    LIMIT 1
                ");
                $stmtDoc->execute([':uid' => $userId, ':pid' => $pacienteId]);
                $vinculoDoc = $stmtDoc->fetch(PDO::FETCH_ASSOC);

                if ($vinculoDoc) {
                    return [
                        'autorizado' => true,
                        'puede_ver' => true,
                        'puede_editar' => false,
                        'puede_suministrar' => false,
                        'es_doctor' => true,
                        'es_propio' => false,
                        'nivel_acceso' => 'doctor',
                        'rol' => 'doctor',
                        'doctor_id' => $vinculoDoc['doctor_id']
                    ];
                }
                return false;
            }

            // 2. Comprobar si es paciente propio (dueño directo o titular de cuenta)
            $stmt = $db->prepare("SELECT id, usuario_id FROM pacientes WHERE id = :pid AND usuario_id = :uid LIMIT 1");
            $stmt->execute([':pid' => $pacienteId, ':uid' => $userId]);
            if ($stmt->fetch()) {
                if (in_array($rol, ['paciente', 'adulto_mayor'], true)) {
                    return [
                        'autorizado' => true,
                        'puede_ver' => true,
                        'puede_editar' => false,
                        'puede_suministrar' => true,
                        'es_doctor' => false,
                        'es_propio' => true,
                        'nivel_acceso' => 'paciente',
                        'rol' => 'paciente'
                    ];
                } else {
                    return [
                        'autorizado' => true,
                        'puede_ver' => true,
                        'puede_editar' => true,
                        'puede_suministrar' => true,
                        'es_doctor' => false,
                        'es_propio' => true,
                        'nivel_acceso' => 'principal',
                        'rol' => $rol
                    ];
                }
            }

            // 3. Comprobar en paciente_cuidador
            $stmtVinc = $db->prepare("
                SELECT nivel_acceso, parentesco 
                FROM paciente_cuidador 
                WHERE usuario_cuidador_id = :uid AND paciente_id = :pid 
                LIMIT 1
            ");
            $stmtVinc->execute([':uid' => $userId, ':pid' => $pacienteId]);
            $vinculo = $stmtVinc->fetch(PDO::FETCH_ASSOC);

            if ($vinculo) {
                $rawNivel = strtolower(trim((string)($vinculo['nivel_acceso'] ?? 'completo')));
                $esSoloLectura = in_array($rawNivel, ['solo_lectura', 'lectura'], true);
                $nivelFinal = $esSoloLectura ? 'solo_lectura' : 'completo';

                return [
                    'autorizado' => true,
                    'puede_ver' => true,
                    'puede_editar' => !$esSoloLectura,
                    'puede_suministrar' => !$esSoloLectura,
                    'es_doctor' => false,
                    'es_propio' => false,
                    'nivel_acceso' => $nivelFinal,
                    'parentesco' => $vinculo['parentesco'] ?? 'Familiar',
                    'rol' => $rol
                ];
            }

        } catch (\Throwable $e) {
            error_log('[AuthMiddleware::autorizarAccesoPaciente] ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Aislamiento de datos Multitenant y Ofuscación de Seguridad.
     * Garantiza que el usuario en sesión es el propio paciente o un familiar/cuidador/médico con vínculo activo.
     * Si no tiene acceso, devuelve un error 404 (en lugar de 403) para ofuscar la existencia del recurso.
     */
    public static function requirePatientAccess($pacienteId): array {
        self::handle();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $userRole = $_SESSION['user_role'] ?? '';

        if (!$userId || empty($pacienteId)) {
            self::abortWith404();
        }

        $auth = self::autorizarAccesoPaciente($pacienteId, $userId, $userRole);

        if ($auth === false || empty($auth['autorizado'])) {
            self::abortWith404();
        }

        return $auth;
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

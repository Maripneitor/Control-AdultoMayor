<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Usuario extends Model {
    protected $table = 'usuarios';
    
    const ROLES = [
        'admin', 
        'cuidador_principal', 
        'cuidador_secundario', 
        'medico', 
        'adulto_mayor'
    ];

    const PERMISOS = [
        'admin' => ['todas_acciones'],
        'cuidador_principal' => ['ver_dashboard', 'gestionar_citas', 'gestionar_medicamentos', 'gestionar_signos', 'gestionar_contactos', 'ver_reportes'],
        'cuidador_secundario' => ['ver_dashboard', 'marcar_tomas', 'registrar_signos', 'ver_contactos'],
        'medico' => ['ver_dashboard', 'ver_reportes', 'gestionar_citas', 'gestionar_medicamentos'],
        'adulto_mayor' => ['ver_dashboard_simplificado', 'marcar_tomas', 'boton_sos']
    ];
    
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("
            SELECT id, nombre, email, password_hash, rol, activo, created_at 
            FROM {$this->table} 
            WHERE email = :email LIMIT 1
        ");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function cargarPermisosRol(string $rol): array {
        return self::PERMISOS[$rol] ?? self::PERMISOS['cuidador_principal'] ?? ['ver_dashboard'];
    }

    public function registrarAuditoria(string $accion, string $detalle): void {
        \App\Security\AuditLogger::log($accion, $_SESSION['paciente_activo_id'] ?? null, $detalle);
    }

    public function register(string $nombre, string $email, string $telefono, string $password, string $rol = 'cuidador'): int {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Mapear rol a los valores del enum en PostgreSQL ('administrador', 'cuidador', 'emergencia')
        $dbRol = 'cuidador';
        if (in_array($rol, ['admin', 'administrador'], true)) {
            $dbRol = 'administrador';
        } elseif (in_array($rol, ['emergencia'], true)) {
            $dbRol = 'emergencia';
        } else {
            $dbRol = 'cuidador';
        }

        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, email, password_hash, rol, activo) 
            VALUES (:nombre, :email, :password_hash, :rol, true) RETURNING id
        ");
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'password_hash' => $hash,
            'rol' => $dbRol
        ]);
        
        $newId = (int) $stmt->fetchColumn();
        error_log("[AUDIT " . date('Y-m-d H:i:s') . "] UserID $newId registrado con rol $dbRol");

        return $newId;
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare("UPDATE usuarios SET password_hash = :hash, updated_at = NOW() WHERE id = :id");
        
        $this->registrarAuditoria("UPDATE_PASSWORD", "Contraseña actualizada para UserID $userId");
        
        return $stmt->execute(['hash' => $hash, 'id' => $userId]);
    }

    public function updateProfile(int $userId, string $nombre, string $email, string $telefono = '', bool $alertas_whatsapp = false): bool {
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET nombre = :nombre, email = :email, updated_at = NOW() WHERE id = :id");
            $res = $stmt->execute([
                'nombre' => $nombre,
                'email' => $email,
                'id' => $userId
            ]);
            $this->registrarAuditoria("UPDATE_PROFILE", "Perfil actualizado para UserID $userId");
            return $res;
        } catch (\PDOException $e) {
            error_log('[Usuario::updateProfile Error] ' . $e->getMessage());
            return false;
        }
    }
}


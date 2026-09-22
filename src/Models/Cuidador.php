<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Cuidador extends Model {
    protected $table = 'paciente_cuidador';

    public function agregarCuidador(int $pacienteId, int $usuarioId, string $parentesco, string $nivelAcceso = 'tomas_y_citas'): int {
        return $this->create([
            'paciente_id' => $pacienteId,
            'usuario_cuidador_id' => $usuarioId,
            'parentesco' => $parentesco,
            'nivel_acceso' => $nivelAcceso
        ]);
    }

    public function obtenerPacientesPorCuidador(int $usuarioId): array {
        $stmt = $this->db->prepare("
            SELECT p.id, p.usuario_id, p.qr_token, p.qr_token AS uuid_publico, p.nombre, p.apellidos, p.fecha_nacimiento, p.tipo_sangre, p.codigo_vinculacion, pc.parentesco, pc.nivel_acceso 
            FROM pacientes p
            JOIN paciente_cuidador pc ON p.id = pc.paciente_id
            WHERE pc.usuario_cuidador_id = :uid
        ");
        $stmt->execute(['uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


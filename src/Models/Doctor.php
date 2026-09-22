<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Doctor extends Model {
    protected $table = 'doctores';

    public function getByPacienteId(int $pacienteId): array {
        $stmt = $this->db->prepare("
            SELECT id, paciente_id, nombre, especialidad, hospital_clinica, telefono_contacto, telefono_contacto AS telefono, horario_atencion 
            FROM doctores 
            WHERE paciente_id = :pid 
            ORDER BY nombre ASC
        ");
        $stmt->execute(['pid' => $pacienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO doctores (paciente_id, nombre, especialidad, telefono_contacto, hospital_clinica, horario_atencion) VALUES (:paciente_id, :nombre, :especialidad, :telefono, :hospital_clinica, :horario_atencion)");
        return $stmt->execute([
            'paciente_id' => $data['paciente_id'],
            'nombre' => $data['nombre'],
            'especialidad' => $data['especialidad'] ?? '',
            'telefono' => $data['telefono'] ?? ($data['telefono_contacto'] ?? ''),
            'hospital_clinica' => $data['hospital_clinica'] ?? null,
            'horario_atencion' => $data['horario_atencion'] ?? null
        ]);
    }

    public function eliminar(int $id, int $pacienteId): bool {
        $stmt = $this->db->prepare("DELETE FROM doctores WHERE id = :id AND paciente_id = :paciente_id");
        return $stmt->execute([
            'id' => $id,
            'paciente_id' => $pacienteId
        ]);
    }
}

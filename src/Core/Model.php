<?php
namespace App\Core;
use Config\Database;
use PDO;

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Sanitiza y proyecta columnas específicas para prevenir SELECT * indiscriminado
     */
    protected function buildColumnProjection(array $columns = []): string {
        if (empty($columns)) {
            // Si la clase hija define columnas por defecto, usarlas; caso contrario campos base
            return property_exists($this, 'safeColumns') && !empty($this->safeColumns)
                ? implode(', ', $this->safeColumns)
                : 'id, created_at';
        }
        $sanitized = array_map(function(string $col): string {
            return preg_replace('/[^a-zA-Z0-9_\.]/', '', trim($col));
        }, $columns);
        return implode(', ', array_filter($sanitized));
    }

    public function all(array $columns = []): array {
        $cols = !empty($columns) ? $this->buildColumnProjection($columns) : '*';
        $stmt = $this->db->prepare("SELECT {$cols} FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id, array $columns = []): ?array {
        $cols = !empty($columns) ? $this->buildColumnProjection($columns) : '*';
        $stmt = $this->db->prepare("SELECT {$cols} FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public function where(string $column, $value, array $columns = []): array {
        $cols = !empty($columns) ? $this->buildColumnProjection($columns) : '*';
        $cleanCol = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $stmt = $this->db->prepare("SELECT {$cols} FROM {$this->table} WHERE {$cleanCol} = :val");
        $stmt->bindValue(':val', $value);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ", ");
        
        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}

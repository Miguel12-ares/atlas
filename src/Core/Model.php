<?php
/**
 * Sistema Atlas - Clase Base Model
 * 
 * Modelo base del que heredarán todos los modelos
 * Proporciona métodos comunes para operaciones de base de datos
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

use PDO;

class Model
{
    /**
     * Instancia de la base de datos
     * @var Database
     */
    protected Database $db;

    /**
     * Nombre de la tabla asociada al modelo
     * @var string
     */
    protected string $table = '';

    /**
     * Clave primaria de la tabla
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Constructor del modelo
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los registros de la tabla
     * 
     * @return array Array con todos los registros
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Obtiene un registro por su ID
     * 
     * @param int $id ID del registro
     * @return array|false Registro encontrado o false
     */
    public function find(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Encuentra registros que coincidan con las condiciones
     * 
     * @param array $conditions Array asociativo de condiciones (columna => valor)
     * @return array Array con los registros encontrados
     */
    public function where(array $conditions): array
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $params[] = $value;
        }

        $whereString = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereString}";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Encuentra un único registro que coincida con las condiciones
     * 
     * @param array $conditions Array asociativo de condiciones
     * @return array|false Registro encontrado o false
     */
    public function findWhere(array $conditions)
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = ?";
            $params[] = $value;
        }

        $whereString = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereString} LIMIT 1";

        return $this->db->fetch($sql, $params);
    }

    /**
     * Inserta un nuevo registro en la tabla
     * 
     * @param array $data Array asociativo con los datos (columna => valor)
     * @return string|false ID del registro insertado o false
     */
    public function create(array $data)
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $columnsString = implode(', ', $columns);
        $placeholdersString = implode(', ', $placeholders);

        $sql = "INSERT INTO {$this->table} ({$columnsString}) VALUES ({$placeholdersString})";

        if ($this->db->execute($sql, array_values($data))) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Actualiza un registro existente
     * 
     * @param int $id ID del registro a actualizar
     * @param array $data Array asociativo con los datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function update(int $id, array $data): bool
    {
        $setClauses = [];
        $params = [];

        foreach ($data as $column => $value) {
            $setClauses[] = "{$column} = ?";
            $params[] = $value;
        }

        $params[] = $id;

        $setString = implode(', ', $setClauses);
        $sql = "UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = ?";

        return $this->db->execute($sql, $params);
    }

    /**
     * Elimina un registro
     * 
     * @param int $id ID del registro a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Cuenta los registros de la tabla
     * 
     * @param array $conditions Condiciones opcionales para el conteo
     * @return int Número de registros
     */
    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->db->fetch($sql);
        } else {
            $whereClauses = [];
            $params = [];

            foreach ($conditions as $column => $value) {
                $whereClauses[] = "{$column} = ?";
                $params[] = $value;
            }

            $whereString = implode(' AND ', $whereClauses);
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereString}";
            $result = $this->db->fetch($sql, $params);
        }

        return (int) $result['total'];
    }

    /**
     * Ejecuta una consulta SQL personalizada
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parámetros para la consulta
     * @return array Array con los resultados
     */
    public function query(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }
}


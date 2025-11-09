<?php
/**
 * Sistema Atlas - Clase Database
 * 
 * Implementa el patrón Singleton para conexión a base de datos usando PDO
 * Proporciona métodos helper para operaciones comunes
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    /**
     * Instancia única de la clase (Singleton)
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * Objeto de conexión PDO
     * @var PDO|null
     */
    private ?PDO $connection = null;

    /**
     * Configuración de la base de datos
     * @var array
     */
    private array $config = [
        'host' => 'mysql',  // Nombre del servicio Docker
        'port' => '3306',
        'dbname' => 'atlas_db',
        'username' => 'root',
        'password' => 'atlas_root_2024',
        'charset' => 'utf8mb4'
    ];

    /**
     * Constructor privado para implementar Singleton
     * Establece la conexión a la base de datos
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Previene la clonación del objeto (Singleton)
     */
    private function __clone() {}

    /**
     * Previene la deserialización del objeto (Singleton)
     */
    public function __wakeup()
    {
        throw new \Exception("No se puede deserializar un Singleton");
    }

    /**
     * Obtiene la instancia única de la clase (Singleton)
     * 
     * @return Database Instancia única de Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establece la conexión a la base de datos usando PDO
     * 
     * @return void
     * @throws PDOException Si la conexión falla
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config['host'],
                $this->config['port'],
                $this->config['dbname'],
                $this->config['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $options
            );

        } catch (PDOException $e) {
            // Log del error (en producción se debería usar un sistema de logs)
            error_log("Error de conexión a la base de datos: " . $e->getMessage());
            throw new PDOException("Error al conectar con la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene la conexión PDO activa
     * 
     * @return PDO Objeto de conexión PDO
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Ejecuta una consulta SQL preparada
     * 
     * @param string $sql Consulta SQL con placeholders
     * @param array $params Parámetros para la consulta preparada
     * @return PDOStatement Objeto PDOStatement con el resultado
     * @throws PDOException Si la consulta falla
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en query: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }

    /**
     * Ejecuta una consulta INSERT, UPDATE o DELETE
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parámetros para la consulta
     * @return bool True si se ejecutó correctamente
     */
    public function execute(string $sql, array $params = []): bool
    {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error en execute: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene todos los registros de una consulta
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parámetros para la consulta
     * @return array Array con todos los registros
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un único registro de una consulta
     * 
     * @param string $sql Consulta SQL
     * @param array $params Parámetros para la consulta
     * @return array|false Registro encontrado o false
     */
    public function fetch(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Obtiene el ID del último registro insertado
     * 
     * @return string ID del último insert
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Inicia una transacción
     * 
     * @return bool True si se inició correctamente
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Confirma una transacción
     * 
     * @return bool True si se confirmó correctamente
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Revierte una transacción
     * 
     * @return bool True si se revirtió correctamente
     */
    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Cierra la conexión a la base de datos
     * 
     * @return void
     */
    public function close(): void
    {
        $this->connection = null;
    }
}


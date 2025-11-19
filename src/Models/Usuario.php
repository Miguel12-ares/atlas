<?php
/**
 * Sistema Atlas - Modelo Usuario
 * 
 * Maneja las operaciones de base de datos relacionadas con usuarios
 * 
 * @package Atlas\Models
 * @version 1.0
 */

namespace Atlas\Models;

use Atlas\Core\Database;

class Usuario
{
    /**
     * Instancia de la conexión a base de datos
     * @var Database
     */
    private Database $db;

    /**
     * Constructor del modelo
     * Inicializa la conexión a base de datos
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca un usuario por su número de identificación
     * 
     * @param string $numero_identificacion Número de identificación del usuario
     * @return array|false Array con datos del usuario o false si no existe
     */
    public function findByIdentificacion(string $numero_identificacion)
    {
        try {
            // Consulta con JOIN a la tabla roles para obtener información completa
            $sql = "SELECT 
                        u.id_usuario,
                        u.numero_identificacion,
                        u.nombres,
                        u.apellidos,
                        u.email,
                        u.telefono,
                        u.password_hash,
                        u.id_rol,
                        r.nombre_rol,
                        r.puede_tener_equipo,
                        u.estado,
                        u.created_at,
                        u.updated_at
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.numero_identificacion = ? 
                    AND u.estado = 'activo'
                    LIMIT 1";

            // Ejecutar consulta con prepared statement
            $resultado = $this->db->fetch($sql, [$numero_identificacion]);

            return $resultado;

        } catch (\PDOException $e) {
            // Log del error (en producción usar sistema de logs apropiado)
            error_log("Error en Usuario::findByIdentificacion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios del sistema
     * 
     * @param string $estado Estado del usuario (activo, inactivo, suspendido) - opcional
     * @return array Array con todos los usuarios
     */
    public function getAllUsers(string $estado = null): array
    {
        try {
            $sql = "SELECT 
                        u.id_usuario,
                        u.numero_identificacion,
                        u.nombres,
                        u.apellidos,
                        u.email,
                        u.telefono,
                        u.id_rol,
                        r.nombre_rol,
                        u.estado,
                        u.created_at,
                        u.updated_at
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol";

            $params = [];

            // Filtrar por estado si se proporciona
            if ($estado !== null) {
                $sql .= " WHERE u.estado = ?";
                $params[] = $estado;
            }

            $sql .= " ORDER BY u.created_at DESC";

            return $this->db->fetchAll($sql, $params);

        } catch (\PDOException $e) {
            error_log("Error en Usuario::getAllUsers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca un usuario por su ID
     * 
     * @param int $id_usuario ID del usuario
     * @return array|false Array con datos del usuario o false si no existe
     */
    public function findById(int $id_usuario)
    {
        try {
            $sql = "SELECT 
                        u.id_usuario,
                        u.numero_identificacion,
                        u.nombres,
                        u.apellidos,
                        u.email,
                        u.telefono,
                        u.id_rol,
                        r.nombre_rol,
                        r.puede_tener_equipo,
                        u.estado,
                        u.created_at,
                        u.updated_at
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.id_usuario = ?
                    LIMIT 1";

            return $this->db->fetch($sql, [$id_usuario]);

        } catch (\PDOException $e) {
            error_log("Error en Usuario::findById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un usuario por su email
     * 
     * @param string $email Email del usuario
     * @return array|false Array con datos del usuario o false si no existe
     */
    public function findByEmail(string $email)
    {
        try {
            $sql = "SELECT 
                        u.id_usuario,
                        u.numero_identificacion,
                        u.nombres,
                        u.apellidos,
                        u.email,
                        u.telefono,
                        u.password_hash,
                        u.id_rol,
                        r.nombre_rol,
                        u.estado
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.email = ?
                    LIMIT 1";

            return $this->db->fetch($sql, [$email]);

        } catch (\PDOException $e) {
            error_log("Error en Usuario::findByEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un nuevo usuario en la base de datos
     * 
     * @param array $data Datos del usuario
     * @return int|false ID del usuario creado o false si falla
     */
    public function create(array $data)
    {
        try {
            $sql = "INSERT INTO usuarios (
                        numero_identificacion,
                        nombres,
                        apellidos,
                        email,
                        telefono,
                        password_hash,
                        id_rol,
                        estado
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $data['numero_identificacion'],
                $data['nombres'],
                $data['apellidos'],
                $data['email'],
                $data['telefono'] ?? null,
                $data['password_hash'],
                $data['id_rol'],
                $data['estado'] ?? 'activo'
            ];

            $this->db->execute($sql, $params);
            return (int)$this->db->lastInsertId();

        } catch (\PDOException $e) {
            error_log("Error en Usuario::create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un usuario existente
     * 
     * @param int $id_usuario ID del usuario
     * @param array $data Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function update(int $id_usuario, array $data): bool
    {
        try {
            $sql = "UPDATE usuarios SET 
                        numero_identificacion = ?,
                        nombres = ?,
                        apellidos = ?,
                        email = ?,
                        telefono = ?,
                        id_rol = ?,
                        estado = ?
                    WHERE id_usuario = ?";

            $params = [
                $data['numero_identificacion'],
                $data['nombres'],
                $data['apellidos'],
                $data['email'],
                $data['telefono'] ?? null,
                $data['id_rol'],
                $data['estado'] ?? 'activo',
                $id_usuario
            ];

            return $this->db->execute($sql, $params);

        } catch (\PDOException $e) {
            error_log("Error en Usuario::update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la contraseña de un usuario
     * 
     * @param int $id_usuario ID del usuario
     * @param string $password_hash Hash de la nueva contraseña
     * @return bool True si se actualizó correctamente
     */
    public function updatePassword(int $id_usuario, string $password_hash): bool
    {
        try {
            $sql = "UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?";
            return $this->db->execute($sql, [$password_hash, $id_usuario]);

        } catch (\PDOException $e) {
            error_log("Error en Usuario::updatePassword: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario (soft delete - cambia estado a inactivo)
     * 
     * @param int $id_usuario ID del usuario
     * @return bool True si se eliminó correctamente
     */
    public function delete(int $id_usuario): bool
    {
        try {
            $sql = "UPDATE usuarios SET estado = 'inactivo' WHERE id_usuario = ?";
            return $this->db->execute($sql, [$id_usuario]);

        } catch (\PDOException $e) {
            error_log("Error en Usuario::delete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si existe un número de identificación en el sistema
     * 
     * @param string $numero_identificacion Número de identificación
     * @param int $except_id ID del usuario a excluir de la búsqueda (para updates)
     * @return bool True si existe
     */
    public function existsIdentificacion(string $numero_identificacion, int $except_id = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE numero_identificacion = ?";
            $params = [$numero_identificacion];

            if ($except_id !== null) {
                $sql .= " AND id_usuario != ?";
                $params[] = $except_id;
            }

            $result = $this->db->fetch($sql, $params);
            return $result['total'] > 0;

        } catch (\PDOException $e) {
            error_log("Error en Usuario::existsIdentificacion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si existe un email en el sistema
     * 
     * @param string $email Email
     * @param int $except_id ID del usuario a excluir de la búsqueda (para updates)
     * @return bool True si existe
     */
    public function existsEmail(string $email, int $except_id = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = ?";
            $params = [$email];

            if ($except_id !== null) {
                $sql .= " AND id_usuario != ?";
                $params[] = $except_id;
            }

            $result = $this->db->fetch($sql, $params);
            return $result['total'] > 0;

        } catch (\PDOException $e) {
            error_log("Error en Usuario::existsEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los roles disponibles para registro público
     * Excluye admin y portería (solo pueden ser asignados por admin)
     * 
     * @return array Array de roles
     */
    public function getRolesForRegistration(): array
    {
        try {
            $sql = "SELECT id_rol, nombre_rol, descripcion 
                    FROM roles 
                    WHERE id_rol NOT IN (1, 6) 
                    ORDER BY nombre_rol ASC";

            return $this->db->fetchAll($sql);

        } catch (\PDOException $e) {
            error_log("Error en Usuario::getRolesForRegistration: " . $e->getMessage());
            return [];
        }
    }
}


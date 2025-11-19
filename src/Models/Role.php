<?php
/**
 * Sistema Atlas - Modelo Role
 * 
 * Maneja las operaciones relacionadas con roles y sus permisos
 * Implementa RBAC (Role-Based Access Control)
 * 
 * @package Atlas\Models
 * @version 1.0
 */

namespace Atlas\Models;

use Atlas\Core\Database;

class Role
{
    /**
     * ID del rol
     * @var int
     */
    public int $id_rol;

    /**
     * Nombre del rol
     * @var string
     */
    public string $nombre_rol;

    /**
     * Descripción del rol
     * @var string|null
     */
    public ?string $descripcion;

    /**
     * Indica si el rol puede tener equipos
     * @var bool
     */
    public bool $puede_tener_equipo;

    /**
     * Array de permisos del rol
     * @var array
     */
    public array $permisos = [];

    /**
     * Instancia de la base de datos
     * @var Database
     */
    private Database $db;

    /**
     * Constructor
     * 
     * @param int|null $id_rol ID del rol a cargar
     */
    public function __construct(?int $id_rol = null)
    {
        $this->db = Database::getInstance();
        
        if ($id_rol !== null) {
            $this->load($id_rol);
        }
    }

    /**
     * Carga un rol desde la base de datos con sus permisos
     * 
     * @param int $id_rol ID del rol
     * @return bool True si se cargó correctamente
     */
    public function load(int $id_rol): bool
    {
        try {
            // Cargar datos básicos del rol
            $sql = "SELECT * FROM roles WHERE id_rol = ? LIMIT 1";
            $rol = $this->db->fetch($sql, [$id_rol]);

            if (!$rol) {
                return false;
            }

            $this->id_rol = (int)$rol['id_rol'];
            $this->nombre_rol = $rol['nombre_rol'];
            $this->descripcion = $rol['descripcion'];
            $this->puede_tener_equipo = (bool)$rol['puede_tener_equipo'];

            // Cargar permisos del rol
            $this->permisos = $this->getRolePerms($id_rol);

            return true;

        } catch (\Exception $e) {
            error_log("Error al cargar rol: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los permisos de un rol
     * 
     * @param int $role_id ID del rol
     * @return array Array de permisos
     */
    public function getRolePerms(int $role_id): array
    {
        try {
            $sql = "SELECT p.* 
                    FROM permisos p
                    INNER JOIN role_perm rp ON p.id_permiso = rp.id_permiso
                    WHERE rp.id_rol = ?
                    ORDER BY p.recurso, p.accion";

            $permisos = $this->db->fetchAll($sql, [$role_id]);

            // Organizar permisos por recurso
            $permisosPorRecurso = [];
            foreach ($permisos as $permiso) {
                $recurso = $permiso['recurso'];
                if (!isset($permisosPorRecurso[$recurso])) {
                    $permisosPorRecurso[$recurso] = [];
                }
                $permisosPorRecurso[$recurso][] = $permiso['accion'];
            }

            return $permisosPorRecurso;

        } catch (\Exception $e) {
            error_log("Error al obtener permisos del rol: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si el rol tiene un permiso específico
     * 
     * @param string $recurso Recurso (ej: 'usuarios')
     * @param string $accion Acción (ej: 'crear')
     * @return bool True si tiene el permiso
     */
    public function hasPermission(string $recurso, string $accion): bool
    {
        // Admin siempre tiene todos los permisos
        if ($this->nombre_rol === 'admin') {
            return true;
        }

        if (!isset($this->permisos[$recurso])) {
            return false;
        }

        return in_array($accion, $this->permisos[$recurso]) || 
               in_array('gestionar', $this->permisos[$recurso]);
    }

    /**
     * Obtiene todos los roles del sistema
     * 
     * @return array Array de roles
     */
    public static function getAll(): array
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT * FROM roles ORDER BY id_rol ASC";
            return $db->fetchAll($sql);

        } catch (\Exception $e) {
            error_log("Error al obtener roles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene un rol por su nombre
     * 
     * @param string $nombre_rol Nombre del rol
     * @return Role|null Rol encontrado o null
     */
    public static function getByName(string $nombre_rol): ?Role
    {
        try {
            $db = Database::getInstance();
            $sql = "SELECT id_rol FROM roles WHERE nombre_rol = ? LIMIT 1";
            $result = $db->fetch($sql, [$nombre_rol]);

            if ($result) {
                return new self($result['id_rol']);
            }

            return null;

        } catch (\Exception $e) {
            error_log("Error al obtener rol por nombre: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Asigna un permiso al rol
     * 
     * @param int $id_permiso ID del permiso
     * @return bool True si se asignó correctamente
     */
    public function assignPermission(int $id_permiso): bool
    {
        try {
            $sql = "INSERT IGNORE INTO role_perm (id_rol, id_permiso) VALUES (?, ?)";
            return $this->db->execute($sql, [$this->id_rol, $id_permiso]);

        } catch (\Exception $e) {
            error_log("Error al asignar permiso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remueve un permiso del rol
     * 
     * @param int $id_permiso ID del permiso
     * @return bool True si se removió correctamente
     */
    public function removePermission(int $id_permiso): bool
    {
        try {
            $sql = "DELETE FROM role_perm WHERE id_rol = ? AND id_permiso = ?";
            return $this->db->execute($sql, [$this->id_rol, $id_permiso]);

        } catch (\Exception $e) {
            error_log("Error al remover permiso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los recursos accesibles para el rol
     * 
     * @return array Array de recursos
     */
    public function getAccessibleResources(): array
    {
        return array_keys($this->permisos);
    }

    /**
     * Convierte el rol a array
     * 
     * @return array Datos del rol
     */
    public function toArray(): array
    {
        return [
            'id_rol' => $this->id_rol,
            'nombre_rol' => $this->nombre_rol,
            'descripcion' => $this->descripcion,
            'puede_tener_equipo' => $this->puede_tener_equipo,
            'permisos' => $this->permisos
        ];
    }
}


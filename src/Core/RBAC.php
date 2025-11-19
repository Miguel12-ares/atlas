<?php
/**
 * Sistema Atlas - Clase RBAC
 * 
 * Role-Based Access Control (Control de Acceso Basado en Roles)
 * Define permisos y capacidades por rol
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

class RBAC
{
    /**
     * Definición de permisos por rol
     * @var array
     */
    private static array $permissions = [
        'admin' => [
            'usuarios' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'equipos' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'registros' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'anomalias' => ['crear', 'leer', 'actualizar', 'eliminar'],
            'configuracion' => ['leer', 'actualizar'],
            'reportes' => ['generar', 'exportar'],
            'roles' => ['gestionar']
        ],
        'administrativo' => [
            'usuarios' => ['crear', 'leer', 'actualizar'],
            'equipos' => ['crear', 'leer', 'actualizar'],
            'registros' => ['leer'],
            'anomalias' => ['leer', 'actualizar'],
            'reportes' => ['generar', 'exportar']
        ],
        'instructor' => [
            'equipos' => ['crear', 'leer', 'actualizar'],
            'registros' => ['leer'],
            'perfil' => ['actualizar']
        ],
        'aprendiz' => [
            'equipos' => ['crear', 'leer', 'actualizar'],
            'registros' => ['leer'],
            'perfil' => ['actualizar']
        ],
        'civil' => [
            'equipos' => ['crear', 'leer', 'actualizar'],
            'registros' => ['leer'],
            'perfil' => ['actualizar']
        ],
        'porteria' => [
            'equipos' => ['leer'],
            'registros' => ['crear', 'leer'],
            'anomalias' => ['crear', 'leer']
        ]
    ];

    /**
     * Verifica si un rol tiene un permiso específico
     * 
     * @param string $role Nombre del rol
     * @param string $resource Recurso (ej: 'usuarios', 'equipos')
     * @param string $action Acción (ej: 'crear', 'leer', 'actualizar', 'eliminar')
     * @return bool True si tiene el permiso
     */
    public static function can(string $role, string $resource, string $action): bool
    {
        // Admin tiene todos los permisos
        if ($role === 'admin') {
            return true;
        }

        // Intentar obtener desde base de datos si existen las tablas
        try {
            $db = Database::getInstance();
            
            // Verificar si existe la tabla permisos
            $tableExists = $db->fetch("SHOW TABLES LIKE 'permisos'");
            
            if ($tableExists) {
                // Obtener desde base de datos
                $sql = "SELECT COUNT(*) as count
                        FROM permisos p
                        INNER JOIN role_perm rp ON p.id_permiso = rp.id_permiso
                        INNER JOIN roles r ON rp.id_rol = r.id_rol
                        WHERE r.nombre_rol = ?
                        AND p.recurso = ?
                        AND (p.accion = ? OR p.accion = 'gestionar')";
                
                $result = $db->fetch($sql, [$role, $resource, $action]);
                return $result && $result['count'] > 0;
            }
        } catch (\Exception $e) {
            // Si falla, usar permisos hardcodeados
            error_log("Error al verificar permisos en BD, usando fallback: " . $e->getMessage());
        }

        // Fallback: Verificar en array hardcodeado
        if (!isset(self::$permissions[$role]) || !isset(self::$permissions[$role][$resource])) {
            return false;
        }

        // Verificar si tiene el permiso específico
        return in_array($action, self::$permissions[$role][$resource]);
    }

    /**
     * Verifica si el usuario autenticado puede realizar una acción
     * 
     * @param string $resource Recurso
     * @param string $action Acción
     * @return bool True si puede realizar la acción
     */
    public static function userCan(string $resource, string $action): bool
    {
        $role = Auth::role();
        
        if (!$role) {
            return false;
        }

        return self::can($role, $resource, $action);
    }

    /**
     * Requiere un permiso específico, lanza excepción si no lo tiene
     * 
     * @param string $resource Recurso
     * @param string $action Acción
     * @throws \Exception Si no tiene el permiso
     * @return void
     */
    public static function requirePermission(string $resource, string $action): void
    {
        if (!self::userCan($resource, $action)) {
            http_response_code(403);
            throw new \Exception("No tienes permisos para realizar esta acción");
        }
    }

    /**
     * Obtiene todos los permisos de un rol
     * 
     * @param string $role Nombre del rol
     * @return array Array de permisos
     */
    public static function getPermissions(string $role): array
    {
        return self::$permissions[$role] ?? [];
    }

    /**
     * Verifica si un rol puede tener equipos
     * 
     * @param string $role Nombre del rol
     * @return bool True si puede tener equipos
     */
    public static function canHaveEquipment(string $role): bool
    {
        $db = Database::getInstance();
        $result = $db->fetch("SELECT puede_tener_equipo FROM roles WHERE nombre_rol = ?", [$role]);
        
        return $result ? (bool) $result['puede_tener_equipo'] : false;
    }

    /**
     * Obtiene los recursos a los que un rol tiene acceso
     * 
     * @param string $role Nombre del rol
     * @return array Array de recursos
     */
    public static function getAccessibleResources(string $role): array
    {
        $permissions = self::getPermissions($role);
        return array_keys($permissions);
    }
}


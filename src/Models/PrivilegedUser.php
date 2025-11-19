<?php
/**
 * Sistema Atlas - Modelo PrivilegedUser
 * 
 * Extiende el modelo Usuario con métodos para verificar permisos
 * Implementa verificación de privilegios basada en roles (RBAC)
 * 
 * @package Atlas\Models
 * @version 1.0
 */

namespace Atlas\Models;

use Atlas\Models\Usuario;
use Atlas\Models\Role;
use Atlas\Core\Database;

class PrivilegedUser extends Usuario
{
    /**
     * Objeto Role con los permisos del usuario
     * @var Role|null
     */
    private ?Role $role = null;

    /**
     * Datos del usuario cargados
     * @var array|null
     */
    private ?array $userData = null;

    /**
     * Constructor
     * 
     * @param int|null $userId ID del usuario a cargar
     */
    public function __construct(?int $userId = null)
    {
        parent::__construct();
        
        if ($userId !== null) {
            $this->loadUserWithPermissions($userId);
        }
    }

    /**
     * Carga un usuario con sus permisos
     * 
     * @param int $userId ID del usuario
     * @return bool True si se cargó correctamente
     */
    public function loadUserWithPermissions(int $userId): bool
    {
        try {
            // Cargar datos del usuario
            $this->userData = $this->findById($userId);

            if (!$this->userData) {
                return false;
            }

            // Cargar el rol con sus permisos
            $this->role = new Role($this->userData['id_rol']);

            return true;

        } catch (\Exception $e) {
            error_log("Error al cargar usuario privilegiado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea una instancia desde la sesión actual
     * 
     * @return PrivilegedUser|null Usuario privilegiado o null
     */
    public static function fromSession(): ?PrivilegedUser
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $user = new self($_SESSION['user_id']);
        
        if ($user->userData === null) {
            return null;
        }

        return $user;
    }

    /**
     * Verifica si el usuario tiene un privilegio específico
     * 
     * @param string $permission Permiso en formato 'recurso.accion' (ej: 'usuarios.crear')
     * @return bool True si tiene el privilegio
     */
    public function hasPrivilege(string $permission): bool
    {
        if ($this->role === null) {
            return false;
        }

        // Separar recurso y acción
        $parts = explode('.', $permission);
        
        if (count($parts) !== 2) {
            error_log("Formato de permiso inválido: {$permission}. Use 'recurso.accion'");
            return false;
        }

        [$recurso, $accion] = $parts;

        return $this->role->hasPermission($recurso, $accion);
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * 
     * @param string $role_name Nombre del rol
     * @return bool True si tiene el rol
     */
    public function hasRole(string $role_name): bool
    {
        if ($this->role === null) {
            return false;
        }

        return $this->role->nombre_rol === $role_name;
    }

    /**
     * Verifica si el usuario tiene alguno de los roles especificados
     * 
     * @param array $roles Array de nombres de roles
     * @return bool True si tiene alguno de los roles
     */
    public function hasAnyRole(array $roles): bool
    {
        if ($this->role === null) {
            return false;
        }

        return in_array($this->role->nombre_rol, $roles);
    }

    /**
     * Verifica si el usuario puede gestionar un recurso específico
     * 
     * @param string $recurso Nombre del recurso
     * @return bool True si puede gestionar el recurso
     */
    public function canManage(string $recurso): bool
    {
        return $this->hasPrivilege("{$recurso}.gestionar");
    }

    /**
     * Obtiene todos los permisos del usuario
     * 
     * @return array Array de permisos organizados por recurso
     */
    public function getPermissions(): array
    {
        if ($this->role === null) {
            return [];
        }

        return $this->role->permisos;
    }

    /**
     * Obtiene los recursos accesibles para el usuario
     * 
     * @return array Array de nombres de recursos
     */
    public function getAccessibleResources(): array
    {
        if ($this->role === null) {
            return [];
        }

        return $this->role->getAccessibleResources();
    }

    /**
     * Obtiene el nombre del rol del usuario
     * 
     * @return string|null Nombre del rol o null
     */
    public function getRoleName(): ?string
    {
        if ($this->role === null) {
            return null;
        }

        return $this->role->nombre_rol;
    }

    /**
     * Obtiene el objeto Role
     * 
     * @return Role|null Objeto Role o null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * Obtiene los datos del usuario
     * 
     * @return array|null Datos del usuario o null
     */
    public function getUserData(): ?array
    {
        return $this->userData;
    }

    /**
     * Verifica si el usuario puede tener equipos
     * 
     * @return bool True si puede tener equipos
     */
    public function canHaveEquipment(): bool
    {
        if ($this->role === null) {
            return false;
        }

        return $this->role->puede_tener_equipo;
    }

    /**
     * Verifica si el usuario es administrador
     * 
     * @return bool True si es administrador
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Verifica si el usuario es portería
     * 
     * @return bool True si es portería
     */
    public function isPorteria(): bool
    {
        return $this->hasRole('porteria');
    }

    /**
     * Obtiene el ID del usuario
     * 
     * @return int|null ID del usuario o null
     */
    public function getId(): ?int
    {
        return $this->userData['id_usuario'] ?? null;
    }

    /**
     * Convierte el usuario privilegiado a array
     * 
     * @return array Datos del usuario con permisos
     */
    public function toArray(): array
    {
        if ($this->userData === null) {
            return [];
        }

        $data = $this->userData;
        $data['role'] = $this->role ? $this->role->toArray() : null;
        
        // Remover password_hash por seguridad
        unset($data['password_hash']);

        return $data;
    }
}


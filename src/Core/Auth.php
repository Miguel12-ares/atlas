<?php
/**
 * Sistema Atlas - Clase Auth
 * 
 * Maneja la autenticación y autorización de usuarios
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

class Auth
{
    /**
     * Nombre de la sesión
     * @var string
     */
    private const SESSION_NAME = 'atlas_user';

    /**
     * Inicia la sesión si no está iniciada
     * 
     * @return void
     */
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Intenta autenticar un usuario por número de identificación
     * 
     * @param string $numero_identificacion Número de identificación del usuario
     * @param string $password Contraseña
     * @return bool True si la autenticación fue exitosa
     */
    public static function attempt(string $numero_identificacion, string $password): bool
    {
        $db = Database::getInstance();
        
        $sql = "SELECT u.*, r.nombre_rol, r.puede_tener_equipo 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.numero_identificacion = ? AND u.estado = 'activo' 
                LIMIT 1";
        
        $user = $db->fetch($sql, [$numero_identificacion]);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Remover password del array antes de guardar en sesión
            unset($user['password_hash']);
            
            self::login($user);
            return true;
        }

        return false;
    }

    /**
     * Inicia sesión para un usuario
     * 
     * @param array $user Datos del usuario
     * @return void
     */
    public static function login(array $user): void
    {
        self::startSession();
        $_SESSION[self::SESSION_NAME] = $user;
        
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
    }

    /**
     * Cierra la sesión del usuario
     * 
     * @return void
     */
    public static function logout(): void
    {
        self::startSession();
        
        // Limpiar ambos formatos de sesión
        unset($_SESSION[self::SESSION_NAME]);
        unset($_SESSION['user_id']);
        unset($_SESSION['numero_identificacion']);
        unset($_SESSION['nombres']);
        unset($_SESSION['apellidos']);
        unset($_SESSION['rol_id']);
        unset($_SESSION['rol_nombre']);
        unset($_SESSION['logged_in']);
        unset($_SESSION['login_time']);
        
        // Destruir la sesión completamente
        session_destroy();
    }

    /**
     * Verifica si hay un usuario autenticado
     * 
     * @return bool True si hay un usuario autenticado
     */
    public static function check(): bool
    {
        self::startSession();
        // Verificar ambos métodos de almacenamiento (compatibilidad)
        return isset($_SESSION[self::SESSION_NAME]) || isset($_SESSION['logged_in']);
    }

    /**
     * Obtiene el usuario autenticado
     * 
     * @return array|null Datos del usuario o null
     */
    public static function user(): ?array
    {
        self::startSession();
        
        // Si existe en el formato antiguo, retornarlo
        if (isset($_SESSION[self::SESSION_NAME])) {
            return $_SESSION[self::SESSION_NAME];
        }
        
        // Si existe en el formato nuevo (variables individuales), construir array
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            return [
                'id_usuario' => $_SESSION['user_id'] ?? null,
                'numero_identificacion' => $_SESSION['numero_identificacion'] ?? null,
                'nombres' => $_SESSION['nombres'] ?? null,
                'apellidos' => $_SESSION['apellidos'] ?? null,
                'id_rol' => $_SESSION['rol_id'] ?? null,
                'nombre_rol' => $_SESSION['rol_nombre'] ?? null,
            ];
        }
        
        return null;
    }

    /**
     * Obtiene el ID del usuario autenticado
     * 
     * @return int|null ID del usuario o null
     */
    public static function id(): ?int
    {
        self::startSession();
        // Leer directamente de $_SESSION para mejor rendimiento
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Obtiene el rol del usuario autenticado
     * 
     * @return string|null Nombre del rol o null
     */
    public static function role(): ?string
    {
        self::startSession();
        // Leer directamente de $_SESSION para mejor rendimiento
        return $_SESSION['rol_nombre'] ?? null;
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * 
     * @param string $role Nombre del rol
     * @return bool True si el usuario tiene el rol
     */
    public static function hasRole(string $role): bool
    {
        return self::role() === $role;
    }

    /**
     * Verifica si el usuario tiene alguno de los roles especificados
     * 
     * @param array $roles Array de nombres de roles
     * @return bool True si el usuario tiene alguno de los roles
     */
    public static function hasAnyRole(array $roles): bool
    {
        return in_array(self::role(), $roles);
    }

    /**
     * Requiere autenticación, redirige si no está autenticado
     * (Alias para compatibilidad)
     * 
     * @param string $redirectUrl URL de redirección
     * @return void
     */
    public static function requireAuth(string $redirectUrl = '/login'): void
    {
        self::requireLogin($redirectUrl);
    }

    /**
     * Requiere que haya un usuario autenticado, redirige si no lo hay
     * 
     * @param string $redirectUrl URL de redirección
     * @return void
     */
    public static function requireLogin(string $redirectUrl = '/login'): void
    {
        if (!self::check()) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Requiere un rol específico, redirige si no lo tiene
     * 
     * @param string $role Rol requerido
     * @param string $redirectUrl URL de redirección
     * @return void
     */
    public static function requireRole(string $role, string $redirectUrl = '/'): void
    {
        self::requireAuth();
        
        if (!self::hasRole($role)) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Hash de contraseña
     * 
     * @param string $password Contraseña en texto plano
     * @return string Hash de la contraseña
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verifica una contraseña contra un hash
     * 
     * @param string $password Contraseña en texto plano
     * @param string $hash Hash de la contraseña
     * @return bool True si coinciden
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}


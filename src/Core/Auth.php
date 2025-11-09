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
     * Intenta autenticar un usuario
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña
     * @return bool True si la autenticación fue exitosa
     */
    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getInstance();
        
        $sql = "SELECT u.*, r.nombre_rol, r.puede_tener_equipo 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.email = ? AND u.estado = 'activo' 
                LIMIT 1";
        
        $user = $db->fetch($sql, [$email]);

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
        unset($_SESSION[self::SESSION_NAME]);
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
        return isset($_SESSION[self::SESSION_NAME]);
    }

    /**
     * Obtiene el usuario autenticado
     * 
     * @return array|null Datos del usuario o null
     */
    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION[self::SESSION_NAME] ?? null;
    }

    /**
     * Obtiene el ID del usuario autenticado
     * 
     * @return int|null ID del usuario o null
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user['id_usuario'] ?? null;
    }

    /**
     * Obtiene el rol del usuario autenticado
     * 
     * @return string|null Nombre del rol o null
     */
    public static function role(): ?string
    {
        $user = self::user();
        return $user['nombre_rol'] ?? null;
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
     * 
     * @param string $redirectUrl URL de redirección
     * @return void
     */
    public static function requireAuth(string $redirectUrl = '/login'): void
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


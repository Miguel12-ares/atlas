<?php
/**
 * Sistema Atlas - Clase Session
 * 
 * Gestión avanzada de sesiones con tokens, expiración y renovación automática
 * 
 * Implementa:
 * - Sesiones seguras con configuración httponly y secure
 * - Tokens de sesión únicos almacenados en base de datos
 * - Tiempo de expiración automática (2 horas de inactividad)
 * - Renovación de sesión en actividad del usuario
 * - Limpieza automática de sesiones expiradas
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

use Atlas\Core\Database;

class Session
{
    /**
     * Tiempo de expiración de sesión en segundos (2 horas)
     * @var int
     */
    private const SESSION_LIFETIME = 7200; // 2 horas

    /**
     * Tiempo para renovar el token de sesión (30 minutos)
     * @var int
     */
    private const SESSION_REFRESH_TIME = 1800; // 30 minutos

    /**
     * Instancia de base de datos
     * @var Database
     */
    private static ?Database $db = null;

    /**
     * Inicializa la sesión con configuración segura
     * 
     * @return void
     */
    public static function init(): void
    {
        // Ya se configuró en config.php, solo iniciamos si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::$db = Database::getInstance();

        // Si hay una sesión activa, verificar su validez
        if (self::exists('user_id')) {
            self::validateAndRefresh();
        }

        // Limpiar sesiones expiradas periódicamente (1% de probabilidad)
        if (rand(1, 100) === 1) {
            self::cleanExpiredSessions();
        }
    }

    /**
     * Crea una nueva sesión para un usuario
     * 
     * @param int $userId ID del usuario
     * @param array $userData Datos adicionales del usuario
     * @return string Token de sesión generado
     */
    public static function create(int $userId, array $userData = []): string
    {
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);

        // Generar token único de sesión
        $token = self::generateToken();

        // Almacenar datos en $_SESSION
        $_SESSION['user_id'] = $userId;
        $_SESSION['session_token'] = $token;
        $_SESSION['session_created'] = time();
        $_SESSION['session_last_activity'] = time();
        $_SESSION['logged_in'] = true;

        // Almacenar datos adicionales del usuario
        foreach ($userData as $key => $value) {
            $_SESSION[$key] = $value;
        }

        // Almacenar sesión en base de datos
        self::storeInDatabase($userId, $token);

        return $token;
    }

    /**
     * Valida y refresca la sesión actual
     * 
     * @return bool True si la sesión es válida
     */
    private static function validateAndRefresh(): bool
    {
        $lastActivity = self::get('session_last_activity', 0);
        $currentTime = time();

        // Verificar si la sesión ha expirado por inactividad
        if (($currentTime - $lastActivity) > self::SESSION_LIFETIME) {
            self::destroy();
            return false;
        }

        // Actualizar última actividad
        $_SESSION['session_last_activity'] = $currentTime;

        // Verificar si necesita renovación del token (cada 30 minutos)
        $sessionCreated = self::get('session_created', 0);
        if (($currentTime - $sessionCreated) > self::SESSION_REFRESH_TIME) {
            self::refresh();
        }

        // Actualizar timestamp en base de datos
        self::updateLastActivity();

        return true;
    }

    /**
     * Refresca el token de sesión
     * 
     * @return void
     */
    private static function refresh(): void
    {
        // Regenerar ID de sesión
        session_regenerate_id(true);

        // Generar nuevo token
        $newToken = self::generateToken();
        $oldToken = self::get('session_token');

        // Actualizar token en sesión
        $_SESSION['session_token'] = $newToken;
        $_SESSION['session_created'] = time();

        // Actualizar token en base de datos
        if ($oldToken) {
            self::updateTokenInDatabase($oldToken, $newToken);
        }
    }

    /**
     * Destruye la sesión actual
     * 
     * @return void
     */
    public static function destroy(): void
    {
        $token = self::get('session_token');

        // Eliminar sesión de base de datos
        if ($token) {
            self::deleteFromDatabase($token);
        }

        // Limpiar todas las variables de sesión
        $_SESSION = [];

        // Destruir cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destruir sesión
        session_destroy();
    }

    /**
     * Verifica si existe una clave en la sesión
     * 
     * @param string $key Clave a verificar
     * @return bool True si existe
     */
    public static function exists(string $key): bool
    {
        self::ensureStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Obtiene un valor de la sesión
     * 
     * @param string $key Clave del valor
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor obtenido o valor por defecto
     */
    public static function get(string $key, $default = null)
    {
        self::ensureStarted();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Establece un valor en la sesión
     * 
     * @param string $key Clave del valor
     * @param mixed $value Valor a establecer
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Elimina un valor de la sesión
     * 
     * @param string $key Clave del valor
     * @return void
     */
    public static function delete(string $key): void
    {
        self::ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Obtiene todos los datos de la sesión
     * 
     * @return array Datos de la sesión
     */
    public static function all(): array
    {
        self::ensureStarted();
        return $_SESSION ?? [];
    }

    /**
     * Establece un mensaje flash
     * 
     * @param string $type Tipo de mensaje (success, error, warning, info)
     * @param string $message Mensaje
     * @return void
     */
    public static function flash(string $type, string $message): void
    {
        self::set("{$type}_message", $message);
    }

    /**
     * Obtiene y elimina un mensaje flash
     * 
     * @param string $type Tipo de mensaje
     * @return string|null Mensaje o null
     */
    public static function getFlash(string $type): ?string
    {
        $message = self::get("{$type}_message");
        self::delete("{$type}_message");
        return $message;
    }

    /**
     * Verifica si hay una sesión activa
     * 
     * @return bool True si hay sesión activa
     */
    public static function isActive(): bool
    {
        return self::exists('logged_in') && self::get('logged_in') === true;
    }

    /**
     * Asegura que la sesión esté iniciada
     * 
     * @return void
     */
    private static function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Genera un token único y seguro
     * 
     * @return string Token generado
     */
    private static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Almacena la sesión en base de datos
     * 
     * @param int $userId ID del usuario
     * @param string $token Token de sesión
     * @return void
     */
    private static function storeInDatabase(int $userId, string $token): void
    {
        try {
            $sql = "INSERT INTO sesiones (
                        id_usuario, 
                        token_sesion, 
                        ip_address, 
                        user_agent, 
                        fecha_inicio, 
                        fecha_expiracion, 
                        activo
                    ) VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND), 1)";

            $params = [
                $userId,
                $token,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                self::SESSION_LIFETIME
            ];

            self::$db->execute($sql, $params);
        } catch (\Exception $e) {
            error_log("Error al almacenar sesión en BD: " . $e->getMessage());
        }
    }

    /**
     * Actualiza el token de sesión en base de datos
     * 
     * @param string $oldToken Token antiguo
     * @param string $newToken Token nuevo
     * @return void
     */
    private static function updateTokenInDatabase(string $oldToken, string $newToken): void
    {
        try {
            $sql = "UPDATE sesiones 
                    SET token_sesion = ?, 
                        fecha_expiracion = DATE_ADD(NOW(), INTERVAL ? SECOND)
                    WHERE token_sesion = ? 
                    AND activo = 1";

            self::$db->execute($sql, [$newToken, self::SESSION_LIFETIME, $oldToken]);
        } catch (\Exception $e) {
            error_log("Error al actualizar token en BD: " . $e->getMessage());
        }
    }

    /**
     * Actualiza la última actividad en base de datos
     * 
     * @return void
     */
    private static function updateLastActivity(): void
    {
        try {
            $token = self::get('session_token');
            if (!$token) {
                return;
            }

            $sql = "UPDATE sesiones 
                    SET fecha_expiracion = DATE_ADD(NOW(), INTERVAL ? SECOND)
                    WHERE token_sesion = ? 
                    AND activo = 1";

            self::$db->execute($sql, [self::SESSION_LIFETIME, $token]);
        } catch (\Exception $e) {
            error_log("Error al actualizar actividad en BD: " . $e->getMessage());
        }
    }

    /**
     * Elimina una sesión de base de datos
     * 
     * @param string $token Token de sesión
     * @return void
     */
    private static function deleteFromDatabase(string $token): void
    {
        try {
            $sql = "UPDATE sesiones SET activo = 0 WHERE token_sesion = ?";
            self::$db->execute($sql, [$token]);
        } catch (\Exception $e) {
            error_log("Error al eliminar sesión de BD: " . $e->getMessage());
        }
    }

    /**
     * Limpia sesiones expiradas de la base de datos
     * 
     * @return void
     */
    private static function cleanExpiredSessions(): void
    {
        try {
            $sql = "UPDATE sesiones 
                    SET activo = 0 
                    WHERE fecha_expiracion < NOW() 
                    AND activo = 1";

            self::$db->execute($sql);
        } catch (\Exception $e) {
            error_log("Error al limpiar sesiones expiradas: " . $e->getMessage());
        }
    }

    /**
     * Obtiene información de la sesión actual
     * 
     * @return array Información de la sesión
     */
    public static function getInfo(): array
    {
        $lastActivity = self::get('session_last_activity', 0);
        $created = self::get('session_created', 0);
        $currentTime = time();

        return [
            'active' => self::isActive(),
            'user_id' => self::get('user_id'),
            'created_at' => $created,
            'last_activity' => $lastActivity,
            'time_remaining' => self::SESSION_LIFETIME - ($currentTime - $lastActivity),
            'token' => self::get('session_token')
        ];
    }
}


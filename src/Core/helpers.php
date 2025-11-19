<?php
/**
 * Sistema Atlas - Funciones Helper Globales
 * 
 * Funciones globales de utilidad para usar en vistas y controladores
 * Estas funciones están disponibles en toda la aplicación
 * 
 * @package Atlas\Core
 * @version 1.0
 */

use Atlas\Core\Auth;
use Atlas\Core\RBAC;
use Atlas\Models\PrivilegedUser;
use Atlas\Core\Session;

/**
 * Verifica si el usuario tiene un permiso específico
 * Helper para vistas - uso: <?php if (can('usuarios.crear')): ?>
 * 
 * @param string $permission Permiso en formato 'recurso.accion'
 * @return bool True si tiene el permiso
 */
function can(string $permission): bool
{
    $user = PrivilegedUser::fromSession();
    
    if (!$user) {
        return false;
    }
    
    return $user->hasPrivilege($permission);
}

/**
 * Verifica si el usuario tiene un rol específico
 * Helper para vistas - uso: <?php if (hasRole('admin')): ?>
 * 
 * @param string $roleName Nombre del rol
 * @return bool True si tiene el rol
 */
function hasRole(string $roleName): bool
{
    $role = Auth::role();
    return $role === $roleName;
}

/**
 * Verifica si el usuario tiene alguno de los roles especificados
 * Helper para vistas - uso: <?php if (hasAnyRole(['admin', 'administrativo'])): ?>
 * 
 * @param array $roles Array de nombres de roles
 * @return bool True si tiene alguno de los roles
 */
function hasAnyRole(array $roles): bool
{
    return Auth::hasAnyRole($roles);
}

/**
 * Obtiene el usuario autenticado actual
 * 
 * @return array|null Datos del usuario o null
 */
function currentUser(): ?array
{
    return Auth::user();
}

/**
 * Obtiene el ID del usuario autenticado
 * 
 * @return int|null ID del usuario o null
 */
function userId(): ?int
{
    return Auth::id();
}

/**
 * Obtiene el rol del usuario autenticado
 * 
 * @return string|null Nombre del rol o null
 */
function userRole(): ?string
{
    return Auth::role();
}

/**
 * Verifica si el usuario está autenticado
 * 
 * @return bool True si está autenticado
 */
function isAuthenticated(): bool
{
    return Auth::check();
}

/**
 * Verifica si el usuario es administrador
 * 
 * @return bool True si es admin
 */
function isAdmin(): bool
{
    return hasRole('admin');
}

/**
 * Escapa HTML para prevenir XSS
 * Alias de htmlspecialchars más corto
 * 
 * @param string $value Valor a escapar
 * @return string Valor escapado
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Genera una URL completa
 * 
 * @param string $path Ruta relativa
 * @return string URL completa
 */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Genera una URL de asset
 * 
 * @param string $path Ruta del asset
 * @return string URL del asset
 */
function asset(string $path): string
{
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Obtiene un mensaje flash de la sesión
 * 
 * @param string $type Tipo de mensaje (success, error, warning, info)
 * @return string|null Mensaje o null
 */
function flash(string $type): ?string
{
    return Session::getFlash($type);
}

/**
 * Establece un mensaje flash en la sesión
 * 
 * @param string $type Tipo de mensaje
 * @param string $message Mensaje
 * @return void
 */
function setFlash(string $type, string $message): void
{
    Session::flash($type, $message);
}

/**
 * Obtiene un valor de la sesión
 * 
 * @param string $key Clave
 * @param mixed $default Valor por defecto
 * @return mixed Valor o default
 */
function session(string $key, $default = null)
{
    return Session::get($key, $default);
}

/**
 * Formatea una fecha
 * 
 * @param string $date Fecha a formatear
 * @param string $format Formato de salida
 * @return string Fecha formateada
 */
function formatDate(string $date, string $format = 'd/m/Y H:i'): string
{
    try {
        $dateTime = new \DateTime($date);
        return $dateTime->format($format);
    } catch (\Exception $e) {
        return $date;
    }
}

/**
 * Obtiene el valor antiguo de un campo del formulario (para repoblar forms)
 * 
 * @param string $key Nombre del campo
 * @param mixed $default Valor por defecto
 * @return mixed Valor antiguo o default
 */
function old(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}

/**
 * Verifica si un recurso puede ser gestionado por el usuario actual
 * 
 * @param string $resource Nombre del recurso
 * @return bool True si puede gestionar
 */
function canManage(string $resource): bool
{
    return can("{$resource}.gestionar");
}

/**
 * Genera un token CSRF (para futura implementación)
 * 
 * @return string Token CSRF
 */
function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Genera un campo hidden con el token CSRF
 * 
 * @return string HTML del campo hidden
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Convierte un texto a formato de slug
 * 
 * @param string $text Texto a convertir
 * @return string Slug
 */
function slug(string $text): string
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Trunca un texto a una longitud específica
 * 
 * @param string $text Texto a truncar
 * @param int $length Longitud máxima
 * @param string $suffix Sufijo a añadir
 * @return string Texto truncado
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Convierte el primer carácter a mayúscula (UTF-8 safe)
 * 
 * @param string $text Texto a capitalizar
 * @return string Texto capitalizado
 */
function capitalize(string $text): string
{
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

/**
 * Debug helper - imprime y muere (solo en desarrollo)
 * 
 * @param mixed ...$vars Variables a imprimir
 * @return void
 */
function dd(...$vars): void
{
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 20px; border-radius: 5px; margin: 10px;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        die();
    }
}

/**
 * Debug helper - imprime sin morir
 * 
 * @param mixed ...$vars Variables a imprimir
 * @return void
 */
function dump(...$vars): void
{
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo '<pre style="background: #1e1e1e; color: #dcdcdc; padding: 20px; border-radius: 5px; margin: 10px;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }
}


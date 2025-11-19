<?php
/**
 * Sistema Atlas - Archivo de Configuración Principal
 * 
 * Define constantes y configuraciones globales de la aplicación
 * 
 * @package Atlas\Config
 * @version 1.0
 */

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores (cambiar en producción)
// Excluir E_DEPRECATED para PHP 8.1+ (evita warnings molestos en desarrollo)
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', 0); // No mostrar errores directamente (usar logs)
ini_set('log_errors', 1); // Habilitar log de errores

// IMPORTANTE: Configurar opciones de sesión ANTES de session_start()
// Estas configuraciones deben establecerse antes de iniciar la sesión
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); // Prevenir acceso a cookies desde JavaScript
    ini_set('session.use_only_cookies', 1); // Solo usar cookies para sesiones
    ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
    ini_set('session.cookie_samesite', 'Lax'); // Protección CSRF
    ini_set('session.gc_maxlifetime', 7200); // 2 horas
    ini_set('session.cookie_lifetime', 0); // Cookie de sesión (expira al cerrar navegador)
}

// Constantes de la aplicación
define('APP_NAME', 'Sistema Atlas');
define('APP_VERSION', '1.0.0');
define('APP_ENV', 'development'); // development, production

// Rutas del sistema
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('SRC_PATH', ROOT_PATH . '/src');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOGS_PATH', STORAGE_PATH . '/logs');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// URLs base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
define('BASE_URL', $protocol . '://' . $host);
define('ASSETS_URL', BASE_URL . '/assets');

// Configuración de base de datos
define('DB_HOST', 'mysql');
define('DB_PORT', '3306');
define('DB_NAME', 'atlas_db');
define('DB_USER', 'root');
define('DB_PASS', 'atlas_root_2024');
define('DB_CHARSET', 'utf8mb4');

// Configuración de uploads
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB en bytes
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('EQUIPOS_UPLOAD_PATH', UPLOADS_PATH . '/equipos');
define('QR_UPLOAD_PATH', UPLOADS_PATH . '/qr');

// Configuración de sesiones
define('SESSION_LIFETIME', 7200); // 2 horas en segundos
define('SESSION_NAME', 'atlas_session');

// Configuración de seguridad
define('BCRYPT_COST', 10);
define('TOKEN_LENGTH', 32);

// Mensajes del sistema
define('MESSAGES', [
    'success' => [
        'login' => 'Inicio de sesión exitoso',
        'logout' => 'Sesión cerrada correctamente',
        'created' => 'Registro creado exitosamente',
        'updated' => 'Registro actualizado exitosamente',
        'deleted' => 'Registro eliminado exitosamente'
    ],
    'error' => [
        'login' => 'Credenciales incorrectas',
        'unauthorized' => 'No tienes permisos para realizar esta acción',
        'not_found' => 'Registro no encontrado',
        'validation' => 'Error de validación',
        'server' => 'Error del servidor, intenta más tarde'
    ]
]);

// Autoloader para las clases
spl_autoload_register(function ($class) {
    // Namespace base del proyecto
    $prefix = 'Atlas\\';
    
    // Directorio base para el namespace
    $base_dir = SRC_PATH . '/';
    
    // Verificar si la clase usa el namespace base
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Obtener el nombre relativo de la clase
    $relative_class = substr($class, $len);
    
    // Reemplazar namespace separators con directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Si el archivo existe, cargarlo
    if (file_exists($file)) {
        require $file;
    }
});

// Cargar funciones helper globales
require_once SRC_PATH . '/Core/helpers.php';


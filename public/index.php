<?php
/**
 * Sistema Atlas - Punto de Entrada Principal
 * 
 * Este archivo es el punto de entrada de todas las peticiones HTTP
 * 
 * @package Atlas
 * @version 1.0
 */

// Iniciar output buffering para evitar problemas con headers
ob_start();

// Cargar archivo de configuración PRIMERO (antes de session_start)
// Esto permite configurar las opciones de sesión antes de iniciarla
require_once __DIR__ . '/../src/config/config.php';

// Iniciar sesión avanzada con gestión de tokens (después de cargar config.php)
use Atlas\Core\Session;
Session::init();

// Manejo de errores personalizado
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Solo loguear errores, no mostrarlos directamente
    // Esto previene problemas con headers already sent
    error_log("Error [{$errno}]: {$errstr} en {$errfile} línea {$errline}");
    
    // En desarrollo, almacenar en variable global para mostrar después
    if (APP_ENV === 'development' && $errno !== E_DEPRECATED && $errno !== E_USER_DEPRECATED) {
        if (!isset($GLOBALS['app_errors'])) {
            $GLOBALS['app_errors'] = [];
        }
        $GLOBALS['app_errors'][] = [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        ];
    }
    
    // No retornar true para permitir el error handler por defecto en casos críticos
    return false;
});

// Manejo de excepciones
set_exception_handler(function($exception) {
    // Limpiar cualquier output buffer
    if (ob_get_length()) ob_clean();
    
    http_response_code(500);
    
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; color: #721c24; margin: 10px;'>";
        echo "<h2>Excepción Capturada</h2>";
        echo "<strong>Mensaje:</strong> " . htmlspecialchars($exception->getMessage()) . "<br>";
        echo "<strong>Archivo:</strong> " . htmlspecialchars($exception->getFile()) . "<br>";
        echo "<strong>Línea:</strong> " . $exception->getLine() . "<br><br>";
        echo "<strong>Trace:</strong><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        echo "</div>";
    } else {
        // Log de la excepción en producción
        error_log("Excepción: " . $exception->getMessage() . " en " . $exception->getFile() . " línea " . $exception->getLine());
        
        // Mostrar mensaje genérico
        echo "<h1>Error del Servidor</h1>";
        echo "<p>Ha ocurrido un error. Por favor, intenta más tarde.</p>";
    }
    
    // Enviar el buffer
    if (ob_get_length()) ob_end_flush();
});

try {
    // Cargar el router
    $router = require_once __DIR__ . '/../src/config/routes.php';
    
    // Ejecutar el router
    $router->run();
    
} catch (Exception $e) {
    // El exception handler se encargará de esto
    throw $e;
}

// Enviar el output buffer al final
if (ob_get_length()) {
    ob_end_flush();
}


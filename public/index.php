<?php
/**
 * Sistema Atlas - Punto de Entrada Principal
 * 
 * Este archivo es el punto de entrada de todas las peticiones HTTP
 * 
 * @package Atlas
 * @version 1.0
 */

// Iniciar sesión
session_start();

// Cargar archivo de configuración
require_once __DIR__ . '/../src/config/config.php';

// Manejo de errores personalizado
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; color: #721c24; margin: 10px;'>";
        echo "<strong>Error [{$errno}]:</strong> {$errstr}<br>";
        echo "<strong>Archivo:</strong> {$errfile}<br>";
        echo "<strong>Línea:</strong> {$errline}";
        echo "</div>";
    } else {
        // Log del error en producción
        error_log("Error [{$errno}]: {$errstr} en {$errfile} línea {$errline}");
    }
});

// Manejo de excepciones
set_exception_handler(function($exception) {
    http_response_code(500);
    
    if (APP_ENV === 'development') {
        echo "<div style='background: #f8d7da; padding: 20px; border: 1px solid #f5c6cb; color: #721c24; margin: 10px;'>";
        echo "<h2>Excepción Capturada</h2>";
        echo "<strong>Mensaje:</strong> " . $exception->getMessage() . "<br>";
        echo "<strong>Archivo:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Línea:</strong> " . $exception->getLine() . "<br><br>";
        echo "<strong>Trace:</strong><pre>" . $exception->getTraceAsString() . "</pre>";
        echo "</div>";
    } else {
        // Log de la excepción en producción
        error_log("Excepción: " . $exception->getMessage() . " en " . $exception->getFile() . " línea " . $exception->getLine());
        
        // Mostrar mensaje genérico
        echo "<h1>Error del Servidor</h1>";
        echo "<p>Ha ocurrido un error. Por favor, intenta más tarde.</p>";
    }
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


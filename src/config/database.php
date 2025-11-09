<?php
/**
 * Sistema Atlas - Configuración de Base de Datos
 * 
 * Archivo de configuración específico para la base de datos
 * 
 * @package Atlas\Config
 * @version 1.0
 */

return [
    // Configuración de conexión
    'connection' => [
        'driver' => 'mysql',
        'host' => getenv('DB_HOST') ?: 'mysql',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'atlas_db',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: 'atlas_root_2024',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],

    // Opciones de PDO
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ],

    // Configuración de pool de conexiones
    'pool' => [
        'min' => 1,
        'max' => 10,
        'timeout' => 30
    ],

    // Configuración de logs de queries (solo en desarrollo)
    'log_queries' => getenv('APP_ENV') === 'development',
    
    // Tablas del sistema
    'tables' => [
        'roles' => 'roles',
        'usuarios' => 'usuarios',
        'equipos' => 'equipos',
        'imagenes_equipo' => 'imagenes_equipo',
        'codigos_qr' => 'codigos_qr',
        'registros_acceso' => 'registros_acceso',
        'anomalias' => 'anomalias',
        'configuracion_horario' => 'configuracion_horario',
        'sesiones' => 'sesiones'
    ]
];


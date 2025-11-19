<?php
/**
 * Sistema Atlas - Configuración de Rutas
 * 
 * Define todas las rutas de la aplicación
 * 
 * @package Atlas\Config
 * @version 1.0
 */

use Atlas\Core\Router;

$router = new Router();

// =====================================================
// RUTAS PÚBLICAS
// =====================================================

// Autenticación (ambas rutas soportadas)
$router->get('/', 'AuthController@showLogin');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->post('/auth/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/auth/logout', 'AuthController@logout');

// =====================================================
// RUTAS PROTEGIDAS - DASHBOARD
// =====================================================

// Dashboard principal
$router->get('/dashboard', 'DashboardController@index');

// =====================================================
// RUTAS PROTEGIDAS - USUARIOS
// =====================================================

// Listar usuarios
$router->get('/usuarios', 'UsuarioController@index');

// Ver usuario
$router->get('/usuarios/{id}', 'UsuarioController@show');

// Crear usuario
$router->get('/usuarios/crear', 'UsuarioController@create');
$router->post('/usuarios/crear', 'UsuarioController@store');

// Editar usuario
$router->get('/usuarios/{id}/editar', 'UsuarioController@edit');
$router->post('/usuarios/{id}/editar', 'UsuarioController@update');

// Eliminar usuario
$router->post('/usuarios/{id}/eliminar', 'UsuarioController@delete');

// =====================================================
// RUTAS PROTEGIDAS - EQUIPOS
// =====================================================

// Listar equipos
$router->get('/equipos', 'EquipoController@index');

// Ver equipo
$router->get('/equipos/{id}', 'EquipoController@show');

// Crear equipo
$router->get('/equipos/crear', 'EquipoController@create');
$router->post('/equipos/crear', 'EquipoController@store');

// Editar equipo
$router->get('/equipos/{id}/editar', 'EquipoController@edit');
$router->post('/equipos/{id}/editar', 'EquipoController@update');

// Eliminar equipo
$router->post('/equipos/{id}/eliminar', 'EquipoController@delete');

// Generar código QR
$router->post('/equipos/{id}/generar-qr', 'EquipoController@generateQR');

// =====================================================
// RUTAS PROTEGIDAS - REGISTROS DE ACCESO
// =====================================================

// Listar registros
$router->get('/registros', 'RegistroController@index');

// Ver registro
$router->get('/registros/{id}', 'RegistroController@show');

// Crear registro (entrada/salida)
$router->get('/registros/crear', 'RegistroController@create');
$router->post('/registros/crear', 'RegistroController@store');

// Registro por QR
$router->post('/registros/qr', 'RegistroController@storeByQR');

// =====================================================
// RUTAS PROTEGIDAS - ANOMALÍAS
// =====================================================

// Listar anomalías
$router->get('/anomalias', 'AnomaliaController@index');

// Ver anomalía
$router->get('/anomalias/{id}', 'AnomaliaController@show');

// Resolver anomalía
$router->post('/anomalias/{id}/resolver', 'AnomaliaController@resolve');

// =====================================================
// RUTAS PROTEGIDAS - REPORTES
// =====================================================

// Reportes
$router->get('/reportes', 'ReporteController@index');
$router->post('/reportes/generar', 'ReporteController@generate');
$router->get('/reportes/exportar', 'ReporteController@export');

// =====================================================
// RUTAS PROTEGIDAS - CONFIGURACIÓN
// =====================================================

// Configuración de horarios
$router->get('/configuracion/horarios', 'ConfiguracionController@horarios');
$router->post('/configuracion/horarios', 'ConfiguracionController@updateHorarios');

// Configuración general
$router->get('/configuracion', 'ConfiguracionController@index');
$router->post('/configuracion', 'ConfiguracionController@update');

// =====================================================
// RUTAS PROTEGIDAS - PERFIL
// =====================================================

// Ver perfil
$router->get('/perfil', 'PerfilController@show');

// Editar perfil
$router->post('/perfil/editar', 'PerfilController@update');

// Cambiar contraseña
$router->post('/perfil/cambiar-password', 'PerfilController@changePassword');

// =====================================================
// API ENDPOINTS (JSON)
// =====================================================

// API - Buscar equipo por número de serie
$router->get('/api/equipos/buscar', 'ApiController@searchEquipo');

// API - Validar QR
$router->post('/api/qr/validar', 'ApiController@validateQR');

// API - Estadísticas del dashboard
$router->get('/api/dashboard/stats', 'ApiController@dashboardStats');

// =====================================================
// EJECUTAR ROUTER
// =====================================================

return $router;


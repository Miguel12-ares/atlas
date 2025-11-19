<?php
/**
 * Sistema Atlas - Clase Middleware
 * 
 * Middleware de autenticación y autorización
 * Intercepta peticiones HTTP para verificar permisos
 * 
 * Implementa:
 * - Verificación de sesión activa
 * - Validación de permisos por rol
 * - Redirección a login si no está autenticado
 * - Respuesta 403 si no tiene permisos
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

use Atlas\Core\Auth;
use Atlas\Core\Session;
use Atlas\Models\PrivilegedUser;

class Middleware
{
    /**
     * Rutas públicas que no requieren autenticación
     * @var array
     */
    private static array $publicRoutes = [
        '/',
        '/login',
        '/auth/login',
        '/register',
        '/auth/register'
    ];

    /**
     * Maneja la petición verificando autenticación y permisos
     * 
     * @param string $route Ruta solicitada
     * @param string $method Método HTTP
     * @return bool True si puede continuar, false si debe abortar
     */
    public static function handle(string $route, string $method = 'GET'): bool
    {
        // Si es una ruta pública, permitir acceso
        if (self::isPublicRoute($route)) {
            return true;
        }

        // Verificar autenticación
        if (!self::checkAuthentication()) {
            self::redirectToLogin();
            return false;
        }

        // Verificar permisos para la ruta
        if (!self::checkAuthorization($route, $method)) {
            self::showForbidden();
            return false;
        }

        return true;
    }

    /**
     * Verifica si una ruta es pública
     * 
     * @param string $route Ruta a verificar
     * @return bool True si es pública
     */
    private static function isPublicRoute(string $route): bool
    {
        // Normalizar ruta
        $route = '/' . trim($route, '/');
        
        return in_array($route, self::$publicRoutes);
    }

    /**
     * Verifica si hay una sesión válida
     * 
     * @return bool True si está autenticado
     */
    private static function checkAuthentication(): bool
    {
        // Inicializar sesión si no está iniciada
        Session::init();

        // Verificar si hay sesión activa
        if (!Session::isActive()) {
            return false;
        }

        // Verificar que el usuario exista en la base de datos
        $userId = Session::get('user_id');
        if (!$userId) {
            return false;
        }

        return true;
    }

    /**
     * Verifica si el usuario tiene permisos para acceder a la ruta
     * 
     * @param string $route Ruta solicitada
     * @param string $method Método HTTP
     * @return bool True si tiene permisos
     */
    private static function checkAuthorization(string $route, string $method): bool
    {
        // Normalizar ruta
        $route = '/' . trim($route, '/');

        // Cargar usuario privilegiado
        $user = PrivilegedUser::fromSession();
        
        if (!$user) {
            return false;
        }

        // Administrador tiene acceso a todo
        if ($user->isAdmin()) {
            return true;
        }

        // Verificar permisos específicos por ruta
        $permission = self::getRequiredPermission($route, $method);
        
        if ($permission === null) {
            // Si no se requiere permiso específico, permitir acceso
            return true;
        }

        return $user->hasPrivilege($permission);
    }

    /**
     * Obtiene el permiso requerido para una ruta específica
     * 
     * @param string $route Ruta
     * @param string $method Método HTTP
     * @return string|null Permiso requerido o null
     */
    private static function getRequiredPermission(string $route, string $method): ?string
    {
        // Mapa de rutas a permisos requeridos
        $routePermissions = [
            // Usuarios
            '/usuarios' => 'usuarios.leer',
            '/usuarios/crear' => ($method === 'GET') ? 'usuarios.leer' : 'usuarios.crear',
            '/usuarios/{id}' => 'usuarios.leer',
            '/usuarios/{id}/editar' => ($method === 'GET') ? 'usuarios.leer' : 'usuarios.actualizar',
            '/usuarios/{id}/eliminar' => 'usuarios.eliminar',
            
            // Equipos
            '/equipos' => 'equipos.leer',
            '/equipos/crear' => ($method === 'GET') ? 'equipos.leer' : 'equipos.crear',
            '/equipos/{id}' => 'equipos.leer',
            '/equipos/{id}/editar' => ($method === 'GET') ? 'equipos.leer' : 'equipos.actualizar',
            '/equipos/{id}/eliminar' => 'equipos.eliminar',
            '/equipos/{id}/generar-qr' => 'equipos.actualizar',
            
            // Registros
            '/registros' => 'registros.leer',
            '/registros/crear' => ($method === 'GET') ? 'registros.leer' : 'registros.crear',
            '/registros/{id}' => 'registros.leer',
            '/registros/qr' => 'registros.crear',
            
            // Anomalías
            '/anomalias' => 'anomalias.leer',
            '/anomalias/{id}' => 'anomalias.leer',
            '/anomalias/{id}/resolver' => 'anomalias.actualizar',
            
            // Reportes
            '/reportes' => 'reportes.generar',
            '/reportes/generar' => 'reportes.generar',
            '/reportes/exportar' => 'reportes.exportar',
            
            // Configuración
            '/configuracion' => 'configuracion.leer',
            '/configuracion/horarios' => ($method === 'GET') ? 'configuracion.leer' : 'configuracion.actualizar',
        ];

        // Buscar permiso exacto
        if (isset($routePermissions[$route])) {
            return $routePermissions[$route];
        }

        // Buscar patrón con parámetros {id}
        foreach ($routePermissions as $pattern => $permission) {
            if (strpos($pattern, '{id}') !== false) {
                $regex = str_replace('{id}', '([0-9]+)', $pattern);
                $regex = '#^' . $regex . '$#';
                
                if (preg_match($regex, $route)) {
                    return $permission;
                }
            }
        }

        // Rutas sin permiso específico (dashboard, perfil, etc.)
        return null;
    }

    /**
     * Redirige al login
     * 
     * @return void
     */
    private static function redirectToLogin(): void
    {
        Session::flash('error', 'Debe iniciar sesión para acceder a esta página');
        header('Location: /login');
        exit;
    }

    /**
     * Muestra error 403 Forbidden
     * 
     * @return void
     */
    private static function showForbidden(): void
    {
        http_response_code(403);
        
        // Si es una petición AJAX, devolver JSON
        if (self::isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No tiene permisos para realizar esta acción'
            ]);
            exit;
        }

        // Mostrar página de error 403
        self::render403Page();
        exit;
    }

    /**
     * Renderiza la página de error 403
     * 
     * @return void
     */
    private static function render403Page(): void
    {
        $user = Auth::user();
        $userName = $user ? ($user['nombres'] . ' ' . $user['apellidos']) : 'Usuario';
        $userRole = $user ? $user['nombre_rol'] : 'Sin rol';
        
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 - Acceso Denegado - Sistema Atlas</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .error-container {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    padding: 50px;
                    text-align: center;
                    max-width: 600px;
                    width: 100%;
                }
                .error-code {
                    font-size: 120px;
                    font-weight: 900;
                    color: #dc3545;
                    line-height: 1;
                    margin-bottom: 20px;
                    text-shadow: 3px 3px 0 rgba(0, 0, 0, 0.1);
                }
                h1 {
                    font-size: 2rem;
                    color: #333;
                    margin-bottom: 15px;
                }
                .description {
                    color: #666;
                    font-size: 1.1rem;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
                .user-info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 25px 0;
                    border-left: 4px solid #dc3545;
                }
                .user-info p {
                    margin: 8px 0;
                    color: #555;
                }
                .btn {
                    display: inline-block;
                    padding: 15px 35px;
                    background: #39A900;
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: all 0.3s;
                    margin: 10px;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(57, 169, 0, 0.3);
                }
                .btn-secondary {
                    background: #6c757d;
                }
                .btn-secondary:hover {
                    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
                }
                .icon {
                    font-size: 80px;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="icon">🔒</div>
                <div class="error-code">403</div>
                <h1>Acceso Denegado</h1>
                <p class="description">
                    Lo sentimos, no tienes permisos suficientes para acceder a esta página.
                    Si crees que esto es un error, contacta al administrador del sistema.
                </p>
                
                <div class="user-info">
                    <p><strong>Usuario:</strong> <?= htmlspecialchars($userName) ?></p>
                    <p><strong>Rol:</strong> <?= htmlspecialchars($userRole) ?></p>
                    <p><strong>Acción denegada:</strong> <?= htmlspecialchars($_SERVER['REQUEST_URI']) ?></p>
                </div>

                <div>
                    <a href="/dashboard" class="btn">
                        🏠 Ir al Dashboard
                    </a>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        ← Volver Atrás
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    /**
     * Verifica si es una petición AJAX
     * 
     * @return bool True si es AJAX
     */
    private static function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Añade una ruta pública
     * 
     * @param string $route Ruta a añadir
     * @return void
     */
    public static function addPublicRoute(string $route): void
    {
        $route = '/' . trim($route, '/');
        if (!in_array($route, self::$publicRoutes)) {
            self::$publicRoutes[] = $route;
        }
    }

    /**
     * Requiere un permiso específico
     * 
     * @param string $permission Permiso requerido (formato: recurso.accion)
     * @return void
     */
    public static function requirePermission(string $permission): void
    {
        $user = PrivilegedUser::fromSession();
        
        if (!$user || !$user->hasPrivilege($permission)) {
            self::showForbidden();
        }
    }

    /**
     * Requiere un rol específico
     * 
     * @param string|array $roles Rol o array de roles requeridos
     * @return void
     */
    public static function requireRole($roles): void
    {
        if (!Auth::check()) {
            self::redirectToLogin();
            return;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        $userRole = Auth::role();
        
        if (!in_array($userRole, $roles)) {
            self::showForbidden();
        }
    }
}


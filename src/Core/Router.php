<?php
/**
 * Sistema Atlas - Clase Router
 * 
 * Maneja el enrutamiento de la aplicación
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

use Atlas\Core\Middleware;

class Router
{
    /**
     * Array de rutas registradas
     * @var array
     */
    private array $routes = [];

    /**
     * Ruta actual
     * @var string
     */
    private string $currentRoute = '';

    /**
     * Método HTTP actual
     * @var string
     */
    private string $method = '';

    /**
     * Constructor del router
     */
    public function __construct()
    {
        $this->currentRoute = $this->getCurrentRoute();
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Obtiene la ruta actual de la URL
     * 
     * @return string Ruta actual
     */
    private function getCurrentRoute(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Remover query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        return '/' . trim($uri, '/');
    }

    /**
     * Registra una ruta GET
     * 
     * @param string $route Patrón de la ruta
     * @param string|callable $action Controlador@método o función callable
     * @return void
     */
    public function get(string $route, $action): void
    {
        $this->addRoute('GET', $route, $action);
    }

    /**
     * Registra una ruta POST
     * 
     * @param string $route Patrón de la ruta
     * @param string|callable $action Controlador@método o función callable
     * @return void
     */
    public function post(string $route, $action): void
    {
        $this->addRoute('POST', $route, $action);
    }

    /**
     * Registra una ruta PUT
     * 
     * @param string $route Patrón de la ruta
     * @param string|callable $action Controlador@método o función callable
     * @return void
     */
    public function put(string $route, $action): void
    {
        $this->addRoute('PUT', $route, $action);
    }

    /**
     * Registra una ruta DELETE
     * 
     * @param string $route Patrón de la ruta
     * @param string|callable $action Controlador@método o función callable
     * @return void
     */
    public function delete(string $route, $action): void
    {
        $this->addRoute('DELETE', $route, $action);
    }

    /**
     * Agrega una ruta al array de rutas
     * 
     * @param string $method Método HTTP
     * @param string $route Patrón de la ruta
     * @param string|callable $action Acción a ejecutar
     * @return void
     */
    private function addRoute(string $method, string $route, $action): void
    {
        $route = '/' . trim($route, '/');
        $this->routes[] = [
            'method' => $method,
            'route' => $route,
            'action' => $action
        ];
    }

    /**
     * Ejecuta el router y busca la ruta coincidente
     * Aplica middleware de autenticación antes de ejecutar la acción
     * 
     * @return void
     */
    public function run(): void
    {
        // Aplicar middleware de autenticación
        if (!Middleware::handle($this->currentRoute, $this->method)) {
            // El middleware ya manejó la respuesta (redirect o 403)
            return;
        }

        foreach ($this->routes as $route) {
            // Convertir parámetros dinámicos {param} a regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $route['route']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $this->method && preg_match($pattern, $this->currentRoute, $matches)) {
                array_shift($matches); // Remover el match completo
                $this->executeAction($route['action'], $matches);
                return;
            }
        }

        // Si no se encuentra la ruta, mostrar error 404
        $this->notFound();
    }

    /**
     * Ejecuta la acción de una ruta
     * 
     * @param string|callable $action Acción a ejecutar
     * @param array $params Parámetros extraídos de la ruta
     * @return void
     */
    private function executeAction($action, array $params = []): void
    {
        if (is_callable($action)) {
            call_user_func_array($action, $params);
        } elseif (is_string($action)) {
            $parts = explode('@', $action);
            
            if (count($parts) !== 2) {
                throw new \Exception("Formato de acción inválido. Use 'Controller@method'");
            }

            $controller = "Atlas\\Controllers\\" . $parts[0];
            $method = $parts[1];

            if (!class_exists($controller)) {
                throw new \Exception("Controlador no encontrado: {$controller}");
            }

            $controllerInstance = new $controller();

            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Método no encontrado: {$method} en {$controller}");
            }

            call_user_func_array([$controllerInstance, $method], $params);
        }
    }

    /**
     * Maneja errores 404
     * 
     * @return void
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo "<h1>404 - Página no encontrada</h1>";
        echo "<p>La ruta <strong>{$this->currentRoute}</strong> no existe.</p>";
        exit;
    }
}


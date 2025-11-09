<?php
/**
 * Sistema Atlas - Clase Base Controller
 * 
 * Controlador base del que heredarán todos los controladores
 * Proporciona métodos comunes para manejo de vistas y respuestas
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

class Controller
{
    /**
     * Instancia de la clase View
     * @var View
     */
    protected View $view;

    /**
     * Constructor del controlador
     */
    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * Renderiza una vista
     * 
     * @param string $viewName Nombre de la vista
     * @param array $data Datos a pasar a la vista
     * @return void
     */
    protected function render(string $viewName, array $data = []): void
    {
        $this->view->render($viewName, $data);
    }

    /**
     * Redirige a una URL
     * 
     * @param string $url URL de destino
     * @return void
     */
    protected function redirect(string $url): void
    {
        header("Location: " . $url);
        exit;
    }

    /**
     * Devuelve una respuesta JSON
     * 
     * @param mixed $data Datos a convertir en JSON
     * @param int $statusCode Código de estado HTTP
     * @return void
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Verifica si la petición es AJAX
     * 
     * @return bool True si es una petición AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Obtiene el método HTTP de la petición
     * 
     * @return string Método HTTP (GET, POST, PUT, DELETE, etc.)
     */
    protected function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Verifica si el método HTTP es POST
     * 
     * @return bool True si es POST
     */
    protected function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Verifica si el método HTTP es GET
     * 
     * @return bool True si es GET
     */
    protected function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Obtiene un valor del array $_POST
     * 
     * @param string $key Clave del valor
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor obtenido o valor por defecto
     */
    protected function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtiene un valor del array $_GET
     * 
     * @param string $key Clave del valor
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor obtenido o valor por defecto
     */
    protected function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Sanitiza una cadena de texto
     * 
     * @param string $data Cadena a sanitizar
     * @return string Cadena sanitizada
     */
    protected function sanitize(string $data): string
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida un email
     * 
     * @param string $email Email a validar
     * @return bool True si es válido
     */
    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}


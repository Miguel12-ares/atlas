<?php
/**
 * Sistema Atlas - Clase View
 * 
 * Maneja la renderización de vistas y layouts
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

class View
{
    /**
     * Directorio base de las vistas
     * @var string
     */
    private string $viewsPath = __DIR__ . '/../Views/';

    /**
     * Layout por defecto
     * @var string
     */
    private string $layout = 'layouts/main';

    /**
     * Datos a pasar a la vista
     * @var array
     */
    private array $data = [];

    /**
     * Establece el layout a utilizar
     * 
     * @param string $layout Nombre del layout
     * @return void
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Renderiza una vista
     * 
     * @param string $viewName Nombre de la vista
     * @param array $data Datos a pasar a la vista
     * @return void
     */
    public function render(string $viewName, array $data = []): void
    {
        $this->data = $data;
        
        // Extraer variables para que estén disponibles en la vista
        extract($data);

        // Capturar el contenido de la vista
        ob_start();
        $viewFile = $this->viewsPath . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \Exception("Vista no encontrada: {$viewFile}");
        }
        
        $content = ob_get_clean();

        // Si hay un layout, renderizar con él
        if ($this->layout) {
            $layoutFile = $this->viewsPath . $this->layout . '.php';
            
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                // Si no existe el layout, mostrar solo el contenido
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Renderiza una vista parcial sin layout
     * 
     * @param string $viewName Nombre de la vista
     * @param array $data Datos a pasar a la vista
     * @return void
     */
    public function partial(string $viewName, array $data = []): void
    {
        extract($data);
        
        $viewFile = $this->viewsPath . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \Exception("Vista parcial no encontrada: {$viewFile}");
        }
    }

    /**
     * Escapa HTML para prevenir XSS
     * 
     * @param string $value Valor a escapar
     * @return string Valor escapado
     */
    public function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Genera una URL base
     * 
     * @param string $path Ruta relativa
     * @return string URL completa
     */
    public function url(string $path = ''): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . '/' . ltrim($path, '/');
    }

    /**
     * Genera una URL de asset
     * 
     * @param string $path Ruta del asset
     * @return string URL del asset
     */
    public function asset(string $path): string
    {
        return $this->url('assets/' . ltrim($path, '/'));
    }
}


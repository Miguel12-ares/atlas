<?php
/**
 * Sistema Atlas - Clase Helper
 * 
 * Funciones helper útiles para toda la aplicación
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

class Helper
{
    /**
     * Genera una URL base
     * 
     * @param string $path Ruta relativa
     * @return string URL completa
     */
    public static function url(string $path = ''): string
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
    public static function asset(string $path): string
    {
        return self::url('assets/' . ltrim($path, '/'));
    }

    /**
     * Redirige a una URL
     * 
     * @param string $url URL de destino
     * @return void
     */
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Escapa HTML para prevenir XSS
     * 
     * @param string $value Valor a escapar
     * @return string Valor escapado
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Formatea una fecha
     * 
     * @param string $date Fecha a formatear
     * @param string $format Formato de salida
     * @return string Fecha formateada
     */
    public static function formatDate(string $date, string $format = 'd/m/Y H:i'): string
    {
        $dateTime = new \DateTime($date);
        return $dateTime->format($format);
    }

    /**
     * Genera un token aleatorio
     * 
     * @param int $length Longitud del token
     * @return string Token generado
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Valida un número de identificación colombiano
     * 
     * @param string $identificacion Número de identificación
     * @return bool True si es válido
     */
    public static function validateIdentificacion(string $identificacion): bool
    {
        // Validación básica: solo números y longitud entre 6 y 10 dígitos
        return preg_match('/^\d{6,10}$/', $identificacion) === 1;
    }

    /**
     * Valida un número de teléfono colombiano
     * 
     * @param string $telefono Número de teléfono
     * @return bool True si es válido
     */
    public static function validateTelefono(string $telefono): bool
    {
        // Validación básica: 10 dígitos que empiecen con 3
        return preg_match('/^3\d{9}$/', $telefono) === 1;
    }

    /**
     * Sanitiza una cadena de texto
     * 
     * @param string $data Cadena a sanitizar
     * @return string Cadena sanitizada
     */
    public static function sanitize(string $data): string
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Convierte un array a JSON
     * 
     * @param mixed $data Datos a convertir
     * @return string JSON
     */
    public static function toJson($data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /**
     * Convierte JSON a array
     * 
     * @param string $json JSON a convertir
     * @return array|null Array o null si falla
     */
    public static function fromJson(string $json): ?array
    {
        $data = json_decode($json, true);
        return json_last_error() === JSON_ERROR_NONE ? $data : null;
    }

    /**
     * Obtiene el tamaño de un archivo en formato legible
     * 
     * @param int $bytes Tamaño en bytes
     * @return string Tamaño formateado
     */
    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Valida una extensión de archivo
     * 
     * @param string $filename Nombre del archivo
     * @param array $allowedExtensions Extensiones permitidas
     * @return bool True si es válida
     */
    public static function validateFileExtension(string $filename, array $allowedExtensions): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $allowedExtensions);
    }

    /**
     * Genera un nombre único para un archivo
     * 
     * @param string $originalName Nombre original
     * @return string Nombre único
     */
    public static function generateUniqueFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $name = pathinfo($originalName, PATHINFO_FILENAME);
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
        
        return $name . '_' . time() . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Verifica si es una petición AJAX
     * 
     * @return bool True si es AJAX
     */
    public static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Obtiene la IP del cliente
     * 
     * @return string IP del cliente
     */
    public static function getClientIp(): string
    {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                $ip = $_SERVER[$key];
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Registra un mensaje en el log
     * 
     * @param string $message Mensaje a registrar
     * @param string $level Nivel del log (info, warning, error)
     * @return void
     */
    public static function log(string $message, string $level = 'info'): void
    {
        $logDir = __DIR__ . '/../../storage/logs/';
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}


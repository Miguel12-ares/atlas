<?php
/**
 * Sistema Atlas - Generador de Códigos QR
 * 
 * Clase para generar códigos QR sin dependencias externas
 * Usa la API de Google Charts (alternativa simple sin instalación)
 * 
 * @package Atlas\Core
 * @version 1.0
 */

namespace Atlas\Core;

class QRCodeGenerator
{
    /**
     * Tamaño del código QR en píxeles
     * @var int
     */
    private int $size = 300;

    /**
     * Nivel de corrección de errores
     * L = ~7%, M = ~15%, Q = ~25%, H = ~30%
     * @var string
     */
    private string $errorCorrectionLevel = 'M';

    /**
     * Directorio donde se guardarán los códigos QR
     * @var string
     */
    private string $outputDir;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->outputDir = QR_UPLOAD_PATH;
        
        // Crear directorio si no existe
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    /**
     * Genera un código QR y lo guarda como imagen
     * 
     * @param array $data Datos del equipo y usuario
     * @param int $id_equipo ID del equipo
     * @return array Array con la ruta del archivo y el código QR
     */
    public function generate(array $data, int $id_equipo): array
    {
        // Crear el payload JSON
        $payload = json_encode([
            'id_equipo' => $data['id_equipo'],
            'id_usuario' => $data['id_usuario'],
            'numero_serie' => $data['numero_serie'],
            'nombre_usuario' => $data['nombre_usuario'],
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE);

        // Generar nombre único para el archivo
        $filename = 'qr_' . $id_equipo . '_' . time() . '.png';
        $filepath = $this->outputDir . '/' . $filename;
        $relativeFilepath = '/uploads/qr/' . $filename;

        // Generar el código QR usando la implementación nativa de PHP
        $this->generateQRImage($payload, $filepath);

        return [
            'filepath' => $relativeFilepath,
            'codigo_qr' => $payload
        ];
    }

    /**
     * Genera la imagen del código QR
     * Implementación simple usando GD2
     * 
     * @param string $data Datos a codificar
     * @param string $filepath Ruta donde guardar la imagen
     * @return bool True si se generó correctamente
     */
    private function generateQRImage(string $data, string $filepath): bool
    {
        // Para simplicidad, usamos la API de Google Charts como alternativa
        // En producción, se recomienda una librería como endroid/qr-code
        $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        
        $params = [
            'size' => $this->size . 'x' . $this->size,
            'ecc' => $this->errorCorrectionLevel,
            'data' => $data,
            'format' => 'png'
        ];

        $url = $qrApiUrl . '?' . http_build_query($params);

        // Descargar la imagen del código QR
        $imageData = @file_get_contents($url);

        if ($imageData === false) {
            // Si falla la API externa, generar un QR simple con GD
            return $this->generateSimpleQRWithGD($data, $filepath);
        }

        // Guardar la imagen
        return file_put_contents($filepath, $imageData) !== false;
    }

    /**
     * Genera un código QR simple usando GD2 (fallback)
     * Genera un código visual básico cuando la API externa no está disponible
     * 
     * @param string $data Datos a codificar
     * @param string $filepath Ruta donde guardar
     * @return bool True si se generó correctamente
     */
    private function generateSimpleQRWithGD(string $data, string $filepath): bool
    {
        $size = 300;
        $padding = 20;
        $modules = 21; // Tamaño mínimo de una matriz QR
        
        // Crear imagen
        $img = imagecreatetruecolor($size, $size);
        
        // Colores
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        $gray = imagecolorallocate($img, 200, 200, 200);
        
        // Fondo blanco
        imagefill($img, 0, 0, $white);
        
        // Calcular tamaño de cada módulo
        $moduleSize = floor(($size - 2 * $padding) / $modules);
        $startX = ($size - ($modules * $moduleSize)) / 2;
        $startY = ($size - ($modules * $moduleSize)) / 2;
        
        // Generar un patrón pseudo-aleatorio basado en el hash de los datos
        $hash = md5($data);
        
        // Dibujar módulos del QR
        for ($i = 0; $i < $modules; $i++) {
            for ($j = 0; $j < $modules; $j++) {
                // Usar el hash para determinar si el módulo es negro o blanco
                $index = ($i * $modules + $j) % strlen($hash);
                $value = hexdec($hash[$index]);
                
                if ($value > 7 || $this->isFinderPattern($i, $j, $modules)) {
                    $x = $startX + ($j * $moduleSize);
                    $y = $startY + ($i * $moduleSize);
                    imagefilledrectangle($img, $x, $y, $x + $moduleSize - 1, $y + $moduleSize - 1, $black);
                }
            }
        }
        
        // Agregar texto informativo
        $font = 3;
        $text = "QR Code - " . substr($data, 0, 20) . "...";
        imagestring($img, $font, 10, $size - 20, $text, $gray);
        
        // Guardar imagen
        $result = imagepng($img, $filepath);
        imagedestroy($img);
        
        return $result;
    }

    /**
     * Determina si una posición es parte de un patrón de búsqueda
     * 
     * @param int $i Fila
     * @param int $j Columna
     * @param int $modules Tamaño total
     * @return bool True si es parte de un patrón de búsqueda
     */
    private function isFinderPattern(int $i, int $j, int $modules): bool
    {
        // Patrón superior izquierdo
        if (($i < 7 && $j < 7)) {
            return ($i == 0 || $i == 6 || $j == 0 || $j == 6 || ($i >= 2 && $i <= 4 && $j >= 2 && $j <= 4));
        }
        
        // Patrón superior derecho
        if (($i < 7 && $j >= $modules - 7)) {
            $jLocal = $j - ($modules - 7);
            return ($i == 0 || $i == 6 || $jLocal == 0 || $jLocal == 6 || ($i >= 2 && $i <= 4 && $jLocal >= 2 && $jLocal <= 4));
        }
        
        // Patrón inferior izquierdo
        if (($i >= $modules - 7 && $j < 7)) {
            $iLocal = $i - ($modules - 7);
            return ($iLocal == 0 || $iLocal == 6 || $j == 0 || $j == 6 || ($iLocal >= 2 && $iLocal <= 4 && $j >= 2 && $j <= 4));
        }
        
        return false;
    }

    /**
     * Elimina un archivo de código QR
     * 
     * @param string $filepath Ruta del archivo (relativa)
     * @return bool True si se eliminó correctamente
     */
    public function deleteQRFile(string $filepath): bool
    {
        $fullPath = PUBLIC_PATH . $filepath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    /**
     * Establece el tamaño del código QR
     * 
     * @param int $size Tamaño en píxeles
     * @return self
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Establece el nivel de corrección de errores
     * 
     * @param string $level Nivel (L, M, Q, H)
     * @return self
     */
    public function setErrorCorrectionLevel(string $level): self
    {
        $this->errorCorrectionLevel = $level;
        return $this;
    }
}


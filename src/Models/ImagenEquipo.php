<?php
/**
 * Sistema Atlas - Modelo ImagenEquipo
 * 
 * Gestiona las imágenes de los equipos
 * 
 * @package Atlas\Models
 * @version 1.0
 */

namespace Atlas\Models;

use Atlas\Core\Model;

class ImagenEquipo extends Model
{
    /**
     * Nombre de la tabla
     * @var string
     */
    protected string $table = 'imagenes_equipo';

    /**
     * Clave primaria
     * @var string
     */
    protected string $primaryKey = 'id_imagen';

    /**
     * Obtiene todas las imágenes de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @return array Lista de imágenes
     */
    public function getByEquipo(int $id_equipo): array
    {
        return $this->where(['id_equipo' => $id_equipo]);
    }

    /**
     * Obtiene la imagen principal de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @return array|false Imagen principal o false
     */
    public function getPrincipal(int $id_equipo)
    {
        return $this->findWhere([
            'id_equipo' => $id_equipo,
            'tipo_imagen' => 'principal'
        ]);
    }

    /**
     * Establece una imagen como principal
     * 
     * @param int $id_equipo ID del equipo
     * @param int $id_imagen ID de la imagen a establecer como principal
     * @return bool True si se actualizó correctamente
     */
    public function setPrincipal(int $id_equipo, int $id_imagen): bool
    {
        // Primero, quitar el estado principal de todas las imágenes del equipo
        $sql = "UPDATE {$this->table} SET tipo_imagen = 'detalle' WHERE id_equipo = ? AND tipo_imagen = 'principal'";
        $this->db->execute($sql, [$id_equipo]);
        
        // Luego, establecer la nueva imagen como principal
        return $this->update($id_imagen, ['tipo_imagen' => 'principal']);
    }

    /**
     * Cuenta las imágenes de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @return int Número de imágenes
     */
    public function countByEquipo(int $id_equipo): int
    {
        return $this->count(['id_equipo' => $id_equipo]);
    }

    /**
     * Elimina todas las imágenes de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @return bool True si se eliminaron correctamente
     */
    public function deleteByEquipo(int $id_equipo): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id_equipo = ?";
        return $this->db->execute($sql, [$id_equipo]);
    }

    /**
     * Guarda una nueva imagen
     * 
     * @param int $id_equipo ID del equipo
     * @param string $ruta_imagen Ruta de la imagen
     * @param string $tipo_imagen Tipo de imagen
     * @return string|false ID de la imagen insertada o false
     */
    public function saveImagen(int $id_equipo, string $ruta_imagen, string $tipo_imagen = 'detalle')
    {
        return $this->create([
            'id_equipo' => $id_equipo,
            'ruta_imagen' => $ruta_imagen,
            'tipo_imagen' => $tipo_imagen
        ]);
    }

    /**
     * Elimina una imagen específica
     * 
     * @param int $id_imagen ID de la imagen
     * @return array|false Datos de la imagen eliminada o false
     */
    public function deleteImagen(int $id_imagen)
    {
        // Primero obtener la ruta de la imagen
        $imagen = $this->find($id_imagen);
        
        if ($imagen) {
            // Eliminar el registro de la base de datos
            $this->delete($id_imagen);
            
            // Retornar los datos de la imagen para eliminar el archivo físico
            return $imagen;
        }
        
        return false;
    }
}


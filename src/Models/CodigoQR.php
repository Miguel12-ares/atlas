<?php
/**
 * Sistema Atlas - Modelo CodigoQR
 * 
 * Gestiona los códigos QR de los equipos
 * 
 * @package Atlas\Models
 * @version 1.0
 */

namespace Atlas\Models;

use Atlas\Core\Model;

class CodigoQR extends Model
{
    /**
     * Nombre de la tabla
     * @var string
     */
    protected string $table = 'codigos_qr';

    /**
     * Clave primaria
     * @var string
     */
    protected string $primaryKey = 'id_qr';

    /**
     * Obtiene el código QR de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @return array|false Código QR o false
     */
    public function getByEquipo(int $id_equipo)
    {
        return $this->findWhere([
            'id_equipo' => $id_equipo,
            'activo' => 1
        ]);
    }

    /**
     * Desactiva todos los códigos QR de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @return bool True si se desactivaron correctamente
     */
    public function deactivateByEquipo(int $id_equipo): bool
    {
        $sql = "UPDATE {$this->table} SET activo = 0 WHERE id_equipo = ?";
        return $this->db->execute($sql, [$id_equipo]);
    }

    /**
     * Crea un nuevo código QR para un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @param string $codigo_qr Código QR generado
     * @param string $ruta_imagen_qr Ruta de la imagen del QR
     * @return string|false ID del código QR insertado o false
     */
    public function createQR(int $id_equipo, string $codigo_qr, string $ruta_imagen_qr)
    {
        // Desactivar códigos QR anteriores
        $this->deactivateByEquipo($id_equipo);
        
        // Crear el nuevo código QR
        return $this->create([
            'id_equipo' => $id_equipo,
            'codigo_qr' => $codigo_qr,
            'ruta_imagen_qr' => $ruta_imagen_qr,
            'activo' => 1
        ]);
    }

    /**
     * Valida un código QR
     * 
     * @param string $codigo_qr Código QR a validar
     * @return array|false Datos del código QR si es válido, false si no
     */
    public function validateQR(string $codigo_qr)
    {
        $sql = "
            SELECT 
                qr.*,
                e.numero_serie,
                e.marca,
                e.modelo,
                e.estado_equipo,
                u.id_usuario,
                u.nombres,
                u.apellidos,
                u.numero_identificacion,
                r.nombre_rol
            FROM {$this->table} qr
            INNER JOIN equipos e ON qr.id_equipo = e.id_equipo
            INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE qr.codigo_qr = ? AND qr.activo = 1
            LIMIT 1
        ";
        
        return $this->db->fetch($sql, [$codigo_qr]);
    }

    /**
     * Elimina un código QR
     * 
     * @param int $id_qr ID del código QR
     * @return array|false Datos del código QR eliminado o false
     */
    public function deleteQR(int $id_qr)
    {
        // Primero obtener los datos del código QR
        $qr = $this->find($id_qr);
        
        if ($qr) {
            // Eliminar el registro
            $this->delete($id_qr);
            
            // Retornar los datos para eliminar el archivo físico
            return $qr;
        }
        
        return false;
    }

    /**
     * Elimina todos los códigos QR de un equipo
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
     * Verifica si un equipo tiene un código QR activo
     * 
     * @param int $id_equipo ID del equipo
     * @return bool True si tiene código QR activo
     */
    public function hasActiveQR(int $id_equipo): bool
    {
        $result = $this->count([
            'id_equipo' => $id_equipo,
            'activo' => 1
        ]);
        
        return $result > 0;
    }
}


<?php
/**
 * Sistema Atlas - Modelo Equipo
 * 
 * Gestiona los datos de los equipos electrónicos
 * 
 * @package Atlas\Models
 * @version 1.0
 */

namespace Atlas\Models;

use Atlas\Core\Model;

class Equipo extends Model
{
    /**
     * Nombre de la tabla
     * @var string
     */
    protected string $table = 'equipos';

    /**
     * Clave primaria
     * @var string
     */
    protected string $primaryKey = 'id_equipo';

    /**
     * Obtiene todos los equipos de un usuario
     * 
     * @param int $id_usuario ID del usuario
     * @return array Lista de equipos
     */
    public function getAllByUser(int $id_usuario): array
    {
        $sql = "
            SELECT 
                e.*,
                u.nombres,
                u.apellidos,
                u.email,
                (SELECT ruta_imagen FROM imagenes_equipo WHERE id_equipo = e.id_equipo AND tipo_imagen = 'principal' LIMIT 1) as imagen_principal,
                (SELECT COUNT(*) FROM imagenes_equipo WHERE id_equipo = e.id_equipo) as total_imagenes,
                (SELECT ruta_imagen_qr FROM codigos_qr WHERE id_equipo = e.id_equipo AND activo = 1 LIMIT 1) as qr_imagen
            FROM equipos e
            INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
            WHERE e.id_usuario = ?
            ORDER BY e.created_at DESC
        ";
        
        return $this->query($sql, [$id_usuario]);
    }

    /**
     * Obtiene todos los equipos con información del usuario
     * 
     * @return array Lista completa de equipos
     */
    public function getAllWithUser(): array
    {
        $sql = "
            SELECT 
                e.*,
                u.nombres,
                u.apellidos,
                u.numero_identificacion,
                u.email,
                r.nombre_rol,
                (SELECT ruta_imagen FROM imagenes_equipo WHERE id_equipo = e.id_equipo AND tipo_imagen = 'principal' LIMIT 1) as imagen_principal,
                (SELECT COUNT(*) FROM imagenes_equipo WHERE id_equipo = e.id_equipo) as total_imagenes
            FROM equipos e
            INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
            INNER JOIN roles r ON u.id_rol = r.id_rol
            ORDER BY e.created_at DESC
        ";
        
        return $this->query($sql);
    }

    /**
     * Obtiene un equipo por su ID con información relacionada
     * 
     * @param int $id_equipo ID del equipo
     * @return array|false Datos del equipo o false
     */
    public function getWithDetails(int $id_equipo)
    {
        $sql = "
            SELECT 
                e.*,
                u.id_usuario,
                u.nombres,
                u.apellidos,
                u.numero_identificacion,
                u.email,
                u.telefono,
                r.nombre_rol,
                (SELECT ruta_imagen_qr FROM codigos_qr WHERE id_equipo = e.id_equipo AND activo = 1 LIMIT 1) as qr_imagen,
                (SELECT codigo_qr FROM codigos_qr WHERE id_equipo = e.id_equipo AND activo = 1 LIMIT 1) as codigo_qr
            FROM equipos e
            INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE e.id_equipo = ?
            LIMIT 1
        ";
        
        return $this->db->fetch($sql, [$id_equipo]);
    }

    /**
     * Busca un equipo por número de serie
     * 
     * @param string $numero_serie Número de serie
     * @return array|false Equipo encontrado o false
     */
    public function findByNumeroSerie(string $numero_serie)
    {
        return $this->findWhere(['numero_serie' => $numero_serie]);
    }

    /**
     * Verifica si un número de serie ya existe
     * 
     * @param string $numero_serie Número de serie
     * @param int|null $exclude_id ID a excluir de la búsqueda (para ediciones)
     * @return bool True si existe
     */
    public function numeroSerieExists(string $numero_serie, ?int $exclude_id = null): bool
    {
        if ($exclude_id) {
            $sql = "SELECT COUNT(*) as total FROM equipos WHERE numero_serie = ? AND id_equipo != ?";
            $result = $this->db->fetch($sql, [$numero_serie, $exclude_id]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM equipos WHERE numero_serie = ?";
            $result = $this->db->fetch($sql, [$numero_serie]);
        }
        
        return (int)$result['total'] > 0;
    }

    /**
     * Busca equipos por marca
     * 
     * @param string $marca Marca del equipo
     * @return array Lista de equipos
     */
    public function findByMarca(string $marca): array
    {
        return $this->where(['marca' => $marca]);
    }

    /**
     * Busca equipos por estado
     * 
     * @param string $estado Estado del equipo
     * @return array Lista de equipos
     */
    public function findByEstado(string $estado): array
    {
        return $this->where(['estado_equipo' => $estado]);
    }

    /**
     * Obtiene estadísticas de equipos de un usuario
     * 
     * @param int $id_usuario ID del usuario
     * @return array Estadísticas
     */
    public function getStatsForUser(int $id_usuario): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado_equipo = 'activo' THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN estado_equipo = 'inactivo' THEN 1 ELSE 0 END) as inactivos,
                SUM(CASE WHEN estado_equipo = 'bloqueado' THEN 1 ELSE 0 END) as bloqueados
            FROM equipos
            WHERE id_usuario = ?
        ";
        
        return $this->db->fetch($sql, [$id_usuario]);
    }

    /**
     * Obtiene el estado actual del equipo (dentro/fuera del centro)
     * 
     * @param int $id_equipo ID del equipo
     * @return array|null Estado actual
     */
    public function getEstadoActual(int $id_equipo): ?array
    {
        $sql = "
            SELECT 
                tipo_registro,
                fecha_hora,
                metodo_verificacion
            FROM registros_acceso
            WHERE id_equipo = ?
            ORDER BY fecha_hora DESC
            LIMIT 1
        ";
        
        $result = $this->db->fetch($sql, [$id_equipo]);
        return $result ?: null;
    }

    /**
     * Actualiza el estado de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @param string $estado Nuevo estado
     * @return bool True si se actualizó correctamente
     */
    public function updateEstado(int $id_equipo, string $estado): bool
    {
        return $this->update($id_equipo, ['estado_equipo' => $estado]);
    }

    /**
     * Realiza soft delete de un equipo
     * 
     * @param int $id_equipo ID del equipo
     * @return bool True si se actualizó correctamente
     */
    public function softDelete(int $id_equipo): bool
    {
        return $this->updateEstado($id_equipo, 'inactivo');
    }

    /**
     * Busca equipos con filtros
     * 
     * @param array $filters Filtros a aplicar
     * @param int|null $id_usuario ID del usuario (null para todos)
     * @return array Lista de equipos
     */
    public function search(array $filters, ?int $id_usuario = null): array
    {
        $sql = "
            SELECT 
                e.*,
                u.nombres,
                u.apellidos,
                (SELECT ruta_imagen FROM imagenes_equipo WHERE id_equipo = e.id_equipo AND tipo_imagen = 'principal' LIMIT 1) as imagen_principal
            FROM equipos e
            INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($id_usuario) {
            $sql .= " AND e.id_usuario = ?";
            $params[] = $id_usuario;
        }
        
        if (!empty($filters['marca'])) {
            $sql .= " AND e.marca LIKE ?";
            $params[] = "%{$filters['marca']}%";
        }
        
        if (!empty($filters['modelo'])) {
            $sql .= " AND e.modelo LIKE ?";
            $params[] = "%{$filters['modelo']}%";
        }
        
        if (!empty($filters['numero_serie'])) {
            $sql .= " AND e.numero_serie LIKE ?";
            $params[] = "%{$filters['numero_serie']}%";
        }
        
        if (!empty($filters['estado_equipo'])) {
            $sql .= " AND e.estado_equipo = ?";
            $params[] = $filters['estado_equipo'];
        }
        
        $sql .= " ORDER BY e.created_at DESC";
        
        return $this->query($sql, $params);
    }
}


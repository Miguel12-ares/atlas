<?php
/**
 * Sistema Atlas - Controlador de Dashboard
 * 
 * Maneja el panel principal del sistema
 * 
 * @package Atlas\Controllers
 * @version 1.0
 */

namespace Atlas\Controllers;

use Atlas\Core\Controller;
use Atlas\Core\Auth;
use Atlas\Core\Database;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard principal
     * 
     * @return void
     */
    public function index(): void
    {
        // Verificar autenticación
        Auth::requireAuth('/login');

        $user = Auth::user();

        // Obtener estadísticas básicas según el rol
        $stats = $this->getStats($user['id_rol'], $user['id_usuario']);

        // Renderizar dashboard usando el layout
        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'styles' => ['dashboard.css'],
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * Obtiene estadísticas según el rol
     * 
     * @param int $roleId ID del rol
     * @param int $userId ID del usuario
     * @return array Estadísticas
     */
    private function getStats(int $roleId, int $userId): array
    {
        $db = Database::getInstance();
        $stats = [];

        try {
            // Estadísticas según el rol
            // Rol 1 = Admin, Rol 2 = Administrativo
            if (in_array($roleId, [1, 2])) {
                // Administradores ven estadísticas globales
                $result = $db->fetch("SELECT COUNT(*) as total FROM usuarios WHERE estado = 'activo'");
                $stats['total_usuarios'] = $result['total'] ?? 0;

                $result = $db->fetch("SELECT COUNT(*) as total FROM equipos WHERE estado_equipo = 'activo'");
                $stats['total_equipos'] = $result['total'] ?? 0;

                $result = $db->fetch("SELECT COUNT(*) as total FROM registros_acceso WHERE DATE(fecha_hora) = CURDATE()");
                $stats['registros_hoy'] = $result['total'] ?? 0;

                $result = $db->fetch("SELECT COUNT(*) as total FROM anomalias WHERE estado = 'pendiente'");
                $stats['anomalias_pendientes'] = $result['total'] ?? 0;
            }
            // Rol 6 = Portería
            elseif ($roleId == 6) {
                // Portería ve registros y anomalías
                $result = $db->fetch("SELECT COUNT(*) as total FROM registros_acceso WHERE DATE(fecha_hora) = CURDATE()");
                $stats['registros_hoy'] = $result['total'] ?? 0;

                $result = $db->fetch("SELECT COUNT(*) as total FROM anomalias WHERE estado = 'pendiente'");
                $stats['anomalias_pendientes'] = $result['total'] ?? 0;

                $result = $db->fetch("SELECT COUNT(*) as total FROM equipos WHERE estado_equipo = 'activo'");
                $stats['total_equipos'] = $result['total'] ?? 0;
            }
            // Roles 3, 4, 5 = Instructor, Aprendiz, Civil
            else {
                // Usuarios regulares solo ven sus propias estadísticas
                $result = $db->fetch("SELECT COUNT(*) as total FROM equipos WHERE id_usuario = ? AND estado_equipo = 'activo'", [$userId]);
                $stats['mis_equipos'] = $result['total'] ?? 0;

                $result = $db->fetch("SELECT COUNT(*) as total FROM equipos WHERE id_usuario = ? AND estado_equipo = 'inactivo'", [$userId]);
                $stats['equipos_inactivos'] = $result['total'] ?? 0;

                // Registros del usuario hoy
                $result = $db->fetch("
                    SELECT COUNT(*) as total 
                    FROM registros_acceso ra
                    INNER JOIN equipos e ON ra.id_equipo = e.id_equipo
                    WHERE e.id_usuario = ? AND DATE(ra.fecha_hora) = CURDATE()
                ", [$userId]);
                $stats['mis_registros_hoy'] = $result['total'] ?? 0;
            }

        } catch (\Exception $e) {
            // En caso de error, devolver stats vacías
            $stats = [];
        }

        return $stats;
    }
}


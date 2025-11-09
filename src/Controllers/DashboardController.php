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
        $db = Database::getInstance();

        // Obtener estadísticas básicas según el rol
        $stats = $this->getStats($user['id_rol'], $user['id_usuario']);

        // Renderizar dashboard
        echo $this->renderDashboard($user, $stats);
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
            // Total de usuarios
            $result = $db->fetch("SELECT COUNT(*) as total FROM usuarios WHERE estado = 'activo'");
            $stats['total_usuarios'] = $result['total'] ?? 0;

            // Total de equipos
            $result = $db->fetch("SELECT COUNT(*) as total FROM equipos WHERE estado_equipo = 'activo'");
            $stats['total_equipos'] = $result['total'] ?? 0;

            // Registros de hoy
            $result = $db->fetch("SELECT COUNT(*) as total FROM registros_acceso WHERE DATE(fecha_hora) = CURDATE()");
            $stats['registros_hoy'] = $result['total'] ?? 0;

            // Anomalías pendientes
            $result = $db->fetch("SELECT COUNT(*) as total FROM anomalias WHERE estado = 'pendiente'");
            $stats['anomalias_pendientes'] = $result['total'] ?? 0;

            // Equipos del usuario actual (si no es portería)
            if ($roleId != 6) {
                $result = $db->fetch("SELECT COUNT(*) as total FROM equipos WHERE id_usuario = ? AND estado_equipo = 'activo'", [$userId]);
                $stats['mis_equipos'] = $result['total'] ?? 0;
            }

        } catch (\Exception $e) {
            // En caso de error, devolver stats vacías
            $stats = [
                'total_usuarios' => 0,
                'total_equipos' => 0,
                'registros_hoy' => 0,
                'anomalias_pendientes' => 0,
                'mis_equipos' => 0
            ];
        }

        return $stats;
    }

    /**
     * Renderiza el dashboard
     * 
     * @param array $user Usuario actual
     * @param array $stats Estadísticas
     * @return string HTML del dashboard
     */
    private function renderDashboard(array $user, array $stats): string
    {
        ob_start();
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Atlas</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card .icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #39A900;
            margin: 10px 0;
        }
        
        .stat-card .label {
            color: #666;
            font-size: 1rem;
        }
        
        .welcome-banner {
            background: #39A900;
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(57, 169, 0, 0.2);
        }
        
        .welcome-banner h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .welcome-banner p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .quick-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .quick-action-btn {
            padding: 12px 24px;
            background: white;
            color: #39A900;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            border: 2px solid #39A900;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(57, 169, 0, 0.3);
            background: #39A900;
            color: white;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>🎓 Sistema Atlas</h1>
                    <span>Control de Acceso de Equipos</span>
                </div>
                
                <div class="user-menu">
                    <span class="user-name">
                        <?= htmlspecialchars($user['nombres'] . ' ' . $user['apellidos']) ?>
                        <small>(<?= htmlspecialchars($user['nombre_rol']) ?>)</small>
                    </span>
                    <a href="/perfil" class="btn-profile">Mi Perfil</a>
                    <a href="/logout" class="btn-logout">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="welcome-banner">
                <h1>¡Bienvenido, <?= htmlspecialchars($user['nombres']) ?>! 👋</h1>
                <p>Sistema de Control de Acceso de Equipos - SENA Colombia</p>
                <p><strong>Rol:</strong> <?= htmlspecialchars($user['nombre_rol']) ?> | <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            </div>

            <h2 style="margin-bottom: 20px; color: #004080;">📊 Estadísticas del Sistema</h2>

            <div class="dashboard-grid">
                <?php if ($user['id_rol'] != 6): ?>
                <div class="stat-card">
                    <div class="icon">👥</div>
                    <div class="number"><?= $stats['total_usuarios'] ?></div>
                    <div class="label">Usuarios Activos</div>
                </div>
                <?php endif; ?>

                <div class="stat-card">
                    <div class="icon">💻</div>
                    <div class="number"><?= $stats['total_equipos'] ?></div>
                    <div class="label">Equipos Registrados</div>
                </div>

                <div class="stat-card">
                    <div class="icon">📝</div>
                    <div class="number"><?= $stats['registros_hoy'] ?></div>
                    <div class="label">Registros Hoy</div>
                </div>

                <div class="stat-card">
                    <div class="icon">⚠️</div>
                    <div class="number"><?= $stats['anomalias_pendientes'] ?></div>
                    <div class="label">Anomalías Pendientes</div>
                </div>

                <?php if ($user['id_rol'] != 6 && isset($stats['mis_equipos'])): ?>
                <div class="stat-card">
                    <div class="icon">🎒</div>
                    <div class="number"><?= $stats['mis_equipos'] ?></div>
                    <div class="label">Mis Equipos</div>
                </div>
                <?php endif; ?>
            </div>

            <div style="background: white; padding: 30px; border-radius: 8px; margin-top: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                <h2 style="color: #39A900; margin-bottom: 20px;">🚀 Acciones Rápidas</h2>
                
                <div class="quick-actions">
                    <?php if ($user['puede_tener_equipo']): ?>
                        <a href="/equipos" class="quick-action-btn">📱 Mis Equipos</a>
                        <a href="/equipos/crear" class="quick-action-btn">➕ Registrar Equipo</a>
                    <?php endif; ?>
                    
                    <?php if ($user['id_rol'] == 6): ?>
                        <a href="/registros/crear" class="quick-action-btn">✅ Registrar Entrada/Salida</a>
                        <a href="/registros" class="quick-action-btn">📋 Ver Registros</a>
                    <?php endif; ?>
                    
                    <?php if (in_array($user['id_rol'], [1, 2])): ?>
                        <a href="/usuarios" class="quick-action-btn">👥 Gestionar Usuarios</a>
                        <a href="/reportes" class="quick-action-btn">📊 Generar Reportes</a>
                    <?php endif; ?>
                    
                    <a href="/anomalias" class="quick-action-btn">⚠️ Ver Anomalías</a>
                </div>

                <div style="margin-top: 30px; padding: 20px; background: #E8F5E0; border-radius: 6px; border-left: 4px solid #39A900;">
                    <h3 style="color: #39A900; margin-bottom: 10px;">ℹ️ Estado del Sistema</h3>
                    <p style="color: #666; line-height: 1.6;">
                        <strong>Versión:</strong> 1.0.0<br>
                        <strong>Última actualización:</strong> <?= date('d/m/Y H:i') ?><br>
                        <strong>Estado:</strong> <span style="color: #28a745; font-weight: bold;">✓ Operativo</span>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Sistema Atlas - SENA Colombia | Versión 1.0.0</p>
        </div>
    </footer>
</body>
</html>
        <?php
        return ob_get_clean();
    }
}


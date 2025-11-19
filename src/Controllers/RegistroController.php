<?php
/**
 * Sistema Atlas - Controlador de Registros
 * 
 * Maneja los registros de entrada y salida de equipos
 * 
 * @package Atlas\Controllers
 * @version 1.0
 */

namespace Atlas\Controllers;

use Atlas\Core\Controller;
use Atlas\Core\Auth;
use Atlas\Core\Middleware;

class RegistroController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra el listado de registros
     * 
     * @return void
     */
    public function index(): void
    {
        // Verificar autenticación
        Auth::requireAuth('/login');

        // Verificar permisos
        Middleware::requirePermission('registros.leer');

        // Por ahora, mostrar página en construcción
        $this->renderEnConstruccion('Registros de Acceso');
    }

    /**
     * Muestra un registro específico
     * 
     * @param int $id ID del registro
     * @return void
     */
    public function show(int $id): void
    {
        // Verificar autenticación
        Auth::requireAuth('/login');

        // Verificar permisos
        Middleware::requirePermission('registros.leer');

        // Por ahora, mostrar página en construcción
        $this->renderEnConstruccion('Detalle de Registro');
    }

    /**
     * Muestra el formulario para crear un registro
     * 
     * @return void
     */
    public function create(): void
    {
        // Verificar autenticación
        Auth::requireAuth('/login');

        // Verificar permisos
        Middleware::requirePermission('registros.crear');

        // Por ahora, mostrar página en construcción
        $this->renderEnConstruccion('Crear Registro de Entrada/Salida');
    }

    /**
     * Almacena un nuevo registro
     * 
     * @return void
     */
    public function store(): void
    {
        // Verificar autenticación
        Auth::requireAuth('/login');

        // Verificar permisos
        Middleware::requirePermission('registros.crear');

        // TODO: Implementar en Fase 4
        $this->json([
            'success' => false,
            'message' => 'Funcionalidad en desarrollo - Fase 4'
        ], 501);
    }

    /**
     * Almacena un registro mediante código QR
     * 
     * @return void
     */
    public function storeByQR(): void
    {
        // Verificar autenticación
        Auth::requireAuth('/login');

        // Verificar permisos
        Middleware::requirePermission('registros.crear');

        // TODO: Implementar en Fase 4
        $this->json([
            'success' => false,
            'message' => 'Funcionalidad en desarrollo - Fase 4'
        ], 501);
    }

    /**
     * Renderiza una página temporal "en construcción"
     * 
     * @param string $titulo Título de la sección
     * @return void
     */
    private function renderEnConstruccion(string $titulo): void
    {
        $user = Auth::user();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($titulo) ?> - Sistema Atlas</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                }
                .header {
                    background: white;
                    padding: 15px 30px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .header h1 {
                    color: #39A900;
                    font-size: 1.5rem;
                }
                .header a {
                    color: #666;
                    text-decoration: none;
                    margin-left: 20px;
                    transition: color 0.3s;
                }
                .header a:hover { color: #39A900; }
                .container {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .content {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    padding: 60px;
                    text-align: center;
                    max-width: 600px;
                }
                .icon {
                    font-size: 100px;
                    margin-bottom: 20px;
                }
                h2 {
                    font-size: 2rem;
                    color: #333;
                    margin-bottom: 15px;
                }
                p {
                    color: #666;
                    font-size: 1.1rem;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
                .info-box {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 25px 0;
                    border-left: 4px solid #39A900;
                    text-align: left;
                }
                .info-box strong {
                    color: #39A900;
                }
                .btn {
                    display: inline-block;
                    padding: 15px 35px;
                    background: #39A900;
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: 600;
                    transition: all 0.3s;
                    margin: 10px;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(57, 169, 0, 0.3);
                }
                .btn-secondary {
                    background: #6c757d;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🎓 Sistema Atlas</h1>
                <div>
                    <span style="color: #666;">
                        <?= htmlspecialchars($user['nombres'] . ' ' . $user['apellidos']) ?> 
                        (<?= htmlspecialchars($user['nombre_rol']) ?>)
                    </span>
                    <a href="/dashboard">Dashboard</a>
                    <a href="/logout">Cerrar Sesión</a>
                </div>
            </div>

            <div class="container">
                <div class="content">
                    <div class="icon">🚧</div>
                    <h2><?= htmlspecialchars($titulo) ?></h2>
                    <p>Esta funcionalidad está en desarrollo y estará disponible próximamente.</p>
                    
                    <div class="info-box">
                        <p><strong>Estado:</strong> En Construcción</p>
                        <p><strong>Sección:</strong> <?= htmlspecialchars($titulo) ?></p>
                        <p><strong>Fase de Desarrollo:</strong> Fase 4 - Sistema de Registro de Accesos</p>
                    </div>

                    <p style="font-size: 0.95rem; color: #999;">
                        La Fase 2 (Autenticación y Roles) está completamente funcional. 
                        Esta sección se implementará en las siguientes fases del proyecto.
                    </p>

                    <div>
                        <a href="/dashboard" class="btn">🏠 Volver al Dashboard</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}


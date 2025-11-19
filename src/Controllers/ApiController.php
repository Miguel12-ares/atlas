<?php
/**
 * Sistema Atlas - Controlador API
 * 
 * Maneja endpoints JSON para operaciones AJAX
 * 
 * @package Atlas\Controllers
 * @version 1.0
 */

namespace Atlas\Controllers;

use Atlas\Core\Controller;
use Atlas\Core\Auth;

class ApiController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Busca un equipo por número de serie
     * 
     * @return void
     */
    public function searchEquipo(): void
    {
        // Verificar autenticación
        Auth::requireAuth();

        // TODO: Implementar en Fase 3
        $this->json([
            'success' => false,
            'message' => 'Endpoint en desarrollo - Fase 3'
        ], 501);
    }

    /**
     * Valida un código QR
     * 
     * @return void
     */
    public function validateQR(): void
    {
        // Verificar autenticación
        Auth::requireAuth();

        // TODO: Implementar en Fase 5
        $this->json([
            'success' => false,
            'message' => 'Endpoint en desarrollo - Fase 5'
        ], 501);
    }

    /**
     * Obtiene estadísticas del dashboard
     * 
     * @return void
     */
    public function dashboardStats(): void
    {
        // Verificar autenticación
        Auth::requireAuth();

        // TODO: Implementar estadísticas reales
        $this->json([
            'success' => false,
            'message' => 'Endpoint en desarrollo'
        ], 501);
    }
}


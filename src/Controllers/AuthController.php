<?php
/**
 * Sistema Atlas - Controlador de Autenticación
 * 
 * Maneja el login, logout y registro de usuarios
 * 
 * @package Atlas\Controllers
 * @version 1.0
 */

namespace Atlas\Controllers;

use Atlas\Core\Controller;
use Atlas\Core\Auth;
use Atlas\Models\Usuario;

class AuthController extends Controller
{
    /**
     * Modelo de Usuario
     * @var Usuario
     */
    private Usuario $usuarioModel;

    /**
     * Mensajes de error constantes
     */
    private const ERROR_CREDENCIALES = 'Credenciales incorrectas';
    private const ERROR_CAMPOS_VACIOS = 'Todos los campos son obligatorios';
    private const ERROR_SERVIDOR = 'Error del servidor, intenta más tarde';

    /**
     * Constructor
     * Inicializa dependencias
     */
    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Muestra el formulario de login
     * 
     * @return void
     */
    public function showLogin(): void
    {
        // Si ya está autenticado, redirigir según rol
        if (Auth::check()) {
            $this->redirectByRole();
            return;
        }

        // Renderizar vista de login sin layout (la vista tiene su propio HTML completo)
        $this->view->setLayout(null);
        $this->render('auth/login');
    }

    /**
     * Procesa el formulario de login
     * 
     * Validaciones y pasos según especificación:
     * 1. Verificar método POST
     * 2. Sanitizar inputs
     * 3. Validar campos no vacíos
     * 4. Consultar base de datos
     * 5. Verificar contraseña
     * 6. Crear sesión y redireccionar
     * 
     * @return void
     */
    public function login(): void
    {
        try {
            // 1. Verificar que el método sea POST
        if (!$this->isPost()) {
            $this->redirect('/login');
                return;
            }

            // 2. Sanitizar inputs con htmlspecialchars() 
            // (FILTER_SANITIZE_STRING está deprecado desde PHP 8.1)
            $numero_identificacion = htmlspecialchars(
                strip_tags(trim($this->post('numero_identificacion', ''))),
                ENT_QUOTES,
                'UTF-8'
            );
            
        $password = $this->post('password', '');

            // 3. Validar que los campos no estén vacíos
            if (empty($numero_identificacion) || empty($password)) {
                $_SESSION['error_message'] = self::ERROR_CAMPOS_VACIOS;
                $this->redirect('/login');
                return;
            }

            // 4. Consultar base de datos usando el modelo Usuario
            $usuario = $this->usuarioModel->findByIdentificacion($numero_identificacion);

            // Si el usuario NO existe, retornar error genérico por seguridad
            if (!$usuario) {
                $_SESSION['error_message'] = self::ERROR_CREDENCIALES;
                $this->redirect('/login');
                return;
            }

            // 5. Verificar contraseña usando password_verify
            if (!password_verify($password, $usuario['password_hash'])) {
                $_SESSION['error_message'] = self::ERROR_CREDENCIALES;
                $this->redirect('/login');
                return;
            }

            // 6. Login exitoso - Configurar sesión
            $this->establecerSesion($usuario);

            // Redireccionar según rol
            $this->redirectByRole();

        } catch (\PDOException $e) {
            // Manejo de errores de base de datos
            error_log("Error en login (PDO): " . $e->getMessage());
            $_SESSION['error_message'] = self::ERROR_SERVIDOR;
            $this->redirect('/login');
        } catch (\Exception $e) {
            // Manejo de cualquier otra excepción
            error_log("Error en login (General): " . $e->getMessage());
            $_SESSION['error_message'] = self::ERROR_SERVIDOR;
            $this->redirect('/login');
        }
    }

    /**
     * Establece la sesión del usuario
     * Almacena datos en $_SESSION y regenera ID por seguridad
     * 
     * @param array $usuario Datos del usuario
     * @return void
     */
    private function establecerSesion(array $usuario): void
    {
        // Regenerar ID de sesión para prevenir session fixation
        // La sesión ya fue iniciada en public/index.php
        session_regenerate_id(true);

        // Almacenar datos del usuario en $_SESSION
        $_SESSION['user_id'] = $usuario['id_usuario'];
        $_SESSION['numero_identificacion'] = $usuario['numero_identificacion'];
        $_SESSION['nombres'] = $usuario['nombres'];
        $_SESSION['apellidos'] = $usuario['apellidos'];
        $_SESSION['rol_id'] = $usuario['id_rol'];
        $_SESSION['rol_nombre'] = $usuario['nombre_rol'];
        $_SESSION['logged_in'] = true;

        // Almacenar timestamp de login
        $_SESSION['login_time'] = time();

        // Establecer mensaje de éxito
        $_SESSION['success_message'] = '¡Bienvenido, ' . $usuario['nombres'] . '!';
    }

    /**
     * Redirecciona al usuario según su rol
     * 
     * - admin o administrativo → /admin/dashboard.php
     * - porteria → /porteria/scan.php
     * - instructor, aprendiz, civil → /equipos/index.php
     * 
     * @return void
     */
    private function redirectByRole(): void
    {
        $rol = Auth::role();

        switch ($rol) {
            case 'admin':
            case 'administrativo':
                // Por ahora redirigir a /dashboard, después crear /admin/dashboard.php
                $this->redirect('/dashboard');
                break;

            case 'porteria':
                // Por ahora redirigir a /dashboard, después crear /porteria/scan.php
                $this->redirect('/dashboard');
                break;

            case 'instructor':
            case 'aprendiz':
            case 'civil':
                // Por ahora redirigir a /dashboard, después crear /equipos/index.php
                $this->redirect('/dashboard');
                break;

            default:
                // Fallback por si acaso
                $this->redirect('/dashboard');
                break;
        }
    }

    /**
     * Cierra la sesión del usuario
     * Destruye la sesión y redirecciona al login
     * 
     * @return void
     */
    public function logout(): void
    {
        // Destruir sesión usando la clase Auth
        Auth::logout();
        
        // Reiniciar sesión para establecer mensaje de éxito
        // (Auth::logout() destruye la sesión, necesitamos reiniciarla)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['success_message'] = 'Sesión cerrada correctamente';
        
        // Redireccionar al login
        $this->redirect('/login');
    }
}


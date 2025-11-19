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
use Atlas\Core\Session;
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
                Session::flash('error', self::ERROR_CAMPOS_VACIOS);
                $this->redirect('/login');
                return;
            }

            // 4. Consultar base de datos usando el modelo Usuario
            $usuario = $this->usuarioModel->findByIdentificacion($numero_identificacion);

            // Si el usuario NO existe, retornar error genérico por seguridad
            if (!$usuario) {
                Session::flash('error', self::ERROR_CREDENCIALES);
                $this->redirect('/login');
                return;
            }

            // 5. Verificar contraseña usando password_verify
            if (!password_verify($password, $usuario['password_hash'])) {
                Session::flash('error', self::ERROR_CREDENCIALES);
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
            Session::flash('error', self::ERROR_SERVIDOR);
            $this->redirect('/login');
        } catch (\Exception $e) {
            // Manejo de cualquier otra excepción
            error_log("Error en login (General): " . $e->getMessage());
            Session::flash('error', self::ERROR_SERVIDOR);
            $this->redirect('/login');
        }
    }

    /**
     * Establece la sesión del usuario
     * Almacena datos en $_SESSION y regenera ID por seguridad
     * Usa la clase Session para gestión avanzada con tokens
     * 
     * @param array $usuario Datos del usuario
     * @return void
     */
    private function establecerSesion(array $usuario): void
    {
        // Datos del usuario para la sesión
        $userData = [
            'numero_identificacion' => $usuario['numero_identificacion'],
            'nombres' => $usuario['nombres'],
            'apellidos' => $usuario['apellidos'],
            'email' => $usuario['email'] ?? '',
            'rol_id' => $usuario['id_rol'],
            'rol_nombre' => $usuario['nombre_rol'],
            'puede_tener_equipo' => $usuario['puede_tener_equipo'] ?? true
        ];

        // Crear sesión con tokens y gestión avanzada
        Session::create($usuario['id_usuario'], $userData);

        // Establecer mensaje de éxito
        Session::flash('success', '¡Bienvenido, ' . $usuario['nombres'] . '!');
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
        // Establecer mensaje antes de destruir la sesión
        $message = 'Sesión cerrada correctamente';
        
        // Destruir sesión usando la clase Session
        Session::destroy();
        
        // Reiniciar sesión solo para el mensaje flash
        Session::init();
        Session::flash('success', $message);
        
        // Redireccionar al login
        $this->redirect('/login');
    }

    /**
     * Muestra el formulario de registro
     * 
     * @return void
     */
    public function showRegister(): void
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
            return;
        }

        // Obtener roles disponibles para registro
        // Solo mostrar roles que NO sean admin ni portería
        $rolesDisponibles = $this->usuarioModel->getRolesForRegistration();

        // Renderizar vista de registro sin layout
        $this->view->setLayout(null);
        $this->render('auth/register', ['roles' => $rolesDisponibles]);
    }

    /**
     * Procesa el formulario de registro
     * 
     * Validaciones implementadas:
     * 1. Verificar método POST
     * 2. Sanitizar todos los inputs
     * 3. Validar campos obligatorios
     * 4. Validar formato de email
     * 5. Validar longitud mínima de contraseña (8 caracteres)
     * 6. Verificar coincidencia de contraseñas
     * 7. Validar unicidad de número de identificación
     * 8. Validar unicidad de email
     * 9. Hashear contraseña con password_hash()
     * 10. Crear usuario en base de datos
     * 
     * @return void
     */
    public function register(): void
    {
        try {
            // 1. Verificar que el método sea POST
            if (!$this->isPost()) {
                $this->redirect('/register');
                return;
            }

            // 2. Sanitizar inputs
            $numero_identificacion = $this->sanitize($this->post('numero_identificacion', ''));
            $nombres = $this->sanitize($this->post('nombres', ''));
            $apellidos = $this->sanitize($this->post('apellidos', ''));
            $email = $this->sanitize($this->post('email', ''));
            $telefono = $this->sanitize($this->post('telefono', ''));
            $password = $this->post('password', '');
            $password_confirm = $this->post('password_confirm', '');
            $id_rol = (int)$this->post('id_rol', 0);

            // 3. Validar campos obligatorios
            $errores = [];

            if (empty($numero_identificacion)) {
                $errores[] = 'El número de identificación es obligatorio';
            }

            if (empty($nombres)) {
                $errores[] = 'Los nombres son obligatorios';
            }

            if (empty($apellidos)) {
                $errores[] = 'Los apellidos son obligatorios';
            }

            if (empty($email)) {
                $errores[] = 'El email es obligatorio';
            }

            if (empty($password)) {
                $errores[] = 'La contraseña es obligatoria';
            }

            if ($id_rol === 0) {
                $errores[] = 'Debe seleccionar un rol';
            }

            // 4. Validar formato de email
            if (!empty($email) && !$this->validateEmail($email)) {
                $errores[] = 'El formato del email no es válido';
            }

            // 5. Validar longitud mínima de contraseña (8 caracteres)
            if (!empty($password) && strlen($password) < 8) {
                $errores[] = 'La contraseña debe tener al menos 8 caracteres';
            }

            // 6. Verificar coincidencia de contraseñas
            if ($password !== $password_confirm) {
                $errores[] = 'Las contraseñas no coinciden';
            }

            // Validar que el rol seleccionado no sea admin ni portería
            if (in_array($id_rol, [1, 6])) {
                $errores[] = 'No puede seleccionar el rol de Administrador o Portería';
            }

            // 7. Validar unicidad de número de identificación
            if ($this->usuarioModel->existsIdentificacion($numero_identificacion)) {
                $errores[] = 'El número de identificación ya está registrado';
            }

            // 8. Validar unicidad de email
            if ($this->usuarioModel->existsEmail($email)) {
                $errores[] = 'El email ya está registrado';
            }

            // Si hay errores, retornar al formulario
            if (!empty($errores)) {
                Session::flash('error', implode('<br>', $errores));
                $this->redirect('/register');
                return;
            }

            // 9. Hashear contraseña
            $password_hash = Auth::hashPassword($password);

            // 10. Crear usuario
            $userData = [
                'numero_identificacion' => $numero_identificacion,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'email' => $email,
                'telefono' => $telefono,
                'password_hash' => $password_hash,
                'id_rol' => $id_rol,
                'estado' => 'activo'
            ];

            $userId = $this->usuarioModel->create($userData);

            if ($userId) {
                Session::flash('success', 'Usuario registrado exitosamente. Por favor, inicie sesión.');
                $this->redirect('/login');
            } else {
                Session::flash('error', 'Error al crear el usuario. Intente nuevamente.');
                $this->redirect('/register');
            }

        } catch (\PDOException $e) {
            error_log("Error en registro (PDO): " . $e->getMessage());
            Session::flash('error', 'Error del servidor. Por favor, intente más tarde.');
            $this->redirect('/register');
        } catch (\Exception $e) {
            error_log("Error en registro (General): " . $e->getMessage());
            Session::flash('error', 'Error del servidor. Por favor, intente más tarde.');
            $this->redirect('/register');
        }
    }
}


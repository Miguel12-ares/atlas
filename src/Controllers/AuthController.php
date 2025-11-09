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
use Atlas\Core\Helper;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de login
     * 
     * @return void
     */
    public function showLogin(): void
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        // Renderizar vista de login (por ahora HTML simple)
        echo $this->renderLoginPage();
    }

    /**
     * Procesa el login
     * 
     * @return void
     */
    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect('/login');
        }

        $email = $this->post('email', '');
        $password = $this->post('password', '');

        // Validar campos
        if (empty($email) || empty($password)) {
            $_SESSION['error_message'] = 'Por favor, completa todos los campos';
            $this->redirect('/login');
        }

        // Intentar autenticación
        if (Auth::attempt($email, $password)) {
            $_SESSION['success_message'] = '¡Bienvenido al Sistema Atlas!';
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error_message'] = 'Credenciales incorrectas';
            $this->redirect('/login');
        }
    }

    /**
     * Cierra la sesión del usuario
     * 
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
        $_SESSION['success_message'] = 'Sesión cerrada correctamente';
        $this->redirect('/login');
    }

    /**
     * Renderiza la página de login (temporal hasta crear la vista)
     * 
     * @return string HTML del login
     */
    private function renderLoginPage(): string
    {
        ob_start();
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Atlas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #39A900 0%, #5DBF1A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 2rem;
            color: #39A900;
            margin-bottom: 10px;
        }
        
        .logo p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .alert-error {
            background-color: #fee;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #39A900;
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            background: #39A900;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(57, 169, 0, 0.4);
            background: #2D8400;
        }
        
        .demo-credentials {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #17a2b8;
        }
        
        .demo-credentials h3 {
            color: #39A900;
            font-size: 0.9rem;
            margin-bottom: 12px;
        }
        
        .demo-credentials table {
            width: 100%;
            font-size: 0.85rem;
            border-collapse: collapse;
        }
        
        .demo-credentials td {
            padding: 6px 0;
        }
        
        .demo-credentials td:first-child {
            color: #666;
            font-weight: 600;
        }
        
        .demo-credentials td:last-child {
            color: #333;
        }
        
        .divider {
            margin: 15px 0;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🎓 Sistema Atlas</h1>
            <p>Control de Acceso de Equipos</p>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required placeholder="usuario@atlas.sena">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>

        <div class="demo-credentials">
            <h3>👤 Usuarios de Prueba</h3>
            <table>
                <tr>
                    <td><strong>Admin:</strong></td>
                    <td>admin@atlas.sena</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Password: <code>admin123</code></td>
                </tr>
                <tr><td colspan="2"><div class="divider"></div></td></tr>
                <tr>
                    <td><strong>Portero:</strong></td>
                    <td>portero@atlas.sena</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Password: <code>portero123</code></td>
                </tr>
                <tr><td colspan="2"><div class="divider"></div></td></tr>
                <tr>
                    <td><strong>Instructor:</strong></td>
                    <td>maria.lopez@sena.edu.co</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Password: <code>instructor123</code></td>
                </tr>
                <tr><td colspan="2"><div class="divider"></div></td></tr>
                <tr>
                    <td><strong>Aprendiz:</strong></td>
                    <td>juan.perez@sena.edu.co</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Password: <code>aprendiz123</code></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
        <?php
        return ob_get_clean();
    }
}


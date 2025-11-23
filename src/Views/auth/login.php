<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Sistema Atlas - Control de Acceso de Equipos SENA">
    <meta name="author" content="SENA">
    <title>Iniciar Sesión - Sistema Atlas</title>
    <link rel="stylesheet" href="/assets/css/auth/login.css">
</head>
<body>
    <div class="login-container">
        <!-- Logo y título -->
        <div class="logo">
            <span class="logo-icon">🎓</span>
            <h1>Sistema Atlas - SENA</h1>
            <p>Control de Acceso de Equipos</p>
        </div>

        <!-- Mensajes de error/éxito desde el servidor -->
        <?php
        use Atlas\Core\Session;
        Session::init();
        
        $errorMsg = Session::getFlash('error');
        $successMsg = Session::getFlash('success');
        ?>
        
        <?php if ($errorMsg): ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>

        <?php if ($successMsg): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de login -->
        <form method="POST" action="<?php echo BASE_URL; ?>/login" id="loginForm" onsubmit="return validateForm()">
            <!-- Campo de Número de Identificación -->
            <div class="form-group">
                <label for="numero_identificacion">Número de Identificación</label>
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        id="numero_identificacion" 
                        name="numero_identificacion" 
                        placeholder="Ingrese su número de documento"
                        autocomplete="username"
                        required
                    >
                    <div class="error-message" id="error_identificacion"></div>
                </div>
            </div>

            <!-- Campo de Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Ingrese su contraseña"
                        autocomplete="current-password"
                        required
                    >
                    <div class="error-message" id="error_password"></div>
                </div>
            </div>

            <!-- Botón de submit -->
            <button type="submit" class="btn" id="submitBtn">
                Iniciar Sesión
            </button>
        </form>

        <!-- Usuarios de prueba -->
        <div class="demo-credentials">
            <h3>👤 Usuarios de Prueba</h3>
            <table>
                <tr>
                    <td><strong>Admin:</strong></td>
                    <td>1000000</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Contraseña: <code>admin123</code></td>
                </tr>
                <tr><td colspan="2"><hr class="divider"></td></tr>
                
                <tr>
                    <td><strong>Portería:</strong></td>
                    <td>52123456</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Contraseña: <code>portero123</code></td>
                </tr>
                <tr><td colspan="2"><hr class="divider"></td></tr>
                
                <tr>
                    <td><strong>Instructor:</strong></td>
                    <td>80456789</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Contraseña: <code>instructor123</code></td>
                </tr>
                <tr><td colspan="2"><hr class="divider"></td></tr>
                
                <tr>
                    <td><strong>Aprendiz:</strong></td>
                    <td>1098765432</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Contraseña: <code>aprendiz123</code></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- JavaScript para validación del formulario -->
    <script>
        /**
         * Función principal de validación del formulario
         * Se ejecuta cuando el usuario intenta enviar el formulario
         * 
         * @returns {boolean} true si el formulario es válido, false en caso contrario
         */
        function validateForm() {
            // Obtener referencias a los campos
            const numeroIdentificacion = document.getElementById('numero_identificacion');
            const password = document.getElementById('password');
            
            // Obtener referencias a los mensajes de error
            const errorIdentificacion = document.getElementById('error_identificacion');
            const errorPassword = document.getElementById('error_password');
            
            // Variable para controlar si hay errores
            let hasErrors = false;
            
            // Limpiar errores previos
            clearErrors();
            
            // Validar número de identificación
            const numeroValue = numeroIdentificacion.value.trim();
            
            if (numeroValue === '') {
                showError(numeroIdentificacion, errorIdentificacion, 'El número de identificación es obligatorio');
                hasErrors = true;
            } else if (!isNumeric(numeroValue)) {
                showError(numeroIdentificacion, errorIdentificacion, 'El número de identificación debe ser numérico');
                hasErrors = true;
            } else if (numeroValue.length < 6 || numeroValue.length > 20) {
                showError(numeroIdentificacion, errorIdentificacion, 'El número de identificación debe tener entre 6 y 20 dígitos');
                hasErrors = true;
            }
            
            // Validar contraseña
            const passwordValue = password.value;
            
            if (passwordValue === '') {
                showError(password, errorPassword, 'La contraseña es obligatoria');
                hasErrors = true;
            } else if (passwordValue.length < 6) {
                showError(password, errorPassword, 'La contraseña debe tener mínimo 6 caracteres');
                hasErrors = true;
            }
            
            // Si hay errores, prevenir el envío del formulario
            if (hasErrors) {
                return false;
            }
            
            // Si no hay errores, permitir el envío
            return true;
        }
        
        /**
         * Muestra un mensaje de error para un campo específico
         * 
         * @param {HTMLElement} input Campo de entrada
         * @param {HTMLElement} errorElement Elemento donde mostrar el error
         * @param {string} message Mensaje de error a mostrar
         */
        function showError(input, errorElement, message) {
            input.classList.add('error');
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
        
        /**
         * Limpia todos los errores del formulario
         */
        function clearErrors() {
            const inputs = document.querySelectorAll('input');
            const errorMessages = document.querySelectorAll('.error-message');
            
            inputs.forEach(input => {
                input.classList.remove('error');
            });
            
            errorMessages.forEach(error => {
                error.classList.remove('show');
                error.textContent = '';
            });
        }
        
        /**
         * Verifica si una cadena es numérica
         * 
         * @param {string} str Cadena a verificar
         * @returns {boolean} true si es numérico, false en caso contrario
         */
        function isNumeric(str) {
            return /^\d+$/.test(str);
        }
        
        /**
         * Limpia los errores cuando el usuario empieza a escribir
         */
        document.getElementById('numero_identificacion').addEventListener('input', function() {
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                document.getElementById('error_identificacion').classList.remove('show');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                document.getElementById('error_password').classList.remove('show');
            }
        });
        
        /**
         * Prevenir múltiples envíos del formulario
         */
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Si la validación pasa, deshabilitar el botón
            if (validateForm()) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Iniciando sesión...';
            }
        });
    </script>
</body>
</html>


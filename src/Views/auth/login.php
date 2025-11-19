<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Sistema Atlas - Control de Acceso de Equipos SENA">
    <meta name="author" content="SENA">
    <title>Iniciar Sesión - Sistema Atlas</title>
    
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Variables de colores SENA */
        :root {
            --verde-sena: #39b54a;
            --verde-sena-hover: #2d8f3a;
            --verde-sena-claro: #e8f5e9;
            --blanco: #ffffff;
            --gris-texto: #333333;
            --gris-claro: #666666;
            --gris-borde: #dddddd;
            --rojo-error: #dc3545;
            --rojo-error-bg: #fee;
            --verde-exito: #28a745;
            --verde-exito-bg: #d4edda;
            --sombra: rgba(0, 0, 0, 0.1);
            --sombra-hover: rgba(57, 181, 74, 0.3);
        }

        /* Estilos del body */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #39b54a 0%, #2d8f3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Patrón de fondo */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.05) 35px, rgba(255,255,255,.05) 70px);
            pointer-events: none;
        }

        /* Contenedor del login */
        .login-container {
            background: var(--blanco);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 45px 40px;
            position: relative;
            z-index: 1;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo y título */
        .logo {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            font-size: 3.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .logo h1 {
            font-size: 2rem;
            color: var(--verde-sena);
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .logo p {
            color: var(--gris-claro);
            font-size: 0.95rem;
            font-weight: 400;
        }

        /* Mensajes de alerta */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            line-height: 1.5;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .alert-error {
            background-color: var(--rojo-error-bg);
            border-left: 4px solid var(--rojo-error);
            color: #721c24;
        }

        .alert-success {
            background-color: var(--verde-exito-bg);
            border-left: 4px solid var(--verde-exito);
            color: #155724;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: var(--gris-texto);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--gris-borde);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
            background-color: var(--blanco);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--verde-sena);
            box-shadow: 0 0 0 3px rgba(57, 181, 74, 0.1);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #999;
        }

        /* Estilos de validación */
        input.error {
            border-color: var(--rojo-error);
        }

        input.error:focus {
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .error-message {
            color: var(--rojo-error);
            font-size: 0.85rem;
            margin-top: 6px;
            display: none;
            animation: shake 0.3s ease-in-out;
        }

        .error-message.show {
            display: block;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Botón de submit */
        .btn {
            width: 100%;
            padding: 15px;
            background: var(--verde-sena);
            color: var(--blanco);
            border: none;
            border-radius: 8px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.3px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--sombra-hover);
            background: var(--verde-sena-hover);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        /* Sección de usuarios de prueba */
        .demo-credentials {
            margin-top: 35px;
            padding: 24px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #17a2b8;
        }

        .demo-credentials h3 {
            color: var(--verde-sena);
            font-size: 0.95rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-credentials table {
            width: 100%;
            font-size: 0.85rem;
        }

        .demo-credentials tr {
            line-height: 1.8;
        }

        .demo-credentials td {
            padding: 4px 0;
        }

        .demo-credentials td:first-child {
            color: var(--gris-claro);
            font-weight: 600;
            width: 35%;
        }

        .demo-credentials td:last-child {
            color: var(--gris-texto);
        }

        .demo-credentials code {
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            color: var(--verde-sena-hover);
            font-weight: 600;
        }

        .divider {
            margin: 12px 0;
            border: 0;
            border-top: 1px solid #dee2e6;
        }

        /* Responsive design */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .login-container {
                padding: 30px 25px;
            }

            .logo h1 {
                font-size: 1.75rem;
            }

            .logo-icon {
                font-size: 3rem;
            }

            .demo-credentials {
                padding: 18px;
            }

            .demo-credentials td:first-child {
                width: 40%;
            }
        }

        /* Loading spinner (opcional para futura implementación) */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
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
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
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


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Sistema Atlas - Registro de Usuario">
    <title>Registro - Sistema Atlas</title>
    
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
        }

        /* Contenedor del registro */
        .register-container {
            background: var(--blanco);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 600px;
            padding: 45px 40px;
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
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }

        .logo h1 {
            font-size: 1.8rem;
            color: var(--verde-sena);
            margin-bottom: 5px;
            font-weight: 700;
        }

        .logo p {
            color: var(--gris-claro);
            font-size: 0.9rem;
        }

        /* Mensajes de alerta */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            line-height: 1.5;
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--gris-texto);
            font-weight: 600;
            font-size: 0.9rem;
        }

        label .required {
            color: var(--rojo-error);
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--gris-borde);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--verde-sena);
            box-shadow: 0 0 0 3px rgba(57, 181, 74, 0.1);
        }

        select {
            background-color: white;
            cursor: pointer;
        }

        input.error,
        select.error {
            border-color: var(--rojo-error);
        }

        .error-message {
            color: var(--rojo-error);
            font-size: 0.8rem;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        /* Botones */
        .btn {
            width: 100%;
            padding: 14px;
            background: var(--verde-sena);
            color: var(--blanco);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(57, 181, 74, 0.3);
            background: var(--verde-sena-hover);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        /* Link de login */
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: var(--gris-claro);
        }

        .login-link a {
            color: var(--verde-sena);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .register-container {
                padding: 30px 25px;
            }

            .logo h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <!-- Logo y título -->
        <div class="logo">
            <span class="logo-icon">📝</span>
            <h1>Registro de Usuario</h1>
            <p>Sistema Atlas - SENA</p>
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
                <?= $errorMsg ?>
            </div>
        <?php endif; ?>

        <?php if ($successMsg): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de registro -->
        <form method="POST" action="<?php echo BASE_URL; ?>/register" id="registerForm" onsubmit="return validateForm()">
            
            <!-- Fila 1: Número de Identificación -->
            <div class="form-group">
                <label for="numero_identificacion">
                    Número de Identificación <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    id="numero_identificacion" 
                    name="numero_identificacion" 
                    placeholder="Ej: 1098765432"
                    required
                >
                <div class="error-message" id="error_identificacion"></div>
            </div>

            <!-- Fila 2: Nombres y Apellidos -->
            <div class="form-row">
                <div class="form-group">
                    <label for="nombres">
                        Nombres <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nombres" 
                        name="nombres" 
                        placeholder="Nombres completos"
                        required
                    >
                    <div class="error-message" id="error_nombres"></div>
                </div>

                <div class="form-group">
                    <label for="apellidos">
                        Apellidos <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="apellidos" 
                        name="apellidos" 
                        placeholder="Apellidos completos"
                        required
                    >
                    <div class="error-message" id="error_apellidos"></div>
                </div>
            </div>

            <!-- Fila 3: Email y Teléfono -->
            <div class="form-row">
                <div class="form-group">
                    <label for="email">
                        Email <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="correo@ejemplo.com"
                        required
                    >
                    <div class="error-message" id="error_email"></div>
                </div>

                <div class="form-group">
                    <label for="telefono">
                        Teléfono
                    </label>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        placeholder="3001234567"
                    >
                </div>
            </div>

            <!-- Fila 4: Rol -->
            <div class="form-group">
                <label for="id_rol">
                    Tipo de Usuario <span class="required">*</span>
                </label>
                <select id="id_rol" name="id_rol" required>
                    <option value="">Seleccione un tipo de usuario</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id_rol'] ?>">
                            <?= ucfirst($rol['nombre_rol']) ?> - <?= htmlspecialchars($rol['descripcion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="error-message" id="error_rol"></div>
            </div>

            <!-- Fila 5: Contraseñas -->
            <div class="form-row">
                <div class="form-group">
                    <label for="password">
                        Contraseña <span class="required">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Mínimo 8 caracteres"
                        required
                    >
                    <div class="error-message" id="error_password"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">
                        Confirmar Contraseña <span class="required">*</span>
                    </label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="Repita la contraseña"
                        required
                    >
                    <div class="error-message" id="error_password_confirm"></div>
                </div>
            </div>

            <!-- Botón de submit -->
            <button type="submit" class="btn" id="submitBtn">
                Registrarse
            </button>
        </form>

        <!-- Link de login -->
        <div class="login-link">
            ¿Ya tienes cuenta? <a href="/login">Inicia sesión aquí</a>
        </div>
    </div>

    <!-- JavaScript para validación del formulario -->
    <script>
        function validateForm() {
            const numeroIdentificacion = document.getElementById('numero_identificacion');
            const nombres = document.getElementById('nombres');
            const apellidos = document.getElementById('apellidos');
            const email = document.getElementById('email');
            const idRol = document.getElementById('id_rol');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');
            
            let hasErrors = false;
            
            clearErrors();
            
            // Validar número de identificación
            if (numeroIdentificacion.value.trim() === '') {
                showError('identificacion', 'El número de identificación es obligatorio');
                hasErrors = true;
            } else if (!/^\d+$/.test(numeroIdentificacion.value.trim())) {
                showError('identificacion', 'Solo se permiten números');
                hasErrors = true;
            }
            
            // Validar nombres
            if (nombres.value.trim() === '') {
                showError('nombres', 'Los nombres son obligatorios');
                hasErrors = true;
            }
            
            // Validar apellidos
            if (apellidos.value.trim() === '') {
                showError('apellidos', 'Los apellidos son obligatorios');
                hasErrors = true;
            }
            
            // Validar email
            if (email.value.trim() === '') {
                showError('email', 'El email es obligatorio');
                hasErrors = true;
            } else if (!isValidEmail(email.value.trim())) {
                showError('email', 'El formato del email no es válido');
                hasErrors = true;
            }
            
            // Validar rol
            if (idRol.value === '') {
                showError('rol', 'Debe seleccionar un tipo de usuario');
                hasErrors = true;
            }
            
            // Validar contraseña
            if (password.value === '') {
                showError('password', 'La contraseña es obligatoria');
                hasErrors = true;
            } else if (password.value.length < 8) {
                showError('password', 'La contraseña debe tener al menos 8 caracteres');
                hasErrors = true;
            }
            
            // Validar confirmación de contraseña
            if (passwordConfirm.value === '') {
                showError('password_confirm', 'Debe confirmar la contraseña');
                hasErrors = true;
            } else if (password.value !== passwordConfirm.value) {
                showError('password_confirm', 'Las contraseñas no coinciden');
                hasErrors = true;
            }
            
            if (hasErrors) {
                return false;
            }
            
            // Deshabilitar botón para prevenir doble envío
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').textContent = 'Registrando...';
            
            return true;
        }
        
        function showError(field, message) {
            const errorElement = document.getElementById('error_' + field);
            const inputElement = document.getElementById(field === 'identificacion' ? 'numero_identificacion' : (field === 'rol' ? 'id_rol' : field));
            
            if (errorElement && inputElement) {
                inputElement.classList.add('error');
                errorElement.textContent = message;
                errorElement.classList.add('show');
            }
        }
        
        function clearErrors() {
            const inputs = document.querySelectorAll('input, select');
            const errorMessages = document.querySelectorAll('.error-message');
            
            inputs.forEach(input => input.classList.remove('error'));
            errorMessages.forEach(error => {
                error.classList.remove('show');
                error.textContent = '';
            });
        }
        
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }
        
        // Limpiar errores cuando el usuario empieza a escribir
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    this.classList.remove('error');
                    const errorId = 'error_' + (this.id === 'numero_identificacion' ? 'identificacion' : (this.id === 'id_rol' ? 'rol' : this.id));
                    const errorElement = document.getElementById(errorId);
                    if (errorElement) {
                        errorElement.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>
</html>


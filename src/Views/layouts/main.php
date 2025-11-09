<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? 'Sistema Atlas' ?> - Control de Acceso de Equipos</title>
    
    <!-- Meta tags de seguridad -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= ASSETS_URL ?>/images/favicon.ico">
    
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/main.css">
    
    <!-- Estilos adicionales de la página -->
    <?php if (isset($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>🎓 Sistema Atlas</h1>
                    <span>Control de Acceso de Equipos</span>
                </div>
                
                <?php if (\Atlas\Core\Auth::check()): ?>
                    <nav class="main-nav">
                        <ul>
                            <li><a href="/dashboard">Dashboard</a></li>
                            
                            <?php if (\Atlas\Core\RBAC::userCan('usuarios', 'leer')): ?>
                                <li><a href="/usuarios">Usuarios</a></li>
                            <?php endif; ?>
                            
                            <?php if (\Atlas\Core\RBAC::userCan('equipos', 'leer')): ?>
                                <li><a href="/equipos">Equipos</a></li>
                            <?php endif; ?>
                            
                            <?php if (\Atlas\Core\RBAC::userCan('registros', 'leer')): ?>
                                <li><a href="/registros">Registros</a></li>
                            <?php endif; ?>
                            
                            <?php if (\Atlas\Core\RBAC::userCan('anomalias', 'leer')): ?>
                                <li><a href="/anomalias">Anomalías</a></li>
                            <?php endif; ?>
                            
                            <?php if (\Atlas\Core\RBAC::userCan('reportes', 'generar')): ?>
                                <li><a href="/reportes">Reportes</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div class="user-menu">
                        <span class="user-name">
                            <?= htmlspecialchars(\Atlas\Core\Auth::user()['nombres']) ?>
                            <small>(<?= htmlspecialchars(\Atlas\Core\Auth::role()) ?>)</small>
                        </span>
                        <a href="/perfil" class="btn-profile">Mi Perfil</a>
                        <a href="/logout" class="btn-logout">Cerrar Sesión</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <!-- Contenido principal -->
    <main class="main-content">
        <div class="container">
            <!-- Mensajes flash -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Contenido de la vista -->
            <?= $content ?? '' ?>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Sistema Atlas - SENA Colombia | Versión <?= APP_VERSION ?></p>
        </div>
    </footer>
    
    <!-- Scripts JavaScript -->
    <script src="<?= ASSETS_URL ?>/js/main.js"></script>
    
    <!-- Scripts adicionales de la página -->
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= ASSETS_URL ?>/js/<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>


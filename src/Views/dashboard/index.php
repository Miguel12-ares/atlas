<div class="welcome-banner">
    <h1>Bienvenido, <?= htmlspecialchars($user['nombres']) ?></h1>
    <p>Sistema de Control de Acceso de Equipos - SENA Colombia</p>
    <p><strong>Rol:</strong> <?= htmlspecialchars($user['nombre_rol']) ?> | <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
</div>

<?php if (in_array($user['id_rol'], [1, 2])): ?>
    <!-- Dashboard para Administradores -->
    <h2 class="stats-title">Estadísticas Globales del Sistema</h2>
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="icon">👥</div>
            <div class="number"><?= $stats['total_usuarios'] ?? 0 ?></div>
            <div class="label">Usuarios Activos</div>
        </div>

        <div class="stat-card">
            <div class="icon">💻</div>
            <div class="number"><?= $stats['total_equipos'] ?? 0 ?></div>
            <div class="label">Equipos Registrados</div>
        </div>

        <div class="stat-card">
            <div class="icon">📝</div>
            <div class="number"><?= $stats['registros_hoy'] ?? 0 ?></div>
            <div class="label">Registros Hoy</div>
        </div>

        <div class="stat-card">
            <div class="icon">⚠️</div>
            <div class="number"><?= $stats['anomalias_pendientes'] ?? 0 ?></div>
            <div class="label">Anomalías Pendientes</div>
        </div>
    </div>

<?php elseif ($user['id_rol'] == 6): ?>
    <!-- Dashboard para Portería -->
    <h2 class="stats-title">Panel de Portería</h2>
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="icon">📝</div>
            <div class="number"><?= $stats['registros_hoy'] ?? 0 ?></div>
            <div class="label">Registros Hoy</div>
        </div>

        <div class="stat-card">
            <div class="icon">💻</div>
            <div class="number"><?= $stats['total_equipos'] ?? 0 ?></div>
            <div class="label">Equipos en Sistema</div>
        </div>

        <div class="stat-card">
            <div class="icon">⚠️</div>
            <div class="number"><?= $stats['anomalias_pendientes'] ?? 0 ?></div>
            <div class="label">Anomalías Pendientes</div>
        </div>
    </div>

<?php else: ?>
    <!-- Dashboard para Usuarios Regulares (Instructor, Aprendiz, Civil) -->
    <h2 class="stats-title">Mis Estadísticas</h2>
    <div class="dashboard-grid">
        <div class="stat-card">
            <div class="icon">📱</div>
            <div class="number"><?= $stats['mis_equipos'] ?? 0 ?></div>
            <div class="label">Mis Equipos Activos</div>
        </div>

        <div class="stat-card">
            <div class="icon">💤</div>
            <div class="number"><?= $stats['equipos_inactivos'] ?? 0 ?></div>
            <div class="label">Equipos Inactivos</div>
        </div>

        <div class="stat-card">
            <div class="icon">📝</div>
            <div class="number"><?= $stats['mis_registros_hoy'] ?? 0 ?></div>
            <div class="label">Mis Registros Hoy</div>
        </div>
    </div>
<?php endif; ?>

<div class="dashboard-section">
    <h2>Acciones Rápidas</h2>
    
    <div class="quick-actions">
        <?php if (in_array($user['id_rol'], [1, 2])): ?>
            <!-- Acciones para Administradores -->
            <a href="/usuarios" class="quick-action-btn">Gestionar Usuarios</a>
            <a href="/equipos" class="quick-action-btn">Ver Equipos</a>
            <a href="/registros" class="quick-action-btn">Ver Registros</a>
            <a href="/reportes" class="quick-action-btn">Generar Reportes</a>
            <a href="/anomalias" class="quick-action-btn">Ver Anomalías</a>
        
        <?php elseif ($user['id_rol'] == 6): ?>
            <!-- Acciones para Portería -->
            <a href="/registros/crear" class="quick-action-btn">Registrar Entrada/Salida</a>
            <a href="/registros" class="quick-action-btn">Ver Registros</a>
            <a href="/equipos" class="quick-action-btn">Buscar Equipo</a>
            <a href="/anomalias" class="quick-action-btn">Ver Anomalías</a>
        
        <?php else: ?>
            <!-- Acciones para Usuarios Regulares (Instructor, Aprendiz, Civil) -->
            <a href="/equipos" class="quick-action-btn">Mis Equipos</a>
            <a href="/equipos/crear" class="quick-action-btn">Registrar Equipo</a>
            <a href="/perfil" class="quick-action-btn">Mi Perfil</a>
        <?php endif; ?>
    </div>

    <div class="system-status">
        <h3>Estado del Sistema</h3>
        <p>
            <strong>Versión:</strong> 1.3.0 (Fase 3 Completada)<br>
            <strong>Última actualización:</strong> <?= date('d/m/Y H:i') ?><br>
            <strong>Estado:</strong> <span style="color: #28a745; font-weight: bold;">Operativo</span>
        </p>
    </div>
</div>


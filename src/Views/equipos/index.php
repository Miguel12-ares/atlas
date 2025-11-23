<div class="page-header">
    <h2>Mis Equipos Registrados</h2>
    <a href="/equipos/crear" class="btn btn-primary">Registrar Nuevo Equipo</a>
</div>

<div class="container">
<!-- Filtros -->
            <div class="filtros-container">
                <form method="GET" action="/equipos" class="filtros-form">
                    <div class="form-group">
                        <label class="form-label">Marca</label>
                        <input type="text" name="marca" class="form-control" placeholder="Filtrar por marca" value="<?= htmlspecialchars($_GET['marca'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="modelo" class="form-control" placeholder="Filtrar por modelo" value="<?= htmlspecialchars($_GET['modelo'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">N° Serie</label>
                        <input type="text" name="numero_serie" class="form-control" placeholder="Filtrar por N° Serie" value="<?= htmlspecialchars($_GET['numero_serie'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Estado</label>
                        <select name="estado_equipo" class="form-control">
                            <option value="">Todos</option>
                            <option value="activo" <?= (($_GET['estado_equipo'] ?? '') === 'activo') ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= (($_GET['estado_equipo'] ?? '') === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
                            <option value="bloqueado" <?= (($_GET['estado_equipo'] ?? '') === 'bloqueado') ? 'selected' : '' ?>>Bloqueado</option>
                            <option value="en_revision" <?= (($_GET['estado_equipo'] ?? '') === 'en_revision') ? 'selected' : '' ?>>En Revisión</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="/equipos" class="btn btn-secondary">Limpiar</a>
                    </div>
                </form>
            </div>

            <!-- Alertas -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['error_message']) ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Lista de Equipos -->
            <?php if (!empty($equipos)): ?>
                <div class="equipos-grid">
                    <?php foreach ($equipos as $equipo): ?>
                        <div class="equipo-card">
                            <div class="equipo-imagen">
                                <?php if (!empty($equipo['imagen_principal'])): ?>
                                    <img src="<?= htmlspecialchars($equipo['imagen_principal']) ?>" alt="<?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="equipo-body">
                                <div class="equipo-marca"><?= htmlspecialchars($equipo['marca']) ?></div>
                                <div class="equipo-modelo"><?= htmlspecialchars($equipo['modelo']) ?></div>
                                <div class="equipo-serie">S/N: <?= htmlspecialchars($equipo['numero_serie']) ?></div>
                                <span class="equipo-estado estado-<?= htmlspecialchars($equipo['estado_equipo']) ?>">
                                    <?php
                                    $estados = [
                                    'activo' => 'Activo',
                                    'inactivo' => 'Inactivo',
                                    'bloqueado' => 'Bloqueado',
                                    'en_revision' => 'En Revisión'
                                    ];
                                    echo $estados[$equipo['estado_equipo']] ?? $equipo['estado_equipo'];
                                    ?>
                                </span>
                                <div class="equipo-acciones">
                                    <a href="/equipos/<?= $equipo['id_equipo'] ?>" class="btn-sm btn-info">Ver</a>
                                    <a href="/equipos/<?= $equipo['id_equipo'] ?>/editar" class="btn-sm btn-warning">Editar</a>
                                    <?php if (!empty($equipo['qr_imagen'])): ?>
                                        <a href="<?= htmlspecialchars($equipo['qr_imagen']) ?>" download class="btn-sm btn-primary">Descargar QR</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon"></div>
                    <h3>No tienes equipos registrados</h3>
                    <p>Comienza registrando tu primer equipo para poder controlar su acceso al centro.</p>
                    <a href="/equipos/crear" class="btn btn-primary">Registrar Mi Primer Equipo</a>
                </div>
            <?php endif; ?>
</div>

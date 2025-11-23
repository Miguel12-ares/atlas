<div class="container detail-container">
            <!-- Header -->
            <div class="detail-header">
                <div class="detail-title">
                    <h2>📱 <?= htmlspecialchars($equipo['marca'] . ' ' . $equipo['modelo']) ?></h2>
                    <div class="detail-subtitle">
                        S/N: <strong><?= htmlspecialchars($equipo['numero_serie']) ?></strong>
                    </div>
                </div>
                <div class="detail-actions">
                    <a href="/equipos" class="btn btn-secondary">← Volver</a>
                    <?php if ($es_propietario || $es_admin): ?>
                        <a href="/equipos/<?= $equipo['id_equipo'] ?>/editar" class="btn btn-warning">✏ Editar</a>
                        <?php if (empty($equipo['qr_imagen'])): ?>
                            <form method="POST" action="/equipos/<?= $equipo['id_equipo'] ?>/generar-qr" style="display: inline;">
                                <button type="submit" class="btn btn-primary">🔲 Generar QR</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Alertas -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    ✅ <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <div class="detail-content">
                <!-- Información del Equipo -->
                <div class="detail-section">
                    <h3 class="section-title">📋 Información del Equipo</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Estado</span>
                            <span class="status-badge status-<?= htmlspecialchars($equipo['estado_equipo']) ?>">
                                <?php
                                $estados = [
                                    'activo' => '✓ Activo',
                                    'inactivo' => '✗ Inactivo',
                                    'bloqueado' => '🔒 Bloqueado',
                                    'en_revision' => '⚠ En Revisión'
                                ];
                                echo $estados[$equipo['estado_equipo']] ?? $equipo['estado_equipo'];
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Marca</span>
                            <span class="info-value"><?= htmlspecialchars($equipo['marca']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Modelo</span>
                            <span class="info-value"><?= htmlspecialchars($equipo['modelo']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Número de Serie</span>
                            <span class="info-value monospace"><?= htmlspecialchars($equipo['numero_serie']) ?></span>
                        </div>
                        <?php if (!empty($equipo['descripcion'])): ?>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <span class="info-label">Descripción</span>
                            <span class="info-value"><?= nl2br(htmlspecialchars($equipo['descripcion'])) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="info-item">
                            <span class="info-label">Fecha de Registro</span>
                            <span class="info-value"><?= date('d/m/Y H:i', strtotime($equipo['created_at'])) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Última Actualización</span>
                            <span class="info-value"><?= date('d/m/Y H:i', strtotime($equipo['updated_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Información del Propietario -->
                <div class="detail-section">
                    <h3 class="section-title">👤 Propietario del Equipo</h3>
                    <div class="user-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Nombre Completo</span>
                                <span class="info-value"><?= htmlspecialchars($equipo['nombres'] . ' ' . $equipo['apellidos']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Identificación</span>
                                <span class="info-value"><?= htmlspecialchars($equipo['numero_identificacion']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Rol</span>
                                <span class="info-value"><?= htmlspecialchars($equipo['nombre_rol']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?= htmlspecialchars($equipo['email']) ?></span>
                            </div>
                            <?php if (!empty($equipo['telefono'])): ?>
                            <div class="info-item">
                                <span class="info-label">Teléfono</span>
                                <span class="info-value"><?= htmlspecialchars($equipo['telefono']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Código QR -->
                <div class="detail-section">
                    <h3 class="section-title">🔲 Código QR</h3>
                    <?php if (!empty($equipo['qr_imagen'])): ?>
                        <div class="qr-container">
                            <div class="qr-image">
                                <img src="<?= htmlspecialchars($equipo['qr_imagen']) ?>" alt="Código QR">
                            </div>
                            <div class="qr-actions">
                                <a href="<?= htmlspecialchars($equipo['qr_imagen']) ?>" download="QR_<?= htmlspecialchars($equipo['numero_serie']) ?>.png" class="btn btn-primary">
                                    ⬇ Descargar QR
                                </a>
                                <button onclick="window.print()" class="btn btn-secondary">
                                    🖨 Imprimir
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-qr">
                            <div class="no-qr-icon">🔲</div>
                            <p>Este equipo aún no tiene un código QR generado.</p>
                            <?php if ($es_propietario || $es_admin): ?>
                                <form method="POST" action="/equipos/<?= $equipo['id_equipo'] ?>/generar-qr">
                                    <button type="submit" class="btn btn-primary">
                                        Generar Código QR
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Estado Actual -->
                <?php if (!empty($estado_actual)): ?>
                <div class="detail-section">
                    <h3 class="section-title">📍 Estado Actual</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Ubicación</span>
                            <span class="info-value">
                                <?php if ($estado_actual['tipo_registro'] === 'entrada'): ?>
                                    <span style="color: #28a745;">✓ Dentro del Centro</span>
                                <?php else: ?>
                                    <span style="color: #6c757d;">✗ Fuera del Centro</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Último Movimiento</span>
                            <span class="info-value">
                                <?= $estado_actual['tipo_registro'] === 'entrada' ? '📥 Entrada' : '📤 Salida' ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha y Hora</span>
                            <span class="info-value">
                                <?= date('d/m/Y H:i:s', strtotime($estado_actual['fecha_hora'])) ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Método de Verificación</span>
                            <span class="info-value">
                                <?php
                                $metodos = [
                                    'qr' => '🔲 Código QR',
                                    'manual' => '✍ Manual',
                                    'numero_serie' => '🔢 Número de Serie'
                                ];
                                echo $metodos[$estado_actual['metodo_verificacion']] ?? $estado_actual['metodo_verificacion'];
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Galería de Imágenes -->
                <div class="detail-section full-width">
                    <h3 class="section-title">📸 Galería de Imágenes</h3>
                    <?php if (!empty($imagenes)): ?>
                        <div class="gallery-grid">
                            <?php foreach ($imagenes as $imagen): ?>
                                <div class="gallery-item" onclick="openModal('<?= htmlspecialchars($imagen['ruta_imagen']) ?>')">
                                    <img src="<?= htmlspecialchars($imagen['ruta_imagen']) ?>" alt="Imagen del equipo">
                                    <?php if ($imagen['tipo_imagen'] === 'principal'): ?>
                                        <span class="gallery-badge">⭐ Principal</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-gallery">
                            <p>📷 Este equipo no tiene imágenes registradas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
</div>

<!-- Modal para ver imágenes -->
<div id="imageModal" class="modal" onclick="closeModal()">
    <span class="modal-close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
    function openModal(imageSrc) {
        document.getElementById('imageModal').style.display = 'block';
        document.getElementById('modalImage').src = imageSrc;
    }

    function closeModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>


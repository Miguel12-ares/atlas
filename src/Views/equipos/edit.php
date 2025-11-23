<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipo - Sistema Atlas</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #39A900;
        }

        .form-header h2 {
            color: #39A900;
            margin-bottom: 0.5rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            padding-left: 0.5rem;
            border-left: 4px solid #39A900;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .required::after {
            content: " *";
            color: #dc3545;
        }

        .form-help {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .existing-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .existing-image {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .existing-image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .image-remove {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .image-remove:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .image-principal-badge {
            position: absolute;
            bottom: 0.5rem;
            left: 0.5rem;
            background: #39A900;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .set-principal-btn {
            position: absolute;
            bottom: 0.5rem;
            left: 0.5rem;
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .set-principal-btn:hover {
            background: #5a6268;
        }

        .file-upload-area {
            border: 2px dashed #39A900;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            background: #E8F5E0;
            border-color: #2D8400;
        }

        .upload-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .preview-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .preview-remove {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .preview-remove:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .btn-group-left {
            display: flex;
            gap: 1rem;
        }

        .char-counter {
            font-size: 0.85rem;
            color: #6c757d;
            text-align: right;
            margin-top: 0.25rem;
        }

        .delete-section {
            background: #f8d7da;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin-top: 2rem;
        }

        .delete-section h4 {
            color: #721c24;
            margin-bottom: 1rem;
        }

        .marcas-comunes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .marca-btn {
            padding: 0.5rem;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 0.9rem;
        }

        .marca-btn:hover {
            background: #E8F5E0;
            border-color: #39A900;
        }

        .marca-btn.selected {
            background: #39A900;
            color: white;
            border-color: #2D8400;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <h1>🎓 Sistema Atlas</h1>
                <span>Control de Acceso de Equipos</span>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="/dashboard">Dashboard</a></li>
                    <li><a href="/equipos" style="background-color: var(--sena-green-dark);">Mis Equipos</a></li>
                    
                    <?php if (in_array($user['nombre_rol'], ['Administrador', 'Administrativo'])): ?>
                        <li><a href="/usuarios">Usuarios</a></li>
                        <li><a href="/reportes">Reportes</a></li>
                    <?php endif; ?>
                    
                    <?php if ($user['nombre_rol'] === 'Portería'): ?>
                        <li><a href="/registros">Registros</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="user-menu">
                <div class="user-name">
                    <strong><?= htmlspecialchars($user['nombres'] . ' ' . $user['apellidos']) ?></strong>
                    <small><?= htmlspecialchars($user['nombre_rol']) ?></small>
                </div>
                <a href="/perfil" class="btn-profile">Mi Perfil</a>
                <a href="/logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>✏ Editar Equipo</h2>
                    <p>Actualiza la información de tu equipo</p>
                </div>

                <!-- Alertas -->
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error">
                        ❌ <?= htmlspecialchars($_SESSION['error_message']) ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <form id="equipoForm" method="POST" action="/equipos/<?= $equipo['id_equipo'] ?>/editar" enctype="multipart/form-data">
                    <!-- Sección 1: Información Básica -->
                    <div class="form-section">
                        <h3 class="form-section-title">📋 Información Básica</h3>
                        
                        <div class="form-group">
                            <label class="form-label required">Número de Serie</label>
                            <input 
                                type="text" 
                                id="numero_serie" 
                                name="numero_serie" 
                                class="form-control" 
                                placeholder="Ej: ABC123456"
                                maxlength="100"
                                required
                                value="<?= htmlspecialchars($equipo['numero_serie']) ?>"
                            >
                            <div class="form-help">Número único de identificación del equipo</div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Marca</label>
                                <input 
                                    type="text" 
                                    id="marca" 
                                    name="marca" 
                                    class="form-control" 
                                    placeholder="Ej: Apple, Samsung, HP..."
                                    maxlength="100"
                                    required
                                    value="<?= htmlspecialchars($equipo['marca']) ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Modelo</label>
                                <input 
                                    type="text" 
                                    id="modelo" 
                                    name="modelo" 
                                    class="form-control" 
                                    placeholder="Ej: MacBook Pro 2023"
                                    maxlength="100"
                                    required
                                    value="<?= htmlspecialchars($equipo['modelo']) ?>"
                                >
                            </div>
                        </div>

                        <div class="marcas-comunes">
                            <button type="button" class="marca-btn" data-marca="Apple">🍎 Apple</button>
                            <button type="button" class="marca-btn" data-marca="Samsung">📱 Samsung</button>
                            <button type="button" class="marca-btn" data-marca="HP">💻 HP</button>
                            <button type="button" class="marca-btn" data-marca="Dell">🖥 Dell</button>
                            <button type="button" class="marca-btn" data-marca="Lenovo">⌨ Lenovo</button>
                            <button type="button" class="marca-btn" data-marca="Asus">🎮 Asus</button>
                            <button type="button" class="marca-btn" data-marca="Acer">💼 Acer</button>
                            <button type="button" class="marca-btn" data-marca="Huawei">📲 Huawei</button>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Estado del Equipo</label>
                            <select name="estado_equipo" class="form-control">
                                <option value="activo" <?= $equipo['estado_equipo'] === 'activo' ? 'selected' : '' ?>>✓ Activo</option>
                                <option value="inactivo" <?= $equipo['estado_equipo'] === 'inactivo' ? 'selected' : '' ?>>✗ Inactivo</option>
                                <option value="bloqueado" <?= $equipo['estado_equipo'] === 'bloqueado' ? 'selected' : '' ?>>🔒 Bloqueado</option>
                                <option value="en_revision" <?= $equipo['estado_equipo'] === 'en_revision' ? 'selected' : '' ?>>⚠ En Revisión</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sección 2: Descripción -->
                    <div class="form-section">
                        <h3 class="form-section-title">📝 Descripción</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Descripción del Equipo (Opcional)</label>
                            <textarea 
                                id="descripcion" 
                                name="descripcion" 
                                class="form-control" 
                                rows="4"
                                maxlength="500"
                                placeholder="Descripción detallada del equipo, características especiales, etc."
                            ><?= htmlspecialchars($equipo['descripcion'] ?? '') ?></textarea>
                            <div class="char-counter">
                                <span id="char-count"><?= strlen($equipo['descripcion'] ?? '') ?></span> / 500 caracteres
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Imágenes Existentes -->
                    <?php if (!empty($imagenes)): ?>
                    <div class="form-section">
                        <h3 class="form-section-title">📸 Imágenes Actuales</h3>
                        <div class="existing-images">
                            <?php foreach ($imagenes as $imagen): ?>
                                <div class="existing-image" data-image-id="<?= $imagen['id_imagen'] ?>">
                                    <img src="<?= htmlspecialchars($imagen['ruta_imagen']) ?>" alt="Imagen del equipo">
                                    <button type="button" class="image-remove" onclick="removeExistingImage(<?= $imagen['id_imagen'] ?>)">×</button>
                                    <?php if ($imagen['tipo_imagen'] === 'principal'): ?>
                                        <span class="image-principal-badge">⭐ Principal</span>
                                    <?php else: ?>
                                        <button type="button" class="set-principal-btn" onclick="setPrincipalImage(<?= $imagen['id_imagen'] ?>)">
                                            Hacer Principal
                                        </button>
                                    <?php endif; ?>
                                    <input type="hidden" name="imagenes_mantener[]" value="<?= $imagen['id_imagen'] ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="imagen_principal_id" id="imagen_principal_id" value="">
                        <input type="hidden" name="imagenes_eliminar" id="imagenes_eliminar" value="">
                    </div>
                    <?php endif; ?>

                    <!-- Sección 4: Nuevas Imágenes -->
                    <div class="form-section">
                        <h3 class="form-section-title">📷 Agregar Nuevas Imágenes</h3>
                        
                        <div class="form-group">
                            <div class="file-upload-area" id="uploadArea">
                                <div class="upload-icon">📷</div>
                                <p><strong>Arrastra y suelta imágenes aquí</strong></p>
                                <p>o</p>
                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('imagenes').click()">
                                    Seleccionar Archivos
                                </button>
                                <input 
                                    type="file" 
                                    id="imagenes" 
                                    name="imagenes_nuevas[]" 
                                    accept="image/jpeg,image/png,image/jpg"
                                    multiple
                                    style="display: none;"
                                >
                            </div>
                            <div class="form-help">
                                Formatos permitidos: JPG, PNG. Tamaño máximo: 5MB por imagen.
                            </div>
                        </div>

                        <div id="previewContainer" class="preview-container"></div>
                    </div>

                    <!-- Botones -->
                    <div class="btn-group">
                        <div class="btn-group-left">
                            <a href="/equipos/<?= $equipo['id_equipo'] ?>" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                ✓ Guardar Cambios
                            </button>
                        </div>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                            🗑 Eliminar Equipo
                        </button>
                    </div>
                </form>

                <!-- Sección de Eliminación -->
                <div class="delete-section">
                    <h4>⚠ Zona Peligrosa</h4>
                    <p>Una vez eliminado, el equipo será marcado como inactivo y no podrá recuperarse fácilmente.</p>
                    <form id="deleteForm" method="POST" action="/equipos/<?= $equipo['id_equipo'] ?>/eliminar" style="display: none;">
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Sistema Atlas - SENA Colombia</p>
        </div>
    </footer>

    <script src="/assets/js/equipo-form.js"></script>
    <script>
        let imagenesAEliminar = [];

        function removeExistingImage(imageId) {
            if (confirm('¿Estás seguro de eliminar esta imagen?')) {
                imagenesAEliminar.push(imageId);
                document.getElementById('imagenes_eliminar').value = imagenesAEliminar.join(',');
                
                const imageElement = document.querySelector(`[data-image-id="${imageId}"]`);
                if (imageElement) {
                    imageElement.style.opacity = '0.3';
                    imageElement.style.pointerEvents = 'none';
                    const input = imageElement.querySelector('input[name="imagenes_mantener[]"]');
                    if (input) {
                        input.remove();
                    }
                }
            }
        }

        function setPrincipalImage(imageId) {
            document.getElementById('imagen_principal_id').value = imageId;
            
            // Actualizar visualmente
            document.querySelectorAll('.image-principal-badge').forEach(badge => badge.remove());
            document.querySelectorAll('.set-principal-btn').forEach(btn => btn.style.display = 'block');
            
            const imageElement = document.querySelector(`[data-image-id="${imageId}"]`);
            if (imageElement) {
                const badge = document.createElement('span');
                badge.className = 'image-principal-badge';
                badge.textContent = '⭐ Principal';
                imageElement.appendChild(badge);
                
                const btn = imageElement.querySelector('.set-principal-btn');
                if (btn) {
                    btn.style.display = 'none';
                }
            }
        }

        function confirmDelete() {
            if (confirm('¿Estás seguro de que deseas eliminar este equipo?\n\nEsta acción marcará el equipo como inactivo.')) {
                document.getElementById('deleteForm').submit();
            }
        }

        // Actualizar contador de caracteres
        const descripcionTextarea = document.getElementById('descripcion');
        const charCount = document.getElementById('char-count');
        
        if (descripcionTextarea && charCount) {
            descripcionTextarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }
    </script>
</body>
</html>


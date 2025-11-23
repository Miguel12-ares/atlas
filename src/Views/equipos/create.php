<div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>📱 Registrar Nuevo Equipo</h2>
                    <p>Completa el formulario para registrar tu equipo en el sistema de control de acceso</p>
                </div>

                <!-- Alertas -->
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error">
                        ❌ <?= htmlspecialchars($_SESSION['error_message']) ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <form id="equipoForm" method="POST" action="/equipos/crear" enctype="multipart/form-data">
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
                                value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>"
                            >
                            <div class="form-help">Número único de identificación del equipo (máx. 100 caracteres)</div>
                            <div class="error-message" id="error-numero_serie"></div>
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
                                    value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>"
                                >
                                <div class="error-message" id="error-marca"></div>
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
                                    value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>"
                                >
                                <div class="error-message" id="error-modelo"></div>
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
                            ><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
                            <div class="char-counter">
                                <span id="char-count">0</span> / 500 caracteres
                            </div>
                        </div>
                    </div>

                    <!-- Sección 3: Imágenes -->
                    <div class="form-section">
                        <h3 class="form-section-title">📸 Imágenes del Equipo</h3>
                        
                        <div class="form-group">
                            <label class="form-label">Imágenes (Máximo 5)</label>
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
                                    name="imagenes[]" 
                                    accept="image/jpeg,image/png,image/jpg"
                                    multiple
                                    style="display: none;"
                                >
                            </div>
                            <div class="form-help">
                                Formatos permitidos: JPG, PNG. Tamaño máximo: 5MB por imagen. La primera imagen será la principal.
                            </div>
                            <div class="error-message" id="error-imagenes"></div>
                        </div>

                        <div id="previewContainer" class="preview-container"></div>
                    </div>

                    <!-- Botones -->
                    <div class="btn-group">
                        <a href="/equipos" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            ✓ Registrar Equipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
</div>

/**
 * Sistema Atlas - Validación y Manejo de Formularios de Equipos
 * @version 1.0
 */

(function() {
    'use strict';

    // Configuración
    const MAX_FILES = 5;
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB en bytes
    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];
    
    let selectedFiles = [];

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        initFormValidation();
        initFileUpload();
        initMarcaButtons();
        initCharCounter();
    });

    /**
     * Inicializa la validación del formulario
     */
    function initFormValidation() {
        const form = document.getElementById('equipoForm');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                showError('Por favor, corrige los errores en el formulario antes de continuar.');
            }
        });

        // Validación en tiempo real
        const numeroSerie = document.getElementById('numero_serie');
        if (numeroSerie) {
            numeroSerie.addEventListener('blur', validateNumeroSerie);
            numeroSerie.addEventListener('input', function() {
                // Solo permitir caracteres alfanuméricos y guiones
                this.value = this.value.replace(/[^a-zA-Z0-9-_]/g, '');
            });
        }

        const marca = document.getElementById('marca');
        if (marca) {
            marca.addEventListener('blur', validateMarca);
        }

        const modelo = document.getElementById('modelo');
        if (modelo) {
            modelo.addEventListener('blur', validateModelo);
        }
    }

    /**
     * Valida el formulario completo
     */
    function validateForm() {
        let isValid = true;

        // Limpiar mensajes de error previos
        clearErrors();

        // Validar número de serie
        if (!validateNumeroSerie()) {
            isValid = false;
        }

        // Validar marca
        if (!validateMarca()) {
            isValid = false;
        }

        // Validar modelo
        if (!validateModelo()) {
            isValid = false;
        }

        return isValid;
    }

    /**
     * Valida el número de serie
     */
    function validateNumeroSerie() {
        const numeroSerie = document.getElementById('numero_serie');
        if (!numeroSerie) return true;

        const value = numeroSerie.value.trim();
        const errorElement = document.getElementById('error-numero_serie');

        if (value.length === 0) {
            showFieldError(numeroSerie, errorElement, 'El número de serie es obligatorio');
            return false;
        }

        if (value.length < 3) {
            showFieldError(numeroSerie, errorElement, 'El número de serie debe tener al menos 3 caracteres');
            return false;
        }

        if (value.length > 100) {
            showFieldError(numeroSerie, errorElement, 'El número de serie no puede exceder 100 caracteres');
            return false;
        }

        // Validar formato (alfanumérico con guiones)
        if (!/^[a-zA-Z0-9-_]+$/.test(value)) {
            showFieldError(numeroSerie, errorElement, 'Solo se permiten letras, números, guiones y guiones bajos');
            return false;
        }

        clearFieldError(numeroSerie, errorElement);
        return true;
    }

    /**
     * Valida la marca
     */
    function validateMarca() {
        const marca = document.getElementById('marca');
        if (!marca) return true;

        const value = marca.value.trim();
        const errorElement = document.getElementById('error-marca');

        if (value.length === 0) {
            showFieldError(marca, errorElement, 'La marca es obligatoria');
            return false;
        }

        if (value.length < 2) {
            showFieldError(marca, errorElement, 'La marca debe tener al menos 2 caracteres');
            return false;
        }

        if (value.length > 100) {
            showFieldError(marca, errorElement, 'La marca no puede exceder 100 caracteres');
            return false;
        }

        clearFieldError(marca, errorElement);
        return true;
    }

    /**
     * Valida el modelo
     */
    function validateModelo() {
        const modelo = document.getElementById('modelo');
        if (!modelo) return true;

        const value = modelo.value.trim();
        const errorElement = document.getElementById('error-modelo');

        if (value.length === 0) {
            showFieldError(modelo, errorElement, 'El modelo es obligatorio');
            return false;
        }

        if (value.length < 2) {
            showFieldError(modelo, errorElement, 'El modelo debe tener al menos 2 caracteres');
            return false;
        }

        if (value.length > 100) {
            showFieldError(modelo, errorElement, 'El modelo no puede exceder 100 caracteres');
            return false;
        }

        clearFieldError(modelo, errorElement);
        return true;
    }

    /**
     * Inicializa la funcionalidad de subida de archivos
     */
    function initFileUpload() {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('imagenes');
        const previewContainer = document.getElementById('previewContainer');

        if (!uploadArea || !fileInput || !previewContainer) return;

        // Eventos de drag & drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });

        // Evento de selección de archivos
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            handleFiles(files);
        });
    }

    /**
     * Maneja los archivos seleccionados
     */
    function handleFiles(files) {
        const errorElement = document.getElementById('error-imagenes');
        clearFieldError(null, errorElement);

        // Validar número máximo de archivos
        if (selectedFiles.length + files.length > MAX_FILES) {
            showFieldError(null, errorElement, `Solo puedes subir un máximo de ${MAX_FILES} imágenes`);
            return;
        }

        // Validar cada archivo
        const validFiles = [];
        for (let file of files) {
            // Validar tipo
            if (!ALLOWED_TYPES.includes(file.type)) {
                showError(`El archivo ${file.name} no es una imagen válida. Solo se permiten JPG y PNG.`);
                continue;
            }

            // Validar tamaño
            if (file.size > MAX_FILE_SIZE) {
                showError(`El archivo ${file.name} excede el tamaño máximo de 5MB.`);
                continue;
            }

            validFiles.push(file);
        }

        // Agregar archivos válidos
        validFiles.forEach(file => {
            selectedFiles.push(file);
            addPreview(file);
        });

        // Actualizar el input de archivos
        updateFileInput();
    }

    /**
     * Agrega una vista previa de la imagen
     */
    function addPreview(file) {
        const previewContainer = document.getElementById('previewContainer');
        if (!previewContainer) return;

        const reader = new FileReader();
        
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.dataset.filename = file.name;

            const img = document.createElement('img');
            img.src = e.target.result;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'preview-remove';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function() {
                removeFile(file.name);
            };

            div.appendChild(img);
            div.appendChild(removeBtn);

            // Marcar la primera imagen como principal
            if (selectedFiles.length === 1) {
                const badge = document.createElement('span');
                badge.className = 'preview-principal';
                badge.textContent = '⭐ Principal';
                div.appendChild(badge);
            }

            previewContainer.appendChild(div);
        };

        reader.readAsDataURL(file);
    }

    /**
     * Elimina un archivo de la selección
     */
    function removeFile(filename) {
        selectedFiles = selectedFiles.filter(f => f.name !== filename);
        
        const previewContainer = document.getElementById('previewContainer');
        if (previewContainer) {
            const previewItem = previewContainer.querySelector(`[data-filename="${filename}"]`);
            if (previewItem) {
                previewItem.remove();
            }
        }

        updateFileInput();

        // Actualizar badge de principal
        if (selectedFiles.length > 0) {
            const firstPreview = previewContainer.querySelector('.preview-item');
            if (firstPreview && !firstPreview.querySelector('.preview-principal')) {
                const badge = document.createElement('span');
                badge.className = 'preview-principal';
                badge.textContent = '⭐ Principal';
                firstPreview.appendChild(badge);
            }
        }
    }

    /**
     * Actualiza el input de archivos con los archivos seleccionados
     */
    function updateFileInput() {
        const fileInput = document.getElementById('imagenes');
        if (!fileInput) return;

        // Crear un nuevo DataTransfer para actualizar el input
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });

        fileInput.files = dataTransfer.files;
    }

    /**
     * Inicializa los botones de marca
     */
    function initMarcaButtons() {
        const marcaBtns = document.querySelectorAll('.marca-btn');
        const marcaInput = document.getElementById('marca');

        if (!marcaInput) return;

        marcaBtns.forEach(btn => {
            // Marcar la marca actual si coincide
            if (btn.dataset.marca === marcaInput.value) {
                btn.classList.add('selected');
            }

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remover selección de todos los botones
                marcaBtns.forEach(b => b.classList.remove('selected'));
                
                // Seleccionar el botón clickeado
                this.classList.add('selected');
                
                // Actualizar el input
                marcaInput.value = this.dataset.marca;
                
                // Validar
                validateMarca();
            });
        });

        // Si el usuario escribe manualmente, deseleccionar botones
        marcaInput.addEventListener('input', function() {
            const value = this.value.trim();
            let found = false;

            marcaBtns.forEach(btn => {
                if (btn.dataset.marca === value) {
                    btn.classList.add('selected');
                    found = true;
                } else {
                    btn.classList.remove('selected');
                }
            });
        });
    }

    /**
     * Inicializa el contador de caracteres para la descripción
     */
    function initCharCounter() {
        const descripcion = document.getElementById('descripcion');
        const charCount = document.getElementById('char-count');

        if (!descripcion || !charCount) return;

        descripcion.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            
            // Cambiar color si está cerca del límite
            if (this.value.length > 450) {
                charCount.style.color = '#dc3545';
            } else if (this.value.length > 400) {
                charCount.style.color = '#ffc107';
            } else {
                charCount.style.color = '#6c757d';
            }
        });
    }

    /**
     * Muestra un error en un campo específico
     */
    function showFieldError(input, errorElement, message) {
        if (input) {
            input.classList.add('error');
        }
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    /**
     * Limpia el error de un campo específico
     */
    function clearFieldError(input, errorElement) {
        if (input) {
            input.classList.remove('error');
        }
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    /**
     * Limpia todos los errores
     */
    function clearErrors() {
        document.querySelectorAll('.form-control.error').forEach(el => {
            el.classList.remove('error');
        });
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
    }

    /**
     * Muestra un mensaje de error general
     */
    function showError(message) {
        alert(message);
    }

})();


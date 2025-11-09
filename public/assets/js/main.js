/**
 * Sistema Atlas - Script Principal
 * Versión: 1.0
 */

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema Atlas v1.0 - Inicializado');
    
    // Inicializar componentes
    initAlerts();
    initForms();
    initTables();
});

/**
 * Inicializa el sistema de alertas
 */
function initAlerts() {
    // Auto-cerrar alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            fadeOut(alert);
        }, 5000);
    });
}

/**
 * Inicializa validación de formularios
 */
function initForms() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showAlert('Por favor, completa todos los campos requeridos', 'error');
            }
        });
    });
}

/**
 * Inicializa funcionalidad de tablas
 */
function initTables() {
    // Añadir clase hover a filas de tabla
    const tables = document.querySelectorAll('.table');
    
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.style.cursor = 'pointer';
        });
    });
}

/**
 * Valida un formulario
 * @param {HTMLFormElement} form 
 * @returns {boolean}
 */
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Muestra una alerta
 * @param {string} message 
 * @param {string} type 
 */
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.main-content .container');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        fadeOut(alertDiv);
    }, 5000);
}

/**
 * Efecto fade out para elementos
 * @param {HTMLElement} element 
 */
function fadeOut(element) {
    element.style.transition = 'opacity 0.5s';
    element.style.opacity = '0';
    
    setTimeout(() => {
        element.remove();
    }, 500);
}

/**
 * Confirma una acción
 * @param {string} message 
 * @returns {boolean}
 */
function confirmAction(message) {
    return confirm(message);
}

/**
 * Formatea una fecha
 * @param {string} dateString 
 * @returns {string}
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleString('es-CO', options);
}

/**
 * Realiza una petición AJAX
 * @param {string} url 
 * @param {object} options 
 * @returns {Promise}
 */
async function ajax(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };
    
    const config = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, config);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Error en petición AJAX:', error);
        throw error;
    }
}

/**
 * Utilidad para debounce
 * @param {Function} func 
 * @param {number} wait 
 * @returns {Function}
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Exportar funciones para uso global
window.Atlas = {
    showAlert,
    confirmAction,
    formatDate,
    ajax,
    debounce,
    validateForm
};


# 📝 Registro de Cambios - Sistema Atlas

## Versión 1.3.0 - Fase 3: Gestión de Equipos (19/11/2025)

### ✨ Nuevas Características

#### 🔧 Gestión Completa de Equipos
- **Registro de equipos** con validación completa
- **Subida múltiple de imágenes** (máximo 5 por equipo)
- **Generación automática de códigos QR** únicos
- **Edición completa** de información y gestión de imágenes
- **Eliminación suave** (soft delete) preservando historial
- **Filtros avanzados** por marca, modelo, número de serie y estado

#### 📸 Sistema de Imágenes
- Drag & drop para subir imágenes
- Redimensionamiento automático (1200x1200 max)
- Compresión automática (JPEG 90%, PNG 9)
- Validación de tipo MIME y tamaño (5MB max)
- Gestión de imagen principal
- Vista previa antes de subir
- Galería con modal para ver en grande

#### 🔲 Códigos QR
- Generación con API externa + fallback GD2
- Payload JSON estructurado con timestamp
- Desactivación automática de códigos anteriores
- Descarga e impresión de códigos
- Almacenamiento organizado en `/uploads/qr/`

#### 🎯 Validaciones
- **Frontend:** JavaScript con validación en tiempo real
- **Backend:** PHP con sanitización exhaustiva
- Verificación de unicidad de número de serie
- Validación de formatos y longitudes
- Mensajes de error específicos

#### 🔒 Seguridad
- Verificación de permisos por acción
- Validación de propiedad de recursos
- Protección contra directory traversal
- Soft delete para integridad referencial
- Timestamps de auditoría automáticos

### 📁 Archivos Nuevos
- `src/Models/Equipo.php`
- `src/Models/ImagenEquipo.php`
- `src/Models/CodigoQR.php`
- `src/Core/QRCodeGenerator.php`
- `src/Views/equipos/index.php`
- `src/Views/equipos/create.php`
- `src/Views/equipos/show.php`
- `src/Views/equipos/edit.php`
- `public/assets/js/equipo-form.js`
- `database/04-equipos-seeds.sql`
- `docs/FASE_3_COMPLETADA.md`
- `docs/TESTING_FASE_3.md`

### 🔄 Archivos Modificados
- `src/Controllers/EquipoController.php` (completamente implementado)
- `README.md` (actualizado con progreso de fases)

### 📊 Estadísticas
- **3 modelos nuevos** con 30+ métodos
- **4 vistas completas** con 1000+ líneas HTML/PHP
- **1 archivo JS** con 500+ líneas de validación
- **2000+ líneas** de código PHP nuevo
- **100% de cobertura** de funcionalidades Fase 3

Ver documentación completa: `docs/FASE_3_COMPLETADA.md`

---

## Versión 1.0.0 - Release Inicial (09/11/2025)

### ✨ Características Principales

- Sistema MVC nativo en PHP 8.2
- 9 tablas en base de datos (3NF)
- Sistema de autenticación con bcrypt
- Control de acceso basado en roles (RBAC)
- Dashboard con estadísticas en tiempo real
- Arquitectura Docker completa

### 🎨 Diseño

- Colores institucionales del SENA
- Verde principal: #39A900
- Interfaz responsive
- Diseño limpio y moderno

### 🔐 Seguridad

- Contraseñas hasheadas con bcrypt
- PDO prepared statements
- Validación y sanitización de entradas
- Control de permisos por rol
- Sesiones seguras

### 🗄️ Base de Datos

- MySQL 8.0
- 9 tablas en 3NF
- Datos de prueba incluidos
- 4 usuarios precargados
- 6 roles del sistema

### 🐳 Docker

- PHP 8.2-apache
- MySQL 8.0
- phpMyAdmin
- Red privada configurada
- Volúmenes persistentes

### 📚 Documentación

- README.md completo
- Guía de instalación (INSTALL.md)
- Comentarios en código en español

---

**Fecha de Release:** 2025-11-09  
**Equipo:** Sistema Atlas  
**Organización:** SENA Colombia


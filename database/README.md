# Base de Datos - Sistema Atlas

## Estructura de Archivos

Esta carpeta contiene todos los archivos SQL necesarios para la creación y mantenimiento de la base de datos del Sistema Atlas.

### Archivos Principales

#### `00-complete-schema.sql`
**Archivo de creación completa de la base de datos**

Este archivo contiene la definición completa de todas las tablas del sistema. Incluye:
- 11 tablas principales
- Índices y foreign keys
- Configuración de charset UTF-8
- Comentarios descriptivos

**Uso:** Ejecutar este archivo primero para crear la estructura completa de la base de datos.

---

### Archivos de Migración por Fase

#### `01-initial-data.sql`
**Datos iniciales del sistema (Fase 1)**

Contiene:
- Roles del sistema
- Usuarios de prueba
- Configuración de horarios
- Datos básicos de equipos

**Uso:** Ejecutar después de `00-complete-schema.sql`

---

#### `02-rbac-permissions.sql`
**Sistema de permisos RBAC (Fase 2)**

Contiene:
- Creación de tabla de permisos
- Creación de tabla de relación roles-permisos
- Inserción de todos los permisos del sistema
- Asignación de permisos a roles

**Uso:** Ejecutar después de `01-initial-data.sql`

---

#### `03-equipos-data.sql`
**Datos de equipos y códigos QR (Fase 3)**

Contiene:
- Equipos de prueba adicionales
- Imágenes de equipos
- Códigos QR generados
- Registros de acceso de ejemplo

**Uso:** Ejecutar después de `02-rbac-permissions.sql`

---

#### `04-fix-permissions.sql`
**Corrección de permisos por rol**

Contiene:
- Corrección de permisos para roles específicos
- Verificación de permisos incorrectos
- Ajustes según especificaciones del sistema

**Uso:** Ejecutar después de `03-equipos-data.sql` si es necesario

---

## Orden de Ejecución Recomendado

Para una instalación limpia, ejecutar los archivos en este orden:

```sql
1. 00-complete-schema.sql    -- Crear estructura completa
2. 01-initial-data.sql       -- Datos iniciales
3. 02-rbac-permissions.sql   -- Sistema de permisos
4. 03-equipos-data.sql       -- Datos de equipos
5. 04-fix-permissions.sql    -- Correcciones (opcional)
```

## Estructura de Tablas

### Tablas Principales

1. **roles** - Catálogo de roles del sistema
2. **usuarios** - Información de usuarios
3. **equipos** - Registro de equipos electrónicos
4. **imagenes_equipo** - Imágenes de equipos
5. **codigos_qr** - Códigos QR generados
6. **registros_acceso** - Registros de entrada/salida
7. **anomalias** - Anomalías detectadas
8. **configuracion_horario** - Horarios permitidos
9. **sesiones** - Sesiones activas
10. **permisos** - Catálogo de permisos (RBAC)
11. **role_perm** - Relación roles-permisos (RBAC)

## Notas Importantes

- Todos los archivos usan transacciones para garantizar integridad
- Los archivos son idempotentes (pueden ejecutarse múltiples veces)
- Se recomienda hacer backup antes de ejecutar migraciones en producción
- Las contraseñas en los archivos de seeds son para desarrollo únicamente

## Configuración de Base de Datos

- **Nombre:** `atlas_db`
- **Charset:** `utf8mb4`
- **Collation:** `utf8mb4_unicode_ci`
- **Engine:** `InnoDB`

## Seguridad

⚠️ **IMPORTANTE:** Los archivos de seeds contienen datos de prueba. En producción:
- Cambiar todas las contraseñas
- Eliminar usuarios de prueba
- Configurar permisos según políticas de seguridad
- No exponer archivos SQL en repositorios públicos


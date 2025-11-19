-- =====================================================
-- SISTEMA ATLAS - MIGRACIÓN COMPLETA FASE 2
-- EJECUTAR MANUALMENTE DESDE phpMyAdmin o MySQL CLI
-- =====================================================
-- 
-- INSTRUCCIONES:
-- 
-- Opción 1: Desde phpMyAdmin (http://localhost:8081)
-- 1. Accede a phpMyAdmin
-- 2. Usuario: root, Contraseña: atlas_root_2024
-- 3. Selecciona la base de datos "atlas_db"
-- 4. Ve a la pestaña "SQL"
-- 5. Copia y pega todo el contenido de este archivo
-- 6. Haz clic en "Continuar"
-- 
-- Opción 2: Desde MySQL CLI
-- docker exec -i atlas_mysql mysql -uroot -patlas_root_2024 atlas_db < database/EJECUTAR_MANUALMENTE.sql
--
-- =====================================================

USE atlas_db;

-- =====================================================
-- PASO 1: CREAR TABLA DE PERMISOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `permisos` (
  `id_permiso` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_permiso` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nombre único del permiso (ej: usuarios.crear)',
  `recurso` VARCHAR(50) NOT NULL COMMENT 'Recurso al que aplica (ej: usuarios, equipos)',
  `accion` VARCHAR(50) NOT NULL COMMENT 'Acción que permite (ej: crear, leer, actualizar, eliminar)',
  `descripcion` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_permiso`),
  INDEX `idx_nombre_permiso` (`nombre_permiso`),
  INDEX `idx_recurso_accion` (`recurso`, `accion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de permisos del sistema';

-- =====================================================
-- PASO 2: CREAR TABLA DE RELACIÓN ROLES-PERMISOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `role_perm` (
  `id_role_perm` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_rol` INT UNSIGNED NOT NULL,
  `id_permiso` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_role_perm`),
  UNIQUE KEY `unique_rol_permiso` (`id_rol`, `id_permiso`),
  INDEX `idx_rol` (`id_rol`),
  INDEX `idx_permiso` (`id_permiso`),
  CONSTRAINT `fk_role_perm_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_role_perm_permiso` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relación entre roles y permisos';

-- =====================================================
-- PASO 3: INSERTAR PERMISOS - USUARIOS
-- =====================================================

INSERT IGNORE INTO `permisos` (`nombre_permiso`, `recurso`, `accion`, `descripcion`) VALUES
('usuarios.crear', 'usuarios', 'crear', 'Permite crear nuevos usuarios'),
('usuarios.leer', 'usuarios', 'leer', 'Permite ver información de usuarios'),
('usuarios.actualizar', 'usuarios', 'actualizar', 'Permite editar información de usuarios'),
('usuarios.eliminar', 'usuarios', 'eliminar', 'Permite eliminar usuarios'),
('usuarios.gestionar', 'usuarios', 'gestionar', 'Acceso completo a gestión de usuarios');

-- =====================================================
-- PASO 4: INSERTAR PERMISOS - EQUIPOS
-- =====================================================

INSERT IGNORE INTO `permisos` (`nombre_permiso`, `recurso`, `accion`, `descripcion`) VALUES
('equipos.crear', 'equipos', 'crear', 'Permite registrar nuevos equipos'),
('equipos.leer', 'equipos', 'leer', 'Permite ver información de equipos'),
('equipos.actualizar', 'equipos', 'actualizar', 'Permite editar información de equipos'),
('equipos.eliminar', 'equipos', 'eliminar', 'Permite eliminar equipos'),
('equipos.gestionar', 'equipos', 'gestionar', 'Acceso completo a gestión de equipos');

-- =====================================================
-- PASO 5: INSERTAR PERMISOS - REGISTROS
-- =====================================================

INSERT IGNORE INTO `permisos` (`nombre_permiso`, `recurso`, `accion`, `descripcion`) VALUES
('registros.crear', 'registros', 'crear', 'Permite crear registros de entrada/salida'),
('registros.leer', 'registros', 'leer', 'Permite ver registros de acceso'),
('registros.actualizar', 'registros', 'actualizar', 'Permite editar registros'),
('registros.eliminar', 'registros', 'eliminar', 'Permite eliminar registros'),
('registros.gestionar', 'registros', 'gestionar', 'Acceso completo a gestión de registros');

-- =====================================================
-- PASO 6: INSERTAR PERMISOS - ANOMALÍAS
-- =====================================================

INSERT IGNORE INTO `permisos` (`nombre_permiso`, `recurso`, `accion`, `descripcion`) VALUES
('anomalias.crear', 'anomalias', 'crear', 'Permite registrar anomalías'),
('anomalias.leer', 'anomalias', 'leer', 'Permite ver anomalías'),
('anomalias.actualizar', 'anomalias', 'actualizar', 'Permite actualizar estado de anomalías'),
('anomalias.eliminar', 'anomalias', 'eliminar', 'Permite eliminar anomalías'),
('anomalias.resolver', 'anomalias', 'resolver', 'Permite resolver anomalías');

-- =====================================================
-- PASO 7: INSERTAR PERMISOS - CONFIGURACIÓN
-- =====================================================

INSERT IGNORE INTO `permisos` (`nombre_permiso`, `recurso`, `accion`, `descripcion`) VALUES
('configuracion.leer', 'configuracion', 'leer', 'Permite ver configuración del sistema'),
('configuracion.actualizar', 'configuracion', 'actualizar', 'Permite modificar configuración del sistema');

-- =====================================================
-- PASO 8: INSERTAR PERMISOS - REPORTES
-- =====================================================

INSERT IGNORE INTO `permisos` (`nombre_permiso`, `recurso`, `accion`, `descripcion`) VALUES
('reportes.generar', 'reportes', 'generar', 'Permite generar reportes'),
('reportes.exportar', 'reportes', 'exportar', 'Permite exportar reportes');

-- =====================================================
-- PASO 9: INSERTAR PERMISOS - ROLES Y PERFIL
-- =====================================================

INSERT IGNORE INTO `permisos` (`nombre_permiso`, `recurso`, `accion`, `descripcion`) VALUES
('roles.gestionar', 'roles', 'gestionar', 'Permite gestionar roles y permisos del sistema'),
('perfil.actualizar', 'perfil', 'actualizar', 'Permite actualizar su propio perfil');

-- =====================================================
-- PASO 10: ASIGNAR PERMISOS AL ROL ADMIN (id_rol = 1)
-- Admin tiene TODOS los permisos
-- =====================================================

INSERT IGNORE INTO `role_perm` (`id_rol`, `id_permiso`) 
SELECT 1, id_permiso FROM permisos;

-- =====================================================
-- PASO 11: ASIGNAR PERMISOS AL ROL ADMINISTRATIVO (id_rol = 2)
-- =====================================================

INSERT IGNORE INTO `role_perm` (`id_rol`, `id_permiso`) 
SELECT 2, id_permiso FROM permisos WHERE nombre_permiso IN (
    'usuarios.crear',
    'usuarios.leer',
    'usuarios.actualizar',
    'equipos.crear',
    'equipos.leer',
    'equipos.actualizar',
    'registros.leer',
    'anomalias.leer',
    'anomalias.actualizar',
    'reportes.generar',
    'reportes.exportar',
    'perfil.actualizar'
);

-- =====================================================
-- PASO 12: ASIGNAR PERMISOS AL ROL INSTRUCTOR (id_rol = 3)
-- =====================================================

INSERT IGNORE INTO `role_perm` (`id_rol`, `id_permiso`) 
SELECT 3, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.crear',
    'equipos.leer',
    'equipos.actualizar',
    'registros.leer',
    'perfil.actualizar'
);

-- =====================================================
-- PASO 13: ASIGNAR PERMISOS AL ROL APRENDIZ (id_rol = 4)
-- =====================================================

INSERT IGNORE INTO `role_perm` (`id_rol`, `id_permiso`) 
SELECT 4, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.crear',
    'equipos.leer',
    'equipos.actualizar',
    'registros.leer',
    'perfil.actualizar'
);

-- =====================================================
-- PASO 14: ASIGNAR PERMISOS AL ROL CIVIL (id_rol = 5)
-- =====================================================

INSERT IGNORE INTO `role_perm` (`id_rol`, `id_permiso`) 
SELECT 5, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.crear',
    'equipos.leer',
    'equipos.actualizar',
    'registros.leer',
    'perfil.actualizar'
);

-- =====================================================
-- PASO 15: ASIGNAR PERMISOS AL ROL PORTERÍA (id_rol = 6)
-- =====================================================

INSERT IGNORE INTO `role_perm` (`id_rol`, `id_permiso`) 
SELECT 6, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.leer',
    'registros.crear',
    'registros.leer',
    'anomalias.crear',
    'anomalias.leer',
    'perfil.actualizar'
);

-- =====================================================
-- VERIFICACIÓN: Contar permisos por rol
-- =====================================================

SELECT 
    r.nombre_rol,
    COUNT(rp.id_permiso) as total_permisos
FROM roles r
LEFT JOIN role_perm rp ON r.id_rol = rp.id_rol
GROUP BY r.id_rol, r.nombre_rol
ORDER BY r.id_rol;

-- Resultado esperado:
-- admin: 26 permisos
-- administrativo: 12 permisos
-- instructor: 5 permisos
-- aprendiz: 5 permisos
-- civil: 5 permisos
-- porteria: 6 permisos

-- =====================================================
-- FIN DE LA MIGRACIÓN
-- =====================================================

SELECT '✅ MIGRACIÓN COMPLETADA EXITOSAMENTE' as mensaje;
SELECT CONCAT('Total de permisos creados: ', COUNT(*)) as resultado FROM permisos;
SELECT CONCAT('Total de asignaciones rol-permiso: ', COUNT(*)) as resultado FROM role_perm;


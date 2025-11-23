-- =====================================================
-- SISTEMA ATLAS - CORRECCIÓN DE PERMISOS
-- =====================================================
-- 
-- Este archivo corrige los permisos según roles específicos
-- Ejecutar solo si es necesario ajustar permisos después
-- de la instalación inicial
-- 
-- Versión: 1.0
-- Fecha: 2024
-- 
-- =====================================================
-- INSTRUCCIONES
-- =====================================================
-- 
-- Ejecutar después de: 03-equipos-data.sql
-- Solo ejecutar si es necesario corregir permisos
-- 
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

USE atlas_db;

-- =====================================================
-- VERIFICAR QUE EXISTAN LAS TABLAS NECESARIAS
-- =====================================================

-- Si no existen las tablas de permisos, ejecutar primero 02-rbac-permissions.sql

-- =====================================================
-- LIMPIAR PERMISOS EXISTENTES PARA RECONFIGURAR
-- =====================================================

DELETE FROM role_perm WHERE id_rol IN (3, 4, 5, 6);

-- =====================================================
-- PERMISOS CORREGIDOS POR ROL
-- =====================================================

-- ==========================
-- ROL 3: INSTRUCTOR
-- ==========================
-- Instructores pueden:
-- - Gestionar sus propios equipos (crear, leer, actualizar)
-- - Ver registros de sus equipos
-- - Actualizar su perfil

INSERT IGNORE INTO role_perm (id_rol, id_permiso)
SELECT 3, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.crear',
    'equipos.leer',
    'equipos.actualizar',
    'registros.leer',
    'perfil.actualizar'
);

-- ==========================
-- ROL 4: APRENDIZ
-- ==========================
-- Aprendices pueden:
-- - Gestionar sus propios equipos (crear, leer, actualizar)
-- - Ver registros de sus equipos
-- - Actualizar su perfil
-- NO pueden ver usuarios ni generar reportes

INSERT IGNORE INTO role_perm (id_rol, id_permiso)
SELECT 4, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.crear',
    'equipos.leer',
    'equipos.actualizar',
    'registros.leer',
    'perfil.actualizar'
);

-- ==========================
-- ROL 5: CIVIL
-- ==========================
-- Civiles pueden:
-- - Gestionar sus propios equipos (crear, leer, actualizar)
-- - Ver registros de sus equipos
-- - Actualizar su perfil

INSERT IGNORE INTO role_perm (id_rol, id_permiso)
SELECT 5, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.crear',
    'equipos.leer',
    'equipos.actualizar',
    'registros.leer',
    'perfil.actualizar'
);

-- ==========================
-- ROL 6: PORTERÍA
-- ==========================
-- Portería puede:
-- - Ver equipos (para verificación)
-- - Crear registros de entrada/salida
-- - Ver registros
-- - Crear anomalías
-- - Ver anomalías
-- - Actualizar su perfil
-- NO pueden crear, editar o eliminar equipos

INSERT IGNORE INTO role_perm (id_rol, id_permiso)
SELECT 6, id_permiso FROM permisos WHERE nombre_permiso IN (
    'equipos.leer',
    'registros.crear',
    'registros.leer',
    'anomalias.crear',
    'anomalias.leer',
    'perfil.actualizar'
);

-- =====================================================
-- VERIFICACIÓN: Mostrar permisos por rol
-- =====================================================

SELECT 
    r.id_rol,
    r.nombre_rol,
    COUNT(rp.id_permiso) as total_permisos,
    GROUP_CONCAT(p.nombre_permiso ORDER BY p.nombre_permiso SEPARATOR ', ') as permisos
FROM roles r
LEFT JOIN role_perm rp ON r.id_rol = rp.id_rol
LEFT JOIN permisos p ON rp.id_permiso = p.id_permiso
GROUP BY r.id_rol, r.nombre_rol
ORDER BY r.id_rol;

-- =====================================================
-- VERIFICAR CONFIGURACIÓN ESPECÍFICA
-- =====================================================

-- Verificar que roles NO tengan permisos que no deberían
-- Aprendices NO deberían tener estos permisos:
SELECT 'VERIFICACION: Aprendices con permisos incorrectos' as verificacion;
SELECT p.nombre_permiso
FROM role_perm rp
INNER JOIN permisos p ON rp.id_permiso = p.id_permiso
WHERE rp.id_rol = 4
AND p.nombre_permiso IN (
    'usuarios.crear',
    'usuarios.leer',
    'usuarios.actualizar',
    'usuarios.eliminar',
    'reportes.generar',
    'reportes.exportar',
    'configuracion.leer',
    'configuracion.actualizar'
);

-- Si devuelve filas, hay permisos incorrectos
-- Si no devuelve nada, está correcto

-- Verificar que Portería NO pueda crear/editar equipos
SELECT 'VERIFICACION: Portería con permisos de gestión de equipos' as verificacion;
SELECT p.nombre_permiso
FROM role_perm rp
INNER JOIN permisos p ON rp.id_permiso = p.id_permiso
WHERE rp.id_rol = 6
AND p.nombre_permiso IN (
    'equipos.crear',
    'equipos.actualizar',
    'equipos.eliminar'
);

-- Si devuelve filas, hay permisos incorrectos
-- Si no devuelve nada, está correcto

-- =====================================================
-- RESULTADOS ESPERADOS
-- =====================================================

/*
Distribución de permisos esperada:

ROL 1 (Administrador): ~26 permisos (TODOS)
ROL 2 (Administrativo): ~12 permisos
ROL 3 (Instructor): 5 permisos
ROL 4 (Aprendiz): 5 permisos
ROL 5 (Civil): 5 permisos
ROL 6 (Portería): 6 permisos

Permisos por rol:

INSTRUCTOR:
  - equipos.crear
  - equipos.leer
  - equipos.actualizar
  - registros.leer
  - perfil.actualizar

APRENDIZ:
  - equipos.crear
  - equipos.leer
  - equipos.actualizar
  - registros.leer
  - perfil.actualizar

CIVIL:
  - equipos.crear
  - equipos.leer
  - equipos.actualizar
  - registros.leer
  - perfil.actualizar

PORTERÍA:
  - equipos.leer (solo lectura)
  - registros.crear
  - registros.leer
  - anomalias.crear
  - anomalias.leer
  - perfil.actualizar
*/

-- =====================================================
-- FIN DE CORRECCIÓN
-- =====================================================

SELECT 'PERMISOS CORREGIDOS EXITOSAMENTE' as resultado;

COMMIT;


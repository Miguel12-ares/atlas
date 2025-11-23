-- =====================================================
-- SISTEMA ATLAS - ESQUEMA COMPLETO DE BASE DE DATOS
-- =====================================================
-- 
-- Este archivo contiene la creación completa de la base de datos
-- Incluye todas las tablas, índices, foreign keys y configuración inicial
-- 
-- Versión: 1.0
-- Fecha: 2024
-- Normalización: 3NF (Tercera Forma Normal)
-- Total de tablas: 11
-- 
-- =====================================================
-- INSTRUCCIONES DE USO
-- =====================================================
-- 
-- 1. Crear la base de datos:
--    CREATE DATABASE IF NOT EXISTS atlas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 
-- 2. Seleccionar la base de datos:
--    USE atlas_db;
-- 
-- 3. Ejecutar este archivo completo
-- 
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Configuración de caracteres UTF-8
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_connection=utf8mb4;

-- =====================================================
-- TABLA 1: ROLES
-- Descripción: Catálogo de roles del sistema con permisos
-- =====================================================
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_rol` VARCHAR(50) NOT NULL UNIQUE,
  `descripcion` TEXT DEFAULT NULL,
  `puede_tener_equipo` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Indica si el rol puede registrar equipos',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rol`),
  INDEX `idx_nombre_rol` (`nombre_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catálogo de roles del sistema';

-- =====================================================
-- TABLA 2: USUARIOS
-- Descripción: Información de usuarios del sistema
-- =====================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_identificacion` VARCHAR(20) NOT NULL UNIQUE,
  `nombres` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `telefono` VARCHAR(15) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `id_rol` INT UNSIGNED NOT NULL,
  `estado` ENUM('activo', 'inactivo', 'suspendido') NOT NULL DEFAULT 'activo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  INDEX `idx_numero_identificacion` (`numero_identificacion`),
  INDEX `idx_email` (`email`),
  INDEX `idx_estado` (`estado`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuarios del sistema';

-- =====================================================
-- TABLA 3: EQUIPOS
-- Descripción: Registro de equipos electrónicos
-- =====================================================
CREATE TABLE IF NOT EXISTS `equipos` (
  `id_equipo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `numero_serie` VARCHAR(100) NOT NULL UNIQUE,
  `marca` VARCHAR(100) NOT NULL,
  `modelo` VARCHAR(100) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `estado_equipo` ENUM('activo', 'inactivo', 'bloqueado', 'en_revision') NOT NULL DEFAULT 'activo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_equipo`),
  INDEX `idx_numero_serie` (`numero_serie`),
  INDEX `idx_estado_equipo` (`estado_equipo`),
  CONSTRAINT `fk_equipos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Equipos registrados en el sistema';

-- =====================================================
-- TABLA 4: IMAGENES_EQUIPO
-- Descripción: Almacenamiento de imágenes de equipos
-- =====================================================
CREATE TABLE IF NOT EXISTS `imagenes_equipo` (
  `id_imagen` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_equipo` INT UNSIGNED NOT NULL,
  `ruta_imagen` VARCHAR(255) NOT NULL,
  `tipo_imagen` ENUM('principal', 'frontal', 'lateral', 'trasera', 'detalle') NOT NULL DEFAULT 'principal',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_imagen`),
  INDEX `idx_equipo` (`id_equipo`),
  CONSTRAINT `fk_imagenes_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Imágenes de los equipos';

-- =====================================================
-- TABLA 5: CODIGOS_QR
-- Descripción: Códigos QR generados para equipos
-- =====================================================
CREATE TABLE IF NOT EXISTS `codigos_qr` (
  `id_qr` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_equipo` INT UNSIGNED NOT NULL UNIQUE,
  `codigo_qr` TEXT NOT NULL COMMENT 'Código único generado para el equipo',
  `ruta_imagen_qr` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta de la imagen QR generada',
  `activo` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_qr`),
  INDEX `idx_activo` (`activo`),
  CONSTRAINT `fk_qr_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Códigos QR de equipos';

-- =====================================================
-- TABLA 6: REGISTROS_ACCESO
-- Descripción: Registro de entradas y salidas de equipos
-- =====================================================
CREATE TABLE IF NOT EXISTS `registros_acceso` (
  `id_registro` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_equipo` INT UNSIGNED NOT NULL,
  `id_portero` INT UNSIGNED NOT NULL COMMENT 'Usuario de portería que registra',
  `tipo_registro` ENUM('entrada', 'salida') NOT NULL,
  `fecha_hora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metodo_verificacion` ENUM('qr', 'manual', 'numero_serie') NOT NULL DEFAULT 'manual',
  `observaciones` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_registro`),
  INDEX `idx_fecha_hora` (`fecha_hora`),
  INDEX `idx_equipo_tipo` (`id_equipo`, `tipo_registro`),
  INDEX `idx_tipo_registro` (`tipo_registro`),
  CONSTRAINT `fk_registro_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_registro_portero` FOREIGN KEY (`id_portero`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registros de entrada y salida';

-- =====================================================
-- TABLA 7: ANOMALIAS
-- Descripción: Registro de anomalías detectadas
-- =====================================================
CREATE TABLE IF NOT EXISTS `anomalias` (
  `id_anomalia` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_equipo` INT UNSIGNED NOT NULL,
  `tipo_anomalia` ENUM('equipo_no_registrado', 'discrepancia_datos', 'fuera_horario', 'equipo_bloqueado', 'otro') NOT NULL,
  `descripcion` TEXT NOT NULL,
  `fecha_deteccion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('pendiente', 'en_revision', 'resuelta', 'desestimada') NOT NULL DEFAULT 'pendiente',
  `id_registro_relacionado` INT UNSIGNED DEFAULT NULL,
  `resolucion` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_anomalia`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_fecha_deteccion` (`fecha_deteccion`),
  INDEX `idx_tipo_anomalia` (`tipo_anomalia`),
  CONSTRAINT `fk_anomalia_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_anomalia_registro` FOREIGN KEY (`id_registro_relacionado`) REFERENCES `registros_acceso` (`id_registro`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anomalías detectadas en el sistema';

-- =====================================================
-- TABLA 8: CONFIGURACION_HORARIO
-- Descripción: Configuración de horarios permitidos
-- =====================================================
CREATE TABLE IF NOT EXISTS `configuracion_horario` (
  `id_horario` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dia_semana` ENUM('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo') NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `activo` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_horario`),
  INDEX `idx_dia_activo` (`dia_semana`, `activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuración de horarios de acceso';

-- =====================================================
-- TABLA 9: SESIONES
-- Descripción: Gestión de sesiones de usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS `sesiones` (
  `id_sesion` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `token_sesion` VARCHAR(255) NOT NULL UNIQUE,
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'Soporta IPv4 e IPv6',
  `user_agent` TEXT DEFAULT NULL,
  `fecha_inicio` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` DATETIME NOT NULL,
  `activo` BOOLEAN NOT NULL DEFAULT TRUE,
  PRIMARY KEY (`id_sesion`),
  INDEX `idx_token` (`token_sesion`),
  INDEX `idx_usuario_activo` (`id_usuario`, `activo`),
  CONSTRAINT `fk_sesion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sesiones activas de usuarios';

-- =====================================================
-- TABLA 10: PERMISOS
-- Descripción: Catálogo de permisos del sistema (RBAC)
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
-- TABLA 11: ROLE_PERM
-- Descripción: Relación entre roles y permisos (RBAC)
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
-- FIN DEL ESQUEMA
-- =====================================================

COMMIT;


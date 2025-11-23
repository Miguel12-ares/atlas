-- =====================================================
-- SISTEMA ATLAS - DATOS INICIALES (FASE 1)
-- =====================================================
-- 
-- Este archivo contiene los datos iniciales del sistema:
-- - Roles del sistema
-- - Usuarios de prueba
-- - Configuración de horarios
-- - Datos básicos de equipos
-- 
-- Versión: 1.0
-- Fecha: 2024
-- 
-- =====================================================
-- INSTRUCCIONES
-- =====================================================
-- 
-- Ejecutar después de: 00-complete-schema.sql
-- 
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

USE atlas_db;

-- =====================================================
-- INSERCIÓN DE ROLES
-- =====================================================
INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`, `puede_tener_equipo`) VALUES
(1, 'admin', 'Administrador del sistema con acceso total', TRUE),
(2, 'administrativo', 'Personal administrativo con permisos de gestión', TRUE),
(3, 'instructor', 'Instructor del SENA', TRUE),
(4, 'aprendiz', 'Aprendiz del SENA', TRUE),
(5, 'civil', 'Personal civil externo', TRUE),
(6, 'porteria', 'Personal de portería para registro de accesos', FALSE);

-- =====================================================
-- INSERCIÓN DE USUARIOS
-- Contraseñas hasheadas con bcrypt
-- =====================================================
INSERT INTO `usuarios` (`id_usuario`, `numero_identificacion`, `nombres`, `apellidos`, `email`, `telefono`, `password_hash`, `id_rol`, `estado`) VALUES
(1, '1000000', 'Admin', 'Sistema', 'admin@atlas.sena', '3001234567', '$2y$10$d2Nsfi6uMLsCm2rcSAxLNONIcfY.jbcDWzTW4iQR.U44QUpCe8EHO', 1, 'activo'),
(2, '52123456', 'Carlos', 'Mendoza Ruiz', 'portero@atlas.sena', '3009876543', '$2y$10$9xitKGHyaepjI5aYsvE9/eLNVS3fbGYk/4zjiVJDVu1cWwX/6FLt.', 6, 'activo'),
(3, '80456789', 'María', 'López García', 'maria.lopez@sena.edu.co', '3012345678', '$2y$10$jMKT1fxYAdk1f2duTv4yn.r5oNhKp64MMa.Pqw1twU9Q5SeuZHlBW', 3, 'activo'),
(4, '1098765432', 'Juan', 'Pérez Martínez', 'juan.perez@sena.edu.co', '3156789012', '$2y$10$5pV6Jn/dlnHi77Azgd6N1eUtJsEkuQGHPjIaXNSoc3u5L1tbxe2tq', 4, 'activo');

-- =====================================================
-- INSERCIÓN DE EQUIPOS BÁSICOS
-- =====================================================
INSERT INTO `equipos` (`id_equipo`, `id_usuario`, `numero_serie`, `marca`, `modelo`, `descripcion`, `estado_equipo`) VALUES
(1, 3, 'DELL-SN-2024-001', 'Dell', 'Latitude 5420', 'Laptop para instructor - 16GB RAM, 512GB SSD', 'activo'),
(2, 4, 'HP-SN-2024-045', 'HP', 'Pavilion 15', 'Laptop de aprendiz - 8GB RAM, 256GB SSD', 'activo'),
(3, 4, 'ASUS-SN-2024-089', 'Asus', 'VivoBook 14', 'Laptop secundaria - 8GB RAM, 512GB HDD', 'activo');

-- =====================================================
-- INSERCIÓN DE IMÁGENES DE EQUIPOS (Ejemplos)
-- =====================================================
INSERT INTO `imagenes_equipo` (`id_imagen`, `id_equipo`, `ruta_imagen`, `tipo_imagen`) VALUES
(1, 1, '/uploads/equipos/dell_lat5420_principal.jpg', 'principal'),
(2, 1, '/uploads/equipos/dell_lat5420_frontal.jpg', 'frontal'),
(3, 2, '/uploads/equipos/hp_pav15_principal.jpg', 'principal'),
(4, 3, '/uploads/equipos/asus_vivo14_principal.jpg', 'principal');

-- =====================================================
-- INSERCIÓN DE CÓDIGOS QR
-- =====================================================
INSERT INTO `codigos_qr` (`id_qr`, `id_equipo`, `codigo_qr`, `ruta_imagen_qr`, `activo`) VALUES
(1, 1, 'ATLAS-QR-001-DELL-2024', '/uploads/qr/equipo_1_qr.png', TRUE),
(2, 2, 'ATLAS-QR-045-HP-2024', '/uploads/qr/equipo_2_qr.png', TRUE),
(3, 3, 'ATLAS-QR-089-ASUS-2024', '/uploads/qr/equipo_3_qr.png', TRUE);

-- =====================================================
-- INSERCIÓN DE CONFIGURACIÓN DE HORARIOS
-- =====================================================
INSERT INTO `configuracion_horario` (`id_horario`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`) VALUES
(1, 'lunes', '06:00:00', '20:00:00', TRUE),
(2, 'martes', '06:00:00', '20:00:00', TRUE),
(3, 'miercoles', '06:00:00', '20:00:00', TRUE),
(4, 'jueves', '06:00:00', '20:00:00', TRUE),
(5, 'viernes', '06:00:00', '20:00:00', TRUE),
(6, 'sabado', '08:00:00', '14:00:00', FALSE),
(7, 'domingo', '08:00:00', '14:00:00', FALSE);

-- =====================================================
-- INSERCIÓN DE REGISTROS DE ACCESO (Ejemplos)
-- =====================================================
INSERT INTO `registros_acceso` (`id_registro`, `id_equipo`, `id_portero`, `tipo_registro`, `fecha_hora`, `metodo_verificacion`, `observaciones`) VALUES
(1, 1, 2, 'entrada', '2024-11-09 07:30:00', 'qr', 'Registro normal de entrada'),
(2, 2, 2, 'entrada', '2024-11-09 08:15:00', 'qr', 'Registro normal de entrada'),
(3, 1, 2, 'salida', '2024-11-09 17:45:00', 'qr', 'Registro normal de salida'),
(4, 3, 2, 'entrada', '2024-11-09 09:00:00', 'manual', 'QR no disponible, verificación manual');

-- =====================================================
-- INSERCIÓN DE ANOMALÍAS (Ejemplos)
-- =====================================================
INSERT INTO `anomalias` (`id_anomalia`, `id_equipo`, `tipo_anomalia`, `descripcion`, `fecha_deteccion`, `estado`, `id_registro_relacionado`, `resolucion`) VALUES
(1, 3, 'discrepancia_datos', 'Número de serie no coincide completamente con base de datos', '2024-11-09 09:05:00', 'resuelta', 4, 'Se verificó físicamente el equipo, el número de serie es correcto. Actualizado en sistema.');

-- =====================================================
-- FIN DE DATOS INICIALES
-- =====================================================

COMMIT;

-- =====================================================
-- INFORMACIÓN DE USUARIOS PARA PRUEBAS
-- =====================================================
-- Usuario: admin@atlas.sena | Password: admin123
-- Usuario: portero@atlas.sena | Password: portero123
-- Usuario: maria.lopez@sena.edu.co | Password: instructor123
-- Usuario: juan.perez@sena.edu.co | Password: aprendiz123
-- =====================================================


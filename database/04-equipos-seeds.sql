-- =====================================================
-- SISTEMA ATLAS - DATOS DE PRUEBA FASE 3
-- Equipos, Imágenes y Códigos QR
-- Versión: 1.0
-- =====================================================

USE atlas_db;

-- =====================================================
-- INSERTAR EQUIPOS DE PRUEBA
-- =====================================================

-- Equipo 1: Laptop del Aprendiz Juan Pérez
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(4, 'HP-LAP-2023-001', 'HP', 'Pavilion 15', 'Laptop HP Pavilion 15 con procesador Intel i5, 8GB RAM, 256GB SSD. Color plateado. Uso para desarrollo de software.', 'activo');

-- Equipo 2: Tablet del Aprendiz Juan Pérez
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(4, 'SAMSUNG-TAB-2023-001', 'Samsung', 'Galaxy Tab S8', 'Tablet Samsung Galaxy Tab S8 para presentaciones y lectura de documentación técnica.', 'activo');

-- Equipo 3: MacBook del Instructor María López
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(3, 'APPLE-MBP-2023-001', 'Apple', 'MacBook Pro 13"', 'MacBook Pro 13 pulgadas modelo 2023, M2, 16GB RAM, 512GB SSD. Para clases de programación.', 'activo');

-- Equipo 4: Laptop Dell del Instructor María López
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(3, 'DELL-XPS-2022-001', 'Dell', 'XPS 15', 'Dell XPS 15 con pantalla 4K, Intel i7, 32GB RAM. Para edición de material educativo.', 'activo');

-- Equipo 5: Laptop Lenovo en revisión
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(4, 'LENOVO-THINK-2021-001', 'Lenovo', 'ThinkPad X1', 'ThinkPad X1 Carbon. Actualmente en mantenimiento por problemas con la batería.', 'en_revision');

-- Equipo 6: Laptop Asus bloqueada
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(3, 'ASUS-ROG-2023-001', 'Asus', 'ROG Strix G15', 'Laptop gaming para clases de diseño gráfico. Bloqueada por incidente de seguridad.', 'bloqueado');

-- Equipo 7: iPad para Admin
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(1, 'APPLE-IPAD-2023-001', 'Apple', 'iPad Pro 12.9"', 'iPad Pro para gestión administrativa y presentaciones.', 'activo');

-- Equipo 8: Laptop HP del Administrativo
INSERT INTO equipos (id_usuario, numero_serie, marca, modelo, descripcion, estado_equipo) VALUES
(2, 'HP-ELITE-2023-001', 'HP', 'EliteBook 840', 'HP EliteBook 840 G9 para tareas administrativas.', 'activo');

-- =====================================================
-- INSERTAR IMÁGENES DE EQUIPOS (SIMULADAS)
-- =====================================================
-- Nota: En producción, estas rutas apuntarían a archivos reales

-- Imágenes para Equipo 1 (HP Pavilion)
INSERT INTO imagenes_equipo (id_equipo, ruta_imagen, tipo_imagen) VALUES
(1, '/uploads/equipos/equipo_1_principal.jpg', 'principal'),
(1, '/uploads/equipos/equipo_1_lateral.jpg', 'lateral'),
(1, '/uploads/equipos/equipo_1_trasera.jpg', 'trasera');

-- Imágenes para Equipo 2 (Samsung Tab)
INSERT INTO imagenes_equipo (id_equipo, ruta_imagen, tipo_imagen) VALUES
(2, '/uploads/equipos/equipo_2_principal.jpg', 'principal'),
(2, '/uploads/equipos/equipo_2_frontal.jpg', 'frontal');

-- Imágenes para Equipo 3 (MacBook Pro)
INSERT INTO imagenes_equipo (id_equipo, ruta_imagen, tipo_imagen) VALUES
(3, '/uploads/equipos/equipo_3_principal.jpg', 'principal'),
(3, '/uploads/equipos/equipo_3_lateral.jpg', 'lateral'),
(3, '/uploads/equipos/equipo_3_detalle.jpg', 'detalle');

-- Imágenes para Equipo 4 (Dell XPS)
INSERT INTO imagenes_equipo (id_equipo, ruta_imagen, tipo_imagen) VALUES
(4, '/uploads/equipos/equipo_4_principal.jpg', 'principal'),
(4, '/uploads/equipos/equipo_4_frontal.jpg', 'frontal'),
(4, '/uploads/equipos/equipo_4_trasera.jpg', 'trasera'),
(4, '/uploads/equipos/equipo_4_detalle.jpg', 'detalle');

-- Imágenes para Equipo 5 (Lenovo ThinkPad)
INSERT INTO imagenes_equipo (id_equipo, ruta_imagen, tipo_imagen) VALUES
(5, '/uploads/equipos/equipo_5_principal.jpg', 'principal');

-- Imágenes para Equipo 7 (iPad Pro)
INSERT INTO imagenes_equipo (id_equipo, ruta_imagen, tipo_imagen) VALUES
(7, '/uploads/equipos/equipo_7_principal.jpg', 'principal'),
(7, '/uploads/equipos/equipo_7_frontal.jpg', 'frontal');

-- Imágenes para Equipo 8 (HP EliteBook)
INSERT INTO imagenes_equipo (id_equipo, ruta_imagen, tipo_imagen) VALUES
(8, '/uploads/equipos/equipo_8_principal.jpg', 'principal'),
(8, '/uploads/equipos/equipo_8_lateral.jpg', 'lateral');

-- =====================================================
-- INSERTAR CÓDIGOS QR (SIMULADOS)
-- =====================================================
-- Nota: En producción, estos serían generados automáticamente

-- QR para Equipo 1
INSERT INTO codigos_qr (id_equipo, codigo_qr, ruta_imagen_qr, activo) VALUES
(1, '{"id_equipo":1,"id_usuario":4,"numero_serie":"HP-LAP-2023-001","nombre_usuario":"Juan Pérez","timestamp":1700000000}', '/uploads/qr/qr_1_1700000000.png', 1);

-- QR para Equipo 2
INSERT INTO codigos_qr (id_equipo, codigo_qr, ruta_imagen_qr, activo) VALUES
(2, '{"id_equipo":2,"id_usuario":4,"numero_serie":"SAMSUNG-TAB-2023-001","nombre_usuario":"Juan Pérez","timestamp":1700000100}', '/uploads/qr/qr_2_1700000100.png', 1);

-- QR para Equipo 3
INSERT INTO codigos_qr (id_equipo, codigo_qr, ruta_imagen_qr, activo) VALUES
(3, '{"id_equipo":3,"id_usuario":3,"numero_serie":"APPLE-MBP-2023-001","nombre_usuario":"María López","timestamp":1700000200}', '/uploads/qr/qr_3_1700000200.png', 1);

-- QR para Equipo 4
INSERT INTO codigos_qr (id_equipo, codigo_qr, ruta_imagen_qr, activo) VALUES
(4, '{"id_equipo":4,"id_usuario":3,"numero_serie":"DELL-XPS-2022-001","nombre_usuario":"María López","timestamp":1700000300}', '/uploads/qr/qr_4_1700000300.png', 1);

-- QR para Equipo 7
INSERT INTO codigos_qr (id_equipo, codigo_qr, ruta_imagen_qr, activo) VALUES
(7, '{"id_equipo":7,"id_usuario":1,"numero_serie":"APPLE-IPAD-2023-001","nombre_usuario":"Carlos Admin","timestamp":1700000600}', '/uploads/qr/qr_7_1700000600.png', 1);

-- QR para Equipo 8
INSERT INTO codigos_qr (id_equipo, codigo_qr, ruta_imagen_qr, activo) VALUES
(8, '{"id_equipo":8,"id_usuario":2,"numero_serie":"HP-ELITE-2023-001","nombre_usuario":"Ana García","timestamp":1700000700}', '/uploads/qr/qr_8_1700000700.png', 1);

-- =====================================================
-- REGISTROS DE ACCESO SIMULADOS
-- (Para demostrar el estado actual de los equipos)
-- =====================================================

-- Equipo 1: Dentro del centro (última entrada)
INSERT INTO registros_acceso (id_equipo, id_portero, tipo_registro, fecha_hora, metodo_verificacion, observaciones) VALUES
(1, 52123456, 'entrada', DATE_SUB(NOW(), INTERVAL 2 HOUR), 'qr', 'Entrada registrada con código QR'),
(1, 52123456, 'salida', DATE_SUB(NOW(), INTERVAL 6 HOUR), 'qr', 'Salida al finalizar clases'),
(1, 52123456, 'entrada', DATE_SUB(NOW(), INTERVAL 8 HOUR), 'qr', 'Entrada por la mañana');

-- Equipo 2: Fuera del centro (última salida)
INSERT INTO registros_acceso (id_equipo, id_portero, tipo_registro, fecha_hora, metodo_verificacion, observaciones) VALUES
(2, 52123456, 'entrada', DATE_SUB(NOW(), INTERVAL 10 HOUR), 'qr', 'Entrada con código QR'),
(2, 52123456, 'salida', DATE_SUB(NOW(), INTERVAL 1 HOUR), 'qr', 'Salida registrada correctamente');

-- Equipo 3: Dentro del centro
INSERT INTO registros_acceso (id_equipo, id_portero, tipo_registro, fecha_hora, metodo_verificacion, observaciones) VALUES
(3, 52123456, 'entrada', DATE_SUB(NOW(), INTERVAL 3 HOUR), 'manual', 'Entrada manual - QR no disponible');

-- Equipo 4: Dentro del centro
INSERT INTO registros_acceso (id_equipo, id_portero, tipo_registro, fecha_hora, metodo_verificacion, observaciones) VALUES
(4, 52123456, 'entrada', DATE_SUB(NOW(), INTERVAL 5 HOUR), 'numero_serie', 'Entrada por número de serie');

-- Equipo 7: Dentro del centro
INSERT INTO registros_acceso (id_equipo, id_portero, tipo_registro, fecha_hora, metodo_verificacion, observaciones) VALUES
(7, 52123456, 'entrada', DATE_SUB(NOW(), INTERVAL 12 HOUR), 'qr', 'Entrada de administración');

-- =====================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- =====================================================

SELECT 'Equipos insertados:' AS Info, COUNT(*) AS Total FROM equipos;
SELECT 'Imágenes insertadas:' AS Info, COUNT(*) AS Total FROM imagenes_equipo;
SELECT 'Códigos QR insertados:' AS Info, COUNT(*) AS Total FROM codigos_qr;
SELECT 'Registros de acceso insertados:' AS Info, COUNT(*) AS Total FROM registros_acceso;

-- Mostrar resumen por usuario
SELECT 
    u.nombres,
    u.apellidos,
    u.numero_identificacion,
    r.nombre_rol,
    COUNT(e.id_equipo) as total_equipos,
    SUM(CASE WHEN e.estado_equipo = 'activo' THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN e.estado_equipo = 'inactivo' THEN 1 ELSE 0 END) as inactivos,
    SUM(CASE WHEN e.estado_equipo = 'bloqueado' THEN 1 ELSE 0 END) as bloqueados,
    SUM(CASE WHEN e.estado_equipo = 'en_revision' THEN 1 ELSE 0 END) as en_revision
FROM usuarios u
INNER JOIN roles r ON u.id_rol = r.id_rol
LEFT JOIN equipos e ON u.id_usuario = e.id_usuario
GROUP BY u.id_usuario
ORDER BY total_equipos DESC;

COMMIT;

-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================
-- 
-- 1. Las rutas de imágenes y códigos QR son simuladas
--    En producción, estas serán generadas automáticamente
--    por el sistema cuando se suban archivos reales
--
-- 2. Los registros de acceso usan el usuario de portería
--    (id_usuario = 52123456 del archivo 02-seeds.sql)
--
-- 3. Los timestamps de registros de acceso están configurados
--    relativos a NOW() para simular actividad reciente
--
-- 4. Los códigos QR en JSON contienen la estructura exacta
--    que el sistema genera automáticamente
--
-- =====================================================


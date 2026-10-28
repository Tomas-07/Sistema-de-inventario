-- =====================================================================
-- TecnoStock · Sistema de inventario
-- Script de creación de la base de datos (MySQL / MariaDB de XAMPP)
-- Importar desde phpMyAdmin: pestaña "Importar" o pestaña "SQL"
-- =====================================================================

DROP DATABASE IF EXISTS tecnostock;
CREATE DATABASE tecnostock
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE tecnostock;

-- ---------------------------------------------------------------------
-- 1. USUARIO
-- ---------------------------------------------------------------------
CREATE TABLE usuario (
  id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre     VARCHAR(100)  NOT NULL,
  correo     VARCHAR(120)  NOT NULL,
  clave      VARCHAR(255)  NOT NULL COMMENT 'Hash generado con password_hash()',
  estado     TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1 = activo, 0 = inactivo',
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uq_usuario_correo (correo),
  CONSTRAINT ck_usuario_estado CHECK (estado IN (0, 1))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. CATEGORIA
-- ---------------------------------------------------------------------
CREATE TABLE categoria (
  id_categoria INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre       VARCHAR(80)  NOT NULL,
  descripcion  VARCHAR(255) NULL,
  estado       TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '1 = activa, 0 = inactiva',
  PRIMARY KEY (id_categoria),
  UNIQUE KEY uq_categoria_nombre (nombre),
  CONSTRAINT ck_categoria_estado CHECK (estado IN (0, 1))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. PRODUCTO
-- ---------------------------------------------------------------------
CREATE TABLE producto (
  id_producto  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  codigo       VARCHAR(30)   NOT NULL,
  nombre       VARCHAR(120)  NOT NULL,
  descripcion  VARCHAR(255)  NULL,
  precio       DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock_actual INT UNSIGNED  NOT NULL DEFAULT 0,
  stock_minimo INT UNSIGNED  NOT NULL DEFAULT 0,
  estado       TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1 = activo, 0 = desactivado',
  id_categoria INT UNSIGNED  NOT NULL,
  PRIMARY KEY (id_producto),
  UNIQUE KEY uq_producto_codigo (codigo),
  KEY idx_producto_nombre (nombre),
  KEY idx_producto_categoria (id_categoria),
  CONSTRAINT fk_producto_categoria
    FOREIGN KEY (id_categoria) REFERENCES categoria (id_categoria)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_producto_precio       CHECK (precio >= 0),
  CONSTRAINT ck_producto_stock_actual CHECK (stock_actual >= 0),
  CONSTRAINT ck_producto_stock_minimo CHECK (stock_minimo >= 0),
  CONSTRAINT ck_producto_estado       CHECK (estado IN (0, 1))
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. MOVIMIENTO
-- ---------------------------------------------------------------------
CREATE TABLE movimiento (
  id_movimiento INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fecha_hora    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo          ENUM('ENTRADA','SALIDA') NOT NULL,
  cantidad      INT UNSIGNED NOT NULL,
  observacion   VARCHAR(255) NULL,
  id_producto   INT UNSIGNED NOT NULL,
  id_usuario    INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_movimiento),
  KEY idx_movimiento_producto_fecha (id_producto, fecha_hora),
  KEY idx_movimiento_usuario (id_usuario),
  CONSTRAINT fk_movimiento_producto
    FOREIGN KEY (id_producto) REFERENCES producto (id_producto)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_movimiento_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuario (id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_movimiento_cantidad CHECK (cantidad > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. VISTA: productos activos con stock bajo el mínimo (alerta)
-- ---------------------------------------------------------------------
CREATE VIEW vista_stock_bajo AS
SELECT p.id_producto,
       p.codigo,
       p.nombre,
       c.nombre AS categoria,
       p.stock_actual,
       p.stock_minimo
FROM producto p
JOIN categoria c ON c.id_categoria = p.id_categoria
WHERE p.estado = 1
  AND p.stock_actual < p.stock_minimo;

-- =====================================================================
-- DATOS DE PRUEBA
-- =====================================================================

-- Usuario de prueba -> correo: admin@tecnostock.cl | clave: Admin123
INSERT INTO usuario (nombre, correo, clave, estado) VALUES
('Encargado de inventario', 'admin@tecnostock.cl',
 '$2y$10$dDJi55k.wt9JDcc6pIwE8OP0fOyj9WcizRcbhWthm4a1IzY8L.iyG', 1);

INSERT INTO categoria (nombre, descripcion, estado) VALUES
('Audio',                'Audífonos, parlantes y micrófonos',     1),
('Cargadores y cables',  'Cargadores, cables y adaptadores',      1),
('Periféricos',          'Mouse, teclados y accesorios de PC',    1),
('Almacenamiento',       'Pendrives, discos y tarjetas de memoria', 1);

-- El stock_actual de cada producto coincide con sus movimientos de más abajo.
INSERT INTO producto
  (codigo, nombre, descripcion, precio, stock_actual, stock_minimo, estado, id_categoria) VALUES
('AUD-001', 'Audífonos Bluetooth Pro', 'Audífonos inalámbricos con cancelación de ruido', 24990, 15,  5, 1, 1),
('CAR-001', 'Cargador USB-C 20W',      'Cargador de pared de carga rápida',               12990,  3, 10, 1, 2),
('CAB-001', 'Cable HDMI 2 m',          'Cable HDMI 4K de 2 metros',                        5990, 40, 10, 1, 2),
('PER-001', 'Mouse inalámbrico',       'Mouse óptico 2.4 GHz',                             9990,  8,  8, 1, 3),
('ALM-001', 'Pendrive 64 GB',          'Memoria USB 3.0 de 64 GB',                         7990,  0,  5, 1, 4),
('PER-002', 'Teclado básico USB',      'Modelo descontinuado',                             8990,  2,  2, 0, 3);

INSERT INTO movimiento (fecha_hora, tipo, cantidad, observacion, id_producto, id_usuario) VALUES
('2026-10-01 09:00:00', 'ENTRADA', 20, 'Compra inicial a proveedor', 1, 1),
('2026-10-05 15:30:00', 'SALIDA',   5, 'Venta en tienda',            1, 1),
('2026-10-01 09:10:00', 'ENTRADA', 15, 'Compra inicial a proveedor', 2, 1),
('2026-10-06 12:00:00', 'SALIDA',  12, 'Venta por mayor',            2, 1),
('2026-10-01 09:20:00', 'ENTRADA', 50, 'Compra inicial a proveedor', 3, 1),
('2026-10-07 11:45:00', 'SALIDA',  10, 'Venta en tienda',            3, 1),
('2026-10-01 09:30:00', 'ENTRADA', 10, 'Compra inicial a proveedor', 4, 1),
('2026-10-08 16:10:00', 'SALIDA',   2, 'Venta en tienda',            4, 1),
('2026-10-01 09:40:00', 'ENTRADA', 10, 'Compra inicial a proveedor', 5, 1),
('2026-10-09 10:20:00', 'SALIDA',  10, 'Venta por mayor',            5, 1),
('2026-09-15 10:00:00', 'ENTRADA',  5, 'Compra inicial a proveedor', 6, 1),
('2026-09-20 14:00:00', 'SALIDA',   3, 'Venta en tienda',            6, 1);

-- =====================================================================
-- CONSULTAS ÚTILES (para probar en phpMyAdmin)
-- =====================================================================
-- SELECT * FROM vista_stock_bajo;
-- SELECT p.codigo, p.nombre, c.nombre AS categoria, p.stock_actual
--   FROM producto p JOIN categoria c USING (id_categoria)
--  WHERE p.nombre LIKE '%cable%' OR p.codigo LIKE '%cable%' OR c.nombre LIKE '%cable%';

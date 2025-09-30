-- Crear base de datos
CREATE DATABASE IF NOT EXISTS `delicias_centro` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `delicias_centro`;

-- =========================
-- Tabla usuarios
-- =========================
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `telefono` varchar(15) DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla categorias
-- =========================
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL UNIQUE,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla productos
-- =========================
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `id_admin` int(11) DEFAULT NULL, -- Admin que creó o modificó el producto
  PRIMARY KEY (`id_producto`),
  KEY `categoria_id` (`categoria_id`),
  KEY `id_admin` (`id_admin`),
  CONSTRAINT `productos_fk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL,
  CONSTRAINT `productos_fk_admin` FOREIGN KEY (`id_admin`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla imagenes_productos (para múltiples imágenes por producto)
-- =========================
DROP TABLE IF EXISTS `imagenes_productos`;
CREATE TABLE `imagenes_productos` (
  `id_imagen` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id_imagen`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `imagenes_productos_fk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla carrito
-- =========================
DROP TABLE IF EXISTS `carrito`;
CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id_carrito`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `carrito_fk_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `carrito_fk_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE pedidos (
  id_pedido INT(11) NOT NULL AUTO_INCREMENT,
  id_usuario INT(11) DEFAULT NULL,
  fecha_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  estado VARCHAR(50) DEFAULT 'Pendiente',
  total DECIMAL(10,2) NOT NULL,
  direccion_entrega VARCHAR(255) DEFAULT NULL,
  tipo_comprobante VARCHAR(20) NOT NULL DEFAULT 'boleta',
  correo VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (id_pedido),
  KEY id_usuario (id_usuario),
  CONSTRAINT pedidos_fk_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla entregas
-- =========================
DROP TABLE IF EXISTS `entregas`;
CREATE TABLE `entregas` (
  `id_entrega` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `fecha_entrega` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `direccion_entrega` varchar(255) DEFAULT NULL,
  `estado_entrega` varchar(50) DEFAULT 'Pendiente',
  PRIMARY KEY (`id_entrega`),
  KEY `id_pedido` (`id_pedido`),
  CONSTRAINT `entregas_fk_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla metodos_pago
-- =========================
CREATE TABLE metodos_pago (
  id_pago INT(11) NOT NULL AUTO_INCREMENT,
  id_pedido INT(11) NOT NULL,
  metodo_pago ENUM('Tarjeta de Crédito','Pago por Depósito') NOT NULL,
  estado_pago ENUM('Pendiente','Completado') DEFAULT 'Pendiente',
  detalles TEXT,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_pago),
  KEY id_pedido (id_pedido),
  CONSTRAINT metodos_pago_fk_pedido FOREIGN KEY (id_pedido) REFERENCES pedidos (id_pedido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla personalizacion
-- =========================
DROP TABLE IF EXISTS `personalizacion`;
CREATE TABLE `personalizacion` (
  `id_personalizacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) DEFAULT NULL,
  `tipo_personalizacion` varchar(100) NOT NULL,
  `costo_adicional` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id_personalizacion`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `personalizacion_fk_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Tabla detalles_pedido (FALTA)
-- =========================
DROP TABLE IF EXISTS `detalles_pedido`;
CREATE TABLE `detalles_pedido` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalles_pedido_fk_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `detalles_pedido_fk_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- =========================
-- Datos iniciales ejemplo
-- =========================

INSERT INTO `usuarios` (nombre, email, telefono, direccion, contrasena, rol) VALUES
('Admin', 'admin@delicias.com', '999999999', 'Local Principal', '$2y$10$HvKgnBc9hoDt4gcVqkA0vOdXaIrtyDpnSkWUv/60NI4IX35gAGVMi', 'admin'),
('Cliente1', 'cliente1@delicias.com', '987654321', 'Av. Siempre Viva 123', '$2y$10$examplehash', 'cliente');

INSERT INTO `categorias` (nombre) VALUES ('Pasteles'), ('Panes'), ('Galletas');

INSERT INTO `productos` (nombre, descripcion, precio, imagen, categoria_id, disponible, id_admin) VALUES
('KARAMANDUKA', 'Delicioso pastel de chocolate con cobertura cremosa y decorado con virutas de chocolate.', 15.50, 'KARAMANDUKA.jpg', 1, 1, 1),
('ALFAJORES', 'Pan baguette crujiente por fuera y suave por dentro, ideal para acompañar comidas.', 2.00, 'ALFAJORES.jpg', 2, 1, 1);

-- Puedes agregar más productos e imágenes según necesites
CREATE DATABASE IF NOT EXISTS tienda CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE tienda;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(100) UNIQUE,
  password VARCHAR(255)
);

-- categorias
CREATE TABLE IF NOT EXISTS categorias (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- productos
CREATE TABLE IF NOT EXISTS productos (
  id INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT NULL,
  precio DECIMAL(10,2) NOT NULL,
  categoria_id INT NULL,
  PRIMARY KEY (id),
  KEY categoria_id (categoria_id),
  CONSTRAINT productos_ibfk_1 FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- imagenes_productos
CREATE TABLE IF NOT EXISTS imagenes_productos (
  id INT NOT NULL AUTO_INCREMENT,
  url VARCHAR(255) NOT NULL,
  producto_id INT NULL,
  PRIMARY KEY (id),
  KEY producto_id (producto_id),
  CONSTRAINT imagenes_productos_ibfk_1 FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

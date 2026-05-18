

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `Id_cliente` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(20) NOT NULL,
  `Ap_paterno` varchar(20) NOT NULL,
  `Telefono` varchar(10) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `Direccion` varchar(100) NOT NULL,
  `CP` varchar(5) NOT NULL,
  PRIMARY KEY (`Id_cliente`),
  KEY `idx_nombre` (`Nombre`),
  KEY `idx_apellido` (`Ap_paterno`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `cliente` VALUES ('1', 'olvera', 'apestoso', '22222', '$2y$10$LmmUf.Vh6.gr47eLbIwKGu4YVInXd//Htk7swzldlA3BbV9R0Jyhu', 'san matias', '74126');
INSERT INTO `cliente` VALUES ('3', 'ss', 'dd', '111', '$2y$10$akgCPurbq/JYHYD5hQSRwOq49P3hsC6QWZdyZM40eNvsficwcRNdS', 'gg', '555');
INSERT INTO `cliente` VALUES ('4', 'Enrique', 'Bolaños Sánchez', '4', '$2y$10$UDmxVH48/amrS3HdTdkSZueS9yCZBx96RCXWte39cVWu6Y2By9Deu', 'si', '11234');

DROP TABLE IF EXISTS `detallepedidos`;
CREATE TABLE `detallepedidos` (
  `id_detallep` int NOT NULL AUTO_INCREMENT,
  `Cantidad` int NOT NULL,
  `Id_producto` int NOT NULL,
  `Id_pedido` int NOT NULL,
  PRIMARY KEY (`id_detallep`),
  KEY `Registroproductos_detallepedidos_fk` (`Id_producto`),
  KEY `Pedido_detallepedidos_fk` (`Id_pedido`),
  CONSTRAINT `fk_pedido_detallep` FOREIGN KEY (`Id_pedido`) REFERENCES `pedido` (`Id_pedido`),
  CONSTRAINT `fk_producto_detallep` FOREIGN KEY (`Id_producto`) REFERENCES `registroproductos` (`Id_producto`),
  CONSTRAINT `Pedido_detallepedidos_fk` FOREIGN KEY (`Id_pedido`) REFERENCES `pedido` (`Id_pedido`),
  CONSTRAINT `Registroproductos_detallepedidos_fk` FOREIGN KEY (`Id_producto`) REFERENCES `registroproductos` (`Id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `detalleventa`;
CREATE TABLE `detalleventa` (
  `Id_detallev` int NOT NULL AUTO_INCREMENT,
  `Cantidad` int NOT NULL,
  `Id_producto` int NOT NULL,
  PRIMARY KEY (`Id_detallev`),
  KEY `Registroproductos_detalleventa_fk` (`Id_producto`),
  CONSTRAINT `fk_producto_detalle` FOREIGN KEY (`Id_producto`) REFERENCES `registroproductos` (`Id_producto`),
  CONSTRAINT `Registroproductos_detalleventa_fk` FOREIGN KEY (`Id_producto`) REFERENCES `registroproductos` (`Id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `empleado`;
CREATE TABLE `empleado` (
  `Id_empleado` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(20) NOT NULL,
  `Ap_paterno` varchar(20) NOT NULL,
  `Telefono` varchar(10) NOT NULL,
  `Direccion` varchar(40) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL,
  PRIMARY KEY (`Id_empleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `inventario`;
CREATE TABLE `inventario` (
  `Stock` int NOT NULL,
  `Foto` blob NOT NULL,
  `Id_categoria` int NOT NULL,
  `Id_producto` int NOT NULL,
  `Precioneto` decimal(10,2) NOT NULL,
  KEY `Categoria_Inventario_fk` (`Id_categoria`),
  KEY `Registroproductos_Inventario_fk` (`Id_producto`),
  CONSTRAINT `Categoria_Inventario_fk` FOREIGN KEY (`Id_categoria`) REFERENCES `categoria` (`Id_categoria`),
  CONSTRAINT `fk_categoria_inv` FOREIGN KEY (`Id_categoria`) REFERENCES `categoria` (`Id_categoria`),
  CONSTRAINT `fk_producto_inv` FOREIGN KEY (`Id_producto`) REFERENCES `registroproductos` (`Id_producto`),
  CONSTRAINT `Registroproductos_Inventario_fk` FOREIGN KEY (`Id_producto`) REFERENCES `registroproductos` (`Id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas` (
  `Id_marca` int NOT NULL AUTO_INCREMENT,
  `Marca` varchar(100) NOT NULL,
  PRIMARY KEY (`Id_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `marcas` VALUES ('1', 'Nike');
INSERT INTO `marcas` VALUES ('2', 'Adidas');
INSERT INTO `marcas` VALUES ('3', 'Puma');
INSERT INTO `marcas` VALUES ('4', 'Reebok');
INSERT INTO `marcas` VALUES ('5', 'Under Armour');

DROP TABLE IF EXISTS `marcas_modelos`;
CREATE TABLE `marcas_modelos` (
  `Id_mm` int NOT NULL AUTO_INCREMENT,
  `Id_marca` int NOT NULL,
  `Id_mc` int NOT NULL,
  PRIMARY KEY (`Id_mm`),
  KEY `Modelo_cortes_Marcas_modelos_fk` (`Id_mc`),
  KEY `Marcas_Marcas_modelos_fk` (`Id_marca`),
  CONSTRAINT `fk_marca` FOREIGN KEY (`Id_marca`) REFERENCES `marcas` (`Id_marca`),
  CONSTRAINT `fk_mc` FOREIGN KEY (`Id_mc`) REFERENCES `modelo_cortes` (`Id_mc`),
  CONSTRAINT `Marcas_Marcas_modelos_fk` FOREIGN KEY (`Id_marca`) REFERENCES `marcas` (`Id_marca`),
  CONSTRAINT `Modelo_cortes_Marcas_modelos_fk` FOREIGN KEY (`Id_mc`) REFERENCES `modelo_cortes` (`Id_mc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `modelo_cortes`;
CREATE TABLE `modelo_cortes` (
  `Id_mc` int NOT NULL AUTO_INCREMENT,
  `Modelo` varchar(100) NOT NULL,
  PRIMARY KEY (`Id_mc`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `modelo_cortes` VALUES ('1', 'Slim Fit');
INSERT INTO `modelo_cortes` VALUES ('2', 'Regular Fit');
INSERT INTO `modelo_cortes` VALUES ('3', 'Relaxed Fit');
INSERT INTO `modelo_cortes` VALUES ('4', 'Skinny Fit');
INSERT INTO `modelo_cortes` VALUES ('5', 'Oversized');

DROP TABLE IF EXISTS `pedido`;
CREATE TABLE `pedido` (
  `Id_pedido` int NOT NULL AUTO_INCREMENT,
  `Id_cliente` int NOT NULL,
  `Fecha` date NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Totalpiezas` int NOT NULL,
  PRIMARY KEY (`Id_pedido`),
  KEY `Cliente_Pedido_fk` (`Id_cliente`),
  KEY `idx_fecha` (`Fecha`),
  KEY `idx_total` (`Total`),
  CONSTRAINT `Cliente_Pedido_fk` FOREIGN KEY (`Id_cliente`) REFERENCES `cliente` (`Id_cliente`),
  CONSTRAINT `fk_cliente_pedido` FOREIGN KEY (`Id_cliente`) REFERENCES `cliente` (`Id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `prendas`;
CREATE TABLE `prendas` (
  `Id_prendas` int NOT NULL AUTO_INCREMENT,
  `Prendas` varchar(10) NOT NULL,
  `Num_piezas` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id_prendas`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `prendas` VALUES ('3', 'jeans', '80');
INSERT INTO `prendas` VALUES ('4', 'camisa', '68');
INSERT INTO `prendas` VALUES ('5', 'blusas', '78');

DROP TABLE IF EXISTS `prendas_tallas`;
CREATE TABLE `prendas_tallas` (
  `Id_pt` int NOT NULL AUTO_INCREMENT,
  `id_genero` int NOT NULL,
  `Id_prendas` int NOT NULL,
  `Id_talla` int NOT NULL,
  PRIMARY KEY (`Id_pt`),
  KEY `Tallas_Prendas_Tallas_fk` (`Id_talla`),
  KEY `Prendas_Prendas_Tallas_fk` (`Id_prendas`),
  KEY `Genero_Prendas_Tallas_fk` (`id_genero`),
  CONSTRAINT `fk_genero` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`),
  CONSTRAINT `fk_prendas` FOREIGN KEY (`Id_prendas`) REFERENCES `prendas` (`Id_prendas`),
  CONSTRAINT `fk_talla` FOREIGN KEY (`Id_talla`) REFERENCES `tallas` (`Id_talla`),
  CONSTRAINT `Genero_Prendas_Tallas_fk` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`),
  CONSTRAINT `Prendas_Prendas_Tallas_fk` FOREIGN KEY (`Id_prendas`) REFERENCES `prendas` (`Id_prendas`),
  CONSTRAINT `Tallas_Prendas_Tallas_fk` FOREIGN KEY (`Id_talla`) REFERENCES `tallas` (`Id_talla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `proovedor`;
CREATE TABLE `proovedor` (
  `Id_proovedor` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(20) NOT NULL,
  `Ap_paterno` varchar(20) NOT NULL,
  `Telefono` varchar(10) NOT NULL,
  `Direccion` varchar(40) NOT NULL,
  PRIMARY KEY (`Id_proovedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `registroproductos`;
CREATE TABLE `registroproductos` (
  `Id_producto` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(20) NOT NULL,
  `Stock` int NOT NULL,
  `Id_categoria` int NOT NULL,
  `Preciopro` decimal(10,2) NOT NULL,
  `Precioneto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Id_producto`),
  KEY `Categoria_Registroproductos_fk` (`Id_categoria`),
  CONSTRAINT `Categoria_Registroproductos_fk` FOREIGN KEY (`Id_categoria`) REFERENCES `categoria` (`Id_categoria`),
  CONSTRAINT `fk_categoria` FOREIGN KEY (`Id_categoria`) REFERENCES `categoria` (`Id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `tallas`;
CREATE TABLE `tallas` (
  `Id_talla` int NOT NULL AUTO_INCREMENT,
  `Talla` int NOT NULL,
  PRIMARY KEY (`Id_talla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `venta`;
CREATE TABLE `venta` (
  `Id_venta` int NOT NULL AUTO_INCREMENT,
  `Fecha` date NOT NULL,
  `Realizacion` varchar(40) NOT NULL,
  `Total` decimal(10,2) NOT NULL,
  `Id_cliente` int NOT NULL,
  `Total_piezas` int NOT NULL,
  `Id_detallev` int DEFAULT NULL,
  PRIMARY KEY (`Id_venta`),
  KEY `detalleventa_Venta_fk` (`Id_detallev`),
  KEY `Cliente_Venta_fk` (`Id_cliente`),
  CONSTRAINT `Cliente_Venta_fk` FOREIGN KEY (`Id_cliente`) REFERENCES `cliente` (`Id_cliente`),
  CONSTRAINT `detalleventa_Venta_fk` FOREIGN KEY (`Id_detallev`) REFERENCES `detalleventa` (`Id_detallev`),
  CONSTRAINT `fk_cliente_venta` FOREIGN KEY (`Id_cliente`) REFERENCES `cliente` (`Id_cliente`),
  CONSTRAINT `fk_detallev_venta` FOREIGN KEY (`Id_detallev`) REFERENCES `detalleventa` (`Id_detallev`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `venta` VALUES ('8', '2026-05-08', 'yo', '100.00', '4', '2', NULL);

SET FOREIGN_KEY_CHECKS=1;

-- ── EMPLEADO CON CONTRASEÑA POR DEFECTO (admin123) ──
-- Contraseña hasheada: admin123
INSERT IGNORE INTO `empleado` VALUES (1,'Admin','Sistema','admin','Sistema DAV','$2y$10$8K1p/a0dR1xqM8K3UyL8YuKX8FeVGcYbH9dQqzNcVVMhqOpF/1R2K','admin');


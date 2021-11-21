DROP TABLE IF EXISTS `articulos`;

CREATE TABLE `articulos` (
  `id` int(11) UNSIGNED NOT NULL,
  `codigo` varchar(10) COLLATE utf8_spanish2_ci NOT NULL,
  `nombre` varchar(30) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8_spanish2_ci DEFAULT NULL,
  `precio` float NOT NULL,
  `tiempo` int(4) NULL,
  `coste` float DEFAULT NULL,
  `tipo` tinyint(1) NOT NULL,
  `id_familia` tinyint(2) NOT NULL DEFAULT '1',
  `estado` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 desabilitado, 1 activo'
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `empresa`;

CREATE TABLE `empresa` (
  `id` tinyint(1) UNSIGNED NOT NULL,
  `nombre` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `nif` char(9) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `id_gerente` int(11) UNSIGNED NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `telefono` varchar(12) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `calle` varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `numero` smallint(6) DEFAULT NULL,
  `piso` tinyint(4) DEFAULT NULL,
  `escalera` varchar(2) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `poblacion` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `CP` smallint(5) DEFAULT NULL,
  `provincia` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `pais` varchar(30) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `historial`;

CREATE TABLE `historial` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `id_registro` bigint(20) UNSIGNED NOT NULL,
  `tabla` varchar(10) COLLATE utf8_spanish2_ci NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `accion` float NOT NULL COMMENT '0 crear, 1 actualizar, 2 eliminar, 3 login, 4  logout '
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `lineas`;

CREATE TABLE `lineas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_ticket` bigint(20) UNSIGNED NOT NULL,
  `articulo` int(11) UNSIGNED NOT NULL,
  `precio` float NOT NULL,
  `cantidad` int(10) NOT NULL DEFAULT '1',
  `dto` float NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `lineas_regalo`;

CREATE TABLE `lineas_regalo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha` datetime DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `promos`;

CREATE TABLE `promos` (
  `id` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(30) COLLATE utf8_spanish2_ci NOT NULL,
  `valor` int(3) NOT NULL,
  `dto` int(3) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 eliminado, 1 activo'
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `tickets`;

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_cliente` int(11) UNSIGNED NOT NULL,
  `estado` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '1 activo, 0 inactivo',
  `iva` tinyint(3) DEFAULT '0',
  `total` FLOAT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `tickets_regalo`;

CREATE TABLE `tickets_regalo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

CREATE TABLE `impagos` (
  `id` BIGINT(20) UNSIGNED NOT NULL,
  `fecha` DATETIME NULL DEFAULT NULL,
  `id_ticket` BIGINT(20) UNSIGNED NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

DROP TABLE IF EXISTS `tokens`;

CREATE TABLE `tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) UNSIGNED NOT NULL,
  `token` varchar(255) COLLATE utf8_spanish2_ci NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

CREATE TABLE `usuarios` (
  `id` int(11) UNSIGNED NOT NULL,
  `codigo` varchar(10) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `dni` char(9) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `nombre` varchar(90) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `email` varchar(60) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `tel` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_alta` datetime DEFAULT NULL,
  `fecha_baja` datetime DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 desactivado, 1 activo, 2 bloqueado',
  `nivel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '2 administrador, 1 usuario, 0 cliente',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL,
  `obs` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish2_ci DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

CREATE TABLE `usuarios_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `promos` tinyint(1) NOT NULL DEFAULT '1',
  `enviar_emails` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `familias`;

CREATE TABLE `familias` (
  `id` tinyint(2) NOT NULL,
  `nombre` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NOT NULL,
  `mostrar` tinyint(1) NOT NULL DEFAULT '1',
  `estado` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE = InnoDB DEFAULT CHARSET = latin1;

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `id` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `iva` tinyint(2) NOT NULL DEFAULT '21',
  `dias` smallint(3) NOT NULL DEFAULT '365'
) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_spanish2_ci;

ALTER TABLE
  `empresa`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `id_user` (`id_gerente`);

ALTER TABLE
  `impagos`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `id_ticket` (`id_ticket`);

ALTER TABLE
  `articulos`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `codigo` (`codigo`),
ADD
  KEY `nombre` (`nombre`),
ADD
  KEY `id_familia` (`id_familia`);

ALTER TABLE
  `lineas_regalo`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `historial`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `id` (`id`),
ADD
  KEY `id_usuario` (`id_usuario`, `id_registro`);

ALTER TABLE
  `lineas`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `id` (`id`),
ADD
  KEY `id_ticket` (`id_ticket`),
ADD
  KEY `articulo` (`articulo`),
ADD
  KEY `articulo_2` (`articulo`);

ALTER TABLE
  `promos`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tickets`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `id` (`id`),
ADD
  KEY `id_usuario` (`id_usuario`),
ADD
  KEY `id_cliente` (`id_cliente`);

ALTER TABLE
  `tickets_regalo`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `tokens`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `token` (`token`),
ADD
  KEY `id_usuario` (`id_usuario`);

ALTER TABLE
  `usuarios`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `dni` (`dni`),
ADD
  UNIQUE KEY `email` (`email`);

ALTER TABLE
  `usuarios_config`
ADD
  KEY `idusuario` (`id`);

ALTER TABLE
  `familias`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `config`
ADD
  PRIMARY KEY (`id`);

ALTER TABLE
  `impagos`
MODIFY
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE
  `articulos`
MODIFY
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `empresa`
MODIFY
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `empresa`
MODIFY
  `id` tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `historial`
MODIFY
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `lineas`
MODIFY
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `promos`
MODIFY
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `tickets`
MODIFY
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `tokens`
MODIFY
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `usuarios`
MODIFY
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `familias`
MODIFY
  `id` tinyint(2) NOT NULL AUTO_INCREMENT;

ALTER TABLE
  `articulos`
ADD
  CONSTRAINT `art_ibfk_1` FOREIGN KEY (`id_familia`) REFERENCES `familias` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE
  `empresa`
ADD
  CONSTRAINT `id_user` FOREIGN KEY (`id_gerente`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE
  `lineas_regalo`
ADD
  CONSTRAINT `lreg_ibfk_1` FOREIGN KEY (`id`) REFERENCES `lineas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE
  `impagos`
ADD
  CONSTRAINT `fac_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE
  `historial`
ADD
  CONSTRAINT `historial_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE
  `lineas`
ADD
  CONSTRAINT `lineas_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD
  CONSTRAINT `lineas_ibfk_2` FOREIGN KEY (`articulo`) REFERENCES `articulos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE
  `tickets`
ADD
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE
  `tickets_regalo`
ADD
  CONSTRAINT `treg_ibfk_1` FOREIGN KEY (`id`) REFERENCES `tickets` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE
  `tokens`
ADD
  CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE
  `usuarios_config`
ADD
  CONSTRAINT `idusuario` FOREIGN KEY (`id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE VIEW vista_usuarios AS
SELECT
  u.*,
  uc.promos,
  uc.enviar_emails
FROM
  usuarios as u
  LEFT JOIN usuarios_config as uc ON u.id = uc.id
WHERE
  u.estado = 1;

CREATE VIEW vista_lineas_regalo AS
SELECT
  l.*,
  lr.fecha
FROM
  lineas as l
  INNER JOIN lineas_regalo as lr ON l.id = lr.id
  INNER JOIN tickets_regalo as tr ON l.id_ticket = tr.id
  INNER JOIN tickets as t ON l.id_ticket = t.id
WHERE
  t.estado = 1;

CREATE VIEW vista_tickets_regalo AS
SELECT
  t.*,
  tr.fecha_vencimiento
FROM
  tickets as t
  INNER JOIN tickets_regalo as tr ON t.id = tr.id
WHERE
  t.estado = 1;

CREATE VIEW vista_deudas AS
SELECT t.fecha, t.id, t.id_cliente, u.nombre, t.total  
  FROM tickets t
  INNER JOIN impagos f ON f.id_ticket = t.id
  INNER JOIN usuarios u ON t.id_cliente = u.id
  WHERE f.fecha IS NULL;

CREATE VIEW vista_tickets AS
SELECT
  t.*,
  i.id as debt
FROM
  tickets as t
  LEFT JOIN impagos as i ON t.id = i.id_ticket
WHERE
  t.estado = 1;


INSERT INTO
  `config`(`id`, `iva`, `dias`)
VALUES
  (1, 21, 365);
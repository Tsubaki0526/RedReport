-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-09-2025 a las 21:47:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `redreport`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_clientes`
--

CREATE TABLE `tb_clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `documento` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `fecha_instalacion` datetime DEFAULT NULL,
  `id_instalador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_ips`
--

CREATE TABLE `tb_ips` (
  `id_ip` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `ip_principal` varchar(50) NOT NULL,
  `megas_contratadas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_red`
--

CREATE TABLE `tb_red` (
  `id_red` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `switch` varchar(100) DEFAULT NULL,
  `ip` varchar(50) NOT NULL,
  `puerto` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_reportes_finalizados`
--

CREATE TABLE `tb_reportes_finalizados` (
  `id_finalizado` int(11) NOT NULL,
  `id_r_registrado` int(11) NOT NULL,
  `fecha_finalizado` date NOT NULL,
  `hora_finalizado` time NOT NULL,
  `personal_encargado` varchar(150) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_reportes_registrador`
--

CREATE TABLE `tb_reportes_registrador` (
  `id_r_registrado` int(11) NOT NULL,
  `empresa` varchar(150) NOT NULL,
  `radicado` varchar(20) NOT NULL,
  `operador` varchar(100) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `forma` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` enum('Pendiente','En Proceso','Finalizado') DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_rol`
--

CREATE TABLE `tb_rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `fh_creasion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_rol`
--

INSERT INTO `tb_rol` (`id_rol`, `nombre_rol`, `fh_creasion`) VALUES
(1, 'Administrador', '2025-08-22 17:48:34'),
(2, 'Gestion', '2025-09-09 12:33:46'),
(3, 'Instalador', '2025-09-09 12:35:00'),
(4, 'Ventas', '2026-07-04 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_usuarios`
--

CREATE TABLE `tb_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `documento` varchar(50) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `token_reset` varchar(255) NOT NULL,
  `token_expira` varchar(50) NOT NULL,
  `fh_creasion` datetime NOT NULL DEFAULT current_timestamp(),
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tb_usuarios`
--

INSERT INTO `tb_usuarios` (`id_usuario`, `nombre`, `documento`, `telefono`, `email`, `password`, `id_rol`, `token_reset`, `token_expira`, `fh_creasion`) VALUES
(1, 'administrador', '1082942195', '3006840430', 'valdeblanquez45@gmail.com', '$2y$10$D/XthZcXfyd0jPH1/uKi0exoJuE2Ru4drTSkHoh.FosA87eCtjqYq', 1, '', '', '2025-08-22 18:33:19'),
(3, 'jorge', '1082942195', '3006840430', 'akira05260512@gmail.com', '$2y$10$4yJAfYYkPxO97zsufcCDdu5//xtn8e.NCwKqkCY0aSlQtwHwokMN.', 2, '076f207bd470eff0f4d2fc17a4cb461f720d64bc0518d6200b96fa2d1da221990a8261779693eef41ad7b697255ae0d6b6e8', '2025-09-12 12:38:53', '2025-09-09 12:35:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `tb_ips`
--
ALTER TABLE `tb_ips`
  ADD PRIMARY KEY (`id_ip`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `tb_red`
--
ALTER TABLE `tb_red`
  ADD PRIMARY KEY (`id_red`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `tb_reportes_finalizados`
--
ALTER TABLE `tb_reportes_finalizados`
  ADD PRIMARY KEY (`id_finalizado`),
  ADD KEY `tb_reportes_finalizados_ibfk_1` (`id_r_registrado`);

--
-- Indices de la tabla `tb_reportes_registrador`
--
ALTER TABLE `tb_reportes_registrador`
  ADD PRIMARY KEY (`id_r_registrado`);

--
-- Indices de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_clientes`
--
ALTER TABLE `tb_clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_ips`
--
ALTER TABLE `tb_ips`
  MODIFY `id_ip` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tb_red`
--
ALTER TABLE `tb_red`
  MODIFY `id_red` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tb_reportes_finalizados`
--
ALTER TABLE `tb_reportes_finalizados`
  MODIFY `id_finalizado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_reportes_registrador`
--
ALTER TABLE `tb_reportes_registrador`
  MODIFY `id_r_registrado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tb_rol`
--
ALTER TABLE `tb_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tb_ips`
--
ALTER TABLE `tb_ips`
  ADD CONSTRAINT `tb_ips_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tb_red`
--
ALTER TABLE `tb_red`
  ADD CONSTRAINT `tb_red_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tb_reportes_finalizados`
--
ALTER TABLE `tb_reportes_finalizados`
  ADD CONSTRAINT `tb_reportes_finalizados_ibfk_1` FOREIGN KEY (`id_r_registrado`) REFERENCES `tb_reportes_registrador` (`id_r_registrado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tb_usuarios`
--
ALTER TABLE `tb_usuarios`
  ADD CONSTRAINT `tb_usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `tb_rol` (`id_rol`) ON DELETE NO ACTION ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_bitacora`
--

CREATE TABLE `tb_bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `tabla_afectada` varchar(50) DEFAULT NULL,
  `id_registro_afectado` int(11) DEFAULT NULL,
  `detalle` text DEFAULT NULL,
  `direccion_ip` varchar(45) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indices de la tabla `tb_bitacora`
--
ALTER TABLE `tb_bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de la tabla `tb_bitacora`
--
ALTER TABLE `tb_bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT;

--
-- Filtros para la tabla `tb_bitacora`
--
ALTER TABLE `tb_bitacora`
  ADD CONSTRAINT `tb_bitacora_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

CREATE TABLE `tb_tipos_equipo` (
  `id_tipo_equipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_tipos_equipo` (`id_tipo_equipo`, `nombre`, `descripcion`) VALUES
(1, 'ONT', 'Optical Network Terminal'),
(2, 'Router', 'Router WiFi'),
(3, 'Fuente de poder', 'Fuente de alimentacion'),
(4, 'Patch cord', 'Cable de fibra optica'),
(5, 'Convertidor', 'Convertidor de medios');

CREATE TABLE `tb_equipos` (
  `id_equipo` int(11) NOT NULL,
  `id_tipo_equipo` int(11) NOT NULL,
  `serial` varchar(100) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `estado` enum('Disponible','Asignado','Dañado','Garantia') NOT NULL DEFAULT 'Disponible',
  `id_cliente` int(11) DEFAULT NULL,
  `id_instalacion` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_asignado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tb_cobertura_zonas` (
  `id_zona` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#3388ff',
  `coordenadas` longtext NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `tb_tipos_equipo`
  ADD PRIMARY KEY (`id_tipo_equipo`);

ALTER TABLE `tb_tipos_equipo`
  MODIFY `id_tipo_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `tb_equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD KEY `id_tipo_equipo` (`id_tipo_equipo`),
  ADD KEY `id_cliente` (`id_cliente`);

ALTER TABLE `tb_equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tb_equipos`
  ADD CONSTRAINT `tb_equipos_ibfk_1` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tb_tipos_equipo` (`id_tipo_equipo`),
  ADD CONSTRAINT `tb_equipos_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `tb_cobertura_zonas`
  ADD PRIMARY KEY (`id_zona`);

ALTER TABLE `tb_cobertura_zonas`
  MODIFY `id_zona` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tb_clientes`
  ADD KEY `id_instalador` (`id_instalador`),
  ADD CONSTRAINT `tb_clientes_ibfk_instalador` FOREIGN KEY (`id_instalador`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_facturas`
--

CREATE TABLE `tb_facturas` (
  `id_factura` int(11) NOT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `iva` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','pagada','vencida','anulada') NOT NULL DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura de tabla para la tabla `tb_factura_items`
--

CREATE TABLE `tb_factura_items` (
  `id_item` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `tb_facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `id_cliente` (`id_cliente`);

ALTER TABLE `tb_factura_items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_factura` (`id_factura`);

ALTER TABLE `tb_facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tb_factura_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tb_facturas`
  ADD CONSTRAINT `tb_facturas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tb_factura_items`
  ADD CONSTRAINT `tb_factura_items_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `tb_facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_planes`
--

CREATE TABLE `tb_planes` (
  `id_plan` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `velocidad` varchar(50) DEFAULT NULL,
  `precio` decimal(12,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_planes` (`id_plan`, `nombre`, `velocidad`, `precio`, `descripcion`, `activo`) VALUES
(1, 'Plan Basico', '20MB', 35000.00, 'Internet 20MB residencial', 1),
(2, 'Plan Estandar', '50MB', 55000.00, 'Internet 50MB residencial', 1),
(3, 'Plan Premium', '100MB', 85000.00, 'Internet 100MB residencial', 1),
(4, 'Plan Empresarial', '200MB', 150000.00, 'Internet 200MB empresarial', 1);

ALTER TABLE `tb_planes`
  ADD PRIMARY KEY (`id_plan`);

ALTER TABLE `tb_planes`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_contratos`
--

CREATE TABLE `tb_contratos` (
  `id_contrato` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_plan` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('activo','cancelado','expirado') NOT NULL DEFAULT 'activo',
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `tb_contratos`
  ADD PRIMARY KEY (`id_contrato`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_plan` (`id_plan`),
  ADD KEY `id_vendedor` (`id_vendedor`);

ALTER TABLE `tb_contratos`
  MODIFY `id_contrato` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tb_contratos`
  ADD CONSTRAINT `tb_contratos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_contratos_ibfk_2` FOREIGN KEY (`id_plan`) REFERENCES `tb_planes` (`id_plan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_contratos_ibfk_3` FOREIGN KEY (`id_vendedor`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_ventas`
--

CREATE TABLE `tb_ventas` (
  `id_venta` int(11) NOT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `tipo` enum('nuevo','renovacion','upgrade') NOT NULL DEFAULT 'nuevo',
  `monto` decimal(12,2) NOT NULL,
  `comision` decimal(12,2) DEFAULT 0.00,
  `fecha` date NOT NULL,
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `tb_ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_contrato` (`id_contrato`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);

ALTER TABLE `tb_ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tb_ventas`
  ADD CONSTRAINT `tb_ventas_ibfk_1` FOREIGN KEY (`id_contrato`) REFERENCES `tb_contratos` (`id_contrato`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_ventas_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_ventas_ibfk_3` FOREIGN KEY (`id_vendedor`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

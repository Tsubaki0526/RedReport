-- ============================================================
-- RedReport - Sistema de Gestión para ISP
-- Database: `redreport`
-- Version: 1.0
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- Table: tb_rol
-- ============================================================
CREATE TABLE `tb_rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `fh_creasion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_rol` (`id_rol`, `nombre_rol`, `fh_creasion`) VALUES
(1, 'Administrador', '2025-01-01 00:00:00'),
(2, 'Gestion', '2025-01-01 00:00:00'),
(3, 'Instalador', '2025-01-01 00:00:00'),
(4, 'Ventas', '2025-01-01 00:00:00');

ALTER TABLE `tb_rol`
  ADD PRIMARY KEY (`id_rol`);
ALTER TABLE `tb_rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- ============================================================
-- Table: tb_usuarios
-- ============================================================
CREATE TABLE `tb_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `documento` varchar(50) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `token_reset` varchar(255) NOT NULL DEFAULT '',
  `token_expira` varchar(50) NOT NULL DEFAULT '',
  `google2fa_secret` varchar(255) DEFAULT NULL,
  `fh_creasion` datetime NOT NULL DEFAULT current_timestamp(),
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0,
  `bloqueado_hasta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Passwords: admin = "admin", 123456 = "123456"
INSERT INTO `tb_usuarios` (`id_usuario`, `nombre`, `documento`, `telefono`, `email`, `password`, `id_rol`, `token_reset`, `token_expira`, `fh_creasion`) VALUES
(1, 'administrador', '1000000001', '3000000001', 'admin@redreport.com', '$2y$10$l8GVLYITggdFsXvYXQ8EZOHPzOOGqbr1CO.9YTlZzFKUtnBas6gOC', 1, '', '', '2025-01-01 00:00:00'),
(2, 'gestor', '1000000002', '3000000002', 'gestor@redreport.com', '$2y$10$/jxDXVj/0UZYvZtBT5IVH.20oVP8huhWNefcVKKJhJGhLK5vFzHoK', 2, '', '', '2025-01-01 00:00:00'),
(3, 'instalador1', '1000000003', '3000000003', 'instalador@redreport.com', '$2y$10$/jxDXVj/0UZYvZtBT5IVH.20oVP8huhWNefcVKKJhJGhLK5vFzHoK', 3, '', '', '2025-01-01 00:00:00'),
(4, 'vendedor1', '1000000004', '3000000004', 'ventas@redreport.com', '$2y$10$/jxDXVj/0UZYvZtBT5IVH.20oVP8huhWNefcVKKJhJGhLK5vFzHoK', 4, '', '', '2025-01-01 00:00:00');

ALTER TABLE `tb_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `email` (`email`);
ALTER TABLE `tb_usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `tb_usuarios`
  ADD CONSTRAINT `tb_usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `tb_rol` (`id_rol`) ON DELETE NO ACTION ON UPDATE CASCADE;

-- ============================================================
-- Table: tb_clientes
-- ============================================================
CREATE TABLE `tb_clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `documento` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `estado_servicio` enum('Activo','Suspendido','Cortado') NOT NULL DEFAULT 'Activo',
  `password` varchar(255) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `fecha_instalacion` datetime DEFAULT NULL,
  `id_instalador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_clientes` (`id_cliente`, `nombre`, `documento`, `telefono`, `direccion`, `email`, `estado_servicio`, `password`, `lat`, `lng`, `fecha_instalacion`, `id_instalador`) VALUES
(1, 'Carlos Mendez', '1001000001', '3101000001', 'Calle 10 #20-30, Centro', 'carlos@example.com', 'Activo', '$2y$10$/jxDXVj/0UZYvZtBT5IVH.20oVP8huhWNefcVKKJhJGhLK5vFzHoK', 10.4805937, -66.9036063, '2025-06-15 10:30:00', 3),
(2, 'Maria Rodriguez', '1001000002', '3101000002', 'Cra 15 #50-60, Norte', 'maria@example.com', 'Activo', NULL, 10.4910000, -66.8900000, '2025-07-20 14:00:00', 3),
(3, 'Pedro Gomez', '1001000003', '3101000003', 'Av 5 #80-90, Sur', 'pedro@example.com', 'Suspendido', NULL, 10.4700000, -66.9200000, '2025-03-10 09:00:00', 3),
(4, 'Ana Jimenez', '1001000004', '3101000004', 'Calle 30 #10-20, Este', 'ana@example.com', 'Activo', NULL, 10.4850000, -66.8800000, '2025-08-05 11:00:00', 3),
(5, 'Luis Torres', '1001000005', '3101000005', 'Cra 25 #70-80, Oeste', 'luis@example.com', 'Cortado', NULL, 10.4750000, -66.9100000, '2024-11-01 08:00:00', 3),
(6, 'Sofia Ramirez', '1001000006', '3101000006', 'Calle 5 #15-25, Este', 'sofia@example.com', 'Activo', '$2y$10$/jxDXVj/0UZYvZtBT5IVH.20oVP8huhWNefcVKKJhJGhLK5vFzHoK', 10.4820000, -66.8950000, '2026-01-15 10:00:00', 3),
(7, 'Diego Hernandez', '1001000007', '3101000007', 'Cra 8 #22-33, Norte', 'diego@example.com', 'Activo', NULL, 10.4950000, -66.8850000, '2026-02-01 08:00:00', 3),
(8, 'Laura Castillo', '1001000008', '3101000008', 'Av 3 #45-67, Sur', 'laura@example.com', 'Suspendido', NULL, 10.4650000, -66.9150000, '2026-02-20 09:30:00', 3),
(9, 'Ricardo Paredes', '1001000009', '3101000009', 'Calle 12 #8-90, Centro', 'ricardo@example.com', 'Activo', NULL, 10.4800000, -66.9020000, '2026-03-05 09:00:00', 3),
(10, 'Elena Vargas', '1001000010', '3101000010', 'Cra 20 #60-40, Oeste', 'elena@example.com', 'Activo', NULL, 10.4780000, -66.9080000, '2026-03-10 11:00:00', 3),
(11, 'Javier Morales', '1001000011', '3101000011', 'Av 7 #30-50, Norte', 'javier@example.com', 'Cortado', NULL, 10.4920000, -66.8920000, '2026-03-15 08:30:00', 3),
(12, 'Valentina Ortiz', '1001000012', '3101000012', 'Calle 25 #12-34, Este', 'valentina@example.com', 'Activo', NULL, 10.4860000, -66.8780000, '2026-04-01 10:00:00', 3),
(13, 'Andres Navarro', '1001000013', '3101000013', 'Cra 30 #5-10, Sur', 'andres@example.com', 'Activo', NULL, 10.4680000, -66.9180000, '2026-04-15 14:00:00', 3),
(14, 'Camila Rojas', '1001000014', '3101000014', 'Calle 18 #9-70, Centro', 'camila@example.com', 'Activo', NULL, 10.4810000, -66.9050000, '2026-05-01 09:00:00', 3),
(15, 'Fernando Diaz', '1001000015', '3101000015', 'Av 10 #55-20, Oeste', 'fernando@example.com', 'Activo', NULL, 10.4760000, -66.9120000, '2026-05-20 10:30:00', 3);

ALTER TABLE `tb_clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `documento` (`documento`),
  ADD KEY `id_instalador` (`id_instalador`);
ALTER TABLE `tb_clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `tb_clientes`
  ADD CONSTRAINT `tb_clientes_ibfk_instalador` FOREIGN KEY (`id_instalador`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================
-- Table: tb_ips
-- ============================================================
CREATE TABLE `tb_ips` (
  `id_ip` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `ip_principal` varchar(50) NOT NULL,
  `megas_contratadas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_ips` (`id_ip`, `id_cliente`, `ip_principal`, `megas_contratadas`) VALUES
(1, 1, '192.168.100.10', 50),
(2, 2, '192.168.100.11', 100),
(3, 3, '192.168.100.12', 20),
(4, 4, '192.168.100.13', 50),
(5, 6, '192.168.100.14', 100),
(6, 7, '192.168.100.15', 200),
(7, 8, '192.168.100.16', 30),
(8, 9, '192.168.100.17', 50),
(9, 10, '192.168.100.18', 50),
(10, 11, '192.168.100.19', 100),
(11, 12, '192.168.100.20', 100),
(12, 13, '192.168.100.21', 20),
(13, 14, '192.168.100.22', 50),
(14, 15, '192.168.100.23', 200);

ALTER TABLE `tb_ips`
  ADD PRIMARY KEY (`id_ip`),
  ADD KEY `id_cliente` (`id_cliente`);
ALTER TABLE `tb_ips`
  MODIFY `id_ip` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `tb_ips`
  ADD CONSTRAINT `tb_ips_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE;

-- ============================================================
-- Table: tb_red
-- ============================================================
CREATE TABLE `tb_red` (
  `id_red` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `switch` varchar(100) DEFAULT NULL,
  `ip` varchar(50) NOT NULL,
  `puerto` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_red` (`id_red`, `id_cliente`, `switch`, `ip`, `puerto`) VALUES
(1, 1, 'SW-CENTRO-01', '10.0.0.1', 'Gig1/0/10'),
(2, 2, 'SW-NORTE-01', '10.0.0.2', 'Gig1/0/15'),
(3, 4, 'SW-ESTE-01', '10.0.0.3', 'Gig1/0/8'),
(4, 6, 'SW-ESTE-02', '10.0.0.4', 'Gig1/0/12'),
(5, 7, 'SW-NORTE-02', '10.0.0.5', 'Gig1/0/5'),
(6, 9, 'SW-CENTRO-02', '10.0.0.6', 'Gig1/0/20'),
(7, 12, 'SW-ESTE-03', '10.0.0.7', 'Gig1/0/14'),
(8, 15, 'SW-OESTE-01', '10.0.0.8', 'Gig1/0/9');

ALTER TABLE `tb_red`
  ADD PRIMARY KEY (`id_red`),
  ADD KEY `id_cliente` (`id_cliente`);
ALTER TABLE `tb_red`
  MODIFY `id_red` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `tb_red`
  ADD CONSTRAINT `tb_red_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE;

-- ============================================================
-- Table: tb_tipos_equipo
-- ============================================================
CREATE TABLE `tb_tipos_equipo` (
  `id_tipo_equipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_tipos_equipo` (`id_tipo_equipo`, `nombre`, `descripcion`) VALUES
(1, 'ONT', 'Optical Network Terminal'),
(2, 'Router', 'Router WiFi inalambrico'),
(3, 'Fuente de poder', 'Fuente de alimentacion 12V'),
(4, 'Patch cord', 'Cable de fibra optica SC/APC'),
(5, 'Convertidor', 'Convertidor de medios FO a RJ45'),
(6, 'Cable FO', 'Cable de fibra optica pigtail'),
(7, 'Splitter', 'Splitter optico 1x8');

ALTER TABLE `tb_tipos_equipo`
  ADD PRIMARY KEY (`id_tipo_equipo`);
ALTER TABLE `tb_tipos_equipo`
  MODIFY `id_tipo_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- ============================================================
-- Table: tb_equipos
-- ============================================================
CREATE TABLE `tb_equipos` (
  `id_equipo` int(11) NOT NULL,
  `id_tipo_equipo` int(11) NOT NULL,
  `serial` varchar(100) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `estado` enum('Disponible','Asignado','Dañado','Garantia') NOT NULL DEFAULT 'Disponible',
  `id_cliente` int(11) DEFAULT NULL,
  `id_instalacion` int(11) DEFAULT NULL,
  `stock_minimo` int(11) NOT NULL DEFAULT 0,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_asignado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_equipos` (`id_equipo`, `id_tipo_equipo`, `serial`, `marca`, `modelo`, `estado`, `id_cliente`, `stock_minimo`, `fecha_asignado`) VALUES
(1, 1, 'ONT-2025-001', 'Huawei', 'HG8010', 'Asignado', 1, 2, '2025-06-15 10:30:00'),
(2, 2, 'RTR-2025-001', 'MikroTik', 'hAP AC2', 'Asignado', 1, 1, '2025-06-15 10:30:00'),
(3, 1, 'ONT-2025-002', 'Huawei', 'HG8010', 'Asignado', 2, 2, '2025-07-20 14:00:00'),
(4, 2, 'RTR-2025-002', 'TP-Link', 'Archer C6', 'Asignado', 2, 1, '2025-07-20 14:00:00'),
(5, 1, 'ONT-2025-003', 'Huawei', 'HG8010', 'Disponible', NULL, 2, NULL),
(6, 2, 'RTR-2025-003', 'MikroTik', 'hAP AC2', 'Disponible', NULL, 1, NULL),
(7, 1, 'ONT-2025-004', 'Huawei', 'HG8245H', 'Disponible', NULL, 1, NULL),
(8, 2, 'RTR-2025-004', 'TP-Link', 'Archer AX10', 'Disponible', NULL, 1, NULL),
(9, 1, 'ONT-2025-005', 'Huawei', 'HG8010', 'Asignado', 6, 2, '2026-01-15 10:00:00'),
(10, 2, 'RTR-2025-005', 'TP-Link', 'Archer AX53', 'Asignado', 6, 1, '2026-01-15 10:00:00'),
(11, 1, 'ONT-2025-006', 'Huawei', 'HG8245H', 'Asignado', 7, 1, '2026-02-01 08:00:00'),
(12, 2, 'RTR-2025-006', 'MikroTik', 'hAP AC3', 'Asignado', 7, 1, '2026-02-01 08:00:00'),
(13, 1, 'ONT-2025-007', 'Huawei', 'HG8010', 'Asignado', 9, 2, '2026-03-05 09:00:00'),
(14, 6, 'CFO-2025-001', 'Commscope', 'LC-3m', 'Disponible', NULL, 0, NULL),
(15, 7, 'SPL-2025-001', 'FiberHome', '1x8 PLC', 'Disponible', NULL, 0, NULL),
(16, 7, 'SPL-2025-002', 'FiberHome', '1x8 PLC', 'Dañado', NULL, 0, NULL);

ALTER TABLE `tb_equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD KEY `id_tipo_equipo` (`id_tipo_equipo`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD UNIQUE KEY `serial` (`serial`);
ALTER TABLE `tb_equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
ALTER TABLE `tb_equipos`
  ADD CONSTRAINT `tb_equipos_ibfk_1` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tb_tipos_equipo` (`id_tipo_equipo`),
  ADD CONSTRAINT `tb_equipos_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================================
-- Table: tb_planes
-- ============================================================
CREATE TABLE `tb_planes` (
  `id_plan` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `velocidad` varchar(50) DEFAULT NULL,
  `precio` decimal(12,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_planes` (`id_plan`, `nombre`, `velocidad`, `precio`, `descripcion`, `activo`) VALUES
(1, 'Plan Basico', '20MB', 35000.00, 'Internet 20MB residencial - ideal para hogares pequenos', 1),
(2, 'Plan Estandar', '50MB', 55000.00, 'Internet 50MB residencial - streaming y video HD', 1),
(3, 'Plan Premium', '100MB', 85000.00, 'Internet 100MB - multiple dispositivos 4K', 1),
(4, 'Plan Empresarial', '200MB', 150000.00, 'Internet 200MB empresarial - prioridad y soporte 24/7', 1);

ALTER TABLE `tb_planes`
  ADD PRIMARY KEY (`id_plan`);
ALTER TABLE `tb_planes`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

-- ============================================================
-- Table: tb_contratos
-- ============================================================
CREATE TABLE `tb_contratos` (
  `id_contrato` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_plan` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('activo','cancelado','expirado') NOT NULL DEFAULT 'activo',
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `firma_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_contratos` (`id_contrato`, `id_cliente`, `id_plan`, `id_vendedor`, `fecha_inicio`, `fecha_fin`, `estado`, `notas`) VALUES
(1, 1, 2, 4, '2025-06-15', '2026-06-15', 'activo', 'Contrato residencial estandar'),
(2, 2, 3, 4, '2025-07-20', '2026-07-20', 'activo', 'Cliente premium - fibra optica'),
(3, 3, 1, 4, '2025-03-10', '2026-03-10', 'activo', 'Plan basico - suspendido por mora'),
(4, 4, 2, 4, '2025-08-05', '2026-08-05', 'activo', 'Instalacion nueva'),
(5, 6, 3, 4, '2026-01-15', '2027-01-15', 'activo', 'Cliente premium zona este'),
(6, 7, 4, 4, '2026-02-01', '2027-02-01', 'activo', 'Plan empresarial - fibra dedicada'),
(7, 8, 1, 4, '2026-02-20', '2027-02-20', 'activo', 'Plan basico residencial'),
(8, 9, 2, 4, '2026-03-05', '2027-03-05', 'activo', 'Cliente nuevo zona centro'),
(9, 10, 2, 4, '2026-03-10', '2027-03-10', 'activo', 'Referido de cliente existente'),
(10, 11, 3, 4, '2026-03-15', '2027-03-15', 'activo', 'Instalacion express - zona norte'),
(11, 12, 3, 4, '2026-04-01', '2027-04-01', 'activo', 'Plan premium residencial'),
(12, 13, 1, 4, '2026-04-15', '2027-04-15', 'activo', 'Plan basico economico'),
(13, 14, 2, 4, '2026-05-01', '2027-05-01', 'activo', 'Cliente zona centro'),
(14, 15, 4, 4, '2026-05-20', '2027-05-20', 'activo', 'Plan empresarial oficina');

ALTER TABLE `tb_contratos`
  ADD PRIMARY KEY (`id_contrato`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_plan` (`id_plan`),
  ADD KEY `id_vendedor` (`id_vendedor`);
ALTER TABLE `tb_contratos`
  MODIFY `id_contrato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `tb_contratos`
  ADD CONSTRAINT `tb_contratos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_contratos_ibfk_2` FOREIGN KEY (`id_plan`) REFERENCES `tb_planes` (`id_plan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_contratos_ibfk_3` FOREIGN KEY (`id_vendedor`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ============================================================
-- Table: tb_ventas
-- ============================================================
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

INSERT INTO `tb_ventas` (`id_venta`, `id_contrato`, `id_cliente`, `id_vendedor`, `tipo`, `monto`, `comision`, `fecha`) VALUES
(1, 1, 1, 4, 'nuevo', 55000.00, 5500.00, '2025-06-15'),
(2, 2, 2, 4, 'nuevo', 85000.00, 8500.00, '2025-07-20'),
(3, 3, 3, 4, 'nuevo', 35000.00, 3500.00, '2025-03-10'),
(4, 4, 4, 4, 'nuevo', 55000.00, 5500.00, '2025-08-05'),
(5, 5, 6, 4, 'nuevo', 85000.00, 8500.00, '2026-01-15'),
(6, 6, 7, 4, 'nuevo', 150000.00, 15000.00, '2026-02-01'),
(7, 7, 8, 4, 'nuevo', 35000.00, 3500.00, '2026-02-20'),
(8, 8, 9, 4, 'nuevo', 55000.00, 5500.00, '2026-03-05'),
(9, 9, 10, 4, 'nuevo', 55000.00, 5500.00, '2026-03-10'),
(10, 10, 11, 4, 'nuevo', 85000.00, 8500.00, '2026-03-15'),
(11, 11, 12, 4, 'nuevo', 85000.00, 8500.00, '2026-04-01'),
(12, 12, 13, 4, 'nuevo', 35000.00, 3500.00, '2026-04-15'),
(13, 13, 14, 4, 'nuevo', 55000.00, 5500.00, '2026-05-01'),
(14, 14, 15, 4, 'nuevo', 150000.00, 15000.00, '2026-05-20');

ALTER TABLE `tb_ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_contrato` (`id_contrato`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_vendedor` (`id_vendedor`);
ALTER TABLE `tb_ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `tb_ventas`
  ADD CONSTRAINT `tb_ventas_ibfk_1` FOREIGN KEY (`id_contrato`) REFERENCES `tb_contratos` (`id_contrato`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_ventas_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_ventas_ibfk_3` FOREIGN KEY (`id_vendedor`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ============================================================
-- Table: tb_facturas
-- ============================================================
CREATE TABLE `tb_facturas` (
  `id_factura` int(11) NOT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `id_contrato` int(11) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `iva` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','pagada','vencida','anulada') NOT NULL DEFAULT 'pendiente',
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_facturas` (`id_factura`, `numero_factura`, `id_cliente`, `fecha_emision`, `fecha_vencimiento`, `fecha_pago`, `id_contrato`, `subtotal`, `iva`, `total`, `estado`) VALUES
(1, 'FAC-00001', 1, '2025-06-15', '2025-07-15', '2025-07-10', 1, 46218.49, 8781.51, 55000.00, 'pagada'),
(2, 'FAC-00002', 1, '2025-07-15', '2025-08-15', '2025-08-12', 1, 46218.49, 8781.51, 55000.00, 'pagada'),
(3, 'FAC-00003', 1, '2025-08-15', '2025-09-15', NULL, 1, 46218.49, 8781.51, 55000.00, 'pendiente'),
(4, 'FAC-00004', 2, '2025-07-20', '2025-08-20', '2025-08-18', 2, 71428.57, 13571.43, 85000.00, 'pagada'),
(5, 'FAC-00005', 2, '2025-08-20', '2025-09-20', NULL, 2, 71428.57, 13571.43, 85000.00, 'pendiente'),
(6, 'FAC-00006', 3, '2025-03-10', '2025-04-10', NULL, 3, 29411.76, 5588.24, 35000.00, 'vencida'),
(7, 'FAC-00007', 4, '2025-08-05', '2025-09-05', '2025-09-01', 4, 46218.49, 8781.51, 55000.00, 'pagada'),
(8, 'FAC-00008', 1, '2026-01-15', '2026-02-15', NULL, 1, 46218.49, 8781.51, 55000.00, 'pendiente'),
(9, 'FAC-00009', 2, '2026-01-20', '2026-02-20', '2026-02-15', 2, 71428.57, 13571.43, 85000.00, 'pagada'),
(10, 'FAC-00010', 4, '2026-01-05', '2026-02-05', '2026-02-01', 4, 46218.49, 8781.51, 55000.00, 'pagada'),
(11, 'FAC-00011', 6, '2026-01-15', '2026-02-15', NULL, 5, 71428.57, 13571.43, 85000.00, 'vencida'),
(12, 'FAC-00012', 7, '2026-02-01', '2026-03-01', '2026-02-25', 6, 126050.42, 23949.58, 150000.00, 'pagada'),
(13, 'FAC-00013', 6, '2026-02-15', '2026-03-15', '2026-03-10', 5, 71428.57, 13571.43, 85000.00, 'pagada'),
(14, 'FAC-00014', 1, '2026-02-15', '2026-03-15', NULL, 1, 46218.49, 8781.51, 55000.00, 'vencida'),
(15, 'FAC-00015', 9, '2026-03-05', '2026-04-05', '2026-03-30', 8, 46218.49, 8781.51, 55000.00, 'pagada'),
(16, 'FAC-00016', 10, '2026-03-10', '2026-04-10', NULL, 9, 46218.49, 8781.51, 55000.00, 'pendiente'),
(17, 'FAC-00017', 11, '2026-03-15', '2026-04-15', NULL, 10, 71428.57, 13571.43, 85000.00, 'vencida'),
(18, 'FAC-00018', 12, '2026-04-01', '2026-05-01', '2026-04-28', 11, 71428.57, 13571.43, 85000.00, 'pagada'),
(19, 'FAC-00019', 14, '2026-05-01', '2026-06-01', NULL, 13, 46218.49, 8781.51, 55000.00, 'pendiente'),
(20, 'FAC-00020', 1, '2026-03-15', '2026-04-15', '2026-04-10', 1, 46218.49, 8781.51, 55000.00, 'pagada');

-- ============================================================
-- Table: tb_factura_items
-- ============================================================
CREATE TABLE `tb_factura_items` (
  `id_item` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_factura_items` (`id_item`, `id_factura`, `descripcion`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 'Plan Estandar 50MB - Junio', 1, 55000.00, 46218.49),
(2, 2, 'Plan Estandar 50MB - Julio', 1, 55000.00, 46218.49),
(3, 3, 'Plan Estandar 50MB - Agosto', 1, 55000.00, 46218.49),
(4, 4, 'Plan Premium 100MB - Julio', 1, 85000.00, 71428.57),
(5, 5, 'Plan Premium 100MB - Agosto', 1, 85000.00, 71428.57),
(6, 6, 'Plan Basico 20MB - Marzo', 1, 35000.00, 29411.76),
(7, 7, 'Plan Estandar 50MB - Agosto', 1, 55000.00, 46218.49),
(8, 8, 'Plan Estandar 50MB - Enero 2026', 1, 55000.00, 46218.49),
(9, 9, 'Plan Premium 100MB - Enero 2026', 1, 85000.00, 71428.57),
(10, 10, 'Plan Estandar 50MB - Enero 2026', 1, 55000.00, 46218.49),
(11, 11, 'Plan Premium 100MB - Enero 2026', 1, 85000.00, 71428.57),
(12, 12, 'Plan Empresarial 200MB - Febrero 2026', 1, 150000.00, 126050.42),
(13, 13, 'Plan Premium 100MB - Febrero 2026', 1, 85000.00, 71428.57),
(14, 14, 'Plan Estandar 50MB - Febrero 2026', 1, 55000.00, 46218.49),
(15, 15, 'Plan Estandar 50MB - Marzo 2026', 1, 55000.00, 46218.49),
(16, 16, 'Plan Estandar 50MB - Marzo 2026', 1, 55000.00, 46218.49),
(17, 17, 'Plan Premium 100MB - Marzo 2026', 1, 85000.00, 71428.57),
(18, 18, 'Plan Premium 100MB - Abril 2026', 1, 85000.00, 71428.57),
(19, 19, 'Plan Estandar 50MB - Mayo 2026', 1, 55000.00, 46218.49),
(20, 20, 'Plan Estandar 50MB - Marzo 2026', 1, 55000.00, 46218.49);

ALTER TABLE `tb_facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD UNIQUE KEY `numero_factura` (`numero_factura`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_contrato` (`id_contrato`);
ALTER TABLE `tb_factura_items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_factura` (`id_factura`);
ALTER TABLE `tb_facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
ALTER TABLE `tb_factura_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
ALTER TABLE `tb_facturas`
  ADD CONSTRAINT `tb_facturas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_facturas_ibfk_2` FOREIGN KEY (`id_contrato`) REFERENCES `tb_contratos` (`id_contrato`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `tb_factura_items`
  ADD CONSTRAINT `tb_factura_items_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `tb_facturas` (`id_factura`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ============================================================
-- Table: tb_pagos
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_pagos` (
  `id_pago` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` enum('Efectivo','Transferencia','Tarjeta','Cheque','Otro') NOT NULL DEFAULT 'Efectivo',
  `referencia` varchar(100) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_pagos` (`id_pago`, `id_factura`, `monto`, `metodo_pago`, `referencia`, `fecha_pago`, `id_usuario`) VALUES
(1, 1, 55000.00, 'Transferencia', 'TRF-001', '2025-07-10 14:30:00', 1),
(2, 2, 55000.00, 'Efectivo', 'REC-001', '2025-08-12 10:00:00', 1),
(3, 4, 85000.00, 'Transferencia', 'TRF-002', '2025-08-18 09:15:00', 1),
(4, 7, 55000.00, 'Tarjeta', 'TAR-001', '2025-09-01 16:45:00', 1),
(5, 9, 85000.00, 'Transferencia', 'TRF-003', '2026-02-15 11:00:00', 1),
(6, 10, 55000.00, 'Efectivo', 'REC-002', '2026-02-01 10:30:00', 1),
(7, 12, 150000.00, 'Transferencia', 'TRF-004', '2026-02-25 14:00:00', 1),
(8, 13, 85000.00, 'Tarjeta', 'TAR-002', '2026-03-10 09:00:00', 1),
(9, 15, 55000.00, 'Transferencia', 'TRF-005', '2026-03-30 16:00:00', 1),
(10, 18, 85000.00, 'Efectivo', 'REC-003', '2026-04-28 11:15:00', 1),
(11, 20, 55000.00, 'Cheque', 'CHQ-001', '2026-04-10 08:45:00', 1);

ALTER TABLE `tb_pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_factura` (`id_factura`),
  ADD KEY `id_usuario` (`id_usuario`);
ALTER TABLE `tb_pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `tb_pagos`
  ADD CONSTRAINT `tb_pagos_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `tb_facturas` (`id_factura`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_pagos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE SET NULL;

-- ============================================================
-- Table: tb_ordenes
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_ordenes` (
  `id_orden` int(11) NOT NULL,
  `numero_orden` varchar(20) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_tecnico` int(11) DEFAULT NULL,
  `tipo` enum('Instalacion','Soporte','Mantenimiento','Retiro','Otro') NOT NULL DEFAULT 'Soporte',
  `descripcion` text DEFAULT NULL,
  `prioridad` enum('Baja','Media','Alta','Urgente') DEFAULT 'Media',
  `estado` enum('Abierta','En Proceso','Completada','Cancelada') DEFAULT 'Abierta',
  `fecha_asignacion` datetime DEFAULT NULL,
  `fecha_completada` datetime DEFAULT NULL,
  `solucion` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_ordenes` (`id_orden`, `numero_orden`, `id_cliente`, `id_tecnico`, `tipo`, `descripcion`, `prioridad`, `estado`, `fecha_asignacion`, `fecha_completada`, `solucion`) VALUES
(1, 'ORD-000001', 1, 3, 'Instalacion', 'Instalacion fibra optica nueva', 'Media', 'Completada', '2025-06-15 08:00:00', '2025-06-15 10:30:00', 'Instalacion completada. ONT y router configurados.'),
(2, 'ORD-000002', 2, 3, 'Instalacion', 'Instalacion fibra optica nueva', 'Media', 'Completada', '2025-07-20 08:00:00', '2025-07-20 14:00:00', 'Instalacion OK'),
(3, 'ORD-000003', 3, 3, 'Soporte', 'Cliente reporta perdida de conexion', 'Alta', 'Abierta', '2025-09-01 09:00:00', NULL, NULL),
(4, 'ORD-000004', 4, NULL, 'Instalacion', 'Instalacion nueva', 'Media', 'Abierta', NULL, NULL, NULL),
(5, 'ORD-000005', 1, 3, 'Mantenimiento', 'Revisar nivel de señal optica', 'Baja', 'En Proceso', '2025-09-10 10:00:00', NULL, NULL),
(6, 'ORD-000006', 5, NULL, 'Soporte', 'Reconexion por corte de servicio - cliente desea reincorporarse', 'Urgente', 'Abierta', NULL, NULL, NULL),
(7, 'ORD-000007', 7, 3, 'Instalacion', 'Instalacion fibra optica 200MB empresarial', 'Alta', 'Completada', '2026-02-01 07:00:00', '2026-02-01 10:00:00', 'Instalacion completada. ONT y router configurados. Señal optima.'),
(8, 'ORD-000008', 9, 3, 'Instalacion', 'Instalacion nueva zona centro', 'Media', 'Completada', '2026-03-05 08:00:00', '2026-03-05 11:30:00', 'Instalacion OK. Cliente satisfecho.'),
(9, 'ORD-000009', 10, NULL, 'Soporte', 'Cliente reporta intermitencia en conexion nocturna', 'Alta', 'En Proceso', '2026-06-15 14:00:00', NULL, NULL),
(10, 'ORD-000010', 6, 3, 'Mantenimiento', 'Revision preventiva anual de equipos', 'Baja', 'Cancelada', NULL, NULL, NULL),
(11, 'ORD-000011', 11, NULL, 'Retiro', 'Retiro de equipos por corte de servicio', 'Media', 'Abierta', NULL, NULL, NULL);

ALTER TABLE `tb_ordenes`
  ADD PRIMARY KEY (`id_orden`),
  ADD UNIQUE KEY `numero_orden` (`numero_orden`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_tecnico` (`id_tecnico`);
ALTER TABLE `tb_ordenes`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `tb_ordenes`
  ADD CONSTRAINT `tb_ordenes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_ordenes_ibfk_2` FOREIGN KEY (`id_tecnico`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE SET NULL;

-- ============================================================
-- Table: tb_tickets
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_tickets` (
  `id_ticket` int(11) NOT NULL,
  `numero_ticket` varchar(20) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `asunto` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` enum('Fallo de conexion','Equipo','Facturacion','Otro') DEFAULT 'Otro',
  `prioridad` enum('Baja','Media','Alta','Urgente') DEFAULT 'Media',
  `estado` enum('Abierto','En Proceso','Resuelto','Cerrado') DEFAULT 'Abierto',
  `solucion` text DEFAULT NULL,
  `fecha_asignacion` datetime DEFAULT NULL,
  `fecha_resolucion` datetime DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_tickets` (`id_ticket`, `numero_ticket`, `id_cliente`, `id_usuario`, `asunto`, `descripcion`, `categoria`, `prioridad`, `estado`, `solucion`, `fecha_resolucion`) VALUES
(1, 'TK-000001', 1, 1, 'Velocidad lenta', 'Desde ayer el internet va muy lento en horas de la tarde', 'Fallo de conexion', 'Media', 'Resuelto', 'Se ajusto la potencia optica. Problema resuelto.', '2025-08-20 15:00:00'),
(2, 'TK-000002', 3, NULL, 'Sin servicio', 'No tengo internet desde el corte del recibo', 'Fallo de conexion', 'Alta', 'Abierto', NULL, NULL),
(3, 'TK-000003', 2, NULL, 'Consulta de factura', 'Quiero saber mi saldo pendiente', 'Facturacion', 'Baja', 'Abierto', NULL, NULL),
(4, 'TK-000004', 4, 1, 'Cobro duplicado', 'Me estan cobrando dos veces el mismo mes en la factura', 'Facturacion', 'Alta', 'En Proceso', NULL, NULL),
(5, 'TK-000005', 6, NULL, 'Sin servicio desde instalacion', 'Desde que instalaron el servicio no tengo internet', 'Fallo de conexion', 'Urgente', 'Abierto', NULL, NULL),
(6, 'TK-000006', 8, 3, 'Cambio de direccion', 'Necesito actualizar mi direccion de servicio por mudanza', 'Otro', 'Baja', 'Cerrado', 'Se actualizo la direccion en el sistema.', '2026-03-01 11:00:00'),
(7, 'TK-000007', 10, 1, 'Velocidad inferior a la contratada', 'Contrate 50MB pero solo llegan 20MB en horas pico', 'Fallo de conexion', 'Alta', 'Resuelto', 'Se reemplazo el ONT por falla en el puerto optico.', '2026-04-01 16:30:00');

ALTER TABLE `tb_tickets`
  ADD PRIMARY KEY (`id_ticket`),
  ADD UNIQUE KEY `numero_ticket` (`numero_ticket`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`);
ALTER TABLE `tb_tickets`
  MODIFY `id_ticket` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `tb_tickets`
  ADD CONSTRAINT `tb_tickets_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_tickets_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE SET NULL;

-- ============================================================
-- Table: tb_bitacora
-- ============================================================
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

INSERT INTO `tb_bitacora` (`id_bitacora`, `id_usuario`, `accion`, `tabla_afectada`, `id_registro_afectado`, `detalle`, `direccion_ip`, `fecha_hora`) VALUES
(1, 1, 'Inicio de sesion', 'tb_usuarios', 1, 'Inicio de sesion exitoso desde IP 192.168.1.100', '192.168.1.100', '2025-01-01 08:00:00'),
(2, 2, 'Inicio de sesion', 'tb_usuarios', 2, 'Inicio de sesion exitoso desde IP 192.168.1.101', '192.168.1.101', '2025-01-01 08:30:00'),
(3, 3, 'Inicio de sesion', 'tb_usuarios', 3, 'Inicio de sesion exitoso desde IP 192.168.1.102', '192.168.1.102', '2025-01-02 07:00:00'),
(4, 4, 'Inicio de sesion', 'tb_usuarios', 4, 'Inicio de sesion exitoso desde IP 192.168.1.103', '192.168.1.103', '2025-01-02 09:00:00'),
(5, 1, 'Creacion', 'tb_clientes', 1, 'Creacion del cliente Carlos Mendez', '192.168.1.100', '2025-01-15 10:00:00'),
(6, 1, 'Creacion', 'tb_clientes', 2, 'Creacion del cliente Maria Rodriguez', '192.168.1.100', '2025-02-01 09:00:00'),
(7, 1, 'Creacion', 'tb_clientes', 3, 'Creacion del cliente Pedro Gomez', '192.168.1.100', '2025-02-20 11:30:00'),
(8, 1, 'Creacion', 'tb_clientes', 4, 'Creacion del cliente Ana Jimenez', '192.168.1.100', '2025-03-05 08:45:00'),
(9, 4, 'Creacion', 'tb_contratos', 1, 'Contrato #1 creado para Carlos Mendez - Plan Estandar', '192.168.1.100', '2025-06-15 10:30:00'),
(10, 4, 'Creacion', 'tb_ventas', 1, 'Venta #1 registrada por contrato #1 - $55,000', '192.168.1.100', '2025-06-15 10:31:00'),
(11, 1, 'Creacion', 'tb_facturas', 1, 'Factura FAC-00001 generada para Carlos Mendez', '192.168.1.100', '2025-06-15 10:32:00'),
(12, 1, 'Registro de pago', 'tb_pagos', 1, 'Pago registrado FAC-00001 - Transferencia $55,000', '192.168.1.100', '2025-07-10 14:30:00'),
(13, 3, 'Creacion', 'tb_ordenes', 1, 'Orden ORD-000001 creada - Instalacion Carlos Mendez', '192.168.1.102', '2025-06-15 08:00:00'),
(14, 3, 'Actualizacion', 'tb_ordenes', 1, 'Orden ORD-000001 completada exitosamente', '192.168.1.102', '2025-06-15 10:30:00'),
(15, 1, 'Inicio de sesion', 'tb_usuarios', 1, 'Inicio de sesion exitoso desde IP 10.0.0.50', '10.0.0.50', '2026-01-01 08:00:00'),
(16, 4, 'Creacion', 'tb_clientes', 6, 'Creacion del cliente Sofia Ramirez', '10.0.0.50', '2026-01-15 10:00:00'),
(17, 4, 'Creacion', 'tb_contratos', 5, 'Contrato #5 creado para Sofia Ramirez - Plan Premium', '10.0.0.50', '2026-01-15 10:01:00'),
(18, 1, 'Registro de pago', 'tb_pagos', 7, 'Pago registrado FAC-00012 - Transferencia $150,000', '10.0.0.50', '2026-02-25 14:00:00'),
(19, 2, 'Inicio de sesion', 'tb_usuarios', 2, 'Inicio de sesion exitoso desde IP 192.168.1.101', '192.168.1.101', '2026-03-05 08:15:00'),
(20, 3, 'Creacion', 'tb_ordenes', 8, 'Orden ORD-000008 creada - Instalacion Ricardo Paredes', '192.168.1.102', '2026-03-05 08:00:00');

ALTER TABLE `tb_bitacora`
  ADD PRIMARY KEY (`id_bitacora`),
  ADD KEY `id_usuario` (`id_usuario`);
ALTER TABLE `tb_bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
ALTER TABLE `tb_bitacora`
  ADD CONSTRAINT `tb_bitacora_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ============================================================
-- Table: tb_dispositivos (Monitoreo SNMP)
-- ============================================================
CREATE TABLE `tb_dispositivos` (
  `id_dispositivo` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT 'Router',
  `id_cliente` int(11) DEFAULT NULL,
  `ultimo_estado` varchar(20) DEFAULT 'Sin dato',
  `ultimo_check_signal` int(11) DEFAULT 0,
  `ultimo_check` datetime DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_dispositivos` (`id_dispositivo`, `ip`, `nombre`, `tipo`, `ultimo_estado`) VALUES
(1, '192.168.100.1', 'Router Principal', 'Router', 'Activo'),
(2, '10.0.0.1', 'Switch Centro', 'Switch', 'Activo'),
(3, '10.0.0.2', 'Switch Norte', 'Switch', 'Inactivo');

ALTER TABLE `tb_dispositivos`
  ADD PRIMARY KEY (`id_dispositivo`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `ip` (`ip`);
ALTER TABLE `tb_dispositivos`
  MODIFY `id_dispositivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- ============================================================
-- Table: tb_cobertura_zonas
-- ============================================================
CREATE TABLE `tb_cobertura_zonas` (
  `id_zona` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#3388ff',
  `coordenadas` longtext NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_cobertura_zonas` (`id_zona`, `nombre`, `color`, `coordenadas`) VALUES
(1, 'Zona Centro', '#3388ff', '[[10.483,-66.91],[10.485,-66.90],[10.478,-66.89],[10.475,-66.90],[10.483,-66.91]]'),
(2, 'Zona Norte', '#ff6633', '[[10.495,-66.90],[10.500,-66.88],[10.490,-66.87],[10.485,-66.89],[10.495,-66.90]]');

ALTER TABLE `tb_cobertura_zonas`
  ADD PRIMARY KEY (`id_zona`);
ALTER TABLE `tb_cobertura_zonas`
  MODIFY `id_zona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- ============================================================
-- Table: tb_empresa
-- ============================================================
CREATE TABLE `tb_empresa` (
  `id_empresa` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL DEFAULT '',
  `documento` varchar(50) DEFAULT '',
  `direccion` varchar(200) DEFAULT '',
  `telefono` varchar(50) DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `logo` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tb_empresa` (`id_empresa`, `nombre`, `documento`, `direccion`, `telefono`, `email`) VALUES
(1, 'RedReport ISP', '900000001-1', 'Av Principal #1-00', '3000000000', 'info@redreport.com');

ALTER TABLE `tb_empresa`
  ADD PRIMARY KEY (`id_empresa`);
ALTER TABLE `tb_empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- ============================================================
-- Table: tb_instalacion_fotos
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_instalacion_fotos` (
  `id_foto` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_instalacion` int(11) DEFAULT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `fecha_subida` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `tb_instalacion_fotos`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `id_cliente` (`id_cliente`);
ALTER TABLE `tb_instalacion_fotos`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_instalacion_fotos`
  ADD CONSTRAINT `tb_instalacion_fotos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `tb_clientes` (`id_cliente`) ON DELETE CASCADE;

-- ============================================================
-- Table: tb_notificaciones
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `tipo` enum('info','warning','success','danger') DEFAULT 'info',
  `titulo` varchar(200) NOT NULL,
  `mensaje` text DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `tb_notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_usuario` (`id_usuario`);
ALTER TABLE `tb_notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_notificaciones`
  ADD CONSTRAINT `tb_notificaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tb_usuarios` (`id_usuario`) ON DELETE CASCADE;

-- ============================================================
-- Table: tb_modulos
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_modulos` (
  `id_modulo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `icono` varchar(50) DEFAULT 'fas fa-circle',
  `ruta` varchar(200) DEFAULT NULL,
  `orden` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_modulos` (`id_modulo`, `nombre`, `icono`, `ruta`, `orden`) VALUES
(1, 'Dashboard', 'fas fa-tachometer-alt', 'index.php', 1),
(2, 'Usuarios', 'fas fa-users-cog', 'usuarios/lista.php', 2),
(3, 'Clientes', 'fas fa-user-friends', 'clientes/vistas/lista.php', 3),
(4, 'Ventas', 'fas fa-chart-line', 'ventas/index.php', 4),
(5, 'Instalaciones', 'fas fa-tools', 'instalaciones/index.php', 5),
(6, 'Inventario', 'fas fa-boxes', 'inventario/index.php', 6),
(7, 'Facturacion', 'fas fa-file-invoice-dollar', 'facturacion/index.php', 7),
(8, 'Mapa', 'fas fa-map-marked-alt', 'mapa/index.php', 8),
(9, 'Configuracion', 'fas fa-cog', 'configuracion/index.php', 9),
(10, 'Ordenes', 'fas fa-clipboard', 'ordenes/index.php', 10),
(11, 'Tickets', 'fas fa-headset', 'tickets/index.php', 11),
(12, 'Informes', 'fas fa-chart-bar', 'informes/index.php', 12),
(13, 'Monitoreo', 'fas fa-network-wired', 'monitoreo/index.php', 13),
(14, 'Backup', 'fas fa-database', 'backup/index.php', 14);

ALTER TABLE `tb_modulos`
  ADD PRIMARY KEY (`id_modulo`);
ALTER TABLE `tb_modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

-- ============================================================
-- Table: tb_permisos
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_permisos` (
  `id_permiso` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `leer` tinyint(1) DEFAULT 1,
  `escribir` tinyint(1) DEFAULT 0,
  `editar` tinyint(1) DEFAULT 0,
  `eliminar` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_permisos` (`id_modulo`, `id_rol`, `leer`, `escribir`, `editar`, `eliminar`)
SELECT id_modulo, 1, 1, 1, 1, 1 FROM `tb_modulos`;

INSERT INTO `tb_permisos` (`id_modulo`, `id_rol`, `leer`, `escribir`, `editar`, `eliminar`)
SELECT id_modulo, 2, 1, 1, 1, 1 FROM `tb_modulos` WHERE nombre NOT IN ('Usuarios','Configuracion');

INSERT INTO `tb_permisos` (`id_modulo`, `id_rol`, `leer`, `escribir`, `editar`, `eliminar`) VALUES
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Dashboard'), 3, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Clientes'), 3, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Instalaciones'), 3, 1, 1, 1, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Inventario'), 3, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Mapa'), 3, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Ordenes'), 3, 1, 1, 1, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Tickets'), 3, 1, 1, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Informes'), 3, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Monitoreo'), 3, 1, 0, 0, 0);

INSERT INTO `tb_permisos` (`id_modulo`, `id_rol`, `leer`, `escribir`, `editar`, `eliminar`) VALUES
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Dashboard'), 4, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Clientes'), 4, 1, 1, 1, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Ventas'), 4, 1, 1, 1, 1),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Mapa'), 4, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Informes'), 4, 1, 0, 0, 0),
((SELECT id_modulo FROM `tb_modulos` WHERE nombre='Monitoreo'), 4, 1, 0, 0, 0);

ALTER TABLE `tb_permisos`
  ADD PRIMARY KEY (`id_permiso`),
  ADD UNIQUE KEY `uq_modulo_rol` (`id_modulo`, `id_rol`),
  ADD KEY `id_rol` (`id_rol`);
ALTER TABLE `tb_permisos`
  MODIFY `id_permiso` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_permisos`
  ADD CONSTRAINT `tb_permisos_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `tb_modulos` (`id_modulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `tb_permisos_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `tb_rol` (`id_rol`) ON DELETE CASCADE;

-- ============================================================
-- Table: tb_plantillas_email
-- ============================================================
CREATE TABLE `tb_plantillas_email` (
  `id_plantilla` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `cuerpo` text NOT NULL,
  `variables` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tb_plantillas_email` (`nombre`, `asunto`, `cuerpo`, `variables`) VALUES
('Factura nuevo cliente', 'Tu factura {numero_factura} - {empresa_nombre}', '<h2>Hola {nombre_cliente}</h2><p>Tu factura <strong>{numero_factura}</strong> por <strong>${monto}</strong> ya esta disponible.</p><p>Vence el <strong>{fecha_vencimiento}</strong>.</p><p><a href=\"{url_pago}\">Pagar ahora</a></p>', '{nombre_cliente},{numero_factura},{monto},{fecha_vencimiento},{empresa_nombre},{url_pago}'),
('Recordatorio de pago', 'Recordatorio: Factura {numero_factura} proxima a vencer', '<h2>Hola {nombre_cliente}</h2><p>Te recordamos que la factura <strong>{numero_factura}</strong> por <strong>${monto}</strong> vence el <strong>{fecha_vencimiento}</strong>.</p><p><a href=\"{url_pago}\">Pagar ahora</a></p>', '{nombre_cliente},{numero_factura},{monto},{fecha_vencimiento},{empresa_nombre},{url_pago}');

ALTER TABLE `tb_plantillas_email`
  ADD PRIMARY KEY (`id_plantilla`),
  ADD UNIQUE KEY `nombre` (`nombre`);
ALTER TABLE `tb_plantillas_email`
  MODIFY `id_plantilla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- ============================================================
-- Table: tb_email_queue
-- ============================================================
CREATE TABLE `tb_email_queue` (
  `id_cola` int(11) NOT NULL AUTO_INCREMENT,
  `para` varchar(255) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `cuerpo` text NOT NULL,
  `adjuntos` text DEFAULT NULL COMMENT 'JSON array of file paths',
  `id_cliente` int(11) DEFAULT NULL,
  `id_factura` int(11) DEFAULT NULL,
  `estado` enum('pendiente','enviado','error') NOT NULL DEFAULT 'pendiente',
  `error_msg` text DEFAULT NULL,
  `intentos` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cola`),
  KEY `idx_estado` (`estado`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_factura` (`id_factura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- Migration: Google2FA column
-- ============================================================
ALTER TABLE `tb_usuarios` ADD COLUMN IF NOT EXISTS `google2fa_secret` VARCHAR(255) DEFAULT NULL AFTER `token_expira`;

COMMIT;

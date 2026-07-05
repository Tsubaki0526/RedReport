CREATE TABLE IF NOT EXISTS `tb_email_queue` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

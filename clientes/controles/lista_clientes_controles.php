<?php
include('../../app/config/conexion.php'); // ajusta la ruta si es diferente

// =================== CLIENTES ===================
$sql_clientes = "SELECT id_cliente, nombre, documento, telefono, direccion, email, estado_servicio 
                 FROM tb_clientes ORDER BY nombre ASC";
$q_clientes = $pdo->prepare($sql_clientes);
$q_clientes->execute();
$clientes = $q_clientes->fetchAll(PDO::FETCH_ASSOC);

// =================== IPS ===================
$sql_ips = "SELECT i.id_cliente, i.ip_principal, i.megas_contratadas
            FROM tb_ips i
            INNER JOIN tb_clientes c ON c.id_cliente = i.id_cliente
            ORDER BY c.nombre ASC";
$q_ips = $pdo->prepare($sql_ips);
$q_ips->execute();
$ips_all = $q_ips->fetchAll(PDO::FETCH_ASSOC);

// Agrupar IPs por cliente
$ips_by_cliente = [];
foreach ($ips_all as $ip) {
    $ips_by_cliente[$ip['id_cliente']][] = $ip;
}

// =================== RED ===================
$sql_red = "SELECT r.id_cliente, r.switch, r.ip, r.puerto
            FROM tb_red r
            ORDER BY r.id_cliente ASC";
$q_red = $pdo->prepare($sql_red);
$q_red->execute();
$red = $q_red->fetchAll(PDO::FETCH_ASSOC);

// Agrupar Red por cliente
$red_by_cliente = [];
foreach ($red as $r) {
    $red_by_cliente[$r['id_cliente']][] = $r;
}

<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/seguridad.php';

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo json_encode([]); exit; }

$like = '%' . $q . '%';
$results = [];

// Clientes
$stmt = $pdo->prepare("SELECT id_cliente, nombre, documento, email FROM tb_clientes WHERE nombre LIKE ? OR documento LIKE ? OR email LIKE ? LIMIT 5");
$stmt->execute([$like, $like, $like]);
while ($r = $stmt->fetch()) {
    $results[] = ['type' => 'Cliente', 'badge' => 'primary', 'label' => hescape($r['nombre']), 'sub' => 'Doc: ' . hescape($r['documento']) . ' | ' . hescape($r['email']), 'url' => APP_URL . 'clientes/vistas/ficha.php?id=' . $r['id_cliente']];
}

// Facturas
$stmt = $pdo->prepare("SELECT f.id_factura, f.numero, f.total, c.nombre AS cliente FROM tb_facturas f JOIN tb_clientes c ON f.id_cliente = c.id_cliente WHERE f.numero LIKE ? OR c.nombre LIKE ? LIMIT 5");
$stmt->execute([$like, $like]);
while ($r = $stmt->fetch()) {
    $results[] = ['type' => 'Factura', 'badge' => 'success', 'label' => hescape($r['numero']), 'sub' => '$' . number_format($r['total'], 0) . ' - ' . hescape($r['cliente']), 'url' => APP_URL . 'facturacion/ver.php?id=' . $r['id_factura']];
}

// Tickets
$stmt = $pdo->prepare("SELECT t.id_ticket, t.asunto, t.estado, c.nombre AS cliente FROM tb_tickets t JOIN tb_clientes c ON t.id_cliente = c.id_cliente WHERE t.asunto LIKE ? OR c.nombre LIKE ? LIMIT 5");
$stmt->execute([$like, $like]);
while ($r = $stmt->fetch()) {
    $results[] = ['type' => 'Ticket', 'badge' => 'warning', 'label' => hescape($r['asunto']), 'sub' => hescape($r['estado']) . ' - ' . hescape($r['cliente']), 'url' => APP_URL . 'tickets/index.php?id=' . $r['id_ticket']];
}

// Ordenes
$stmt = $pdo->prepare("SELECT o.id_orden, o.descripcion, o.estado, c.nombre AS cliente FROM tb_ordenes o JOIN tb_clientes c ON o.id_cliente = c.id_cliente WHERE o.descripcion LIKE ? OR c.nombre LIKE ? LIMIT 5");
$stmt->execute([$like, $like]);
while ($r = $stmt->fetch()) {
    $results[] = ['type' => 'Orden', 'badge' => 'info', 'label' => '#' . $r['id_orden'] . ' ' . hescape(substr($r['descripcion'], 0, 60)), 'sub' => hescape($r['estado']) . ' - ' . hescape($r['cliente']), 'url' => APP_URL . 'ordenes/index.php?id=' . $r['id_orden']];
}

echo json_encode($results);

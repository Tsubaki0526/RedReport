<?php
require_once '../../app/config/conexion.php';

$tipo = $_GET['tipo'] ?? 'facturacion';
$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');
$filtro_id = $_GET['filtro_id'] ?? '';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="informe_' . $tipo . '_' . date('Ymd') . '.csv"');
header('Pragma: no-cache');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF");

if ($tipo == 'facturacion') {
    fputcsv($output, ['Factura', 'Cliente', 'Emision', 'Vencimiento', 'Total', 'Estado']);
    $stmt = $pdo->prepare("SELECT f.*,c.nombre AS cn FROM tb_facturas f LEFT JOIN tb_clientes c ON f.id_cliente=c.id_cliente WHERE f.fecha_emision BETWEEN :d AND :h ORDER BY f.fecha_emision DESC");
    $stmt->execute(['d'=>$fecha_desde,'h'=>$fecha_hasta]);
    while ($r = $stmt->fetch()) fputcsv($output, [$r['numero_factura'], $r['cn']??'-', $r['fecha_emision'], $r['fecha_vencimiento'], $r['total'], $r['estado']]);
} elseif ($tipo == 'ventas') {
    fputcsv($output, ['ID', 'Cliente', 'Vendedor', 'Plan', 'Monto', 'Tipo', 'Fecha']);
    $sql = "SELECT v.*,c.nombre AS cn,u.nombre AS vn,p.nombre AS pn FROM tb_ventas v LEFT JOIN tb_clientes c ON v.id_cliente=c.id_cliente LEFT JOIN tb_usuarios u ON v.id_vendedor=u.id_usuario LEFT JOIN tb_planes p ON v.id_plan=p.id_plan WHERE v.fecha BETWEEN :d AND :h ORDER BY v.fecha DESC";
    $params = ['d'=>$fecha_desde,'h'=>$fecha_hasta];
    if ($filtro_id) { $sql = str_replace('WHERE v.fecha', 'WHERE v.id_vendedor=:f AND v.fecha', $sql); $params['f'] = $filtro_id; }
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    while ($r = $stmt->fetch()) fputcsv($output, [$r['id_venta'], $r['cn']??'-', $r['vn']??'-', $r['pn']??'-', $r['monto'], $r['tipo_venta'], $r['fecha']]);
} elseif ($tipo == 'instalaciones') {
    fputcsv($output, ['Cliente', 'Direccion', 'Tecnico', 'Fecha instalacion']);
    $sql = "SELECT c.nombre AS cn,c.direccion,u.nombre AS tn,c.fecha_instalacion FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador=u.id_usuario WHERE c.fecha_instalacion BETWEEN :d AND :h ORDER BY c.fecha_instalacion DESC";
    $params = ['d'=>$fecha_desde,'h'=>$fecha_hasta];
    if ($filtro_id) { $sql = str_replace('WHERE c.fecha_instalacion', 'WHERE c.id_instalador=:f AND c.fecha_instalacion', $sql); $params['f'] = $filtro_id; }
    $stmt = $pdo->prepare($sql); $stmt->execute($params);
    while ($r = $stmt->fetch()) fputcsv($output, [$r['cn']??'-', $r['direccion']??'-', $r['tn']??'-', $r['fecha_instalacion']]);
} elseif ($tipo == 'tickets') {
    fputcsv($output, ['Ticket', 'Cliente', 'Asunto', 'Categoria', 'Prioridad', 'Estado', 'Fecha']);
    $stmt = $pdo->prepare("SELECT t.*,c.nombre AS cn FROM tb_tickets t LEFT JOIN tb_clientes c ON t.id_cliente=c.id_cliente WHERE t.fecha_creacion BETWEEN :d AND :h ORDER BY t.fecha_creacion DESC");
    $stmt->execute(['d'=>$fecha_desde,'h'=>$fecha_hasta]);
    while ($r = $stmt->fetch()) fputcsv($output, [$r['numero_ticket'], $r['cn']??'-', $r['asunto'], $r['categoria'], $r['prioridad'], $r['estado'], $r['fecha_creacion']]);
} elseif ($tipo == 'cartera') {
    fputcsv($output, ['Cliente', 'Telefono', 'Facturas vencidas', 'Deuda total', 'Dias mora']);
    $stmt = $pdo->query("SELECT c.nombre,c.telefono,COUNT(f.id_factura) AS fv,SUM(f.total) AS dt,DATEDIFF(CURDATE(),MIN(f.fecha_vencimiento)) AS dm FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente=c.id_cliente WHERE f.estado IN ('pendiente','vencida') GROUP BY f.id_cliente ORDER BY dm DESC");
    while ($r = $stmt->fetch()) fputcsv($output, [$r['nombre'], $r['telefono'], $r['fv'], $r['dt'], $r['dm']]);
}
fclose($output);

<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_cliente = intval($_POST['id_cliente'] ?? 0);
$fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d');
$fecha_vencimiento = $_POST['fecha_vencimiento'] ?? date('Y-m-d', strtotime('+30 days'));
$items_json = $_POST['items'] ?? '[]';
$notas = $_POST['notas'] ?? '';

if ($id_cliente <= 0) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Seleccione un cliente'}).then(()=>window.location='../crear.php');</script>";
    exit;
}

$items = json_decode($items_json, true);
if (empty($items)) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Agregue al menos un item'}).then(()=>window.location='../crear.php');</script>";
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->query("SELECT COALESCE(MAX(id_factura), 0) + 1 AS next FROM tb_facturas");
    $next = $stmt->fetch(PDO::FETCH_ASSOC)['next'];
    $numero = 'FAC-' . str_pad($next, 5, '0', STR_PAD_LEFT);

    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += floatval($item['cantidad'] ?? 1) * floatval($item['precio'] ?? 0);
    }
    $iva = round($subtotal * 0.19, 2);
    $total = round($subtotal + $iva, 2);

    $sql = "INSERT INTO tb_facturas (numero_factura, id_cliente, fecha_emision, fecha_vencimiento, subtotal, iva, total, notas, fecha_creacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$numero, $id_cliente, $fecha_emision, $fecha_vencimiento, $subtotal, $iva, $total, $notas]);
    $id_factura = $pdo->lastInsertId();

    $sqlItem = "INSERT INTO tb_factura_items (id_factura, descripcion, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
    $stmtItem = $pdo->prepare($sqlItem);
    foreach ($items as $item) {
        $cantidad = intval($item['cantidad'] ?? 1);
        $precio = floatval($item['precio'] ?? 0);
        $sub = round($cantidad * $precio, 2);
        $stmtItem->execute([$id_factura, $item['descripcion'], $cantidad, $precio, $sub]);
    }

    $pdo->commit();
    bitacora($pdo, $id_usuario, 'CREAR_FACTURA', 'tb_facturas', $id_factura, "Factura $numero creada por $total");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Factura creada',text:'$numero por $$total'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("crear_factura error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error al crear la factura.'}).then(()=>window.location='../crear.php');</script>";
}

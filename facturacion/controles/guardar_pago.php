<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_factura = intval($_POST['id_factura'] ?? 0);
$monto = floatval($_POST['monto'] ?? 0);
$metodo = $_POST['metodo_pago'] ?? 'Efectivo';
$referencia = trim($_POST['referencia'] ?? '');
$notas = trim($_POST['notas'] ?? '');

if ($id_factura <= 0 || $monto <= 0) {
    header('Location: ../index.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE tb_facturas SET estado = 'pagada', fecha_pago = CURDATE() WHERE id_factura = ? AND estado IN ('pendiente','vencida')");
    $stmt->execute([$id_factura]);

    if ($stmt->rowCount() == 0) {
        $pdo->rollBack();
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>Swal.fire({icon:'warning',title:'Sin cambios',text:'La factura ya estaba pagada o no existe.'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO tb_pagos (id_factura, monto, metodo_pago, referencia, id_usuario, notas) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$id_factura, $monto, $metodo, $referencia, $id_usuario, $notas]);
    $id_pago = $pdo->lastInsertId();

    $pdo->commit();

    bitacora($pdo, $id_usuario, 'PAGAR_FACTURA', 'tb_facturas', $id_factura,
             "Pago #$id_pago registrado: $$monto via $metodo" . ($referencia ? " (Ref: $referencia)" : ''));

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Pago registrado',text:'Factura pagada correctamente.'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("guardar_pago error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error al procesar el pago.'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
}

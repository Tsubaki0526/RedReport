<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_factura = intval($_POST['id_factura'] ?? 0);

if ($id_factura <= 0) {
    header('Location: ../index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE tb_facturas SET estado = 'anulada' WHERE id_factura = ? AND estado != 'pagada'");
    $stmt->execute([$id_factura]);

    if ($stmt->rowCount() > 0) {
        bitacora($pdo, $id_usuario, 'ANULAR_FACTURA', 'tb_facturas', $id_factura, "Factura ID $id_factura anulada");
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>Swal.fire({icon:'success',title:'Factura anulada',text:'La factura fue anulada correctamente.'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>Swal.fire({icon:'warning',title:'Sin cambios',text:'No se pudo anular (ya esta pagada o no existe).'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
    }
} catch (Exception $e) {
    error_log("anular_factura error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error al anular la factura.'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
}

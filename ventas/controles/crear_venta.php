<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

verificar_acceso([1, 2, 4]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_cliente = intval($_POST['id_cliente'] ?? 0);
$id_contrato = !empty($_POST['id_contrato']) ? intval($_POST['id_contrato']) : null;
$id_vendedor = intval($_POST['id_vendedor'] ?? 0);
$tipo = $_POST['tipo'] ?? 'nuevo';
$monto = floatval($_POST['monto'] ?? 0);
$comision = floatval($_POST['comision'] ?? 0);
$fecha = $_POST['fecha'] ?? date('Y-m-d');
$notas = trim($_POST['notas'] ?? '');

if ($id_cliente <= 0 || $id_vendedor <= 0 || $monto <= 0) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Complete todos los campos requeridos'}).then(()=>window.location='../ventas.php');</script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO tb_ventas (id_contrato, id_cliente, id_vendedor, tipo, monto, comision, fecha, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_contrato, $id_cliente, $id_vendedor, $tipo, $monto, $comision, $fecha, $notas]);
    $id_venta = $pdo->lastInsertId();
    bitacora($pdo, $id_usuario, 'CREAR_VENTA', 'tb_ventas', $id_venta, "Venta #$id_venta por $$monto");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Venta registrada'}).then(()=>window.location='../ventas.php');</script>";
} catch (Exception $e) {
    error_log("crear_venta error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error al registrar la venta'}).then(()=>window.location='../ventas.php');</script>";
}

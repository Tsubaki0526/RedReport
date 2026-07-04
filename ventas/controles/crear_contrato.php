<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_cliente = intval($_POST['id_cliente'] ?? 0);
$id_plan = intval($_POST['id_plan'] ?? 0);
$id_vendedor = intval($_POST['id_vendedor'] ?? 0);
$fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_POST['fecha_fin'] ?: null;
$notas = trim($_POST['notas'] ?? '');

if ($id_cliente <= 0 || $id_plan <= 0 || $id_vendedor <= 0) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Complete todos los campos requeridos'}).then(()=>window.location='../contrato_nuevo.php');</script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO tb_contratos (id_cliente, id_plan, id_vendedor, fecha_inicio, fecha_fin, notas) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_cliente, $id_plan, $id_vendedor, $fecha_inicio, $fecha_fin, $notas]);
    $id_contrato = $pdo->lastInsertId();

    $plan = $pdo->query("SELECT precio FROM tb_planes WHERE id_plan = $id_plan")->fetch(PDO::FETCH_ASSOC);
    $monto = $plan['precio'] ?? 0;

    $sqlVenta = "INSERT INTO tb_ventas (id_contrato, id_cliente, id_vendedor, tipo, monto, fecha, notas) VALUES (?, ?, ?, 'nuevo', ?, CURDATE(), ?)";
    $stmtV = $pdo->prepare($sqlVenta);
    $stmtV->execute([$id_contrato, $id_cliente, $id_vendedor, $monto, 'Contrato #' . $id_contrato]);

    bitacora($pdo, $id_usuario, 'CREAR_CONTRATO', 'tb_contratos', $id_contrato, "Contrato #$id_contrato creado");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Contrato creado',text:'Venta registrada automaticamente'}).then(()=>window.location='../contratos.php');</script>";
} catch (Exception $e) {
    error_log("crear_contrato error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error al crear el contrato'}).then(()=>window.location='../contrato_nuevo.php');</script>";
}

<?php
session_start();
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if (!isset($_SESSION['movil_user']) || $_SESSION['movil_user']['tipo'] !== 'empleado') {
    header('Location: ../login.php'); exit;
}

if (!csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_orden = intval($_POST['id_orden'] ?? 0);
$estado = $_POST['estado'] ?? '';
$solucion = trim($_POST['solucion'] ?? '');
$id_tecnico = $_SESSION['movil_user']['id'];

$allowed = ['En Proceso', 'Completada', 'Cancelada'];
if (!in_array($estado, $allowed)) {
    echo "<script>alert('Estado inválido');history.back();</script>";
    exit;
}

$stmt = $pdo->prepare("SELECT id_orden FROM tb_ordenes WHERE id_orden=? AND id_tecnico=?");
$stmt->execute([$id_orden, $id_tecnico]);
if (!$stmt->fetch()) {
    echo "<script>alert('Orden no encontrada');window.location='ordenes.php';</script>";
    exit;
}

$fecha_col = $estado === 'Completada' ? ", fecha_completada=NOW()" : "";
$upd = $pdo->prepare("UPDATE tb_ordenes SET estado=?, solucion=CONCAT(IFNULL(solucion,''), ?)$fecha_col WHERE id_orden=?");
$upd->execute([$estado, ($solucion ? "\n$solucion" : ''), $id_orden]);

bitacora($pdo, $id_tecnico, 'Orden actualizada', 'tb_ordenes', $id_orden, "Estado: $estado - $solucion");
header("Location: orden_editar.php?id=$id_orden&success=Orden actualizada");

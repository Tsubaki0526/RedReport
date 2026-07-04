<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SESSION['id_rol'] != 1) die("Acceso denegado");
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_cliente = (int)($_POST['id_cliente'] ?? 0);
$id_instalador = (int)($_POST['id_instalador'] ?? 0);

$pdo->prepare("UPDATE tb_clientes SET id_instalador = :inst WHERE id_cliente = :cli")
   ->execute([':inst' => $id_instalador, ':cli' => $id_cliente]);
bitacora($pdo, $_SESSION['id_usuario'], 'ASIGNAR_INSTALADOR', 'tb_clientes', $id_cliente, "Instalador ID: $id_instalador");

header("Location: ../index.php");
exit;
?>

<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
verificar_acceso([1]);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_cliente = (int)($_POST['id_cliente'] ?? 0);
$estado = $_POST['estado'] ?? '';
$motivo = trim($_POST['motivo'] ?? '');

if (!in_array($estado, ['Activo','Suspendido','Cortado'])) die("Estado invalido");

$stmt = $pdo->prepare("UPDATE tb_clientes SET estado_servicio = :estado WHERE id_cliente = :id");
$stmt->execute([':estado' => $estado, ':id' => $id_cliente]);
bitacora($pdo, $_SESSION['id_usuario'], 'CAMBIAR_ESTADO', 'tb_clientes', $id_cliente, "Estado cambiado a $estado" . ($motivo ? " - $motivo" : ""));

header("Location: ../vistas/ficha.php?id=$id_cliente");
exit;

<?php
include('../../sesion.php');
require_once '../../app/config/conexion.php';
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM tb_dispositivos WHERE id_dispositivo=?");
    $stmt->execute([$id]);
    $pdo->prepare("INSERT INTO tb_bitacora (id_usuario, accion, tabla_afectada, detalle, direccion_ip, fecha_hora) VALUES (?,'ELIMINAR','tb_dispositivos',?,?,NOW())")
        ->execute([$_SESSION['id_usuario'], "Dispositivo ID: $id", $_SERVER['REMOTE_ADDR']??'127.0.0.1']);
}
header('Location: ../index.php');

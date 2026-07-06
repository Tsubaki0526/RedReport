<?php
include('../../sesion.php');
require_once '../../app/config/conexion.php';
require_once '../../app/config/seguridad.php';
verificar_acceso([1, 2]);

$ip = trim($_POST['ip'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$tipo = $_POST['tipo'] ?? 'Router';
$id_cliente = intval($_POST['id_cliente'] ?? 0) ?: null;

$stmt = $pdo->prepare("INSERT INTO tb_dispositivos (ip, nombre, tipo, id_cliente, ultimo_estado) VALUES (?,?,?,?,'Sin dato')");
$stmt->execute([$ip, $nombre, $tipo, $id_cliente]);

$pdo->prepare("INSERT INTO tb_bitacora (id_usuario, accion, tabla_afectada, detalle, direccion_ip, fecha_hora) VALUES (?,'CREAR','tb_dispositivos',?,?,NOW())")
    ->execute([$_SESSION['id_usuario'], "Nuevo dispositivo: $ip", $_SERVER['REMOTE_ADDR']??'127.0.0.1']);

header('Location: ../index.php');

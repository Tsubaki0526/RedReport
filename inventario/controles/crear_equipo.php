<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SESSION['id_rol'] != 1) die("Acceso denegado");
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_tipo = $_POST['id_tipo_equipo'] ?? 0;
$serial = $_POST['serial'] ?? '';
$marca = $_POST['marca'] ?? '';
$modelo = $_POST['modelo'] ?? '';
$estado = $_POST['estado'] ?? 'Disponible';

$sql = "INSERT INTO tb_equipos (id_tipo_equipo, serial, marca, modelo, estado) VALUES (:tipo, :serial, :marca, :modelo, :estado)";
$stmt = $pdo->prepare($sql);
$stmt->execute([':tipo' => $id_tipo, ':serial' => $serial, ':marca' => $marca, ':modelo' => $modelo, ':estado' => $estado]);
bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_equipos', $pdo->lastInsertId(), "Serial: $serial");

echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>Swal.fire({icon:'success',title:'Equipo registrado'}).then(()=>window.location='../index.php');</script>";
?>

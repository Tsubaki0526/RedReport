<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SESSION['id_rol'] != 1) die("Acceso denegado");
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$nombre = $_POST['nombre'] ?? '';
$color = $_POST['color'] ?? '#3388ff';
$coordenadas = $_POST['coordenadas'] ?? '';

$sql = "INSERT INTO tb_cobertura_zonas (nombre, color, coordenadas) VALUES (:nombre, :color, :coordenadas)";
$stmt = $pdo->prepare($sql);
$stmt->execute([':nombre' => $nombre, ':color' => $color, ':coordenadas' => $coordenadas]);
bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_cobertura_zonas', $pdo->lastInsertId(), "Zona: $nombre");

echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>Swal.fire({icon:'success',title:'Zona creada'}).then(()=>window.location='../admin.php');</script>";
?>

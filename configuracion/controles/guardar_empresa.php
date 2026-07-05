<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SESSION['id_rol'] != 1) die("Acceso denegado");
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$nombre = trim($_POST['nombre'] ?? '');
$documento = trim($_POST['documento'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$email = trim($_POST['email'] ?? '');
$lat = $_POST['lat'] !== '' ? floatval($_POST['lat']) : null;
$lng = $_POST['lng'] !== '' ? floatval($_POST['lng']) : null;

if ($nombre === '') {
    echo "<script>alert('El nombre de la empresa es requerido');window.location='../empresa.php';</script>";
    exit;
}

try {
    $sql = "UPDATE tb_empresa SET nombre=:nombre, documento=:documento, direccion=:direccion, telefono=:telefono, email=:email, lat=:lat, lng=:lng WHERE id_empresa=1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':documento' => $documento,
        ':direccion' => $direccion,
        ':telefono' => $telefono,
        ':email' => $email,
        ':lat' => $lat,
        ':lng' => $lng
    ]);
    bitacora($pdo, $_SESSION['id_usuario'], 'EDITAR_EMPRESA', 'tb_empresa', 1, 'Datos de empresa actualizados');
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Guardado',text:'Datos de la empresa actualizados'}).then(()=>window.location='../empresa.php');</script>";
} catch (Exception $e) {
    error_log("guardar_empresa error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error al guardar'}).then(()=>window.location='../empresa.php');</script>";
}

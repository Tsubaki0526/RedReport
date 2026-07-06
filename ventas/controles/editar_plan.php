<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

verificar_acceso([1]);

$id_plan = intval($_POST['id_plan'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$velocidad = trim($_POST['velocidad'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$descripcion = trim($_POST['descripcion'] ?? '');
$activo = isset($_POST['activo']) ? 1 : 0;

if ($id_plan <= 0 || empty($nombre) || $precio <= 0) {
    header('Location: ../planes.php');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE tb_planes SET nombre = ?, velocidad = ?, precio = ?, descripcion = ?, activo = ? WHERE id_plan = ?");
    $stmt->execute([$nombre, $velocidad, $precio, $descripcion, $activo, $id_plan]);
    bitacora($pdo, $_SESSION['id_usuario'], 'EDITAR_PLAN', 'tb_planes', $id_plan, "Plan $nombre actualizado");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Plan actualizado'}).then(()=>window.location='../planes.php');</script>";
} catch (Exception $e) {
    error_log("editar_plan error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error'}).then(()=>window.location='../planes.php');</script>";
}

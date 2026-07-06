<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

verificar_acceso([1]);

$nombre = trim($_POST['nombre'] ?? '');
$velocidad = trim($_POST['velocidad'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$descripcion = trim($_POST['descripcion'] ?? '');
$activo = isset($_POST['activo']) ? 1 : 0;

if (empty($nombre) || $precio <= 0) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Nombre y precio requeridos'}).then(()=>window.location='../planes.php');</script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO tb_planes (nombre, velocidad, precio, descripcion, activo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $velocidad, $precio, $descripcion, $activo]);
    $id = $pdo->lastInsertId();
    bitacora($pdo, $_SESSION['id_usuario'], 'CREAR_PLAN', 'tb_planes', $id, "Plan $nombre creado");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Plan creado'}).then(()=>window.location='../planes.php');</script>";
} catch (Exception $e) {
    error_log("crear_plan error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error'}).then(()=>window.location='../planes.php');</script>";
}

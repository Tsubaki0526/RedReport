<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

verificar_acceso([1]);

$id_plan = intval($_POST['id_plan'] ?? 0);
if ($id_plan <= 0) {
    header('Location: ../planes.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM tb_planes WHERE id_plan = ?");
    $stmt->execute([$id_plan]);
    bitacora($pdo, $_SESSION['id_usuario'], 'ELIMINAR_PLAN', 'tb_planes', $id_plan, "Plan ID $id_plan eliminado");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Plan eliminado'}).then(()=>window.location='../planes.php');</script>";
} catch (Exception $e) {
    error_log("eliminar_plan error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'No se puede eliminar (tiene contratos asociados)'}).then(()=>window.location='../planes.php');</script>";
}

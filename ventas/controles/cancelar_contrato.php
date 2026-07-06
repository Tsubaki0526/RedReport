<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

verificar_acceso([1, 2, 4]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_contrato = intval($_POST['id_contrato'] ?? 0);

if ($id_contrato <= 0) {
    header('Location: ../contratos.php');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE tb_contratos SET estado = 'cancelado' WHERE id_contrato = ? AND estado = 'activo'");
    $stmt->execute([$id_contrato]);
    bitacora($pdo, $id_usuario, 'CANCELAR_CONTRATO', 'tb_contratos', $id_contrato, "Contrato #$id_contrato cancelado");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Contrato cancelado'}).then(()=>window.location='../contratos.php');</script>";
} catch (Exception $e) {
    error_log("cancelar_contrato error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrio un error'}).then(()=>window.location='../contratos.php');</script>";
}

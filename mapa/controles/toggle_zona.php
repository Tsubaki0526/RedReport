<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SESSION['id_rol'] != 1) die("Acceso denegado");
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id = (int)($_POST['id'] ?? 0);
$zona = $pdo->prepare("SELECT activo FROM tb_cobertura_zonas WHERE id_zona = :id");
$zona->execute([':id' => $id]);
$z = $zona->fetch();
if ($z) {
    $nuevo = $z['activo'] ? 0 : 1;
    $pdo->prepare("UPDATE tb_cobertura_zonas SET activo = :activo WHERE id_zona = :id")
       ->execute([':activo' => $nuevo, ':id' => $id]);
    bitacora($pdo, $_SESSION['id_usuario'], 'TOGGLE_ZONA', 'tb_cobertura_zonas', $id, "Zona cambiada a " . ($nuevo ? 'activa' : 'inactiva'));
}
header("Location: ../admin.php");
exit;
?>

<?php
session_start();
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if (!csrf_verify($_POST['_csrf_token'] ?? '')) { csrf_die(); }

$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function jsonExit($data) { header('Content-Type: application/json'); echo json_encode($data); exit; }
function errorExit($msg) { global $is_ajax; if ($is_ajax) jsonExit(['ok' => false, 'error' => $msg]); echo "<script>alert('" . addslashes($msg) . "'); window.history.back();</script>"; exit; }

$id_cliente = intval($_POST['id_cliente'] ?? 0);
$descripcion = trim($_POST['descripcion'] ?? '');
$id_instalacion = !empty($_POST['id_instalacion']) ? intval($_POST['id_instalacion']) : null;

if ($id_cliente <= 0) { errorExit('Cliente invalido'); }

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    errorExit('Error al subir archivo');
}

$allowed = ['jpg','jpeg','png','gif','webp'];
$ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) { errorExit('Formato no permitido (solo jpg, png, gif, webp)'); }

$maxSize = 5 * 1024 * 1024;
if ($_FILES['foto']['size'] > $maxSize) { errorExit('Archivo demasiado grande (max 5MB)'); }

$nombreArchivo = 'inst_' . $id_cliente . '_' . time() . '.' . $ext;
$destino = __DIR__ . '/../../public/uploads/' . $nombreArchivo;

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
    errorExit('Error al guardar archivo');
}

$stmt = $pdo->prepare("INSERT INTO tb_instalacion_fotos (id_cliente, id_instalacion, nombre_archivo, descripcion) VALUES (?,?,?,?)");
$stmt->execute([$id_cliente, $id_instalacion, $nombreArchivo, $descripcion]);

$id_foto = $pdo->lastInsertId();
bitacora($pdo, $_SESSION['id_usuario'], 'SUBIR_FOTO', 'tb_instalacion_fotos', $id_foto, "Foto subida para cliente #$id_cliente");

if ($is_ajax) {
    jsonExit(['ok' => true, 'nombre_archivo' => $nombreArchivo, 'descripcion' => $descripcion, 'url' => APP_URL . 'public/uploads/' . $nombreArchivo]);
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));

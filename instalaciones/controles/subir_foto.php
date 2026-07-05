<?php
session_start();
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

csrf_verify($_POST['_csrf_token'] ?? '');

$id_cliente = intval($_POST['id_cliente'] ?? 0);
$descripcion = trim($_POST['descripcion'] ?? '');
$id_instalacion = !empty($_POST['id_instalacion']) ? intval($_POST['id_instalacion']) : null;

if ($id_cliente <= 0) { die('Cliente invalido'); }

if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    echo "<script>alert('Error al subir archivo'); window.history.back();</script>";
    exit;
}

$allowed = ['jpg','jpeg','png','gif','webp'];
$ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) { echo "<script>alert('Formato no permitido (solo jpg, png, gif, webp)'); window.history.back();</script>"; exit; }

$maxSize = 5 * 1024 * 1024;
if ($_FILES['foto']['size'] > $maxSize) { echo "<script>alert('Archivo demasiado grande (max 5MB)'); window.history.back();</script>"; exit; }

$nombreArchivo = 'inst_' . $id_cliente . '_' . time() . '.' . $ext;
$destino = __DIR__ . '/../../public/uploads/' . $nombreArchivo;

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
    echo "<script>alert('Error al guardar archivo'); window.history.back();</script>";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO tb_instalacion_fotos (id_cliente, id_instalacion, nombre_archivo, descripcion) VALUES (?,?,?,?)");
$stmt->execute([$id_cliente, $id_instalacion, $nombreArchivo, $descripcion]);

bitacora($pdo, $_SESSION['id_usuario'], 'SUBIR_FOTO', 'tb_instalacion_fotos', $pdo->lastInsertId(), "Foto subida para cliente #$id_cliente");

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));

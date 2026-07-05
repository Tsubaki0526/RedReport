<?php
include('../sesion.php');
require_once '../app/config/conexion.php';

$file = basename($_GET['file'] ?? '');
$path = __DIR__ . '/' . $file;

if (!file_exists($path) || !str_starts_with($file, 'backup_') || !str_ends_with($file, '.sql')) {
    header('Location: index.php?error=Archivo no válido'); exit;
}

$db_host = DB_HOST;
$db_user = DB_USER;
$db_pass = DB_PASS;
$db_name = DB_NAME;

$cmd = "\"C:\\xampp\\mysql\\bin\\mysql\" -h$db_host -u$db_user -p\"$db_pass\" $db_name < \"$path\" 2>&1";
exec($cmd, $output, $exit_code);

if ($exit_code === 0) {
    $pdo->prepare("INSERT INTO tb_bitacora (id_usuario, accion, tabla_afectada, detalle, direccion_ip, fecha_hora) VALUES (?, 'RESTORE', 'tb_*', ?, ?, NOW())")->execute([$_SESSION['id_usuario'], "Restaurado: $file", $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
    header('Location: index.php?mensaje=Backup restaurado correctamente');
} else {
    header('Location: index.php?error=' . urlencode(implode("\n", $output)));
}

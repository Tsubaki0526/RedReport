<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    http_response_code(419);
    echo json_encode(['success' => false, 'message' => 'Sesi&oacute;n expirada. Recarga la p&aacute;gina.']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'] ?? 0;
if (!$id_usuario) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesi&oacute;n.']);
    exit();
}

$sql = "UPDATE tb_usuarios SET google2fa_secret = NULL WHERE id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_usuario]);

bitacora($pdo, $id_usuario, 'DESACTIVAR_2FA', 'tb_usuarios', $id_usuario, 'Usuario desactiv&oacute; autenticaci&oacute;n en dos pasos');

echo json_encode(['success' => true]);

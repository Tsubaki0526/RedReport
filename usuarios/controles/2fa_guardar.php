<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
require_once('../../app/config/2fa.php');

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

$code = $_POST['code'] ?? '';
$secret = $_POST['secret'] ?? '';

if (empty($code) || empty($secret)) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos requeridos.']);
    exit();
}

$totp = new TOTP();
if (!$totp->verify($secret, $code)) {
    echo json_encode(['success' => false, 'message' => 'C&oacute;digo inv&aacute;lido. Verifica que la hora de tu tel&eacute;fono est&eacute; sincronizada.']);
    exit();
}

$sql = "UPDATE tb_usuarios SET google2fa_secret = :secret WHERE id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':secret' => $secret, ':id' => $id_usuario]);

bitacora($pdo, $id_usuario, 'ACTIVAR_2FA', 'tb_usuarios', $id_usuario, 'Usuario activ&oacute; autenticaci&oacute;n en dos pasos');

echo json_encode(['success' => true, 'message' => '2FA activado correctamente.']);

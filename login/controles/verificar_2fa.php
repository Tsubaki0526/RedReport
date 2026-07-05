<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
require_once('../../app/config/2fa.php');

$userId = $_SESSION['tentativa_2fa_user_id'] ?? 0;
$secret = $_SESSION['tentativa_2fa_secret'] ?? '';

if (!$userId || !$secret) {
    echo json_encode(['success' => false, 'message' => 'Sesi&oacute;n de verificaci&oacute;n no encontrada. Inicia sesi&oacute;n de nuevo.']);
    exit();
}

$codigo = $_POST['codigo'] ?? '';

if (empty($codigo)) {
    echo json_encode(['success' => false, 'message' => 'Ingresa el c&oacute;digo de 6 d&iacute;gitos.']);
    exit();
}

// Fetch current secret from DB in case it changed
$sql = "SELECT id_usuario, nombre, id_rol, google2fa_secret FROM tb_usuarios WHERE id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if (!$user || empty($user['google2fa_secret'])) {
    unset($_SESSION['tentativa_2fa_user_id'], $_SESSION['tentativa_2fa_secret']);
    echo json_encode(['success' => false, 'message' => '2FA no est&aacute; configurado en esta cuenta.']);
    exit();
}

$totp = new TOTP();
if (!$totp->verify($user['google2fa_secret'], $codigo)) {
    echo json_encode(['success' => false, 'message' => 'C&oacute;digo inv&aacute;lido. Verifica la hora de tu tel&eacute;fono.']);
    exit();
}

session_regenerate_id(true);
$_SESSION['id_usuario'] = $user['id_usuario'];
$_SESSION['usuario'] = $user['nombre'];
$_SESSION['id_rol'] = $user['id_rol'];

unset($_SESSION['tentativa_2fa_user_id'], $_SESSION['tentativa_2fa_secret']);

bitacora($pdo, $user['id_usuario'], 'ACCESO_2FA', 'tb_usuarios', $user['id_usuario'], 'Acceso con 2FA v&aacute;lido');

$redirect = APP_URL . 'index.php';
echo json_encode(['success' => true, 'redirect' => $redirect]);

<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';
require_once __DIR__ . '/../../autoload.inc.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    http_response_code(419);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit;
}

$destinatario = trim($_POST['destinatario'] ?? '');
if ($destinatario === '') {
    $destinatario = $_SESSION['email'] ?? $_SESSION['usuario'] ?? '';
}
if ($destinatario === '' || !filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo destinatario inválido']);
    exit;
}

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress($destinatario);
    $mail->Subject = 'Prueba de configuración SMTP - ' . APP_NAME;

    $appName = APP_NAME;
    $mail->Body    = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
<tr><td style="background:#2563eb;padding:30px 40px;text-align:center;">
<h1 style="color:#ffffff;margin:0;font-size:24px;">{$appName}</h1>
</td></tr>
<tr><td style="padding:40px;">
<h2 style="color:#1e293b;margin:0 0 12px;">Prueba de configuración SMTP</h2>
<p style="color:#475569;font-size:15px;line-height:1.6;margin:0 0 20px;">Este es un mensaje de prueba enviado desde el panel de configuración de <strong>{$appName}</strong>.</p>
<p style="color:#475569;font-size:15px;line-height:1.6;margin:0 0 20px;">Si estás recibiendo este correo, la configuración SMTP está funcionando correctamente.</p>
<div style="background:#f1f5f9;border-left:4px solid #2563eb;padding:16px 20px;border-radius:6px;">
<p style="margin:0;color:#334155;font-size:14px;"><strong>Fecha y hora del envío:</strong> {$appName}</p>
</div>
</td></tr>
<tr><td style="background:#f8fafc;padding:20px 40px;text-align:center;border-top:1px solid #e2e8f0;">
<p style="color:#94a3b8;font-size:12px;margin:0;">&copy; " . date('Y') . " {$appName} - Todos los derechos reservados</p>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
HTML;

    $mail->isHTML(true);
    $mail->send();

    bitacora($pdo, $_SESSION['id_usuario'], 'PRUEBA_EMAIL', 'configuracion', 0, "Correo de prueba enviado a {$destinatario}");

    echo json_encode(['success' => true, 'message' => 'Email enviado correctamente a ' . $destinatario]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $mail->ErrorInfo]);
}

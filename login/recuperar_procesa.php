<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
include("../app/config/conexion.php");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperación de contraseña</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Buscar usuario
    $sql = "SELECT id_usuario, nombre FROM tb_usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $token = bin2hex(random_bytes(50));
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Guardar token
        $sql = "UPDATE tb_usuarios SET token_reset = ?, token_expira = ? WHERE id_usuario = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token, $expira, $usuario['id_usuario']]);

        // Link
        $link = APP_URL . "login/reset.php?token=" . $token;

        // Mensaje HTML
        $asunto = "Recuperación de contraseña - RedReport";
        $mensaje = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
                .container { background: #fff; padding: 20px; border-radius: 10px; max-width: 600px; margin: auto; }
                .header { text-align: center; }
                .header img { max-width: 150px; }
                .button {
                    display: inline-block;
                    padding: 12px 20px;
                    margin: 20px 0;
                    font-size: 16px;
                    color: #fff;
                    background-color: #007bff;
                    text-decoration: none;
                    border-radius: 8px;
                }
                .footer { font-size: 12px; color: #666; margin-top: 20px; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://i.ibb.co/5R3N8YN/moon.png' alt='RedReport Logo'>
                    <h2>Recuperación de Contraseña</h2>
                </div>
                <p>Hola <b>{$usuario['nombre']}</b>,</p>
                <p>Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el siguiente botón:</p>
                <p style='text-align: center;'>
                    <a href='{$link}' class='button'>Restablecer contraseña</a>
                </p>
                <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                <div class='footer'>
                    <p>Este enlace expirará en 1 hora.<br>&copy; " . date('Y') . " RedReport</p>
                </div>
            </div>
        </body>
        </html>";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = SMTP_PORT;

            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($email, $usuario['nombre']);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body    = $mensaje;

            $mail->send();
            ?>
            <script>
            Swal.fire({
                icon: 'success',
                title: '¡Correo enviado!',
                text: 'Se ha enviado un enlace de recuperación a tu correo.',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location = 'login.php';
            });
            </script>
            <?php
        } catch (Exception $e) {
            ?>
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo enviar el correo. Intenta de nuevo mas tarde.',
                confirmButtonColor: '#d33'
            });
            </script>
            <?php
        }
    } else {
        ?>
        <script>
        Swal.fire({
            icon: 'warning',
            title: 'Correo no registrado',
            text: 'El correo ingresado no está en nuestra base de datos.',
            confirmButtonColor: '#f0ad4e'
        });
        </script>
        <?php
    }
}
?>
</body>
</html>

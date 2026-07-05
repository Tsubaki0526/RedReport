<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../app/config/seguridad.php';
require_once '../app/config/conexion.php';
require_once '../app/config/2fa.php';

$userId = $_SESSION['tentativa_2fa_user_id'] ?? 0;
$secret = $_SESSION['tentativa_2fa_secret'] ?? '';

if (!$userId || !$secret) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT email FROM tb_usuarios WHERE id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();
$email = $user ? $user['email'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= APP_NAME ?> | Verificaci&oacute;n 2FA</title>
    <link rel="icon" href="../public/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../public/css/redreport.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="../public/img/logo.png" alt="Logo">
            <h3><?= APP_NAME ?></h3>
            <p class="text-muted">Autenticaci&oacute;n en dos pasos</p>
        </div>

        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-shield-alt text-primary mb-3" style="font-size:3rem;"></i>
                <p class="mb-1 fw-semibold"><?= hescape($email) ?></p>
                <p class="text-muted small">Ingresa el c&oacute;digo de 6 d&iacute;gitos de tu aplicaci&oacute;n Google Authenticator.</p>

                <form id="frmVerificar2FA">
                    <div class="mb-3">
                        <input type="text" name="codigo" class="form-control text-center font-monospace fs-4" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="off" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btnVerificar">
                        <i class="fas fa-check me-1"></i> Verificar c&oacute;digo
                    </button>
                </form>
                <div id="msg2fa" class="mt-3"></div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Volver al inicio de sesi&oacute;n</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.getElementById('frmVerificar2FA').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnVerificar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verificando...';
        const msgDiv = document.getElementById('msg2fa');
        msgDiv.innerHTML = '';

        const formData = new FormData(this);
        fetch('controles/verificar_2fa.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Acceso concedido', timer: 1500, showConfirmButton: false, heightAuto: false }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    msgDiv.innerHTML = '<div class="alert alert-danger mb-0">' + data.message + '</div>';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i> Verificar c&oacute;digo';
                }
            })
            .catch(() => {
                msgDiv.innerHTML = '<div class="alert alert-danger mb-0">Error de conexi&oacute;n. Intenta de nuevo.</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check me-1"></i> Verificar c&oacute;digo';
            });
    });
    </script>
</body>
</html>

<?php
date_default_timezone_set('America/Bogota');
include("../app/config/conexion.php");

$token = $_GET['token'] ?? null;

if ($token) {
    $sql = "SELECT id_usuario, token_expira FROM tb_usuarios WHERE token_reset = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && strtotime($usuario['token_expira']) > time()) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contrasena - RedReport</title>
    <link rel="icon" href="../public/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/redreport.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="../public/img/logo.png" alt="Logo">
            <h3>Restablecer contrasena</h3>
        </div>
        <form action="reset_guardar.php" method="POST">
            <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="mb-3">
                <label for="password" class="form-label">Nueva contrasena</label>
                <input type="password" class="form-control" name="password" id="password" required minlength="6">
            </div>
            <div class="mb-3">
                <label for="password2" class="form-label">Confirmar contrasena</label>
                <input type="password" class="form-control" name="password2" id="password2" required minlength="6">
            </div>
            <button type="submit" class="btn btn-success w-100">Guardar contrasena</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
    } else {
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Enlace invalido</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='../public/css/redreport.css'>
        </head><body class='login-page'>
        <div class='login-box text-center'><p class='text-danger fw-bold'>Enlace invalido o expirado.</p></div></body></html>";
    }
} else {
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Token no valido</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='../public/css/redreport.css'>
    </head><body class='login-page'>
    <div class='login-box text-center'><p class='text-warning fw-bold'>Token no valido.</p></div></body></html>";
}

<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../app/config/seguridad.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RedReport | Iniciar sesion</title>

    <link rel="icon" href="../public/img/favicon.png" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../public/css/redreport.css">


</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="../public/img/favicon.png" alt="Logo">
            <h3>RedReport</h3>
            <p class="text-muted">Inicia sesion para comenzar</p>
        </div>

        <form action="../app/controles/controles_login/ingreso.php" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="nombre" class="form-control" placeholder="Usuario" autocomplete="off" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Contrasena" required>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary w-100">Iniciar sesion</button>
        </form>

        <div class="text-end mt-3">
            <a href="recuperar.php" class="text-decoration-none small">Olvidaste tu contrasena?</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.addEventListener("pageshow", function(event) {
            if (event.persisted) window.location.reload();
        });
    </script>
</body>
</html>

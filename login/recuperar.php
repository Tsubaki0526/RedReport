<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contrasena - RedReport</title>
    <link rel="icon" href="../public/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/redreport.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="../public/img/logo.png" alt="Logo">
            <h3>Recuperar contrasena</h3>
        </div>
        <form action="recuperar_procesa.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo electronico</label>
                <input type="email" class="form-control" name="email" id="email"
                       placeholder="ejemplo@correo.com" required autocomplete="off">
                <div class="form-text">Te enviaremos un enlace de recuperacion si tu correo esta registrado.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

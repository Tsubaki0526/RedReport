<?php
session_start();
if (isset($_SESSION['portal_cliente'])) {
    header('Location: dashboard.php');
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Portal Cliente - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body{background:#0f172a;display:flex;align-items:center;min-height:100vh;}
.login-card{background:#1e293b;border-radius:12px;padding:2rem;color:#fff;max-width:420px;margin:auto;width:100%;}
.login-card h3{color:#2563eb;font-weight:700;}.form-control{background:#334155;border:1px solid #475569;color:#fff;}
.form-control:focus{background:#334155;color:#fff;border-color:#2563eb;box-shadow:0 0 0 2px rgba(37,99,235,.25);}
.form-control::placeholder{color:#94a3b8;}.btn-primary{background:#2563eb;border:none;padding:12px;font-weight:600;}
.btn-primary:hover{background:#1d4ed8;}.text-muted{color:#94a3b8!important;}
a{color:#60a5fa;}a:hover{color:#93c5fd;}
</style>
</head>
<body>
<div class="login-card">
    <div class="text-center mb-4">
        <i class="fas fa-user-circle fa-3x" style="color:#2563eb;"></i>
        <h3 class="mt-2">Portal Cliente</h3>
        <p class="text-muted">Accede a tus facturas y tickets</p>
    </div>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= hescape($error) ?></div><?php endif; ?>
    <form method="POST" action="controles/login.php">
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-envelope me-1"></i>Email o teléfono</label>
            <input type="text" name="usuario" class="form-control" placeholder="tu@email.com o teléfono" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-lock me-1"></i>Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="Tu contraseña" required>
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt me-2"></i>Ingresar</button>
    </form>
    <div class="text-center mt-3">
        <small class="text-muted">¿No tienes cuenta? <a href="registro.php">Solicitar acceso</a></small>
    </div>
</div>
</body>
</html>

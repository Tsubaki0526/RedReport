<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['movil_user'])) { header('Location: index.php'); exit; }

$error = $_GET['error'] ?? '';
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/seguridad.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta name="theme-color" content="#2563eb">
<link rel="manifest" href="manifest.json">
<link rel="icon" href="/RedReport/public/img/favicon.png" type="image/png">
<title>Ingresar - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body{background:#0f172a;display:flex;align-items:center;min-height:100vh;padding:20px}
.card{border:none;border-radius:16px;background:#1e293b;color:#fff;max-width:380px;margin:auto;width:100%;padding:32px 24px}
.card h4{color:#2563eb;font-weight:700}
.form-control{background:#334155;border:1.5px solid #475569;color:#fff;border-radius:10px;padding:12px 14px;font-size:15px}
.form-control:focus{background:#334155;color:#fff;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.2)}
.form-control::placeholder{color:#94a3b8}
.btn-primary{background:#2563eb;border:none;border-radius:10px;padding:14px;font-weight:600;font-size:15px}
.btn-primary:active{background:#1d4ed8}
.btn-outline-light{border-radius:10px;padding:14px;font-weight:600;font-size:15px}
.role-tab{display:flex;gap:8px;margin-bottom:20px}
.role-tab button{flex:1;padding:10px;border:2px solid #475569;border-radius:10px;background:transparent;color:#94a3b8;font-weight:600;font-size:14px;transition:all .15s}
.role-tab button.active{border-color:#2563eb;color:#fff;background:#2563eb}
.text-muted{color:#94a3b8!important}
a{color:#60a5fa}
</style>
</head>
<body>
<div class="card">
  <div class="text-center mb-3"><i class="fas fa-wifi fa-3x" style="color:#2563eb"></i><h4 class="mt-2"><?= APP_NAME ?></h4><p class="text-muted" style="font-size:14px">Acceso móvil</p></div>
  <?php if ($error): ?><div class="alert alert-danger py-2" style="font-size:14px;border-radius:8px"><?= hescape($error) ?></div><?php endif; ?>
  <form method="POST" action="controles/login.php">
    <div class="role-tab" id="roleTab">
      <button type="button" class="active" data-role="empleado" onclick="setRole('empleado')"><i class="fas fa-user-cog me-1"></i>Empleado</button>
      <button type="button" data-role="cliente" onclick="setRole('cliente')"><i class="fas fa-user me-1"></i>Cliente</button>
    </div>
    <input type="hidden" name="tipo" id="tipoInput" value="empleado">
    <div class="mb-3"><label class="form-label" style="font-size:13px"><i class="fas fa-envelope me-1"></i>Usuario</label><input type="text" name="usuario" class="form-control" placeholder="usuario o email" required autocomplete="username"></div>
    <div class="mb-3"><label class="form-label" style="font-size:13px"><i class="fas fa-lock me-1"></i>Contraseña</label><input type="password" name="password" class="form-control" placeholder="contraseña" required autocomplete="current-password"></div>
    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt me-2"></i>Ingresar</button>
  </form>
  <div class="text-center mt-3"><small class="text-muted">¿Cliente sin cuenta? <a href="../portal/registro.php">Solicitar acceso</a></small></div>
</div>
<script>
function setRole(r){document.querySelectorAll('.role-tab button').forEach(b=>b.classList.toggle('active',b.dataset.role===r));document.getElementById('tipoInput').value=r}
if ('serviceWorker' in navigator) { navigator.serviceWorker.register('sw.js'); }
</script>
</body>
</html>

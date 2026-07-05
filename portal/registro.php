<?php
session_start();
require_once '../app/config/conexion.php';
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = trim($_POST['documento'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $cliente = $pdo->prepare("SELECT * FROM tb_clientes WHERE documento=?");
    $cliente->execute([$documento]);
    $c = $cliente->fetch();
    if ($c) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE tb_clientes SET password=? WHERE id_cliente=?")->execute([$hash, $c['id_cliente']]);
        $mensaje = 'Contraseña creada. Ahora puedes iniciar sesión.';
    } else {
        $mensaje = 'Documento no encontrado. Contacta a soporte.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Solicitar acceso - Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>body{background:#0f172a;display:flex;align-items:center;min-height:100vh;}.card{background:#1e293b;border-radius:12px;padding:2rem;color:#fff;max-width:420px;margin:auto;}.form-control{background:#334155;border:1px solid #475569;color:#fff;}.form-control:focus{background:#334155;color:#fff;border-color:#2563eb;}</style>
</head>
<body>
<div class="card">
    <div class="text-center mb-4"><i class="fas fa-user-plus fa-3x" style="color:#2563eb;"></i><h3 class="mt-2">Activar acceso</h3></div>
    <?php if ($mensaje): ?><div class="alert alert-info"><?= hescape($mensaje) ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3"><label class="form-label">N° Documento</label><input type="text" name="documento" class="form-control" placeholder="Tu número de identificación" required></div>
        <div class="mb-3"><label class="form-label">Crear contraseña</label><input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres" required minlength="6"></div>
        <button type="submit" class="btn btn-primary w-100">Activar cuenta</button>
    </form>
    <div class="text-center mt-3"><a href="index.php" class="text-muted">Volver al login</a></div>
</div>
</body>
</html>

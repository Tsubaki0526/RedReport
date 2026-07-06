<?php
session_start();
if (!isset($_SESSION['portal_cliente'])) { header('Location: index.php'); exit; }
require_once '../app/config/conexion.php';
require_once '../app/config/seguridad.php';
$c = $_SESSION['portal_cliente'];
$id = $_GET['id'] ?? 0;

$factura = $pdo->prepare("SELECT * FROM tb_facturas WHERE id_factura=? AND id_cliente=?");
$factura->execute([$id, $c['id_cliente']]);
$factura = $factura->fetch(PDO::FETCH_ASSOC);
if (!$factura) { header('Location: dashboard.php'); exit; }

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $monto = floatval($_POST['monto'] ?? 0);
    $metodo = $_POST['metodo'] ?? 'Transferencia';
    $referencia = trim($_POST['referencia'] ?? '');

    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO tb_pagos (id_factura, monto, metodo_pago, referencia, id_usuario, fecha_pago) VALUES (?,?,?,?,1,NOW())");
    $stmt->execute([$id, $monto, $metodo, $referencia]);

    $stmt = $pdo->prepare("UPDATE tb_facturas SET estado='pagada', fecha_pago=NOW() WHERE id_factura=?");
    $stmt->execute([$id]);
    $pdo->commit();

    $mensaje = 'Pago registrado correctamente';
}
csrf_field();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pagar factura</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:500px;">
    <div class="card">
        <div class="card-header bg-success text-white"><h5 class="m-0"><i class="fas fa-credit-card me-2"></i>Pagar factura</h5></div>
        <div class="card-body">
            <p><strong>Factura:</strong> <?= hescape($factura['numero_factura']) ?></p>
            <p><strong>Total:</strong> $<?= number_format($factura['total'],0) ?></p>
            <?php if ($mensaje): ?><div class="alert alert-success"><?= $mensaje ?><br><a href="dashboard.php" class="alert-link">Volver al portal</a></div><?php endif; ?>
            <?php if (!$mensaje): ?>
            <form method="POST">
                <?php csrf_field(); ?>
                <div class="mb-3"><label class="form-label">Monto a pagar</label><input type="number" name="monto" step="0.01" class="form-control" value="<?= $factura['total'] ?>" required></div>
                <div class="mb-3"><label class="form-label">Método</label><select name="metodo" class="form-select">
                    <option>Transferencia</option><option>Efectivo</option><option>Tarjeta</option><option>Cheque</option>
                </select></div>
                <div class="mb-3"><label class="form-label">Referencia</label><input type="text" name="referencia" class="form-control" placeholder="N° transferencia o comprobante"></div>
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check-circle me-2"></i>Confirmar pago</button>
                <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Cancelar</a>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

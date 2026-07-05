<?php
session_start();
if (!isset($_SESSION['portal_cliente'])) { header('Location: index.php'); exit; }
require_once '../app/config/conexion.php';
$c = $_SESSION['portal_cliente'];

$facturas = $pdo->prepare("SELECT * FROM tb_facturas WHERE id_cliente=? ORDER BY fecha_emision DESC LIMIT 10");
$facturas->execute([$c['id_cliente']]);
$facturas = $facturas->fetchAll();

$tickets = $pdo->prepare("SELECT * FROM tb_tickets WHERE id_cliente=? ORDER BY fecha_creacion DESC LIMIT 5");
$tickets->execute([$c['id_cliente']]);
$tickets = $tickets->fetchAll();

$deuda = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM tb_facturas WHERE id_cliente=? AND estado IN ('pendiente','vencida')");
$deuda->execute([$c['id_cliente']]);
$deuda = $deuda->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mi Portal - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body{background:#f1f5f9;font-family:system-ui,sans-serif;}
.topbar{background:#0f172a;color:#fff;padding:12px 0;}
.topbar h5{margin:0;}.card{border:none;box-shadow:0 1px 3px rgba(0,0,0,.08);border-radius:10px;}
.stat-card{border-radius:10px;padding:20px;color:#fff;}
.stat-card.blue{background:linear-gradient(135deg,#2563eb,#1e40af);}
.stat-card.green{background:linear-gradient(135deg,#16a34a,#15803d);}
.stat-card.red{background:linear-gradient(135deg,#dc2626,#b91c1c);}
.stat-card.orange{background:linear-gradient(135deg,#f59e0b,#d97706);}
.badge-{font-size:11px;}
</style>
</head>
<body>
<div class="topbar">
    <div class="container d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-user-circle me-2"></i><?= hescape($c['nombre']) ?></h5>
        <div>
            <span class="badge bg-<?= $c['estado_servicio']=='Activo'?'success':($c['estado_servicio']=='Suspendido'?'warning text-dark':'danger') ?> me-2"><?= $c['estado_servicio'] ?></span>
            <a href="controles/cerrar.php" class="btn btn-sm btn-outline-light"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>
</div>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-4 mb-3"><div class="stat-card blue"><i class="fas fa-file-invoice fa-2x mb-2"></i><h6>Total facturas</h6><h3><?= count($facturas) ?></h3></div></div>
        <div class="col-md-4 mb-3"><div class="stat-card orange"><i class="fas fa-dollar-sign fa-2x mb-2"></i><h6>Deuda pendiente</h6><h3>$<?= number_format($deuda,0) ?></h3></div></div>
        <div class="col-md-4 mb-3"><div class="stat-card green"><i class="fas fa-headset fa-2x mb-2"></i><h6>Mis tickets</h6><h3><?= count($tickets) ?></h3></div></div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Mis facturas</h6>
            <a href="facturas.php" class="btn btn-sm btn-primary">Ver todas</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive"><table class="table table-sm mb-0">
                <thead><tr><th>Factura</th><th>Emisión</th><th>Vencimiento</th><th>Total</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($facturas as $f): ?>
                    <tr>
                        <td><?= hescape($f['numero_factura']) ?></td>
                        <td><?= $f['fecha_emision'] ?></td>
                        <td><?= $f['fecha_vencimiento'] ?></td>
                        <td>$<?= number_format($f['total'],0) ?></td>
                        <td><span class="badge bg-<?= ['pendiente'=>'warning','pagada'=>'success','vencida'=>'danger','anulada'=>'secondary'][$f['estado']]??'secondary' ?>"><?= $f['estado'] ?></span></td>
                        <td class="text-end">
                            <a href="../facturacion/pdf.php?id=<?= $f['id_factura'] ?>" class="btn btn-sm btn-outline-danger" target="_blank"><i class="fas fa-file-pdf"></i></a>
                            <?php if ($f['estado'] != 'pagada' && $f['estado'] != 'anulada'): ?>
                            <a href="pagar.php?id=<?= $f['id_factura'] ?>" class="btn btn-sm btn-success"><i class="fas fa-credit-card"></i> Pagar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($facturas)): ?><tr><td colspan="6" class="text-center text-muted py-3">Sin facturas</td></tr><?php endif; ?>
                </tbody>
            </table></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0"><i class="fas fa-headset me-2 text-info"></i>Mis tickets</h6>
            <a href="nuevo_ticket.php" class="btn btn-sm btn-info text-white"><i class="fas fa-plus"></i> Nuevo ticket</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive"><table class="table table-sm mb-0">
                <thead><tr><th>Ticket</th><th>Asunto</th><th>Categoría</th><th>Estado</th><th>Fecha</th></tr></thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= hescape($t['numero_ticket']) ?></td>
                        <td><?= hescape(mb_substr($t['asunto'],0,40)) ?></td>
                        <td><span class="badge bg-secondary"><?= $t['categoria'] ?></span></td>
                        <td><span class="badge bg-<?= ['Abierto'=>'warning','En Proceso'=>'info','Resuelto'=>'success','Cerrado'=>'secondary'][$t['estado']]??'secondary' ?>"><?= $t['estado'] ?></span></td>
                        <td><?= $t['fecha_creacion'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tickets)): ?><tr><td colspan="5" class="text-center text-muted py-3">Sin tickets</td></tr><?php endif; ?>
                </tbody>
            </table></div>
        </div>
    </div>
</div>
</body>
</html>

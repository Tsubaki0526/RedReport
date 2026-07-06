<?php
$id_cliente = $movil_user['id'];

$facturas = $pdo->prepare("SELECT * FROM tb_facturas WHERE id_cliente=? ORDER BY fecha_emision DESC LIMIT 5");
$facturas->execute([$id_cliente]);
$facturas = $facturas->fetchAll();

$tickets = $pdo->prepare("SELECT * FROM tb_tickets WHERE id_cliente=? ORDER BY fecha_creacion DESC LIMIT 5");
$tickets->execute([$id_cliente]);
$tickets = $tickets->fetchAll();

$deuda = $pdo->prepare("SELECT COALESCE(SUM(total),0) FROM tb_facturas WHERE id_cliente=? AND estado IN ('pendiente','vencida')");
$deuda->execute([$id_cliente]);
$deuda = $deuda->fetchColumn();

$pendientes = $pdo->prepare("SELECT COUNT(*) FROM tb_facturas WHERE id_cliente=? AND estado IN ('pendiente','vencida')");
$pendientes->execute([$id_cliente]);
$pendientes = (int)$pendientes->fetchColumn();
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-user-circle me-2"></i>Mi Portal</h6><span class="sub"><?= hescape($movil_user['nombre']) ?></span></div>
    <span class="badge bg-<?= $movil_user['estado_servicio']=='Activo'?'success':'warning' ?>" style="padding:6px 12px;border-radius:20px"><?= $movil_user['estado_servicio'] ?></span>
  </div>
</div>

<div class="container-fluid px-3 py-3">
  <div class="row g-2 mb-3">
    <div class="col-6"><div class="card p-3 text-center"><div class="card-value text-primary"><?= count($facturas) ?></div><div class="card-title">Facturas</div></div></div>
    <div class="col-6"><div class="card p-3 text-center"><div class="card-value text-danger">$<?= number_format($deuda,0) ?></div><div class="card-title">Deuda</div></div></div>
  </div>

  <div class="d-grid gap-2 mb-3">
    <a href="cliente/facturas.php" class="btn btn-primary btn-mobile"><i class="fas fa-file-invoice me-2"></i>Ver Facturas</a>
    <a href="cliente/tickets.php" class="btn btn-info btn-mobile text-white"><i class="fas fa-headset me-2"></i>Soporte Técnico</a>
    <a href="cliente/ticket_nuevo.php" class="btn btn-outline-danger btn-mobile"><i class="fas fa-exclamation-triangle me-2"></i>Reportar Falla</a>
  </div>

  <?php if ($pendientes > 0): ?>
  <div class="alert alert-warning alert-mobile">
    <i class="fas fa-exclamation-circle me-2"></i>Tienes <strong><?= $pendientes ?></strong> factura(s) pendiente(s) por $<?= number_format($deuda,0) ?>
  </div>
  <?php endif; ?>

  <h6 style="font-size:13px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px"><i class="fas fa-file-invoice me-1"></i>Últimas facturas</h6>
  <?php if (empty($facturas)): ?>
  <div class="empty-state"><i class="fas fa-file-invoice"></i><p>Sin facturas</p></div>
  <?php else: ?>
  <div class="card p-0">
    <?php foreach ($facturas as $f): ?>
    <div class="list-item">
      <div class="icon" style="background:#dbeafe;color:#2563eb"><i class="fas fa-file-invoice"></i></div>
      <div class="body">
        <div class="title"><?= hescape($f['numero_factura']) ?></div>
        <div class="sub"><?= $f['fecha_emision'] ?> · $<?= number_format($f['total'],0) ?></div>
      </div>
      <div class="right">
        <span class="badge bg-<?= ['pendiente'=>'warning','pagada'=>'success','vencida'=>'danger','anulada'=>'secondary'][$f['estado']]??'secondary' ?>"><?= $f['estado'] ?></span>
        <?php if ($f['estado'] !== 'pagada' && $f['estado'] !== 'anulada'): ?>
        <a href="../portal/pagar.php?id=<?= $f['id_factura'] ?>" class="btn btn-sm btn-success mt-1" style="border-radius:20px">Pagar</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <h6 style="font-size:13px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin:16px 0 8px"><i class="fas fa-headset me-1"></i>Tickets recientes</h6>
  <?php if (empty($tickets)): ?>
  <div class="empty-state"><i class="fas fa-headset"></i><p>Sin tickets</p></div>
  <?php else: ?>
  <div class="card p-0">
    <?php foreach ($tickets as $t): ?>
    <div class="list-item">
      <div class="icon" style="background:#fef3c7;color:#d97706"><i class="fas fa-ticket"></i></div>
      <div class="body">
        <div class="title"><?= hescape($t['asunto']) ?></div>
        <div class="sub"><?= $t['numero_ticket'] ?> · <?= $t['fecha_creacion'] ?></div>
      </div>
      <div class="right"><span class="badge bg-<?= ['Abierto'=>'warning','En Proceso'=>'info','Resuelto'=>'success','Cerrado'=>'secondary'][$t['estado']]??'secondary' ?>"><?= $t['estado'] ?></span></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

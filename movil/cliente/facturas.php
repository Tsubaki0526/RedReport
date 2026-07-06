<?php
$titulo = 'Facturas';
$seccion = 'facturas';
require_once __DIR__ . '/../header.php';
if (!$movil_user || !$es_cliente) { header('Location: ../login.php'); exit; }

$id_cliente = $movil_user['id'];
$stmt = $pdo->prepare("SELECT * FROM tb_facturas WHERE id_cliente=? ORDER BY fecha_emision DESC");
$stmt->execute([$id_cliente]);
$facturas = $stmt->fetchAll();
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-file-invoice me-2"></i>Facturas</h6></div>
    <a href="/RedReport/movil/index.php" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left"></i></a>
  </div>
</div>

<div class="container-fluid px-3 py-3">
  <?php if (empty($facturas)): ?>
  <div class="empty-state"><i class="fas fa-file-invoice"></i><p>No tienes facturas registradas</p></div>
  <?php else: ?>
  <div class="card p-0">
    <?php foreach ($facturas as $f): ?>
    <div class="list-item">
      <div class="icon" style="background:#dbeafe;color:#2563eb"><i class="fas fa-file-invoice"></i></div>
      <div class="body">
        <div class="title"><?= hescape($f['numero_factura']) ?></div>
        <div class="sub"><?= $f['fecha_emision'] ?> · Vence: <?= $f['fecha_vencimiento'] ?></div>
        <div class="sub" style="font-size:15px;font-weight:600;color:var(--text);margin-top:4px">$<?= number_format($f['total'],0) ?></div>
      </div>
      <div class="right">
        <span class="badge bg-<?= ['pendiente'=>'warning','pagada'=>'success','vencida'=>'danger','anulada'=>'secondary'][$f['estado']]??'secondary' ?>" style="font-size:12px"><?= $f['estado'] ?></span>
        <a href="../../facturacion/pdf.php?id=<?= $f['id_factura'] ?>" class="btn btn-sm btn-outline-danger mt-1" style="border-radius:20px" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
        <?php if ($f['estado'] !== 'pagada' && $f['estado'] !== 'anulada'): ?>
        <a href="../../portal/pagar.php?id=<?= $f['id_factura'] ?>" class="btn btn-sm btn-success mt-1" style="border-radius:20px">Pagar ahora</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>

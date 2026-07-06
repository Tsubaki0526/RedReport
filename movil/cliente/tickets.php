<?php
$titulo = 'Soporte';
$seccion = 'tickets';
require_once __DIR__ . '/../header.php';
if (!$movil_user || !$es_cliente) { header('Location: ../login.php'); exit; }

$id_cliente = $movil_user['id'];
$stmt = $pdo->prepare("SELECT * FROM tb_tickets WHERE id_cliente=? ORDER BY fecha_creacion DESC");
$stmt->execute([$id_cliente]);
$tickets = $stmt->fetchAll();
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-headset me-2"></i>Soporte</h6></div>
    <a href="/RedReport/movil/index.php" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left"></i></a>
  </div>
</div>

<div class="container-fluid px-3 py-3">
  <a href="ticket_nuevo.php" class="btn btn-danger btn-mobile w-100 mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Reportar una falla</a>

  <?php if (empty($tickets)): ?>
  <div class="empty-state"><i class="fas fa-ticket"></i><p>No has creado tickets de soporte</p></div>
  <?php else: ?>
  <h6 style="font-size:13px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Mis tickets</h6>
  <div class="card p-0">
    <?php foreach ($tickets as $t): ?>
    <div class="list-item">
      <div class="icon" style="background:<?= match($t['estado']){'Abierto'=>'#fef3c7','En Proceso'=>'#dbeafe','Resuelto'=>'#d1fae5','Cerrado'=>'#e2e8f0',default=>'#e2e8f0'} ?>;color:<?= match($t['estado']){'Abierto'=>'#d97706','En Proceso'=>'#2563eb','Resuelto'=>'#16a34a','Cerrado'=>'#64748b',default=>'#64748b'} ?>"><i class="fas fa-ticket-alt"></i></div>
      <div class="body">
        <div class="title"><?= hescape($t['asunto']) ?></div>
        <div class="sub"><?= $t['numero_ticket'] ?> · <?= $t['categoria'] ?> · <?= $t['fecha_creacion'] ?></div>
      </div>
      <div class="right"><span class="badge bg-<?= ['Abierto'=>'warning','En Proceso'=>'info','Resuelto'=>'success','Cerrado'=>'secondary'][$t['estado']]??'secondary' ?>"><?= $t['estado'] ?></span></div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>

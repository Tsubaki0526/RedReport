<?php
$id_tecnico = $movil_user['id'];
$hoy = date('Y-m-d');

$st1 = $pdo->prepare("SELECT COUNT(*) FROM tb_ordenes WHERE id_tecnico=? AND DATE(fecha_asignacion)=?");
$st1->execute([$id_tecnico, $hoy]);
$ordenes_hoy = (int)$st1->fetchColumn();

$st2 = $pdo->prepare("SELECT COUNT(*) FROM tb_ordenes WHERE id_tecnico=? AND estado IN ('Abierta','En Proceso')");
$st2->execute([$id_tecnico]);
$pendientes = (int)$st2->fetchColumn();

$st3 = $pdo->prepare("SELECT COUNT(*) FROM tb_clientes WHERE id_instalador=? AND DATE(fecha_instalacion)=?");
$st3->execute([$id_tecnico, $hoy]);
$instalaciones_hoy = (int)$st3->fetchColumn();

// Recent orders
$stmt = $pdo->prepare("SELECT o.*, c.nombre AS cliente_nombre FROM tb_ordenes o INNER JOIN tb_clientes c ON o.id_cliente=c.id_cliente WHERE o.id_tecnico=? ORDER BY o.fecha_asignacion DESC LIMIT 10");
$stmt->execute([$id_tecnico]);
$ordenes = $stmt->fetchAll();
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-tools me-2"></i>Instalador</h6><span class="sub"><?= hescape($movil_user['nombre']) ?></span></div>
    <span class="badge bg-primary" style="padding:6px 12px;border-radius:20px"><?= date('d/m') ?></span>
  </div>
</div>
<div class="container-fluid px-3 py-3">
  <div class="row g-2 mb-3">
    <div class="col-4"><div class="card p-3 text-center"><div class="card-value text-primary"><?= $ordenes_hoy ?></div><div class="card-title">Hoy</div></div></div>
    <div class="col-4"><div class="card p-3 text-center"><div class="card-value text-warning"><?= $pendientes ?></div><div class="card-title">Pendientes</div></div></div>
    <div class="col-4"><div class="card p-3 text-center"><div class="card-value text-success"><?= $instalaciones_hoy ?></div><div class="card-title">Instalaciones</div></div></div>
  </div>

  <div class="d-grid gap-2 mb-3">
    <a href="instalador/instalacion.php" class="btn btn-primary btn-mobile"><i class="fas fa-plus-circle me-2"></i>Nueva Instalación</a>
    <a href="instalador/ordenes.php" class="btn btn-outline-primary btn-mobile"><i class="fas fa-clipboard-list me-2"></i>Mis Órdenes</a>
  </div>

  <h6 style="font-size:13px;color:var(--text-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px"><i class="fas fa-history me-1"></i>Órdenes recientes</h6>
  <?php if (empty($ordenes)): ?>
  <div class="empty-state"><i class="fas fa-inbox"></i><p>Sin órdenes asignadas</p></div>
  <?php else: ?>
  <div class="card p-0">
    <?php foreach ($ordenes as $o): ?>
    <a href="instalador/orden_editar.php?id=<?= $o['id_orden'] ?>" class="list-item">
      <div class="icon" style="background:<?= match($o['tipo']){'Instalacion'=>'#dbeafe','Soporte'=>'#fef3c7','Mantenimiento'=>'#d1fae5',default=>'#e2e8f0'} ?>;color:<?= match($o['tipo']){'Instalacion'=>'#2563eb','Soporte'=>'#d97706','Mantenimiento'=>'#16a34a',default=>'#64748b'} ?>"><i class="fas fa-<?= match($o['tipo']){'Instalacion'=>'wifi','Soporte'=>'headset','Mantenimiento'=>'tools',default=>'clipboard'} ?>"></i></div>
      <div class="body">
        <div class="title"><?= hescape($o['cliente_nombre']) ?></div>
        <div class="sub"><?= $o['numero_orden'] ?> · <?= $o['tipo'] ?></div>
      </div>
      <div class="right"><span class="badge bg-<?= match($o['estado']){'Abierta'=>'warning','En Proceso'=>'info','Completada'=>'success','Cancelada'=>'secondary',default=>'secondary'} ?>"><?= $o['estado'] ?></span></div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

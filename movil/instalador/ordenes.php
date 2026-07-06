<?php
$titulo = 'Mis Órdenes';
$seccion = 'ordenes';
require_once __DIR__ . '/../header.php';
if (!$movil_user || !$es_empleado) { header('Location: ../login.php'); exit; }

$filtro = $_GET['estado'] ?? 'todas';
$id_tecnico = $movil_user['id'];

$sql = "SELECT o.*, c.nombre AS cliente_nombre FROM tb_ordenes o INNER JOIN tb_clientes c ON o.id_cliente=c.id_cliente WHERE o.id_tecnico=?";
$params = [$id_tecnico];
if ($filtro !== 'todas') { $sql .= " AND o.estado=?"; $params[] = $filtro; }
$sql .= " ORDER BY o.fecha_asignacion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ordenes = $stmt->fetchAll();

$estados = ['todas'=>'Todas','Abierta'=>'Abierta','En Proceso'=>'En Proceso','Completada'=>'Completada','Cancelada'=>'Cancelada'];
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-clipboard-list me-2"></i>Órdenes</h6></div>
    <a href="/RedReport/movil/index.php" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left"></i></a>
  </div>
</div>

<div class="container-fluid px-3 py-3">
  <div class="d-flex gap-1 mb-3" style="overflow-x:auto">
    <?php foreach ($estados as $k => $v): ?>
    <a href="?estado=<?= $k ?>" class="btn btn-sm <?= $filtro===$k?'btn-primary':'btn-outline-secondary' ?>" style="border-radius:20px;white-space:nowrap"><?= $v ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($ordenes)): ?>
  <div class="empty-state"><i class="fas fa-inbox"></i><p>Sin órdenes</p></div>
  <?php else: ?>
  <div class="card p-0">
    <?php foreach ($ordenes as $o): ?>
    <a href="orden_editar.php?id=<?= $o['id_orden'] ?>" class="list-item">
      <div class="icon" style="background:<?= match($o['tipo']){'Instalacion'=>'#dbeafe','Soporte'=>'#fef3c7','Mantenimiento'=>'#d1fae5',default=>'#e2e8f0'} ?>;color:<?= match($o['tipo']){'Instalacion'=>'#2563eb','Soporte'=>'#d97706','Mantenimiento'=>'#16a34a',default=>'#64748b'} ?>"><i class="fas fa-<?= match($o['tipo']){'Instalacion'=>'wifi','Soporte'=>'headset','Mantenimiento'=>'tools',default=>'clipboard'} ?>"></i></div>
      <div class="body">
        <div class="title"><?= hescape($o['cliente_nombre']) ?></div>
        <div class="sub"><?= $o['numero_orden'] ?> · <?= $o['tipo'] ?> · <?= $o['fecha_asignacion'] ?></div>
      </div>
      <div class="right"><span class="badge bg-<?= match($o['estado']){'Abierta'=>'warning','En Proceso'=>'info','Completada'=>'success','Cancelada'=>'secondary',default=>'secondary'} ?>"><?= $o['estado'] ?></span></div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>

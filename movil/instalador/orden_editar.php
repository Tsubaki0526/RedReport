<?php
$titulo = 'Editar Orden';
require_once __DIR__ . '/../header.php';
if (!$movil_user || !$es_empleado) { header('Location: ../login.php'); exit; }

$id_orden = intval($_GET['id'] ?? 0);
$id_tecnico = $movil_user['id'];

$stmt = $pdo->prepare("SELECT o.*, c.nombre AS cliente_nombre, c.direccion, c.telefono FROM tb_ordenes o INNER JOIN tb_clientes c ON o.id_cliente=c.id_cliente WHERE o.id_orden=? AND o.id_tecnico=?");
$stmt->execute([$id_orden, $id_tecnico]);
$orden = $stmt->fetch();

if (!$orden) { echo "<script>alert('Orden no encontrada');window.location='ordenes.php';</script>"; exit; }

$success = $_GET['success'] ?? '';
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-edit me-2"></i>Orden #<?= hescape($orden['numero_orden']) ?></h6></div>
    <a href="ordenes.php" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left"></i></a>
  </div>
</div>

<div class="container-fluid px-3 py-3">
  <?php if ($success): ?><div class="alert alert-success alert-mobile"><?= hescape($success) ?></div><?php endif; ?>

  <div class="card p-3">
    <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:8px"><i class="fas fa-user me-1"></i>Cliente</h6>
    <p class="mb-1" style="font-weight:600"><?= hescape($orden['cliente_nombre']) ?></p>
    <p class="mb-1" style="font-size:13px;color:var(--text-muted)"><i class="fas fa-map-marker-alt me-1"></i><?= hescape($orden['direccion'] ?? '-') ?></p>
    <p class="mb-0" style="font-size:13px;color:var(--text-muted)"><i class="fas fa-phone me-1"></i><?= hescape($orden['telefono'] ?? '-') ?></p>
  </div>

  <div class="card p-3">
    <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:8px"><i class="fas fa-info-circle me-1"></i>Detalle</h6>
    <p style="font-size:14px"><?= hescape($orden['descripcion'] ?? 'Sin descripción') ?></p>
    <div class="mb-1"><span class="badge bg-<?= match($orden['tipo']){'Instalacion'=>'primary','Soporte'=>'warning','Mantenimiento'=>'success','Retiro'=>'danger',default=>'secondary'} ?>"><?= $orden['tipo'] ?></span> <span class="badge bg-<?= match($orden['prioridad']){'Baja'=>'success','Media'=>'warning','Alta'=>'danger','Urgente'=>'danger',default=>'secondary'} ?>"><?= $orden['prioridad'] ?></span> <span class="badge bg-<?= match($orden['estado']){'Abierta'=>'warning','En Proceso'=>'info','Completada'=>'success','Cancelada'=>'secondary',default=>'secondary'} ?>"><?= $orden['estado'] ?></span></div>
    <p style="font-size:12px;color:var(--text-muted);margin-bottom:0">Asignada: <?= $orden['fecha_asignacion'] ?></p>
  </div>

  <?php if ($orden['estado'] !== 'Completada' && $orden['estado'] !== 'Cancelada'): ?>
  <form method="POST" action="orden_guardar.php">
    <input type="hidden" name="_csrf_token" value="<?= hescape($_SESSION['_csrf_token'] ?? '') ?>">
    <input type="hidden" name="id_orden" value="<?= $id_orden ?>">

    <div class="card p-3">
      <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:8px"><i class="fas fa-check-circle me-1"></i>Actualizar estado</h6>
      <div class="mb-2">
        <select name="estado" class="form-select form-control-mobile">
          <option value="En Proceso" <?= $orden['estado']==='En Proceso'?'selected':'' ?>>En Proceso</option>
          <option value="Completada">Completada</option>
          <option value="Cancelada">Cancelada</option>
        </select>
      </div>
      <div class="mb-2"><textarea name="solucion" class="form-control form-control-mobile" rows="3" placeholder="Solución / Notas..."><?= hescape($orden['solucion'] ?? '') ?></textarea></div>
      <button type="submit" class="btn btn-success btn-mobile w-100"><i class="fas fa-save me-2"></i>Guardar cambios</button>
    </div>
  </form>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>

<?php
require_once __DIR__ . '/../app/config/conexion.php';
require_once __DIR__ . '/../app/config/seguridad.php';
verificar_sesion($_SESSION['id_rol'] ?? 0, [1]);

$filtro_usuario = $_GET['usuario'] ?? '';
$filtro_accion  = $_GET['accion'] ?? '';
$filtro_tabla   = $_GET['tabla'] ?? '';
$filtro_desde   = $_GET['desde'] ?? '';
$filtro_hasta   = $_GET['hasta'] ?? '';

$where = []; $params = [];
if ($filtro_usuario) { $where[] = 'u.nombre LIKE ?'; $params[] = "%$filtro_usuario%"; }
if ($filtro_accion)  { $where[] = 'b.accion LIKE ?';  $params[] = "%$filtro_accion%"; }
if ($filtro_tabla)   { $where[] = 'b.tabla_afectada LIKE ?'; $params[] = "%$filtro_tabla%"; }
if ($filtro_desde)   { $where[] = 'b.fecha_hora >= ?'; $params[] = $filtro_desde . ' 00:00:00'; }
if ($filtro_hasta)   { $where[] = 'b.fecha_hora <= ?'; $params[] = $filtro_hasta . ' 23:59:59'; }

$sql = "SELECT b.*, u.nombre AS nombre_usuario FROM tb_bitacora b LEFT JOIN tb_usuarios u ON b.id_usuario = u.id_usuario";
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY b.fecha_hora DESC LIMIT 1000';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registros = $stmt->fetchAll();
?>
<?php include __DIR__ . '/../parte1.php'; ?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6"><h1><i class="fas fa-history me-2"></i>Auditoría del Sistema</h1></div>
      <div class="col-sm-6 text-end"><small class="text-muted">Últimos 1000 registros</small></div>
    </div>
  </div>
</div>
<div class="content">
  <div class="card">
    <div class="card-header">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-2"><label class="form-label">Usuario</label><input type="text" name="usuario" class="form-control form-control-sm" value="<?=hescape($filtro_usuario)?>"></div>
        <div class="col-md-2"><label class="form-label">Acción</label><input type="text" name="accion" class="form-control form-control-sm" value="<?=hescape($filtro_accion)?>"></div>
        <div class="col-md-2"><label class="form-label">Tabla</label><input type="text" name="tabla" class="form-control form-control-sm" value="<?=hescape($filtro_tabla)?>"></div>
        <div class="col-md-2"><label class="form-label">Desde</label><input type="date" name="desde" class="form-control form-control-sm" value="<?=hescape($filtro_desde)?>"></div>
        <div class="col-md-2"><label class="form-label">Hasta</label><input type="date" name="hasta" class="form-control form-control-sm" value="<?=hescape($filtro_hasta)?>"></div>
        <div class="col-md-2 d-flex gap-1"><button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-filter"></i> Filtrar</button><a href="index.php" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i></a></div>
      </form>
    </div>
    <div class="card-body p-0">
      <div class="table-container">
      <table class="table table-sm mb-0" id="tablaAuditoria">
        <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Tabla</th><th>ID</th><th>Detalle</th><th>IP</th></tr></thead>
        <tbody>
          <?php foreach ($registros as $r): ?>
          <tr>
            <td class="text-nowrap"><?=hescape($r['fecha_hora'])?></td>
            <td><?=hescape($r['nombre_usuario'] ?: ('ID ' . $r['id_usuario']))?></td>
            <td><span class="badge bg-<?=$r['accion']==='ELIMINAR'?'danger':($r['accion']==='INSERTAR'?'success':($r['accion']==='ACTUALIZAR'?'warning':'info'))?>"><?=hescape($r['accion'])?></span></td>
            <td><?=hescape($r['tabla_afectada'])?></td>
            <td><?=hescape($r['id_registro_afectado'])?></td>
            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?=hescape($r['detalle'])?>"><?=hescape($r['detalle'])?></td>
            <td class="text-nowrap"><code><?=hescape($r['direccion_ip'])?></code></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($registros)): ?><tr><td colspan="7" class="text-center text-muted py-3">Sin registros</td></tr><?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../parte2.php'; ?>
<script>
$('#tablaAuditoria').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
    order: [[0, 'desc']],
    pageLength: 25,
    responsive: true,
    autoWidth: false,
    columnDefs: [{ orderable: false, targets: [1,2,3,4,5,6] }]
});
</script>

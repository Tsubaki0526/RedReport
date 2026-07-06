<?php
include('../../sesion.php');
verificar_acceso([1, 2]);
include('../../parte1.php');
require_once '../../app/config/conexion.php';

$id_cliente = (int)($_GET['id'] ?? 0);
$cliente = $pdo->prepare("SELECT c.*, u.nombre AS instalador_nombre FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario WHERE c.id_cliente = :id");
$cliente->execute([':id' => $id_cliente]);
$cliente = $cliente->fetch();
if (!$cliente) { echo "<script>alert('Cliente no encontrado');window.location='lista.php';</script>"; exit; }

$contratos = $pdo->prepare("SELECT co.*, p.nombre AS plan_nombre, p.precio FROM tb_contratos co INNER JOIN tb_planes p ON co.id_plan = p.id_plan WHERE co.id_cliente = :id ORDER BY co.id_contrato DESC");
$contratos->execute([':id' => $id_cliente]);
$contratos = $contratos->fetchAll();

$facturas = $pdo->prepare("SELECT * FROM tb_facturas WHERE id_cliente = :id ORDER BY id_factura DESC");
$facturas->execute([':id' => $id_cliente]);
$facturas = $facturas->fetchAll();

$ventas = $pdo->prepare("SELECT v.*, u.nombre AS vendedor_nombre FROM tb_ventas v LEFT JOIN tb_usuarios u ON v.id_vendedor = u.id_usuario WHERE v.id_cliente = :id ORDER BY v.fecha DESC");
$ventas->execute([':id' => $id_cliente]);
$ventas = $ventas->fetchAll();

$equipos = $pdo->prepare("SELECT e.*, t.nombre AS tipo_nombre FROM tb_equipos e INNER JOIN tb_tipos_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo WHERE e.id_cliente = :id");
$equipos->execute([':id' => $id_cliente]);
$equipos = $equipos->fetchAll();

$total_pagado = $pdo->prepare("SELECT COALESCE(SUM(total),0) AS total FROM tb_facturas WHERE id_cliente = :id AND estado = 'pagada'");
$total_pagado->execute([':id' => $id_cliente]);
$total_pagado = $total_pagado->fetch()['total'];

$adeudado = $pdo->prepare("SELECT COALESCE(SUM(total),0) AS total FROM tb_facturas WHERE id_cliente = :id AND estado IN ('pendiente','vencida')");
$adeudado->execute([':id' => $id_cliente]);
$adeudado = $adeudado->fetch()['total'];

// Actividad reciente (timeline)
$timeline = [];
$stmt = $pdo->prepare("SELECT 'factura' AS tipo, CONCAT('Factura ', numero_factura) AS titulo, CONCAT('$', FORMAT(total, 0)) AS detalle, fecha_emision AS fecha, CASE WHEN estado='pagada' THEN 'success' WHEN estado='anulada' THEN 'danger' ELSE 'primary' END AS color, estado AS estado FROM tb_facturas WHERE id_cliente = :id");
$stmt->execute([':id' => $id_cliente]);
$timeline = array_merge($timeline, $stmt->fetchAll());
$stmt = $pdo->prepare("SELECT 'pago' AS tipo, CONCAT('Pago - ', p.metodo_pago) AS titulo, CONCAT('$', FORMAT(p.monto, 0)) AS detalle, p.fecha_pago AS fecha, 'success' AS color, p.metodo_pago AS estado FROM tb_pagos p INNER JOIN tb_facturas f ON p.id_factura = f.id_factura WHERE f.id_cliente = :id");
$stmt->execute([':id' => $id_cliente]);
$timeline = array_merge($timeline, $stmt->fetchAll());
$stmt = $pdo->prepare("SELECT 'ticket' AS tipo, CONCAT('Ticket: ', asunto) AS titulo, '' AS detalle, fecha_creacion AS fecha, CASE WHEN estado='resuelto' THEN 'success' WHEN estado='cerrado' THEN 'secondary' ELSE 'warning' END AS color, estado AS estado FROM tb_tickets WHERE id_cliente = :id");
$stmt->execute([':id' => $id_cliente]);
$timeline = array_merge($timeline, $stmt->fetchAll());
$stmt = $pdo->prepare("SELECT 'orden' AS tipo, CONCAT('Orden #', id_orden, ': ', LEFT(descripcion, 80)) AS titulo, '' AS detalle, fecha_creacion AS fecha, CASE WHEN estado='completada' THEN 'success' WHEN estado='cancelada' THEN 'danger' ELSE 'info' END AS color, estado AS estado FROM tb_ordenes WHERE id_cliente = :id");
$stmt->execute([':id' => $id_cliente]);
$timeline = array_merge($timeline, $stmt->fetchAll());
usort($timeline, function($a, $b) { return strtotime($b['fecha']) - strtotime($a['fecha']); });
$timeline = array_slice($timeline, 0, 20);
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-user-circle me-2 text-primary"></i><?= hescape($cliente['nombre']) ?></h1>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="registrar.php?id=<?= $id_cliente ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                    <a href="lista.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-3 col-6">
                    <div class="info-box bg-info"><div class="info-box-content"><span class="info-box-text">Total pagado</span><span class="info-box-number">$<?= number_format($total_pagado, 0) ?></span></div></div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box bg-danger"><div class="info-box-content"><span class="info-box-text">Adeudado</span><span class="info-box-number">$<?= number_format($adeudado, 0) ?></span></div></div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box bg-success"><div class="info-box-content"><span class="info-box-text">Contratos</span><span class="info-box-number"><?= count($contratos) ?></span></div></div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box bg-warning"><div class="info-box-content"><span class="info-box-text">Equipos</span><span class="info-box-number"><?= count($equipos) ?></span></div></div>
                </div>
            </div>

            <div class="row">
                <!-- Datos del cliente -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-info-circle me-2 text-primary"></i>Datos generales</h3></div>
                        <div class="card-body">
                            <div class="table-container">
                            <table class="table table-sm">
                                <tr><th>Documento</th><td><?= hescape($cliente['documento']) ?></td></tr>
                                <tr><th>Teléfono</th><td><?= hescape($cliente['telefono'] ?? '-') ?></td></tr>
                                <tr><th>Dirección</th><td><?= hescape($cliente['direccion'] ?? '-') ?></td></tr>
                                <tr><th>Email</th><td><?= hescape($cliente['email'] ?? '-') ?></td></tr>
                                <tr><th>Estado servicio</th>
                                    <td>
                                        <span class="badge bg-<?= $cliente['estado_servicio'] == 'Activo' ? 'success' : ($cliente['estado_servicio'] == 'Suspendido' ? 'warning text-dark' : 'danger') ?>">
                                            <?= $cliente['estado_servicio'] ?>
                                        </span>
                                        <?php if ($_SESSION['id_rol'] == 1): ?>
                                        <button class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#estadoModal"><i class="fas fa-edit"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr><th>Instalador</th><td><?= hescape($cliente['instalador_nombre'] ?? 'Sin asignar') ?></td></tr>
                                <tr><th>Instalación</th><td><?= $cliente['fecha_instalacion'] ? date('d/m/Y', strtotime($cliente['fecha_instalacion'])) : 'Pendiente' ?></td></tr>
                            </table>
                            </div>
                        </div>
                    </div>
                    <?php if ($cliente['lat'] && $cliente['lng']): ?>
                    <div class="card"><div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Ubicación</h3></div>
                        <div class="card-body p-0"><div id="miniMap" style="height:200px;"></div></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Contratos -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-file-contract me-2 text-primary"></i>Contratos</h3></div>
                        <div class="card-body p-0">
                            <div class="table-wrap">
                                <table class="table table-sm table-hover mb-0">
                                    <thead><tr><th>#</th><th>Plan</th><th>Valor</th><th>Inicio</th><th>Fin</th><th>Estado</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($contratos as $co): ?>
                                        <tr>
                                            <td><?= $co['id_contrato'] ?></td>
                                            <td><?= hescape($co['plan_nombre']) ?></td>
                                            <td>$<?= number_format($co['precio'], 0) ?></td>
                                            <td><?= date('d/m/Y', strtotime($co['fecha_inicio'])) ?></td>
                                            <td><?= $co['fecha_fin'] ? date('d/m/Y', strtotime($co['fecha_fin'])) : '-' ?></td>
                                            <td><span class="badge bg-<?= $co['estado'] == 'activo' ? 'success' : 'secondary' ?>"><?= ucfirst($co['estado']) ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($contratos)): ?><tr><td colspan="6" class="text-muted text-center">Sin contratos</td></tr><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Facturas -->
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-file-invoice me-2 text-primary"></i>Facturas</h3></div>
                        <div class="card-body p-0">
                            <div class="table-wrap">
                                <table class="table table-sm table-hover mb-0">
                                    <thead><tr><th>#</th><th>Emision</th><th>Vencimiento</th><th>Total</th><th>Estado</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($facturas as $f): ?>
                                        <tr>
                                            <td><a href="<?= $url ?>facturacion/ver.php?id=<?= $f['id_factura'] ?>"><?= hescape($f['numero_factura']) ?></a></td>
                                            <td><?= date('d/m/Y', strtotime($f['fecha_emision'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($f['fecha_vencimiento'])) ?></td>
                                            <td>$<?= number_format($f['total'], 0) ?></td>
                                            <td><span class="badge bg-<?= $f['estado'] == 'pagada' ? 'success' : ($f['estado'] == 'pendiente' ? 'warning text-dark' : ($f['estado'] == 'vencida' ? 'danger' : 'secondary')) ?>"><?= ucfirst($f['estado']) ?></span></td>
                                            <td><?= $f['fecha_pago'] ? date('d/m/Y', strtotime($f['fecha_pago'])) : '-' ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($facturas)): ?><tr><td colspan="6" class="text-muted text-center">Sin facturas</td></tr><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Equipos -->
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-microchip me-2 text-primary"></i>Equipos asignados</h3></div>
                        <div class="card-body p-0">
                            <div class="table-wrap">
                                <table class="table table-sm table-hover mb-0">
                                    <thead><tr><th>Serial</th><th>Tipo</th><th>Marca</th><th>Estado</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($equipos as $eq): ?>
                                        <tr>
                                            <td><?= hescape($eq['tipo_nombre']) ?></td>
                                            <td><?= hescape($eq['serial']) ?></td>
                                            <td><?= hescape($eq['marca'] ?? '-') ?></td>
                                            <td><span class="badge bg-info"><?= $eq['estado'] ?></span></td>
                                            <td><?= date('d/m/Y', strtotime($eq['fecha_asignado'])) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($equipos)): ?><tr><td colspan="5" class="text-muted text-center">Sin equipos asignados</td></tr><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Fotos -->
                    <?php
                    $fotos = $pdo->prepare("SELECT * FROM tb_instalacion_fotos WHERE id_cliente = ? ORDER BY fecha_subida DESC");
                    $fotos->execute([$id_cliente]);
                    $fotos = $fotos->fetchAll();
                    if (!empty($fotos)): ?>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-images me-2 text-success"></i>Fotos de instalacion</h3></div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($fotos as $f): ?>
                                <div class="col-4 col-md-3 mb-3">
                                    <a href="<?= $url ?>public/uploads/<?= hescape($f['nombre_archivo']) ?>" target="_blank">
                                        <img src="<?= $url ?>public/uploads/<?= hescape($f['nombre_archivo']) ?>" class="img-thumbnail" style="height:120px;width:100%;object-fit:cover;">
                                    </a>
                                    <?php if ($f['descripcion']): ?><small class="d-block text-muted mt-1"><?= hescape($f['descripcion']) ?></small><?php endif; ?>
                                    <small class="d-block text-muted"><?= date('d/m/Y', strtotime($f['fecha_subida'])) ?></small>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Ordenes de servicio -->
                    <?php
                    $ordenes = $pdo->prepare("SELECT * FROM tb_ordenes WHERE id_cliente = ? ORDER BY fecha_creacion DESC LIMIT 5");
                    $ordenes->execute([$id_cliente]);
                    $ordenes = $ordenes->fetchAll();
                    if (!empty($ordenes)): ?>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-clipboard me-2 text-warning"></i>Ordenes de servicio</h3></div>
                        <div class="card-body p-0">
                            <div class="table-container">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>#</th><th>Tipo</th><th>Estado</th><th>Prioridad</th><th>Tecnico</th><th>Fecha</th></tr></thead>
                                <tbody>
                                    <?php foreach ($ordenes as $o):
                                    $tec = $pdo->prepare("SELECT nombre FROM tb_usuarios WHERE id_usuario = ?");
                                    $tec->execute([$o['id_tecnico']]);
                                    $tecnico = $tec->fetchColumn();
                                    ?>
                                    <tr><td><?= hescape($o['numero_orden']) ?></td><td><?= hescape($o['tipo']) ?></td>
                                        <td><span class="badge bg-<?= ['Abierta'=>'warning','En Proceso'=>'info','Completada'=>'success','Cancelada'=>'secondary'][$o['estado']] ?? 'secondary' ?>"><?= $o['estado'] ?></span></td>
                                        <td><span class="badge bg-<?= $o['prioridad'] == 'Urgente' || $o['prioridad'] == 'Alta' ? 'danger' : ($o['prioridad'] == 'Media' ? 'warning' : 'success') ?>"><?= $o['prioridad'] ?></span></td>
                                        <td><?= $tecnico ? hescape($tecnico) : '-' ?></td>
                                        <td><?= date('d/m/Y', strtotime($o['fecha_creacion'])) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tickets -->
                    <?php
                    $tickets = $pdo->prepare("SELECT t.*, u.nombre AS usuario_nombre FROM tb_tickets t LEFT JOIN tb_usuarios u ON t.id_usuario = u.id_usuario WHERE t.id_cliente = ? ORDER BY t.fecha_creacion DESC LIMIT 5");
                    $tickets->execute([$id_cliente]);
                    $tickets = $tickets->fetchAll();
                    if (!empty($tickets)): ?>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-headset me-2 text-info"></i>Tickets de soporte</h3></div>
                        <div class="card-body p-0">
                            <div class="table-container">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Ticket</th><th>Asunto</th><th>Estado</th><th>Asignado</th><th>Fecha</th></tr></thead>
                                <tbody>
                                    <?php foreach ($tickets as $t): ?>
                                    <tr><td><?= hescape($t['numero_ticket']) ?></td><td><?= hescape($t['asunto']) ?></td>
                                        <td><span class="badge bg-<?= ['Abierto'=>'warning','En Proceso'=>'info','Resuelto'=>'success','Cerrado'=>'secondary'][$t['estado']] ?? 'secondary' ?>"><?= $t['estado'] ?></span></td>
                                        <td><?= hescape($t['usuario_nombre'] ?? '-') ?></td>
                                        <td><?= date('d/m/Y', strtotime($t['fecha_creacion'])) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timeline Actividad Reciente -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-clock me-2 text-info"></i>Actividad Reciente</h5>
    </div>
    <div class="card-body" style="max-height:400px;overflow-y:auto;">
        <?php if (empty($timeline)): ?>
        <div class="text-center text-muted py-3">Sin actividad registrada</div>
        <?php else: ?>
        <div class="position-relative" style="padding-left:30px;">
            <!-- Línea vertical -->
            <div style="position:absolute;left:10px;top:0;bottom:0;width:2px;background:var(--border-color,#e2e8f0);"></div>
            <?php foreach ($timeline as $evt): ?>
            <div class="mb-3 position-relative" style="padding-left:20px;">
                <div style="position:absolute;left:-24px;top:4px;width:12px;height:12px;border-radius:50%;background:var(--bs-<?=$evt['color']?>,#2563eb);border:2px solid #fff;box-shadow:0 0 0 2px var(--bs-<?=$evt['color']?>,#2563eb);"></div>
                <div class="d-flex align-items-center gap-2">
                    <strong class="small"><?=hescape($evt['titulo'])?></strong>
                    <span class="badge bg-<?=$evt['color']?> rounded-pill"><?=hescape($evt['estado'])?></span>
                </div>
                <?php if ($evt['detalle']): ?><div class="text-muted small"><?=hescape($evt['detalle'])?></div><?php endif; ?>
                <div class="text-muted" style="font-size:11px;"><?=hescape(date('d/m/Y H:i', strtotime($evt['fecha'])))?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal cambio de estado -->
<?php if ($_SESSION['id_rol'] == 1): ?>
<div class="modal fade" id="estadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="../controles/cambiar_estado.php">
                <?php require_once '../../app/config/seguridad.php'; echo csrf_field(); ?>
                <input type="hidden" name="id_cliente" value="<?= $id_cliente ?>">
                <div class="modal-header"><h5 class="modal-title">Cambiar estado del servicio</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nuevo estado</label>
                        <select name="estado" class="form-select" required>
                            <option value="Activo" <?= $cliente['estado_servicio'] == 'Activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="Suspendido" <?= $cliente['estado_servicio'] == 'Suspendido' ? 'selected' : '' ?>>Suspendido</option>
                            <option value="Cortado" <?= $cliente['estado_servicio'] == 'Cortado' ? 'selected' : '' ?>>Cortado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <textarea name="motivo" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include('../../parte2.php'); ?>
<?php if ($cliente['lat'] && $cliente['lng']): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const m = L.map('miniMap').setView([<?= $cliente['lat'] ?>, <?= $cliente['lng'] ?>], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OSM'}).addTo(m);
L.marker([<?= $cliente['lat'] ?>, <?= $cliente['lng'] ?>]).addTo(m);
</script>
<?php endif; ?>

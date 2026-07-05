<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

$tipo = $_GET['tipo'] ?? 'facturacion';
$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');
$filtro_id = $_GET['filtro_id'] ?? '';

$tecnicos = $pdo->query("SELECT id_usuario, nombre FROM tb_usuarios WHERE id_rol=3 ORDER BY nombre")->fetchAll();
$vendedores = $pdo->query("SELECT id_usuario, nombre FROM tb_usuarios WHERE id_rol=4 ORDER BY nombre")->fetchAll();
?>
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Informes</h1></div></div></div></div>
    <div class="content"><div class="container-fluid">
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-filter me-2"></i>Filtros</h3></div>
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Tipo informe</label>
                        <select name="tipo" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="facturacion" <?= $tipo=='facturacion'?'selected':'' ?>>Facturación</option>
                            <option value="ventas" <?= $tipo=='ventas'?'selected':'' ?>>Ventas</option>
                            <option value="instalaciones" <?= $tipo=='instalaciones'?'selected':'' ?>>Instalaciones</option>
                            <option value="tickets" <?= $tipo=='tickets'?'selected':'' ?>>Tickets</option>
                            <option value="cartera" <?= $tipo=='cartera'?'selected':'' ?>>Cartera</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" class="form-control form-control-sm" value="<?= $fecha_desde ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="<?= $fecha_hasta ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><?= $tipo=='ventas'?'Vendedor':($tipo=='instalaciones'?'Técnico':'Filtro') ?></label>
                        <select name="filtro_id" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <?php if ($tipo=='ventas'): foreach ($vendedores as $v): ?>
                                <option value="<?= $v['id_usuario'] ?>" <?= $filtro_id==$v['id_usuario']?'selected':'' ?>><?= hescape($v['nombre']) ?></option>
                            <?php endforeach; elseif ($tipo=='instalaciones'): foreach ($tecnicos as $t): ?>
                                <option value="<?= $t['id_usuario'] ?>" <?= $filtro_id==$t['id_usuario']?'selected':'' ?>><?= hescape($t['nombre']) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filtrar</button>
                        <a href="controles/generar_pdf.php?tipo=<?= $tipo ?>&fecha_desde=<?= $fecha_desde ?>&fecha_hasta=<?= $fecha_hasta ?>&filtro_id=<?= $filtro_id ?>" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
                        <a href="controles/generar_excel.php?tipo=<?= $tipo ?>&fecha_desde=<?= $fecha_desde ?>&fecha_hasta=<?= $fecha_hasta ?>&filtro_id=<?= $filtro_id ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-table me-2"></i><?= ucfirst($tipo) ?></h3></div>
            <div class="card-body p-0">
                <div class="table-wrap">
                    <table class="table table-sm table-hover mb-0" id="tablaInforme">
                        <thead>
                            <?php if ($tipo == 'facturacion'): ?>
                                <tr><th># Factura</th><th>Cliente</th><th>Emisión</th><th>Vencimiento</th><th>Total</th><th>Estado</th></tr>
                            <?php elseif ($tipo == 'ventas'): ?>
                                <tr><th># Venta</th><th>Cliente</th><th>Vendedor</th><th>Plan</th><th>Monto</th><th>Tipo</th><th>Fecha</th></tr>
                            <?php elseif ($tipo == 'instalaciones'): ?>
                                <tr><th>Cliente</th><th>Técnico</th><th>Fecha instalación</th><th>Dirección</th></tr>
                            <?php elseif ($tipo == 'tickets'): ?>
                                <tr><th>Ticket</th><th>Cliente</th><th>Asunto</th><th>Categoría</th><th>Prioridad</th><th>Estado</th><th>Fecha</th></tr>
                            <?php elseif ($tipo == 'cartera'): ?>
                                <tr><th>Cliente</th><th>Teléfono</th><th>Facturas vencidas</th><th>Deuda total</th><th>Días mora</th></tr>
                            <?php endif; ?>
                        </thead>
                        <tbody>
                            <?php if ($tipo == 'facturacion'):
                                $sql = "SELECT f.*, c.nombre AS cliente_nombre FROM tb_facturas f LEFT JOIN tb_clientes c ON f.id_cliente=c.id_cliente WHERE f.fecha_emision BETWEEN :desde AND :hasta ORDER BY f.fecha_emision DESC";
                                $stmt = $pdo->prepare($sql); $stmt->execute(['desde'=>$fecha_desde, 'hasta'=>$fecha_hasta]);
                                while ($r = $stmt->fetch()): ?>
                                    <tr><td><?= hescape($r['numero_factura']) ?></td><td><?= hescape($r['cliente_nombre']??'-') ?></td><td><?= $r['fecha_emision'] ?></td><td><?= $r['fecha_vencimiento'] ?></td><td>$<?= number_format($r['total'], 0) ?></td><td><span class="badge bg-<?= ['pendiente'=>'warning','pagada'=>'success','vencida'=>'danger','anulada'=>'secondary'][$r['estado']]??'secondary' ?>"><?= $r['estado'] ?></span></td></tr>
                                <?php endwhile; ?>
                            <?php elseif ($tipo == 'ventas'):
                                $sql = "SELECT v.*, c.nombre AS cliente_nombre, u.nombre AS vendedor_nombre, p.nombre AS plan_nombre FROM tb_ventas v LEFT JOIN tb_clientes c ON v.id_cliente=c.id_cliente LEFT JOIN tb_usuarios u ON v.id_vendedor=u.id_usuario LEFT JOIN tb_planes p ON v.id_plan=p.id_plan WHERE v.fecha BETWEEN :desde AND :hasta ORDER BY v.fecha DESC";
                                $params = ['desde'=>$fecha_desde, 'hasta'=>$fecha_hasta];
                                if ($filtro_id) { $sql = str_replace('WHERE v.fecha', 'WHERE v.id_vendedor=:filtro AND v.fecha', $sql); $params['filtro'] = $filtro_id; }
                                $stmt = $pdo->prepare($sql); $stmt->execute($params);
                                while ($r = $stmt->fetch()): ?>
                                    <tr><td><?= $r['id_venta'] ?></td><td><?= hescape($r['cliente_nombre']??'-') ?></td><td><?= hescape($r['vendedor_nombre']??'-') ?></td><td><?= hescape($r['plan_nombre']??'-') ?></td><td>$<?= number_format($r['monto'], 0) ?></td><td><span class="badge bg-info"><?= $r['tipo_venta'] ?></span></td><td><?= $r['fecha'] ?></td></tr>
                                <?php endwhile; ?>
                            <?php elseif ($tipo == 'instalaciones'):
                                $sql = "SELECT c.nombre AS cliente_nombre, c.direccion, u.nombre AS tecnico_nombre, c.fecha_instalacion FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador=u.id_usuario WHERE c.fecha_instalacion BETWEEN :desde AND :hasta ORDER BY c.fecha_instalacion DESC";
                                $params = ['desde'=>$fecha_desde, 'hasta'=>$fecha_hasta];
                                if ($filtro_id) { $sql = str_replace('WHERE c.fecha_instalacion', 'WHERE c.id_instalador=:filtro AND c.fecha_instalacion', $sql); $params['filtro'] = $filtro_id; }
                                $stmt = $pdo->prepare($sql); $stmt->execute($params);
                                while ($r = $stmt->fetch()): ?>
                                    <tr><td><?= hescape($r['cliente_nombre']??'-') ?></td><td><?= hescape($r['tecnico_nombre']??'-') ?></td><td><?= $r['fecha_instalacion'] ?></td><td><?= hescape($r['direccion']??'-') ?></td></tr>
                                <?php endwhile; ?>
                            <?php elseif ($tipo == 'tickets'):
                                $sql = "SELECT t.*, c.nombre AS cliente_nombre FROM tb_tickets t LEFT JOIN tb_clientes c ON t.id_cliente=c.id_cliente WHERE t.fecha_creacion BETWEEN :desde AND :hasta ORDER BY t.fecha_creacion DESC";
                                $stmt = $pdo->prepare($sql); $stmt->execute(['desde'=>$fecha_desde, 'hasta'=>$fecha_hasta]);
                                while ($r = $stmt->fetch()): ?>
                                    <tr><td><?= hescape($r['numero_ticket']) ?></td><td><?= hescape($r['cliente_nombre']??'-') ?></td><td><?= hescape(mb_substr($r['asunto'],0,40)) ?></td><td><span class="badge bg-secondary"><?= $r['categoria'] ?></span></td><td><span class="badge bg-<?= ['Baja'=>'success','Media'=>'warning','Alta'=>'danger','Urgente'=>'danger'][$r['prioridad']]??'secondary' ?>"><?= $r['prioridad'] ?></span></td><td><span class="badge bg-<?= ['Abierto'=>'warning','En Proceso'=>'info','Resuelto'=>'success','Cerrado'=>'secondary'][$r['estado']]??'secondary' ?>"><?= $r['estado'] ?></span></td><td><?= $r['fecha_creacion'] ?></td></tr>
                                <?php endwhile; ?>
                            <?php elseif ($tipo == 'cartera'):
                                $sql = "SELECT c.nombre, c.telefono, COUNT(f.id_factura) AS facturas_vencidas, SUM(f.total) AS deuda_total, DATEDIFF(CURDATE(), MIN(f.fecha_vencimiento)) AS dias_mora FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente=c.id_cliente WHERE f.estado IN ('pendiente','vencida') GROUP BY f.id_cliente ORDER BY dias_mora DESC";
                                $stmt = $pdo->query($sql);
                                while ($r = $stmt->fetch()): ?>
                                    <tr><td><?= hescape($r['nombre']) ?></td><td><?= hescape($r['telefono']) ?></td><td><?= $r['facturas_vencidas'] ?></td><td class="text-danger fw-bold">$<?= number_format($r['deuda_total'], 0) ?></td><td><span class="badge bg-<?= $r['dias_mora']>60?'danger':($r['dias_mora']>30?'warning text-dark':'secondary') ?>"><?= $r['dias_mora'] ?> días</span></td></tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div>
</div>
<?php include('../parte2.php'); ?>
<script>$('#tablaInforme').DataTable({pageLength:25,responsive:true,autoWidth:false,language:{url:'//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json'},dom:'Bfrtip',buttons:['copy','excel','pdf','print'],columnDefs:[{orderable:false,targets:-1}]});</script>

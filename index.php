<?php
include('sesion.php');
require_once 'app/config/conexion.php';
require_once 'notificaciones/controles/crear_notificacion.php';
generar_notificaciones_automaticas($pdo);
include('parte1.php');

// KPIs principales
$total_clientes = $pdo->query("SELECT COUNT(*) FROM tb_clientes")->fetchColumn();
$clientes_activos = $pdo->query("SELECT COUNT(*) FROM tb_clientes WHERE estado_servicio = 'Activo'")->fetchColumn();
$clientes_suspendidos = $pdo->query("SELECT COUNT(*) FROM tb_clientes WHERE estado_servicio = 'Suspendido'")->fetchColumn();
$clientes_cortados = $pdo->query("SELECT COUNT(*) FROM tb_clientes WHERE estado_servicio = 'Cortado'")->fetchColumn();

$contratos_activos = $pdo->query("SELECT COUNT(*) FROM tb_contratos WHERE estado = 'activo'")->fetchColumn();
$instalaciones_pendientes = $pdo->query("SELECT COUNT(*) FROM tb_clientes WHERE fecha_instalacion IS NULL")->fetchColumn();
$instalaciones_completadas = $pdo->query("SELECT COUNT(*) FROM tb_clientes WHERE fecha_instalacion IS NOT NULL")->fetchColumn();
$equipos_inventario = $pdo->query("SELECT COUNT(*) FROM tb_equipos WHERE estado = 'Disponible'")->fetchColumn();

// Facturacion del mes
$ingresos_mes = $pdo->query("SELECT COALESCE(SUM(total),0) FROM tb_facturas WHERE estado='pagada' AND MONTH(fecha_pago)=MONTH(CURDATE()) AND YEAR(fecha_pago)=YEAR(CURDATE())")->fetchColumn();
$facturas_pendientes = $pdo->query("SELECT COUNT(*) FROM tb_facturas WHERE estado IN ('pendiente','vencida')")->fetchColumn();
$total_adeudado = $pdo->query("SELECT COALESCE(SUM(total),0) FROM tb_facturas WHERE estado IN ('pendiente','vencida')")->fetchColumn();
$facturas_mes = $pdo->query("SELECT COUNT(*) FROM tb_facturas WHERE MONTH(fecha_emision)=MONTH(CURDATE()) AND YEAR(fecha_emision)=YEAR(CURDATE())")->fetchColumn();

// Ventas del mes
$ventas_mes = $pdo->query("SELECT COUNT(*), COALESCE(SUM(monto),0) FROM tb_ventas WHERE MONTH(fecha)=MONTH(CURDATE()) AND YEAR(fecha)=YEAR(CURDATE())")->fetch(PDO::FETCH_NUM);
$ventas_mes_count = $ventas_mes[0];
$ventas_mes_monto = $ventas_mes[1];

// Top 5 clientes morosos
$morosos = $pdo->query("SELECT c.nombre, c.telefono, SUM(f.total) AS deuda, DATEDIFF(CURDATE(), MIN(f.fecha_vencimiento)) AS dias_mora
    FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente
    WHERE f.estado IN ('pendiente','vencida')
    GROUP BY f.id_cliente ORDER BY deuda DESC LIMIT 5")->fetchAll();

// Instalaciones recientes
$instalaciones_recientes = $pdo->query("SELECT c.nombre, c.fecha_instalacion, u.nombre AS instalador FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario WHERE c.fecha_instalacion IS NOT NULL ORDER BY c.fecha_instalacion DESC LIMIT 5")->fetchAll();

// Ventas por mes (grafico)
$ventas_anual = $pdo->query("SELECT MONTH(fecha) AS mes, COUNT(*) AS total, COALESCE(SUM(monto),0) AS monto FROM tb_ventas WHERE YEAR(fecha)=YEAR(CURDATE()) GROUP BY MONTH(fecha) ORDER BY mes")->fetchAll();
$meses_labels = json_encode(array_map(fn($v) => date('M', mktime(0,0,0,$v['mes'],1)), $ventas_anual));
$montos_data = json_encode(array_map(fn($v) => floatval($v['monto']), $ventas_anual));
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Dashboard</h1></div>
                <div class="col-sm-6 text-end text-muted"><span id="fechaHora"></span></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <!-- Fila 1: KPIs principales -->
            <div class="row mb-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card blue position-relative">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-label">Clientes</div>
                        <div class="stat-value"><?= $total_clientes ?></div>
                        <a href="clientes/vistas/lista.php" class="stretched-link"></a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card green position-relative">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-label">Activos</div>
                        <div class="stat-value"><?= $clientes_activos ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card teal position-relative">
                        <div class="stat-icon"><i class="fas fa-file-contract"></i></div>
                        <div class="stat-label">Contratos activos</div>
                        <div class="stat-value"><?= $contratos_activos ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card red position-relative">
                        <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="stat-label">En cartera</div>
                        <div class="stat-value">$<?= number_format($total_adeudado, 0) ?></div>
                        <a href="facturacion/cartera.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <!-- Fila 2: Facturacion + Ventas -->
            <div class="row mb-4">
                <div class="col-md-3 col-6">
                    <div class="stat-card purple position-relative">
                        <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="stat-label">Ingresos del mes</div>
                        <div class="stat-value">$<?= number_format($ingresos_mes, 0) ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card orange position-relative">
                        <div class="stat-icon"><i class="fas fa-file-invoice"></i></div>
                        <div class="stat-label">Facturas del mes</div>
                        <div class="stat-value"><?= $facturas_mes ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card pink position-relative">
                        <div class="stat-icon"><i class="fas fa-tools"></i></div>
                        <div class="stat-label">Inst. pendientes</div>
                        <div class="stat-value"><?= $instalaciones_pendientes ?></div>
                        <a href="instalaciones/index.php" class="stretched-link"></a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card indigo position-relative">
                        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="stat-label">Ventas del mes</div>
                        <div class="stat-value"><?= $ventas_mes_count ?></div>
                        <a href="ventas/index.php" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Grafico ventas anual -->
                <div class="col-md-8">
                    <div class="card"><div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar me-2 text-primary"></i>Ventas mensuales <?= date('Y') ?></h3></div>
                        <div class="card-body"><canvas id="chartVentas" height="120"></canvas></div>
                    </div>
                </div>
                <!-- Morosos top 5 -->
                <div class="col-md-4">
                    <div class="card"><div class="card-header" style="background:#dc3545;color:#fff;"><h3 class="card-title"><i class="fas fa-exclamation-circle me-2"></i>Top morosos</h3></div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Cliente</th><th>Deuda</th><th>Días</th></tr></thead>
                                <tbody>
                                    <?php foreach ($morosos as $m): ?>
                                    <tr><td><?= hescape($m['nombre']) ?></td><td>$<?= number_format($m['deuda'], 0) ?></td><td><span class="badge bg-<?= $m['dias_mora'] > 60 ? 'danger' : ($m['dias_mora'] > 30 ? 'warning text-dark' : 'secondary') ?>"><?= $m['dias_mora'] ?> días</span></td></tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($morosos)): ?><tr><td colspan="3" class="text-center text-muted">¡Todos al día!</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer"><a href="facturacion/cartera.php" class="btn btn-sm btn-danger">Ver cartera completa</a></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Instalaciones recientes -->
                <div class="col-md-6">
                    <div class="card"><div class="card-header" style="background:#28a745;color:#fff;"><h3 class="card-title"><i class="fas fa-hard-hat me-2"></i>Últimas instalaciones</h3></div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Cliente</th><th>Fecha</th><th>Instalador</th></tr></thead>
                                <tbody>
                                    <?php foreach ($instalaciones_recientes as $i): ?>
                                    <tr><td><?= hescape($i['nombre']) ?></td><td><?= date('d/m/Y', strtotime($i['fecha_instalacion'])) ?></td><td><?= hescape($i['instalador'] ?? '-') ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Stats rapidas -->
                <div class="col-md-3 col-6">
                    <div class="card mb-3"><div class="card-body py-2 d-flex justify-content-between align-items-center"><span>Clientes activos</span><strong><?= $clientes_activos ?> / <?= $total_clientes ?></strong></div></div>
                    <div class="card mb-3"><div class="card-body py-2 d-flex justify-content-between align-items-center"><span>Suspendidos</span><strong class="text-warning"><?= $clientes_suspendidos ?></strong></div></div>
                    <div class="card mb-3"><div class="card-body py-2 d-flex justify-content-between align-items-center"><span>Cortados</span><strong class="text-danger"><?= $clientes_cortados ?></strong></div></div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card mb-3"><div class="card-body py-2 d-flex justify-content-between align-items-center"><span>Instalaciones</span><strong class="text-success"><?= $instalaciones_completadas ?></strong></div></div>
                    <div class="card mb-3"><div class="card-body py-2 d-flex justify-content-between align-items-center"><span>Pendientes</span><strong class="text-warning"><?= $instalaciones_pendientes ?></strong></div></div>
                    <div class="card mb-3"><div class="card-body py-2 d-flex justify-content-between align-items-center"><span>Equipos disp.</span><strong class="text-primary"><?= $equipos_inventario ?></strong></div></div>
                </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card"><div class="card-header bg-primary text-white"><h3 class="card-title"><i class="fas fa-clipboard me-2"></i>Ordenes de servicio recientes</h3></div>
                        <div class="card-body p-0">
                            <?php $ordenes = $pdo->query("SELECT o.*, c.nombre AS cliente_nombre FROM tb_ordenes o LEFT JOIN tb_clientes c ON o.id_cliente = c.id_cliente ORDER BY o.fecha_creacion DESC LIMIT 5")->fetchAll(); ?>
                            <table class="table table-sm mb-0">
                                <thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Prioridad</th></tr></thead>
                                <tbody>
                                    <?php foreach ($ordenes as $o): ?>
                                    <tr><td><?= hescape($o['numero_orden']) ?></td><td><?= hescape($o['cliente_nombre'] ?? '-') ?></td>
                                        <td><span class="badge bg-<?= ['Abierta'=>'warning','En Proceso'=>'info','Completada'=>'success','Cancelada'=>'secondary'][$o['estado']] ?? 'secondary' ?>"><?= $o['estado'] ?></span></td>
                                        <td><span class="badge bg-<?= $o['prioridad'] == 'Alta' || $o['prioridad'] == 'Urgente' ? 'danger' : ($o['prioridad'] == 'Media' ? 'warning' : 'success') ?>"><?= $o['prioridad'] ?></span></td></tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($ordenes)): ?><tr><td colspan="4" class="text-center text-muted">Sin ordenes recientes</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer"><a href="ordenes/index.php" class="btn btn-sm btn-primary">Ver todas</a></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card"><div class="card-header bg-info text-white"><h3 class="card-title"><i class="fas fa-headset me-2"></i>Tickets de soporte recientes</h3></div>
                        <div class="card-body p-0">
                            <?php $tickets = $pdo->query("SELECT t.*, c.nombre AS cliente_nombre FROM tb_tickets t LEFT JOIN tb_clientes c ON t.id_cliente = c.id_cliente ORDER BY t.fecha_creacion DESC LIMIT 5")->fetchAll(); ?>
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Ticket</th><th>Cliente</th><th>Asunto</th><th>Estado</th></tr></thead>
                                <tbody>
                                    <?php foreach ($tickets as $t): ?>
                                    <tr><td><?= hescape($t['numero_ticket']) ?></td><td><?= hescape($t['cliente_nombre'] ?? '-') ?></td>
                                        <td><?= hescape(mb_substr($t['asunto'], 0, 30)) ?>...</td>
                                        <td><span class="badge bg-<?= ['Abierto'=>'warning','En Proceso'=>'info','Resuelto'=>'success','Cerrado'=>'secondary'][$t['estado']] ?? 'secondary' ?>"><?= $t['estado'] ?></span></td></tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($tickets)): ?><tr><td colspan="4" class="text-center text-muted">Sin tickets recientes</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer"><a href="tickets/index.php" class="btn btn-sm btn-info text-white">Ver todos</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('parte2.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chartVentas'), {
    type: 'bar',
    data: {
        labels: <?= $meses_labels ?>,
        datasets: [{
            label: 'Ventas ($)',
            data: <?= $montos_data ?>,
            backgroundColor: '#2563eb',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } } }
    }
});
</script>

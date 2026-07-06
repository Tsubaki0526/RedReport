<?php
include('../sesion.php');
verificar_acceso([1, 2]);
include('../parte1.php');
require_once '../app/config/conexion.php';

$contratos_activos = $pdo->query("
    SELECT co.id_contrato, c.id_cliente, c.nombre AS cliente, p.nombre AS plan_nombre, p.precio,
           co.fecha_inicio
    FROM tb_contratos co
    INNER JOIN tb_clientes c ON co.id_cliente = c.id_cliente
    INNER JOIN tb_planes p ON co.id_plan = p.id_plan
    WHERE co.estado = 'activo'
    ORDER BY c.nombre
")->fetchAll();

// Check if each contract already has an invoice this month
$ya_facturados = [];
$pendientes = [];
foreach ($contratos_activos as $co) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM tb_facturas WHERE id_cliente = :cli AND MONTH(fecha_emision) = MONTH(CURDATE()) AND YEAR(fecha_emision) = YEAR(CURDATE())");
    $check->execute([':cli' => $co['id_cliente']]);
    if ($check->fetchColumn() > 0) {
        $ya_facturados[] = $co;
    } else {
        $pendientes[] = $co;
    }
}
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-sync-alt me-2 text-primary"></i>Facturación recurrente</h1></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <?php if (!empty($pendientes)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong><?= count($pendientes) ?> contratos</strong> sin factura este mes.
                <form method="POST" action="controles/generar_recurrentes.php" class="d-inline">
                    <?php require_once '../app/config/seguridad.php'; echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-primary ms-3"><i class="fas fa-file-invoice me-1"></i>Generar facturas pendientes</button>
                </form>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-clock me-2"></i>Por facturar este mes (<?= count($pendientes) ?>)</h5></div>
                        <div class="card-body p-0">
                            <div class="table-container">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Cliente</th><th>Plan</th><th>Valor</th></tr></thead>
                                <tbody>
                                    <?php foreach ($pendientes as $p): ?>
                                    <tr><td><?= hescape($p['cliente']) ?></td><td><?= hescape($p['plan_nombre']) ?></td><td>$<?= number_format($p['precio'], 0) ?></td></tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($pendientes)): ?><tr><td colspan="3" class="text-center text-muted">Todos facturados este mes</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-secondary text-white"><h5 class="mb-0"><i class="fas fa-check me-2"></i>Ya facturados (<?= count($ya_facturados) ?>)</h5></div>
                        <div class="card-body p-0">
                            <div class="table-container">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Cliente</th><th>Plan</th><th>Valor</th></tr></thead>
                                <tbody>
                                    <?php foreach ($ya_facturados as $y): ?>
                                    <tr><td><?= hescape($y['cliente']) ?></td><td><?= hescape($y['plan_nombre']) ?></td><td>$<?= number_format($y['precio'], 0) ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>

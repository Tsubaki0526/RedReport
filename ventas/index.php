<?php
include('../sesion.php');
include('../parte1.php');
require_once '../app/config/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_rol = $_SESSION['id_rol'] ?? 0;
$sql_and = ($id_rol == 4) ? "AND v.id_vendedor = $id_usuario" : '';
$sql_and_c = ($id_rol == 4) ? "AND c.id_vendedor = $id_usuario" : '';

$ventas_mes = $pdo->query("SELECT COUNT(*) AS total, COALESCE(SUM(monto),0) AS monto FROM tb_ventas v WHERE 1=1 $sql_and AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())")->fetch(PDO::FETCH_ASSOC);
$contratos_activos = $pdo->query("SELECT COUNT(*) AS total FROM tb_contratos c WHERE 1=1 $sql_and_c AND c.estado = 'activo'")->fetch(PDO::FETCH_ASSOC);
$comision_mes = $pdo->query("SELECT COALESCE(SUM(comision),0) AS total FROM tb_ventas v WHERE 1=1 $sql_and AND MONTH(v.fecha) = MONTH(CURDATE()) AND YEAR(v.fecha) = YEAR(CURDATE())")->fetch(PDO::FETCH_ASSOC);

$top_planes = $pdo->query("SELECT p.nombre, COUNT(*) AS total FROM tb_contratos c INNER JOIN tb_planes p ON c.id_plan = p.id_plan GROUP BY c.id_plan ORDER BY total DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

$ventas_recientes = $pdo->query("SELECT v.*, c.nombre AS cliente, u.nombre AS vendedor FROM tb_ventas v INNER JOIN tb_clientes c ON v.id_cliente = c.id_cliente INNER JOIN tb_usuarios u ON v.id_vendedor = u.id_usuario WHERE 1=1 $sql_and ORDER BY v.id_venta DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../public/css/redreport.css">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard de Ventas</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card green">
                        <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div>
                            <div class="stat-value">$<?= number_format($ventas_mes['monto'], 0) ?></div>
                            <div class="stat-label">Ventas del mes (<?= $ventas_mes['total'] ?>)</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card blue">
                        <div class="stat-icon"><i class="fas fa-file-contract"></i></div>
                        <div>
                            <div class="stat-value"><?= $contratos_activos['total'] ?></div>
                            <div class="stat-label">Contratos activos</div>
                        </div>
                        <a href="contratos.php" class="stat-link">Ver <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card purple">
                        <div class="stat-icon"><i class="fas fa-percentage"></i></div>
                        <div>
                            <div class="stat-value">$<?= number_format($comision_mes['total'], 0) ?></div>
                            <div class="stat-label">Comisiones del mes</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-chart-pie me-2 text-primary"></i>Planes mas contratados</div>
                        <div class="card-body">
                            <?php if ($top_planes): ?>
                            <div class="table-container">
                            <table class="table table-sm">
                                <thead><tr><th>Plan</th><th class="text-end">Contratos</th></tr></thead>
                                <tbody>
                                <?php foreach ($top_planes as $p): ?>
                                <tr><td><?= hescape($p['nombre']) ?></td><td class="text-end fw-bold"><?= $p['total'] ?></td></tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                            <?php else: ?>
                            <p class="text-muted mb-0">Sin contratos registrados</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clock me-2 text-primary"></i>Ventas recientes</span>
                            <a href="ventas.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva Venta</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>Cliente</th><th>Tipo</th><th>Monto</th><th>Vendedor</th><th>Fecha</th></tr></thead>
                                    <tbody>
                                    <?php foreach ($ventas_recientes as $v): ?>
                                    <tr>
                                        <td><?= hescape($v['cliente']) ?></td>
                                        <td><span class="badge bg-info"><?= ucfirst($v['tipo']) ?></span></td>
                                        <td class="fw-bold">$<?= number_format($v['monto'], 0) ?></td>
                                        <td><?= hescape($v['vendedor']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($v['fecha'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (!$ventas_recientes): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Sin ventas registradas</td></tr>
                                    <?php endif; ?>
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

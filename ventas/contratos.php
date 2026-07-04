<?php
include('../sesion.php');
include('../parte1.php');
require_once '../app/config/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_rol = $_SESSION['id_rol'] ?? 0;
$where = ($id_rol == 4) ? "WHERE c.id_vendedor = $id_usuario" : '';

$contratos = $pdo->query("SELECT c.*, cl.nombre AS cliente, p.nombre AS plan, u.nombre AS vendedor
    FROM tb_contratos c
    INNER JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente
    INNER JOIN tb_planes p ON c.id_plan = p.id_plan
    INNER JOIN tb_usuarios u ON c.id_vendedor = u.id_usuario
    $where
    ORDER BY c.id_contrato DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../public/css/redreport.css">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Contratos</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="contrato_nuevo.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo Contrato</a>
                    <a href="ventas.php" class="btn btn-success btn-sm"><i class="fas fa-cart-plus"></i> Registrar Venta</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header"><i class="fas fa-file-contract me-2 text-primary"></i>Listado de contratos</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaContratos" class="table table-hover">
                            <thead>
                                <tr><th>#</th><th>Cliente</th><th>Plan</th><th>Vendedor</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Acciones</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contratos as $c):
                                    $badge = match($c['estado']) {
                                        'activo' => 'bg-success',
                                        'cancelado' => 'bg-danger',
                                        'expirado' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <tr>
                                    <td><?= $c['id_contrato'] ?></td>
                                    <td><?= hescape($c['cliente']) ?></td>
                                    <td><?= hescape($c['plan']) ?></td>
                                    <td><?= hescape($c['vendedor']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?></td>
                                    <td><?= $c['fecha_fin'] ? date('d/m/Y', strtotime($c['fecha_fin'])) : '-' ?></td>
                                    <td><span class="badge <?= $badge ?>"><?= ucfirst($c['estado']) ?></span></td>
                                    <td>
                                        <a href="ventas.php?contrato=<?= $c['id_contrato'] ?>" class="btn btn-sm btn-success" title="Registrar venta"><i class="fas fa-cart-plus"></i></a>
                                        <?php if ($c['estado'] == 'activo'): ?>
                                        <form method="POST" action="controles/cancelar_contrato.php" class="d-inline" onsubmit="return confirm('Cancelar este contrato?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id_contrato" value="<?= $c['id_contrato'] ?>">
                                            <button class="btn btn-sm btn-danger" title="Cancelar"><i class="fas fa-ban"></i></button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaContratos').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>

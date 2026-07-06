<?php
include('../sesion.php');
verificar_acceso([1, 2, 4]);
include('../parte1.php');
require_once '../app/config/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_rol = $_SESSION['id_rol'] ?? 0;

$clientes = $pdo->query("SELECT id_cliente, nombre, documento FROM tb_clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$contratos = $pdo->query("SELECT c.id_contrato, cl.nombre AS cliente, p.nombre AS plan FROM tb_contratos c INNER JOIN tb_clientes cl ON c.id_cliente = cl.id_cliente INNER JOIN tb_planes p ON c.id_plan = p.id_plan WHERE c.estado = 'activo' ORDER BY cl.nombre")->fetchAll(PDO::FETCH_ASSOC);

$where = ($id_rol == 4) ? "WHERE v.id_vendedor = $id_usuario" : '';
$contrato_id = intval($_GET['contrato'] ?? 0);
$ventas = $pdo->query("SELECT v.*, c.nombre AS cliente, u.nombre AS vendedor FROM tb_ventas v INNER JOIN tb_clientes c ON v.id_cliente = c.id_cliente INNER JOIN tb_usuarios u ON v.id_vendedor = u.id_usuario $where ORDER BY v.id_venta DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../public/css/redreport.css">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registro de Ventas</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-cart-plus me-2 text-primary"></i>Nueva Venta</div>
                <div class="card-body">
                    <form method="POST" action="controles/crear_venta.php">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="id_cliente" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['id_cliente'] ?>"><?= hescape($c['nombre']) ?> (<?= hescape($c['documento']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Contrato (opcional)</label>
                                <select name="id_contrato" class="form-select">
                                    <option value="">Sin contrato</option>
                                    <?php foreach ($contratos as $c): ?>
                                    <option value="<?= $c['id_contrato'] ?>" <?= ($contrato_id == $c['id_contrato']) ? 'selected' : '' ?>><?= hescape($c['cliente']) ?> - <?= hescape($c['plan']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="nuevo">Nuevo</option>
                                    <option value="renovacion">Renovacion</option>
                                    <option value="upgrade">Upgrade</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Monto <span class="text-danger">*</span></label>
                                <input type="number" name="monto" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Comision</label>
                                <input type="number" name="comision" class="form-control" step="0.01" value="0">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vendedor</label>
                                <select name="id_vendedor" class="form-select" required>
                                    <?php
                                    $vendedores = $pdo->query("SELECT id_usuario, nombre FROM tb_usuarios WHERE id_rol IN (1,2,4) ORDER BY nombre")->fetchAll();
                                    foreach ($vendedores as $v):
                                    ?>
                                    <option value="<?= $v['id_usuario'] ?>" <?= ($id_rol == 4) ? 'selected' : '' ?>><?= hescape($v['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Notas</label>
                                <input type="text" name="notas" class="form-control" placeholder="Opcional">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Registrar Venta</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><i class="fas fa-history me-2 text-primary"></i>Historial de Ventas</div>
                <div class="card-body">
                    <div class="table-wrap">
                        <table id="tablaVentas" class="table table-hover">
                            <thead>
                                <tr><th>#</th><th>Cliente</th><th>Tipo</th><th>Monto</th><th>Comision</th><th>Vendedor</th><th>Fecha</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ventas as $v): ?>
                                <tr>
                                    <td><?= $v['id_venta'] ?></td>
                                    <td><?= hescape($v['cliente']) ?></td>
                                    <td><span class="badge bg-info"><?= ucfirst($v['tipo']) ?></span></td>
                                    <td class="fw-bold">$<?= number_format($v['monto'], 0) ?></td>
                                    <td class="text-success">$<?= number_format($v['comision'], 0) ?></td>
                                    <td><?= hescape($v['vendedor']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($v['fecha'])) ?></td>
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
<script>
$('#tablaVentas').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
    order: [[0, 'desc']],
    pageLength: 25,
    responsive: true,
    autoWidth: false
});
</script>

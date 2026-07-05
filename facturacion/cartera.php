<?php
include('../sesion.php');
include('../parte1.php');
require_once '../app/config/conexion.php';

$sql = "SELECT c.id_cliente, c.nombre, c.telefono, c.estado_servicio,
               COUNT(f.id_factura) AS facturas_vencidas,
               SUM(f.total) AS total_deuda,
               MAX(f.fecha_vencimiento) AS ultimo_vencimiento,
               DATEDIFF(CURDATE(), MIN(f.fecha_vencimiento)) AS dias_mora
        FROM tb_facturas f
        INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente
        WHERE f.estado IN ('pendiente','vencida')
        GROUP BY f.id_cliente
        ORDER BY total_deuda DESC";
$cartera = $pdo->query($sql)->fetchAll();
$total_cartera = array_sum(array_column($cartera, 'total_deuda'));
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0"><i class="fas fa-file-invoice-dollar me-2 text-danger"></i>Cartera</h1></div>
                <div class="col-sm-6 text-end"><h4 class="m-0">Total: <strong class="text-danger">$<?= number_format($total_cartera, 0) ?></strong></h4></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Cuentas por cobrar</span>
                    <button class="btn btn-sm btn-success" onclick="window.print()"><i class="fas fa-print"></i> Imprimir</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="tablaCartera" class="table table-hover mb-0">
                            <thead><tr><th>Cliente</th><th>Teléfono</th><th>Estado</th><th>Facturas vencidas</th><th>Total deuda</th><th>Días mora</th><th>Último vencimiento</th><th>Acciones</th></tr></thead>
                            <tbody>
                                <?php foreach ($cartera as $c): ?>
                                <tr class="<?= $c['dias_mora'] > 60 ? 'table-danger' : ($c['dias_mora'] > 30 ? 'table-warning' : '') ?>">
                                    <td><a href="<?= $url ?>clientes/vistas/ficha.php?id=<?= $c['id_cliente'] ?>"><?= hescape($c['nombre']) ?></a></td>
                                    <td><?= hescape($c['telefono'] ?? '-') ?></td>
                                    <td><span class="badge bg-<?= $c['estado_servicio'] == 'Activo' ? 'success' : ($c['estado_servicio'] == 'Suspendido' ? 'warning text-dark' : 'danger') ?>"><?= $c['estado_servicio'] ?></span></td>
                                    <td><?= $c['facturas_vencidas'] ?></td>
                                    <td><strong>$<?= number_format($c['total_deuda'], 0) ?></strong></td>
                                    <td><span class="badge bg-<?= $c['dias_mora'] > 60 ? 'danger' : ($c['dias_mora'] > 30 ? 'warning text-dark' : 'secondary') ?>"><?= $c['dias_mora'] ?> días</span></td>
                                    <td><?= date('d/m/Y', strtotime($c['ultimo_vencimiento'])) ?></td>
                                    <td><a href="<?= $url ?>clientes/vistas/ficha.php?id=<?= $c['id_cliente'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
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
<script>$(function(){$('#tablaCartera').DataTable({responsive:true,order:[[4,'desc']],dom:'Bfrtip',buttons:[{extend:'excel',text:'<i class=\"fas fa-file-excel\"></i> Excel'},{extend:'csv',text:'<i class=\"fas fa-file-csv\"></i> CSV'},{extend:'print',text:'<i class=\"fas fa-print\"></i> Imprimir'}],columnDefs:[{orderable:false,targets:7}],language:{url:'//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json',emptyTable:'Todos los clientes estan al dia'}});});</script>

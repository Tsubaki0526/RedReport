<?php
include('../sesion.php');
include('../parte1.php');
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Facturacion</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="crear.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva Factura</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-file-invoice me-2 text-primary"></i>Listado de Facturas</span>
                    <div>
                        <select id="filtroEstado" class="form-select form-select-sm d-inline-block w-auto" onchange="filtrar()">
                            <option value="">Todas</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="pagada">Pagadas</option>
                            <option value="vencida">Vencidas</option>
                            <option value="anulada">Anuladas</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaFacturas" class="table table-hover">
                            <thead>
                                <tr>
                                    <th># Factura</th>
                                    <th>Cliente</th>
                                    <th>Emision</th>
                                    <th>Vencimiento</th>
                                    <th>Subtotal</th>
                                    <th>IVA</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once '../app/config/conexion.php';
                                $sql = "SELECT f.*, c.nombre AS cliente_nombre
                                        FROM tb_facturas f
                                        INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente
                                        ORDER BY f.id_factura DESC";
                                $facturas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($facturas as $f):
                                    $badge = match($f['estado']) {
                                        'pagada' => 'bg-success',
                                        'pendiente' => 'bg-warning text-dark',
                                        'vencida' => 'bg-danger',
                                        'anulada' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                ?>
                                <tr>
                                    <td><strong><?= hescape($f['numero_factura']) ?></strong></td>
                                    <td><?= hescape($f['cliente_nombre']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($f['fecha_emision'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($f['fecha_vencimiento'])) ?></td>
                                    <td>$<?= number_format($f['subtotal'], 0) ?></td>
                                    <td>$<?= number_format($f['iva'], 0) ?></td>
                                    <td><strong>$<?= number_format($f['total'], 0) ?></strong></td>
                                    <td><span class="badge <?= $badge ?>"><?= ucfirst($f['estado']) ?></span></td>
                                    <td>
                                        <a href="ver.php?id=<?= $f['id_factura'] ?>" class="btn btn-sm btn-info" title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="pdf.php?id=<?= $f['id_factura'] ?>" class="btn btn-sm btn-secondary" title="PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>
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
<script>
var tablaFacturas = $('#tablaFacturas').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
    order: [[0, 'desc']],
    pageLength: 25,
    responsive: true,
    autoWidth: false,
    dom: 'Bfrtip',
    buttons: [{extend:'copy',text:'<i class="fas fa-copy"></i> Copiar'},{extend:'excel',text:'<i class="fas fa-file-excel"></i> Excel'},{extend:'csv',text:'<i class="fas fa-file-csv"></i> CSV'},{extend:'pdf',text:'<i class="fas fa-file-pdf"></i> PDF'},{extend:'print',text:'<i class="fas fa-print"></i> Imprimir'}],
    columnDefs: [{ orderable: false, targets: -1 }]
});
function filtrar() {
    tablaFacturas.column(7).search($('#filtroEstado').val()).draw();
}
</script>

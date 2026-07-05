<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

$ordenes = $pdo->query("SELECT o.*, c.nombre AS cliente_nombre, u.nombre AS tecnico_nombre
    FROM tb_ordenes o
    LEFT JOIN tb_clientes c ON o.id_cliente = c.id_cliente
    LEFT JOIN tb_usuarios u ON o.id_tecnico = u.id_usuario
    ORDER BY o.fecha_creacion DESC")->fetchAll(PDO::FETCH_ASSOC);

$tecnicos = $pdo->query("SELECT id_usuario, nombre FROM tb_usuarios WHERE id_rol IN (1,3) ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$clientes = $pdo->query("SELECT id_cliente, nombre FROM tb_clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$estados = ['Abierta' => 'warning', 'En Proceso' => 'info', 'Completada' => 'success', 'Cancelada' => 'secondary'];
$prioridades = ['Baja' => 'success', 'Media' => 'warning', 'Alta' => 'danger', 'Urgente' => 'danger'];
?>
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0"><i class="fas fa-clipboard me-2"></i>Ordenes de servicio</h1></div><div class="col-sm-6 text-end"><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaOrden"><i class="fas fa-plus"></i> Nueva orden</button></div></div></div></div>
    <div class="content"><div class="container-fluid">
        <div class="card"><div class="card-body p-0">
            <div class="table-container">
            <table id="tablaOrdenes" class="table table-sm mb-0">
                <thead><tr><th>#</th><th>Cliente</th><th>Tipo</th><th>Prioridad</th><th>Tecnico</th><th>Estado</th><th>Creada</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($ordenes as $o): ?>
                    <tr>
                        <td><?= hescape($o['numero_orden']) ?></td>
                        <td><?= hescape($o['cliente_nombre'] ?? '-') ?></td>
                        <td><?= hescape($o['tipo']) ?></td>
                        <td><span class="badge bg-<?= $prioridades[$o['prioridad']] ?? 'secondary' ?>"><?= $o['prioridad'] ?></span></td>
                        <td><?= hescape($o['tecnico_nombre'] ?? '-') ?></td>
                        <td><span class="badge bg-<?= $estados[$o['estado']] ?? 'secondary' ?>"><?= $o['estado'] ?></span></td>
                        <td><?= date('d/m/Y', strtotime($o['fecha_creacion'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-ver-orden" data-id="<?= $o['id_orden'] ?>"><i class="fas fa-eye"></i></button>
                            <?php if ($o['estado'] != 'Completada' && $o['estado'] != 'Cancelada'): ?>
                            <button class="btn btn-sm btn-outline-success btn-completar-orden" data-id="<?= $o['id_orden'] ?>"><i class="fas fa-check"></i></button>
                            <button class="btn btn-sm btn-outline-danger btn-cancelar-orden" data-id="<?= $o['id_orden'] ?>"><i class="fas fa-ban"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ordenes)): ?><tr><td colspan="8" class="text-center text-muted">No hay ordenes registradas</td></tr><?php endif; ?>
                </tbody>
            </table>
            </div>
        </div></div>
    </div></div>
</div>

<!-- Modal nueva orden -->
<div class="modal fade" id="modalNuevaOrden" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="controles/crear_orden.php">
        <?= csrf_field() ?>
        <div class="modal-header"><h5>Nueva orden de servicio</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Cliente</label>
                <select name="id_cliente" class="form-select" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c['id_cliente'] ?>"><?= hescape($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="Soporte">Soporte</option>
                        <option value="Instalacion">Instalacion</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Retiro">Retiro</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select" required>
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Urgente">Urgente</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Tecnico asignado</label>
                <select name="id_tecnico" class="form-select">
                    <option value="">Sin asignar</option>
                    <?php foreach ($tecnicos as $t): ?>
                    <option value="<?= $t['id_usuario'] ?>"><?= hescape($t['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Crear orden</button>
        </div>
    </form>
</div></div></div>

<!-- Modal ver orden -->
<div class="modal fade" id="modalVerOrden" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5>Detalle de orden</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body" id="detalleOrdenBody">Cargando...</div>
</div></div></div>

<script>
$('#tablaOrdenes').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
    order: [[0, 'desc']],
    pageLength: 25,
    responsive: true,
    autoWidth: false,
    columnDefs: [{ orderable: false, targets: -1 }]
});
$(document).on('click', '.btn-ver-orden', function() {
    $.get('controles/ver_orden.php?id=' + $(this).data('id'), function(r) {
        $('#detalleOrdenBody').html(r);
        $('#modalVerOrden').modal('show');
    });
});
$(document).on('click', '.btn-completar-orden', function() {
    const id = $(this).data('id');
    Swal.fire({title:'Completar orden',input:'textarea',inputLabel:'Solucion aplicada',inputPlaceholder:'Describa la solucion...',showCancelButton:true}).then(r => {
        if (r.isConfirmed) {
            $.post('controles/cambiar_estado.php', {id_orden:id,estado:'Completada',solucion:r.value,_csrf_token:'<?= $_SESSION['_csrf_token'] ?>'}, function() { location.reload(); });
        }
    });
});
$(document).on('click', '.btn-cancelar-orden', function() {
    const id = $(this).data('id');
    Swal.fire({title:'Cancelar orden',text:'Esta seguro?',icon:'warning',showCancelButton:true,confirmButtonColor:'#d33'}).then(r => {
        if (r.isConfirmed) {
            $.post('controles/cambiar_estado.php', {id_orden:id,estado:'Cancelada',_csrf_token:'<?= $_SESSION['_csrf_token'] ?>'}, function() { location.reload(); });
        }
    });
});
</script>
<?php include('../parte2.php'); ?>

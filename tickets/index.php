<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

$tickets = $pdo->query("SELECT t.*, c.nombre AS cliente_nombre, c.telefono AS cliente_telefono,
    u.nombre AS usuario_nombre FROM tb_tickets t
    LEFT JOIN tb_clientes c ON t.id_cliente = c.id_cliente
    LEFT JOIN tb_usuarios u ON t.id_usuario = u.id_usuario
    ORDER BY t.fecha_creacion DESC")->fetchAll(PDO::FETCH_ASSOC);

$clientes = $pdo->query("SELECT id_cliente, nombre FROM tb_clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$estados = ['Abierto' => 'warning', 'En Proceso' => 'info', 'Resuelto' => 'success', 'Cerrado' => 'secondary'];
$prioridades = ['Baja' => 'success', 'Media' => 'warning', 'Alta' => 'danger', 'Urgente' => 'danger'];
?>
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0"><i class="fas fa-headset me-2"></i>Soporte / Tickets</h1></div><div class="col-sm-6 text-end"><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoTicket"><i class="fas fa-plus"></i> Nuevo ticket</button></div></div></div></div>
    <div class="content"><div class="container-fluid">
        <div class="card"><div class="card-body p-0">
            <div class="table-container">
            <table id="tablaTickets" class="table table-sm mb-0">
                <thead><tr><th>Ticket</th><th>Cliente</th><th>Asunto</th><th>Categoria</th><th>Prioridad</th><th>Estado</th><th>Asignado</th><th>Fecha</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><?= hescape($t['numero_ticket']) ?></td>
                        <td><?= hescape($t['cliente_nombre'] ?? '-') ?></td>
                        <td><?= hescape(mb_substr($t['asunto'], 0, 40)) ?><?= mb_strlen($t['asunto']) > 40 ? '...' : '' ?></td>
                        <td><span class="badge bg-secondary"><?= $t['categoria'] ?></span></td>
                        <td><span class="badge bg-<?= $prioridades[$t['prioridad']] ?? 'secondary' ?>"><?= $t['prioridad'] ?></span></td>
                        <td><span class="badge bg-<?= $estados[$t['estado']] ?? 'secondary' ?>"><?= $t['estado'] ?></span></td>
                        <td><?= hescape($t['usuario_nombre'] ?? '-') ?></td>
                        <td><?= date('d/m/Y', strtotime($t['fecha_creacion'])) ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-info btn-ver-ticket" data-id="<?= $t['id_ticket'] ?>"><i class="fas fa-eye"></i></button>
                            <?php if ($t['estado'] != 'Resuelto' && $t['estado'] != 'Cerrado'): ?>
                            <button class="btn btn-sm btn-outline-primary btn-asignar-ticket" data-id="<?= $t['id_ticket'] ?>"><i class="fas fa-user-plus"></i></button>
                            <button class="btn btn-sm btn-outline-success btn-resolver-ticket" data-id="<?= $t['id_ticket'] ?>"><i class="fas fa-check"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tickets)): ?><tr><td colspan="9" class="text-center text-muted">No hay tickets registrados</td></tr><?php endif; ?>
                </tbody>
            </table>
            </div>
        </div></div>
    </div></div>
</div>

<!-- Modal nuevo ticket -->
<div class="modal fade" id="modalNuevoTicket" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="controles/crear_ticket.php">
        <?= csrf_field() ?>
        <div class="modal-header"><h5>Nuevo ticket de soporte</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
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
            <div class="mb-3">
                <label class="form-label">Asunto</label>
                <input type="text" name="asunto" class="form-control" required maxlength="200">
            </div>
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <select name="categoria" class="form-select">
                    <option value="Fallo de conexion">Fallo de conexion</option>
                    <option value="Equipo">Equipo</option>
                    <option value="Facturacion">Facturacion</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-select">
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Urgente">Urgente</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Asignar a</label>
                    <select name="id_usuario" class="form-select">
                        <option value="">Sin asignar</option>
                        <?php
                        $usuarios = $pdo->query("SELECT id_usuario, nombre FROM tb_usuarios ORDER BY nombre")->fetchAll();
                        foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id_usuario'] ?>"><?= hescape($u['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Crear ticket</button>
        </div>
    </form>
</div></div></div>

<!-- Modal ver ticket -->
<div class="modal fade" id="modalVerTicket" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5>Detalle de ticket</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body" id="detalleTicketBody">Cargando...</div>
</div></div></div>

<script>
$('#tablaTickets').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
    order: [[0, 'desc']],
    pageLength: 25,
    responsive: true,
    autoWidth: false
});
$(document).on('click', '.btn-ver-ticket', function() {
    $.get('controles/ver_ticket.php?id=' + $(this).data('id'), function(r) {
        $('#detalleTicketBody').html(r);
        $('#modalVerTicket').modal('show');
    });
});
$(document).on('click', '.btn-asignar-ticket', function() {
    const id = $(this).data('id');
    Swal.fire({title:'Asignar ticket',input:'number',inputLabel:'ID del tecnico',showCancelButton:true}).then(r => {
        if (r.isConfirmed) {
            $.post('controles/cambiar_estado.php', {id_ticket:id,id_usuario:r.value,estado:'En Proceso',_csrf_token:'<?= $_SESSION['_csrf_token'] ?>'}, function() { location.reload(); });
        }
    });
});
$(document).on('click', '.btn-resolver-ticket', function() {
    const id = $(this).data('id');
    Swal.fire({title:'Resolver ticket',input:'textarea',inputLabel:'Solucion',inputPlaceholder:'Describa la solucion...',showCancelButton:true}).then(r => {
        if (r.isConfirmed) {
            $.post('controles/cambiar_estado.php', {id_ticket:id,estado:'Resuelto',solucion:r.value,_csrf_token:'<?= $_SESSION['_csrf_token'] ?>'}, function() { location.reload(); });
        }
    });
});
</script>
<?php include('../parte2.php'); ?>

<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

// SNMP config
$comunidad = getenv('SNMP_COMMUNITY') ?: 'public';
$oid_estado = getenv('SNMP_OID_ESTADO') ?: '1.3.6.1.2.1.1.1.0'; // sysDescr
$timeout = 3;

$dispositivos = $pdo->query("SELECT d.*, c.nombre AS cliente_nombre FROM tb_dispositivos d LEFT JOIN tb_clientes c ON d.id_cliente=c.id_cliente ORDER BY d.ip")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0"><i class="fas fa-network-wired me-2 text-primary"></i>Monitoreo SNMP</h1></div><div class="col-sm-6 text-end">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevo"><i class="fas fa-plus"></i> Agregar dispositivo</button>
        <button class="btn btn-primary btn-sm" onclick="escanearTodos()"><i class="fas fa-sync"></i> Escanear todos</button>
    </div></div></div></div>
    <div class="content"><div class="container-fluid">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-container">
                <table class="table table-sm mb-0" id="tablaMonitoreo">
                    <thead><tr><th>IP</th><th>Nombre</th><th>Cliente</th><th>Tipo</th><th>Estado</th><th>Señal</th><th>Último check</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($dispositivos as $d): ?>
                        <tr id="row-<?= $d['id_dispositivo'] ?>">
                            <td><code><?= hescape($d['ip']) ?></code></td>
                            <td><?= hescape($d['nombre'] ?: '-') ?></td>
                            <td><?= hescape($d['cliente_nombre'] ?: '-') ?></td>
                            <td><span class="badge bg-secondary"><?= hescape($d['tipo']) ?></span></td>
                            <td id="estado-<?= $d['id_dispositivo'] ?>">
                                <span class="badge bg-<?= $d['ultimo_estado']=='Online'?'success':($d['ultimo_estado']=='Offline'?'danger':'secondary') ?> estatus-<?= $d['id_dispositivo'] ?>">
                                    <?= $d['ultimo_estado'] ?: 'Sin dato' ?>
                                </span>
                            </td>
                            <td id="senal-<?= $d['id_dispositivo'] ?>">
                                <?php if ($d['ultimo_estado']=='Online'): ?>
                                <div class="progress" style="height:8px;width:80px;">
                                    <div class="progress-bar bg-<?= $d['ultimo_check_signal']>70?'success':($d['ultimo_check_signal']>30?'warning':'danger') ?>" style="width:<?= $d['ultimo_check_signal'] ?? 0 ?>%"></div>
                                </div>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td id="fecha-<?= $d['id_dispositivo'] ?>"><?= (!empty($d['ultimo_check']) && $d['ultimo_check'] != '0000-00-00 00:00:00') ? date('d/m/Y H:i', strtotime($d['ultimo_check'])) : '-' ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary escanear-btn" data-id="<?= $d['id_dispositivo'] ?>" data-ip="<?= hescape($d['ip']) ?>"><i class="fas fa-sync"></i></button>
                                <a href="javascript:void(0)" onclick="eliminar(<?= $d['id_dispositivo'] ?>)" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div></div>
</div>

<div class="modal fade" id="modalNuevo"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Nuevo dispositivo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="controles/guardar.php">
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">IP del dispositivo</label><input type="text" name="ip" class="form-control" placeholder="192.168.1.1" required></div>
            <div class="mb-3"><label class="form-label">Nombre (opcional)</label><input type="text" name="nombre" class="form-control" placeholder="Router principal"></div>
            <div class="mb-3"><label class="form-label">Tipo</label><select name="tipo" class="form-select">
                <option>Router</option><option>Switch</option><option>Access Point</option><option>ONT</option><option>Servidor</option><option>Otro</option>
            </select></div>
            <div class="mb-3"><label class="form-label">Cliente (opcional)</label>
                <select name="id_cliente" class="form-select"><option value="">Ninguno</option>
                <?php $clientes = $pdo->query("SELECT id_cliente,nombre FROM tb_clientes ORDER BY nombre")->fetchAll(); foreach ($clientes as $c): ?>
                    <option value="<?= $c['id_cliente'] ?>"><?= hescape($c['nombre']) ?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </form>
</div></div></div>

<?php include('../parte2.php'); ?>
<script>
$(function() {
    $('#tablaMonitoreo').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.10/i18n/es-ES.json' },
        order: [[0, 'asc']],
        pageLength: 25,
        autoWidth: false,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.10/i18n/es-ES.json',
            emptyTable: 'Sin dispositivos registrados'
        },
        columns: [
            null, null, null, null,
            null, null, null, { orderable: false }
        ]
    });
});
function escanearBtn(id, ip) {
    $('#estado-' + id).html('<span class="badge bg-secondary"><i class="fas fa-spinner fa-spin"></i></span>');
    $('#senal-' + id).html('<span class="badge bg-secondary"><i class="fas fa-spinner fa-spin"></i></span>');
    $('#fecha-' + id).html('<span class="badge bg-secondary"><i class="fas fa-spinner fa-spin"></i></span>');
    $.get('controles/escanear.php?id=' + id + '&ip=' + encodeURIComponent(ip), function(r) {
        var d = JSON.parse(r);
        var cls = d.estado == 'Online' ? 'success' : 'danger';
        $('#estado-' + id).html('<span class="badge bg-' + cls + '">' + d.estado + '</span>');
        if (d.estado == 'Online') {
            var pcls = d.senal > 70 ? 'success' : (d.senal > 30 ? 'warning' : 'danger');
            $('#senal-' + id).html('<div class="progress" style="height:8px;width:80px;"><div class="progress-bar bg-' + pcls + '" style="width:' + d.senal + '%"></div></div>');
        } else { $('#senal-' + id).html('-'); }
        $('#fecha-' + id).html(d.fecha);
    });
}
function escanearTodos() {
    $('.escanear-btn').each(function() {
        var id = $(this).data('id');
        var ip = $(this).data('ip');
        escanearBtn(id, ip);
    });
}
function eliminar(id) {
    Swal.fire({title:'Eliminar dispositivo?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Eliminar'}).then(r=>{if(r.isConfirmed)window.location='controles/eliminar.php?id='+id;});
}
$(document).ready(function() {
    $('.escanear-btn').click(function() {
        var id = $(this).data('id');
        var ip = $(this).data('ip');
        escanearBtn(id, ip);
    });
});
</script>

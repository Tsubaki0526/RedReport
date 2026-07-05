<?php
include('../sesion.php');
include('../parte1.php');
if ($_SESSION['id_rol'] != 1) {
    echo "<script>alert('Acceso denegado'); window.location='../index.php';</script>";
    exit;
}
require_once '../app/config/conexion.php';
$plantillas = $pdo->query("SELECT * FROM tb_plantillas_email ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-envelope-open-text me-2"></i>Plantillas de Email</h1>
            </div>
            <div class="col-sm-6 text-end">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPlantilla"><i class="fas fa-plus"></i> Nueva plantilla</button>
            </div>
        </div>
    </div>
</div>
<div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header"><i class="fas fa-list me-2 text-primary"></i>Plantillas disponibles</div>
                <div class="card-body">
                    <div class="table-container">
                        <table id="tablaPlantillas" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Asunto</th>
                                    <th>Variables</th>
                                    <th>Actualizado</th>
                                    <th style="width:100px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plantillas as $p): ?>
                                <tr>
                                    <td class="fw-bold"><?= hescape($p['nombre']) ?></td>
                                    <td><?= hescape($p['asunto']) ?></td>
                                    <td><code style="font-size:12px;"><?= hescape($p['variables'] ?? '') ?></code></td>
                                    <td class="text-muted small"><?= hescape($p['updated_at'] ?? $p['created_at']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning btn-editar" title="Editar"
                                            data-id="<?= $p['id_plantilla'] ?>"
                                            data-nombre="<?= hescape($p['nombre']) ?>"
                                            data-asunto="<?= hescape($p['asunto']) ?>"
                                            data-cuerpo="<?= hescape($p['cuerpo']) ?>"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger btn-eliminar" title="Eliminar"
                                            data-id="<?= $p['id_plantilla'] ?>"
                                            data-nombre="<?= hescape($p['nombre']) ?>"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
        </div>
    </div>
</div>

<!-- Modal crear/editar plantilla -->
<div class="modal fade" id="modalPlantilla" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formPlantilla" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="plantillaId">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPlantillaTitle">Nueva plantilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="plantillaNombre" class="form-control" required maxlength="100" placeholder="Ej: Factura nuevo cliente">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Asunto <span class="text-danger">*</span></label>
                                <input type="text" name="asunto" id="plantillaAsunto" class="form-control" required maxlength="255" placeholder="Ej: Factura #{numero_factura}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cuerpo del correo <span class="text-danger">*</span></label>
                        <textarea name="cuerpo" id="plantillaCuerpo" class="form-control" rows="12" required placeholder="Escribe el contenido HTML del correo..."></textarea>
                    </div>
                    <div class="alert alert-info mb-0 py-2 small">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Variables disponibles:</strong> Puedes usar las siguientes variables en el asunto y el cuerpo. Serán reemplazadas automáticamente al enviar.
                    </div>
                    <div class="mt-2 p-2 bg-light rounded">
                        <code class="d-block mb-1">{nombre_cliente}</code>
                        <code class="d-block mb-1">{numero_factura}</code>
                        <code class="d-block mb-1">{monto}</code>
                        <code class="d-block mb-1">{fecha_vencimiento}</code>
                        <code class="d-block mb-1">{empresa_nombre}</code>
                        <code class="d-block mb-1">{url_pago}</code>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarPlantilla"><i class="fas fa-save me-1"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../parte2.php'); ?>
<script>
$(document).ready(function() {
    const table = $('#tablaPlantillas').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
        columnDefs: [{ orderable: false, targets: 4 }],
        order: [[0, 'asc']]
    });

    function resetForm() {
        $('#formPlantilla')[0].reset();
        $('#plantillaId').val('');
        $('#modalPlantillaTitle').text('Nueva plantilla');
    }

    $('#modalPlantilla').on('hidden.bs.modal', resetForm);

    $('.btn-editar').on('click', function() {
        const btn = $(this);
        $('#plantillaId').val(btn.data('id'));
        $('#plantillaNombre').val(btn.data('nombre'));
        $('#plantillaAsunto').val(btn.data('asunto'));
        $('#plantillaCuerpo').val(btn.data('cuerpo'));
        $('#modalPlantillaTitle').text('Editar plantilla');
        $('#modalPlantilla').modal('show');
    });

    $('#formPlantilla').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnGuardarPlantilla').prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');
        const data = $(this).serialize() + '&action=guardar';
        $.ajax({
            url: 'controles/guardar_plantilla.php',
            type: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Guardado', text: res.message, timer: 1500, showConfirmButton: false })
                    .then(() => window.location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message });
            }
        }).fail(function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar');
        });
    });

    $('.btn-eliminar').on('click', function() {
        const btn = $(this);
        const id = btn.data('id');
        const nombre = btn.data('nombre');
        Swal.fire({
            title: 'Eliminar plantilla',
            text: 'Se eliminará la plantilla "' + nombre + '". Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controles/eliminar_plantilla.php',
                    type: 'POST',
                    data: { id: id, _csrf_token: '<?= csrf_token() ?>' },
                    dataType: 'json'
                }).done(function(res) {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Eliminado', text: res.message, timer: 1500, showConfirmButton: false })
                            .then(() => window.location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                    }
                }).fail(function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
                });
            }
        });
    });
});
</script>

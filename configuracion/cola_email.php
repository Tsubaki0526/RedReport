<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

if ($_SESSION['id_rol'] != 1) { echo "<script>window.location='../index.php';</script>"; exit; }

$status = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(estado='pendiente') AS pendientes,
        SUM(estado='enviado') AS enviados,
        SUM(estado='error') AS errores
    FROM tb_email_queue
")->fetch();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-envelope-open-text me-2 text-primary"></i>Cola de Correos</h1>
            </div>
            <div class="col-sm-6 text-end text-muted">
                <span id="fechaHora"></span>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?= (int)$status['pendientes'] ?></div>
                    <div class="stat-label">Pendientes</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value"><?= (int)$status['enviados'] ?></div>
                    <div class="stat-label">Enviados</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card red">
                    <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-value"><?= (int)$status['errores'] ?></div>
                    <div class="stat-label">Errores</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card teal">
                    <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                    <div class="stat-value"><?= (int)$status['total'] ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="fas fa-list me-2"></i>Historial de Correos</h3>
                <div>
                    <button class="btn btn-warning btn-sm me-2" id="btnRetry">
                        <i class="fas fa-redo"></i> Reintentar errores
                    </button>
                    <button class="btn btn-primary btn-sm" id="btnProcess">
                        <i class="fas fa-play"></i> Procesar cola
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaCola" class="table table-bordered table-hover w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Para</th>
                                <th>Asunto</th>
                                <th>Estado</th>
                                <th>Intentos</th>
                                <th>Error</th>
                                <th>Creado</th>
                                <th>Enviado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rows = $pdo->query("SELECT * FROM tb_email_queue ORDER BY created_at DESC LIMIT 500")->fetchAll();
                            foreach ($rows as $r):
                                $badge = $r['estado'] == 'enviado' ? 'success' : ($r['estado'] == 'error' ? 'danger' : 'warning');
                            ?>
                            <tr>
                                <td><?= $r['id_cola'] ?></td>
                                <td><?= hescape($r['para']) ?></td>
                                <td><?= hescape($r['asunto']) ?></td>
                                <td><span class="badge bg-<?= $badge ?>"><?= $r['estado'] ?></span></td>
                                <td><?= $r['intentos'] ?></td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= hescape($r['error_msg'] ?? '-') ?></td>
                                <td><?= $r['created_at'] ?></td>
                                <td><?= $r['sent_at'] ?? '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
$(document).ready(function() {
    const table = $('#tablaCola').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        pageLength: 25,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });

    function processQueue() {
        $('#btnProcess').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        $.get('../app/controles/procesar_cola.php?action=process&limit=10', function(res) {
            if (res.success) {
                const d = res.data;
                Swal.fire({
                    icon: 'success',
                    title: 'Cola procesada',
                    html: '<strong>' + d.sent + '</strong> enviados, <strong>' + d.failed + '</strong> fallos',
                    confirmButtonText: 'Aceptar'
                }).then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.error });
                $('#btnProcess').prop('disabled', false).html('<i class="fas fa-play"></i> Procesar cola');
            }
        }).fail(function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexion' });
            $('#btnProcess').prop('disabled', false).html('<i class="fas fa-play"></i> Procesar cola');
        });
    }

    $('#btnProcess').click(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Procesar cola',
            text: 'Se enviaran hasta 10 correos pendientes',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Procesar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) processQueue();
        });
    });

    $('#btnRetry').click(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Reintentar errores',
            text: 'Se marcaran todos los correos con error como pendientes',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Reintentar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.get('../app/controles/procesar_cola.php?action=retry', function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Listo',
                            text: res.data.reintentados + ' correos marcados como pendientes',
                            confirmButtonText: 'Aceptar'
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.error });
                    }
                });
            }
        });
    });
});
</script>
<?php include('../parte2.php'); ?>

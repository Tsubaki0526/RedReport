<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

if ($_SESSION['id_rol'] != 1) { echo "<script>window.location='../index.php';</script>"; exit; }

// Get DB name
$dbName = DB_NAME;

// Get table status
$tables = $pdo->query("SHOW TABLE STATUS FROM `$dbName`")->fetchAll(PDO::FETCH_ASSOC);

$totalRows = 0;
$totalData = 0;
$totalIndex = 0;
$totalFree = 0;

foreach ($tables as &$t) {
    $t['data_mb'] = round(($t['Data_length'] ?? 0) / 1048576, 2);
    $t['index_mb'] = round(($t['Index_length'] ?? 0) / 1048576, 2);
    $t['free_mb'] = round(($t['Data_free'] ?? 0) / 1048576, 2);
    $t['rows_f'] = $t['Rows'] ?? 0;
    $totalRows += $t['Rows'] ?? 0;
    $totalData += $t['Data_length'] ?? 0;
    $totalIndex += $t['Index_length'] ?? 0;
    $totalFree += $t['Data_free'] ?? 0;
}
unset($t);

$totalDataMB = round($totalData / 1048576, 2);
$totalIndexMB = round($totalIndex / 1048576, 2);
$totalFreeMB = round($totalFree / 1048576, 2);
$csrfToken = csrf_token();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-database me-2 text-primary"></i>Mantenimiento de Base de Datos</h1>
            </div>
            <div class="col-sm-6 text-end">
                <span class="badge bg-info fs-6 p-2"><?= hescape($dbName) ?></span>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-table"></i></div>
                    <div class="stat-value"><?= count($tables) ?></div>
                    <div class="stat-label">Tablas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-list"></i></div>
                    <div class="stat-value"><?= number_format($totalRows) ?></div>
                    <div class="stat-label">Total Registros</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card orange">
                    <div class="stat-icon"><i class="fas fa-hdd"></i></div>
                    <div class="stat-value"><?= $totalDataMB ?> MB</div>
                    <div class="stat-label">Tamaño datos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card teal">
                    <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
                    <div class="stat-value"><?= $totalIndexMB ?> MB</div>
                    <div class="stat-label">Tamaño índices</div>
                </div>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-success me-2" id="btnOptimizar"><i class="fas fa-tools me-1"></i>Optimizar todas</button>
                <button class="btn btn-warning me-2" id="btnReparar"><i class="fas fa-wrench me-1"></i>Reparar todas</button>
                <button class="btn btn-info me-2" id="btnAnalizar"><i class="fas fa-chart-bar me-1"></i>Analizar todas</button>
            </div>
        </div>

        <!-- Table listing -->
        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-list me-2 text-primary"></i>Tablas</h3></div>
            <div class="card-body">
                <div class="table-container">
                    <table id="tablaMantenimiento" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Engine</th>
                                <th>Filas</th>
                                <th>Datos (MB)</th>
                                <th>Índices (MB)</th>
                                <th>Libre (MB)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tables as $t): ?>
                            <tr>
                                <td class="fw-semibold"><?= hescape($t['Name']) ?></td>
                                <td><?= hescape($t['Engine'] ?? '-') ?></td>
                                <td><?= number_format($t['rows_f']) ?></td>
                                <td><?= $t['data_mb'] ?></td>
                                <td><?= $t['index_mb'] ?></td>
                                <td><?= $t['free_mb'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php include('../parte2.php'); ?>
<script>
const CSRF = '<?= $csrfToken ?>';

function ejecutarAccion(accion, label) {
    Swal.fire({
        title: 'Procesando...',
        text: label + ' todas las tablas. Esto puede tomar unos segundos.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    $.ajax({
        url: 'controles/mantenimiento_bd.php',
        type: 'POST',
        data: { action: accion, _csrf_token: CSRF },
        dataType: 'json'
    }).done(function(res) {
        if (res.success) {
            let html = '<div style="text-align:left;max-height:300px;overflow-y:auto;">';
            (res.results || []).forEach(function(r) {
                const icon = r.status === 'OK' || r.status === 'success' ? '✅' : '❌';
                html += '<div>' + icon + ' <strong>' + r.table + '</strong>: ' + r.status + '</div>';
            });
            html += '</div>';
            Swal.fire({
                icon: 'success',
                title: label + ' completado',
                html: html,
                confirmButtonText: 'Aceptar'
            });
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Ocurrió un error' });
        }
    }).fail(function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión' });
    });
}

$('#btnOptimizar').on('click', function() { ejecutarAccion('optimize', 'Optimizando'); });
$('#btnReparar').on('click', function() { ejecutarAccion('repair', 'Reparando'); });
$('#btnAnalizar').on('click', function() { ejecutarAccion('analyze', 'Analizando'); });

$('#tablaMantenimiento').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
    order: [[0, 'asc']]
});
</script>

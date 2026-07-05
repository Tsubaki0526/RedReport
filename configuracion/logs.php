<?php
include('../sesion.php');
include('../parte1.php');

if ($_SESSION['id_rol'] != 1) { echo "<script>window.location='../index.php';</script>"; exit; }

// Find error log file
$logCandidates = [
    ini_get('error_log'),
    'C:/xampp/php/logs/php_error_log',
    'C:/xampp/apache/logs/error.log',
    __DIR__ . '/../../error_log',
    __DIR__ . '/../../logs/app.log',
    __DIR__ . '/../../logs/error.log',
    __DIR__ . '/../../logs/php_error.log',
];

$logFile = null;
foreach ($logCandidates as $path) {
    if ($path && file_exists($path) && is_file($path) && is_readable($path)) {
        $logFile = $path;
        break;
    }
}

$logEntries = [];
if ($logFile) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_slice($lines, -500);
    $logFileSize = filesize($logFile);
    $logFileSizeFormatted = $logFileSize > 1048576 ? round($logFileSize / 1048576, 2) . ' MB' : ($logFileSize > 1024 ? round($logFileSize / 1024, 1) . ' KB' : $logFileSize . ' B');
    $logFileMtime = date('d/m/Y H:i:s', filemtime($logFile));

    $pattern = '/^(\d{4}[-\/]\d{2}[-\/]\d{2}[ \d:]+)\s+\[(.+?)\]\s+(.+)$/';
    foreach ($lines as $line) {
        if (preg_match($pattern, $line, $m)) {
            $type = strtoupper(trim($m[2]));
            if (str_contains($type, 'ERROR') || str_contains($type, 'FATAL') || str_contains($type, 'CRITICAL')) {
                $typeClass = 'danger';
            } elseif (str_contains($type, 'WARN') || str_contains($type, 'PARSE') || str_contains($type, 'EXCEPTION')) {
                $typeClass = 'warning';
            } else {
                $typeClass = 'info';
            }
            $logEntries[] = [
                'fecha' => $m[1],
                'tipo' => $type,
                'tipo_class' => $typeClass,
                'mensaje' => hescape($m[3])
            ];
        } else {
            // Lines without a date pattern might be continuation
            if (count($logEntries) > 0) {
                $logEntries[count($logEntries) - 1]['mensaje'] .= '<br>' . hescape(substr($line, 0, 200));
            } else {
                $logEntries[] = [
                    'fecha' => '-',
                    'tipo' => 'INFO',
                    'tipo_class' => 'info',
                    'mensaje' => hescape(substr($line, 0, 200))
                ];
            }
        }
    }
}

$csrfToken = csrf_token();
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-clipboard-list me-2 text-primary"></i>Visor de Logs</h1>
            </div>
            <div class="col-sm-6 text-end">
                <?php if ($logFile): ?>
                <a href="controles/descargar_log.php" class="btn btn-success btn-sm me-2"><i class="fas fa-download me-1"></i>Descargar log</a>
                <button class="btn btn-danger btn-sm" id="btnLimpiarLog"><i class="fas fa-eraser me-1"></i>Limpiar log</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="container-fluid">

        <?php if ($logFile): ?>
        <!-- Info -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-file"></i></div>
                    <div class="stat-value" style="font-size:14px;"><?= hescape(basename($logFile)) ?></div>
                    <div class="stat-label">Archivo</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card green">
                    <div class="stat-value"><?= hescape($logFileSizeFormatted) ?></div>
                    <div class="stat-label">Tamaño</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card orange">
                    <div class="stat-value"><?= hescape($logFileMtime) ?></div>
                    <div class="stat-label">Última modificación</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-list me-2 text-primary"></i>Entradas del log (últimas 500 líneas)</h3></div>
            <div class="card-body">
                <div class="table-container">
                    <table id="tablaLogs" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th style="width:170px;">Fecha</th>
                                <th style="width:120px;">Tipo</th>
                                <th>Mensaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logEntries as $entry): ?>
                            <tr>
                                <td class="text-muted small"><?= hescape($entry['fecha']) ?></td>
                                <td><span class="badge bg-<?= $entry['tipo_class'] ?>"><?= hescape($entry['tipo']) ?></span></td>
                                <td class="small" style="word-break:break-word;max-width:500px;"><?= $entry['mensaje'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- No log found -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No se encontró ningún archivo de log</h4>
                <p class="text-muted">El sistema buscó en las siguientes ubicaciones:</p>
                <div class="d-flex justify-content-center">
                    <ul class="list-unstyled text-start">
                        <?php foreach ($logCandidates as $path): ?>
                        <?php if ($path): ?>
                        <li><code><?= hescape($path) ?></code> <?= file_exists($path) ? '<span class="text-success"><i class="fas fa-check"></i></span>' : '<span class="text-danger"><i class="fas fa-times"></i></span>' ?></li>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <p class="text-muted mt-3 small">Verifica la configuración de <code>error_log</code> en php.ini o la existencia de archivos de log de Apache.</p>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php include('../parte2.php'); ?>
<script>
$(document).ready(function() {
    $('#tablaLogs').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 50
    });

    $('#btnLimpiarLog').on('click', function() {
        Swal.fire({
            title: 'Limpiar archivo de log',
            text: 'Se eliminará todo el contenido del archivo de log. Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'controles/limpiar_log.php',
                    type: 'POST',
                    data: { _csrf_token: '<?= $csrfToken ?>' },
                    dataType: 'json'
                }).done(function(res) {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Log limpiado', text: res.message, timer: 1500, showConfirmButton: false })
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

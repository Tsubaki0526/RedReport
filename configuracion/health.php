<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

if ($_SESSION['id_rol'] != 1) { echo "<script>window.location='../index.php';</script>"; exit; }

// PHP info
$phpVersion = phpversion();
$serverSoft = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';
$memLimit = ini_get('memory_limit');
$uploadMax = ini_get('upload_max_filesize');
$maxExecTime = ini_get('max_execution_time') . 's';

// MySQL info
$mysqlVersion = $pdo->query("SELECT VERSION()")->fetchColumn();
$mysqlUptime = $pdo->query("SHOW GLOBAL STATUS LIKE 'Uptime'")->fetch(PDO::FETCH_ASSOC);
$mysqlUptime = $mysqlUptime ? $mysqlUptime['Value'] : 0;
$totalQueries = $pdo->query("SHOW GLOBAL STATUS LIKE 'Questions'")->fetch(PDO::FETCH_ASSOC);
$totalQueries = $totalQueries ? $totalQueries['Value'] : 0;
$dbCount = $pdo->query("SELECT COUNT(*) FROM information_schema.SCHEMATA")->fetchColumn();
$connections = $pdo->query("SHOW GLOBAL STATUS LIKE 'Threads_connected'")->fetch(PDO::FETCH_ASSOC);
$connections = $connections ? $connections['Value'] : 0;

// Disk space (use project root as base)
$rootPath = __DIR__ . '/../..';
$diskTotal = @disk_total_space($rootPath);
$diskFree = @disk_free_space($rootPath);
$diskUsed = $diskTotal - $diskFree;
$diskPct = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

// Backup info
$backupDir = __DIR__ . '/../backup';
$backupFiles = glob($backupDir . '/backup_*.sql');
$backupCount = count($backupFiles);
$backupSize = 0;
$lastBackup = 'Nunca';
$lastBackupSize = '';
if ($backupCount > 0) {
    $latest = null;
    $latestTime = 0;
    foreach ($backupFiles as $bf) {
        $mtime = filemtime($bf);
        $backupSize += filesize($bf);
        if ($mtime > $latestTime) {
            $latestTime = $mtime;
            $latest = $bf;
        }
    }
    $lastBackup = date('d/m/Y H:i', $latestTime);
    $lastBackupSize = $latest ? (filesize($latest) > 1048576 ? round(filesize($latest) / 1048576, 2) . ' MB' : round(filesize($latest) / 1024, 1) . ' KB') : '';
}
$backupSizeFormatted = $backupSize > 1073741824 ? round($backupSize / 1073741824, 2) . ' GB' : ($backupSize > 1048576 ? round($backupSize / 1048576, 2) . ' MB' : round($backupSize / 1024, 1) . ' KB');

// System time
$serverTime = date('d/m/Y H:i:s');
$timezone = date_default_timezone_get();

// System uptime (Windows)
$systemUptime = 'N/A';
if (function_exists('exec')) {
    @exec('wmic os get lastbootuptime', $bootOutput, $retCode);
    if ($retCode === 0 && !empty($bootOutput[1])) {
        $bootStr = trim($bootOutput[1]);
        if (strlen($bootStr) >= 14) {
            $year = substr($bootStr, 0, 4);
            $month = substr($bootStr, 4, 2);
            $day = substr($bootStr, 6, 2);
            $hour = substr($bootStr, 8, 2);
            $min = substr($bootStr, 10, 2);
            $sec = substr($bootStr, 12, 2);
            $bootTime = strtotime("$year-$month-$day $hour:$min:$sec");
            if ($bootTime > 0) {
                $diff = time() - $bootTime;
                $days = floor($diff / 86400);
                $hours = floor(($diff % 86400) / 3600);
                $mins = floor(($diff % 3600) / 60);
                $systemUptime = "{$days}d {$hours}h {$mins}m";
            }
        }
    }
}

// Format MySQL uptime
$mysqlUptimeDays = floor($mysqlUptime / 86400);
$mysqlUptimeRem = $mysqlUptime % 86400;
$mysqlUptimeHours = floor($mysqlUptimeRem / 3600);
$mysqlUptimeMins = floor(($mysqlUptimeRem % 3600) / 60);
$mysqlUptimeStr = "{$mysqlUptimeDays}d {$mysqlUptimeHours}h {$mysqlUptimeMins}m";

// Disk color
$diskColor = $diskPct > 90 ? 'red' : ($diskPct > 70 ? 'orange' : 'teal');

function formatSize($bytes) {
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}
?>
<div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-heartbeat me-2 text-primary"></i>Health Dashboard</h1>
                </div>
                <div class="col-sm-6 text-end text-muted">
                    <span id="fechaHora"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">

            <!-- Stat Cards -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card blue">
                        <div class="stat-icon"><i class="fab fa-php"></i></div>
                        <div class="stat-value"><?= hescape($phpVersion) ?></div>
                        <div class="stat-label">PHP Version</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card green">
                        <div class="stat-icon"><i class="fas fa-database"></i></div>
                        <div class="stat-value"><?= hescape($mysqlVersion) ?></div>
                        <div class="stat-label">MySQL Version</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card <?= $diskColor ?>">
                        <div class="stat-icon"><i class="fas fa-hdd"></i></div>
                        <div class="stat-value"><?= $diskPct ?>%</div>
                        <div class="stat-label">Disco usado</div>
                    </div>
                </div>
            </div>

            <!-- Detail Cards Row 1 -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fab fa-php me-2 text-primary"></i>PHP</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr><td class="text-muted ps-0" style="width:50%">Servidor</td><td class="fw-semibold pe-0 text-end"><?= hescape($serverSoft) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Memoria límite</td><td class="fw-semibold pe-0 text-end"><?= hescape($memLimit) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Subida máxima</td><td class="fw-semibold pe-0 text-end"><?= hescape($uploadMax) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Tiempo ejecución</td><td class="fw-semibold pe-0 text-end"><?= hescape($maxExecTime) ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-database me-2 text-success"></i>MySQL</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr><td class="text-muted ps-0" style="width:50%">Uptime</td><td class="fw-semibold pe-0 text-end"><?= hescape($mysqlUptimeStr) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Consultas totales</td><td class="fw-semibold pe-0 text-end"><?= number_format($totalQueries) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Bases de datos</td><td class="fw-semibold pe-0 text-end"><?= $dbCount ?></td></tr>
                                    <tr><td class="text-muted ps-0">Conexiones activas</td><td class="fw-semibold pe-0 text-end"><?= $connections ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock me-2 text-info"></i>Sistema</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr><td class="text-muted ps-0" style="width:50%">Fecha/Hora</td><td class="fw-semibold pe-0 text-end"><?= hescape($serverTime) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Zona horaria</td><td class="fw-semibold pe-0 text-end"><?= hescape($timezone) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Uptime sistema</td><td class="fw-semibold pe-0 text-end"><?= hescape($systemUptime) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Sistema operativo</td><td class="fw-semibold pe-0 text-end"><?= hescape(php_uname('s') . ' ' . php_uname('r')) ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Cards Row 2 -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-hdd me-2 text-warning"></i>Disco</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-3">
                                <tbody>
                                    <tr><td class="text-muted ps-0" style="width:50%">Capacidad total</td><td class="fw-semibold pe-0 text-end"><?= formatSize($diskTotal) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Espacio libre</td><td class="fw-semibold pe-0 text-end"><?= formatSize($diskFree) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Espacio usado</td><td class="fw-semibold pe-0 text-end"><?= formatSize($diskUsed) ?></td></tr>
                                </tbody>
                            </table>
                            <div class="progress" style="height:12px;border-radius:8px;">
                                <div class="progress-bar <?= $diskPct > 90 ? 'bg-danger' : ($diskPct > 70 ? 'bg-warning' : 'bg-info') ?>" role="progressbar" style="width:<?= $diskPct ?>%" aria-valuenow="<?= $diskPct ?>" aria-valuemin="0" aria-valuemax="100"><?= $diskPct ?>%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-archive me-2 text-purple"></i>Backups</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr><td class="text-muted ps-0" style="width:50%">Último backup</td><td class="fw-semibold pe-0 text-end"><?= hescape($lastBackup) ?></td></tr>
                                    <tr><td class="text-muted ps-0">Tamaño último</td><td class="fw-semibold pe-0 text-end"><?= $lastBackupSize ? hescape($lastBackupSize) : '-' ?></td></tr>
                                    <tr><td class="text-muted ps-0">Total backups</td><td class="fw-semibold pe-0 text-end"><?= $backupCount ?></td></tr>
                                    <tr><td class="text-muted ps-0">Tamaño total</td><td class="fw-semibold pe-0 text-end"><?= hescape($backupSizeFormatted) ?></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

    </div>
</div>
<?php include('../parte2.php'); ?>

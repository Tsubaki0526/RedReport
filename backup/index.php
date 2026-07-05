<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

$backup_dir = __DIR__;
$backup_file = '';
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_backup'])) {
    $db_host = DB_HOST;
    $db_user = DB_USER;
    $db_pass = DB_PASS;
    $db_name = DB_NAME;
    $fecha = date('Y-m-d_H-i-s');
    $backup_file = "backup_{$db_name}_{$fecha}.sql";
    $backup_path = "$backup_dir/$backup_file";

    $cmd = "\"C:\\xampp\\mysql\\bin\\mysqldump\" -h$db_host -u$db_user -p\"$db_pass\" $db_name --routines --triggers > \"$backup_path\" 2>&1";
    exec($cmd, $output, $exit_code);

    if ($exit_code === 0) {
        $mensaje = "Backup creado: $backup_file";
        $pdo->prepare("INSERT INTO tb_bitacora (id_usuario, accion, tabla_afectada, detalle, direccion_ip, fecha_hora) VALUES (?, 'BACKUP', 'tb_*', ?, ?, NOW())")->execute([$_SESSION['id_usuario'], "Backup BD: $backup_file", $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
    } else {
        $error = "Error al crear backup: " . implode("\n", $output);
    }
}

$backups = glob("$backup_dir/backup_*.sql");
usort($backups, function($a, $b) { return filemtime($b) - filemtime($a); });
?>
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0"><i class="fas fa-database me-2 text-primary"></i>Backup de Base de Datos</h1></div></div></div></div>
    <div class="content"><div class="container-fluid">
        <?php if ($mensaje): ?><div class="alert alert-success"><?= hescape($mensaje) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger"><?= nl2br(hescape($error)) ?></div><?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-plus-circle me-2"></i>Nuevo backup</h3></div>
                    <div class="card-body text-center py-4">
                        <form method="POST">
                            <p class="text-muted mb-3">Genera un respaldo completo de la base de datos (estructura + datos + rutinas)</p>
                            <button type="submit" name="crear_backup" class="btn btn-primary btn-lg"><i class="fas fa-download me-2"></i>Crear backup ahora</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-history me-2"></i>Backups disponibles</h3></div>
                    <div class="card-body p-0">
                        <?php if (empty($backups)): ?>
                            <p class="text-muted text-center py-3">No hay backups disponibles</p>
                        <?php else: ?>
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Archivo</th><th>Tamaño</th><th>Fecha</th><th></th></tr></thead>
                                <tbody>
                                    <?php foreach ($backups as $b): $name = basename($b); $size = filesize($b); ?>
                                    <tr>
                                        <td><?= hescape($name) ?></td>
                                        <td><?= $size > 1048576 ? round($size/1048576,2).' MB' : round($size/1024,1).' KB' ?></td>
                                        <td><?= date('d/m/Y H:i', filemtime($b)) ?></td>
                                        <td class="text-end">
                                            <a href="descargar.php?file=<?= urlencode($name) ?>" class="btn btn-sm btn-success"><i class="fas fa-download"></i></a>
                                            <a href="restaurar.php?file=<?= urlencode($name) ?>" class="btn btn-sm btn-warning" onclick="return confirm('Restaurar este backup? Se perderán los datos actuales.')"><i class="fas fa-undo"></i></a>
                                            <a href="eliminar.php?file=<?= urlencode($name) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar este backup?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div></div>
</div>
<?php include('../parte2.php'); ?>

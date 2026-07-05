<?php
include('../sesion.php');
require_once '../app/config/conexion.php';

$id_usuario = $_SESSION['id_usuario'];

$notif = $pdo->prepare("SELECT * FROM tb_notificaciones WHERE (id_usuario = ? OR id_usuario IS NULL) ORDER BY fecha_creacion DESC");
$notif->execute([$id_usuario]);
$notificaciones = $notif->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("UPDATE tb_notificaciones SET leida = 1 WHERE (id_usuario = ? OR id_usuario IS NULL) AND leida = 0");
$stmt->execute([$id_usuario]);

include('../parte1.php');
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-bell me-2"></i>Notificaciones</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <?php if (empty($notificaciones)): ?>
            <div class="card"><div class="card-body text-center text-muted py-5"><i class="fas fa-check-circle fa-3x mb-3"></i><p>No hay notificaciones</p></div></div>
            <?php else: ?>
            <div class="list-group">
                <?php foreach ($notificaciones as $n): ?>
                <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 <?= $n['leida'] ? '' : 'list-group-item-primary' ?>">
                    <div class="mt-1">
                        <span class="badge bg-<?= $n['tipo'] ?> rounded-circle p-2"><i class="fas fa-<?= $n['tipo'] == 'danger' ? 'exclamation' : ($n['tipo'] == 'warning' ? 'exclamation-triangle' : ($n['tipo'] == 'success' ? 'check' : 'info-circle')) ?>"></i></span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1"><?= hescape($n['titulo']) ?></h6>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n['fecha_creacion'])) ?></small>
                        </div>
                        <p class="mb-1"><?= nl2br(hescape($n['mensaje'] ?? '')) ?></p>
                        <?php if ($n['url']): ?><a href="<?= hescape($n['url']) ?>" class="btn btn-sm btn-outline-primary">Ver</a><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>

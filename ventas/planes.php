<?php
include('../sesion.php');
include('../parte1.php');
require_once '../app/config/conexion.php';

$planes = $pdo->query("SELECT * FROM tb_planes ORDER BY precio")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../public/css/redreport.css">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Planes de Servicio</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPlan"><i class="fas fa-plus"></i> Nuevo Plan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header"><i class="fas fa-tags me-2 text-primary"></i>Planes disponibles</div>
                <div class="card-body">
                    <div class="table-wrap">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Nombre</th><th>Velocidad</th><th>Precio</th><th>Descripcion</th><th>Estado</th>
                                    <?php if ($_SESSION['id_rol'] == 1): ?><th>Acciones</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($planes as $p): ?>
                                <tr>
                                    <td class="fw-bold"><?= hescape($p['nombre']) ?></td>
                                    <td><?= hescape($p['velocidad']) ?></td>
                                    <td class="fw-bold text-primary">$<?= number_format($p['precio'], 0) ?></td>
                                    <td><?= hescape($p['descripcion'] ?? '') ?></td>
                                    <td><span class="badge <?= $p['activo'] ? 'bg-success' : 'bg-secondary' ?>"><?= $p['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
                                    <?php if ($_SESSION['id_rol'] == 1): ?>
                                    <td>
                                        <button class="btn btn-sm btn-warning" title="Editar" data-bs-toggle="modal" data-bs-target="#modalPlan<?= $p['id_plan'] ?>"><i class="fas fa-edit"></i></button>
                                        <form method="POST" action="controles/eliminar_plan.php" class="d-inline" onsubmit="return confirm('Eliminar este plan?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id_plan" value="<?= $p['id_plan'] ?>">
                                            <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal crear plan -->
<div class="modal fade" id="modalPlan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="controles/crear_plan.php">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Nuevo Plan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Velocidad</label><input type="text" name="velocidad" class="form-control" placeholder="Ej: 50MB"></div>
                    <div class="mb-3"><label class="form-label">Precio</label><input type="number" name="precio" class="form-control" step="0.01" required></div>
                    <div class="mb-3"><label class="form-label">Descripcion</label><textarea name="descripcion" class="form-control" rows="2"></textarea></div>
                    <div class="form-check"><input type="checkbox" name="activo" class="form-check-input" value="1" checked id="activoPlan"><label class="form-check-label" for="activoPlan">Activo</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales editar plan -->
<?php foreach ($planes as $p): ?>
<div class="modal fade" id="modalPlan<?= $p['id_plan'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="controles/editar_plan.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id_plan" value="<?= $p['id_plan'] ?>">
                <div class="modal-header"><h5 class="modal-title">Editar Plan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" value="<?= hescape($p['nombre']) ?>" required></div>
                    <div class="mb-3"><label class="form-label">Velocidad</label><input type="text" name="velocidad" class="form-control" value="<?= hescape($p['velocidad'] ?? '') ?>"></div>
                    <div class="mb-3"><label class="form-label">Precio</label><input type="number" name="precio" class="form-control" step="0.01" value="<?= $p['precio'] ?>" required></div>
                    <div class="mb-3"><label class="form-label">Descripcion</label><textarea name="descripcion" class="form-control" rows="2"><?= hescape($p['descripcion'] ?? '') ?></textarea></div>
                    <div class="form-check"><input type="checkbox" name="activo" class="form-check-input" value="1" <?= $p['activo'] ? 'checked' : '' ?> id="activoPlan<?= $p['id_plan'] ?>"><label class="form-check-label" for="activoPlan<?= $p['id_plan'] ?>">Activo</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include('../parte2.php'); ?>

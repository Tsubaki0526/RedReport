<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/seguridad.php');
$tipos = $pdo->query("SELECT * FROM tb_tipos_equipo")->fetchAll();
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Registrar Equipo</h1></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Nuevo equipo</h3></div>
                <div class="card-body">
                    <form action="controles/crear_equipo.php" method="POST">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo de equipo</label>
                                <select name="id_tipo_equipo" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($tipos as $t): ?>
                                    <option value="<?= $t['id_tipo_equipo'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Serial</label>
                                <input type="text" name="serial" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-control">
                                    <option value="Disponible">Disponible</option>
                                    <option value="Dañado">Dañado</option>
                                    <option value="Garantia">Garantía</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">Registrar equipo</button>
                        <a href="index.php" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>

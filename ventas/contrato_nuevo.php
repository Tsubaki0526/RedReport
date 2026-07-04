<?php
include('../sesion.php');
include('../parte1.php');
require_once '../app/config/conexion.php';

$clientes = $pdo->query("SELECT id_cliente, nombre, documento, telefono FROM tb_clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$planes = $pdo->query("SELECT * FROM tb_planes WHERE activo = 1 ORDER BY precio")->fetchAll(PDO::FETCH_ASSOC);
$vendedores = $pdo->query("SELECT id_usuario, nombre FROM tb_usuarios WHERE id_rol IN (1,2,4) ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../public/css/redreport.css">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nuevo Contrato</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="controles/crear_contrato.php">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="id_cliente" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['id_cliente'] ?>"><?= hescape($c['nombre']) ?> - <?= hescape($c['documento']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Plan <span class="text-danger">*</span></label>
                                <select name="id_plan" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($planes as $p): ?>
                                    <option value="<?= $p['id_plan'] ?>" data-precio="<?= $p['precio'] ?>"><?= hescape($p['nombre']) ?> - $<?= number_format($p['precio'], 0) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vendedor <span class="text-danger">*</span></label>
                                <select name="id_vendedor" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($vendedores as $v): ?>
                                    <option value="<?= $v['id_usuario'] ?>" <?= ($_SESSION['id_rol'] == 4) ? 'selected' : '' ?>><?= hescape($v['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha de inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha de fin (opcional)</label>
                                <input type="date" name="fecha_fin" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Valor contrato</label>
                                <input type="text" id="valorContrato" class="form-control" readonly placeholder="Seleccione un plan">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Guardar Contrato</button>
                            <a href="contratos.php" class="btn btn-secondary btn-lg"><i class="fas fa-times me-2"></i>Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<script>
document.querySelector('select[name="id_plan"]').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const precio = opt ? opt.getAttribute('data-precio') : 0;
    document.getElementById('valorContrato').value = precio ? '$' + Number(precio).toLocaleString('es-CO') : '';
});
</script>

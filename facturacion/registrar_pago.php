<?php
include('../sesion.php');
verificar_acceso([1, 2]);
include('../parte1.php');
require_once '../app/config/conexion.php';
require_once '../app/config/seguridad.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT f.*, c.nombre AS cliente_nombre, c.documento FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente WHERE f.id_factura = ?");
$stmt->execute([$id]);
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$factura || !in_array($factura['estado'], ['pendiente', 'vencida'])) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Factura no disponible para pago'}).then(()=>window.location='ver.php?id=$id');</script>";
    include('../parte2.php');
    exit;
}
?>
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0">Registrar pago</h1></div></div></div></div>
    <div class="content"><div class="container-fluid">
        <div class="card"><div class="card-body">
            <h5><?= hescape($factura['numero_factura']) ?> - <?= hescape($factura['cliente_nombre']) ?></h5>
            <p class="text-muted">Total: <strong>$<?= number_format($factura['total'], 0) ?></strong></p>
            <form method="POST" action="controles/guardar_pago.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id_factura" value="<?= $id ?>">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Monto a pagar</label>
                        <input type="number" step="0.01" name="monto" class="form-control" value="<?= $factura['total'] ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Metodo de pago</label>
                        <select name="metodo_pago" class="form-select" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Referencia (opcional)</label>
                        <input type="text" name="referencia" class="form-control" placeholder="Nro. transferencia, cheque...">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notas (opcional)</label>
                    <textarea name="notas" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Confirmar pago</button>
                <a href="ver.php?id=<?= $id ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div></div>
    </div></div>
</div>
<?php include('../parte2.php'); ?>

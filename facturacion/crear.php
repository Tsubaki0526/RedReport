<?php
include('../sesion.php');
verificar_acceso([1, 2]);
include('../parte1.php');
require_once '../app/config/conexion.php';
$clientes = $pdo->query("SELECT id_cliente, nombre, documento FROM tb_clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../public/css/redreport.css">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Nueva Factura</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <form id="formFactura" method="POST" action="controles/crear_factura.php">
                <?= csrf_field() ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><i class="fas fa-user me-2 text-primary"></i>Datos del Cliente</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select name="id_cliente" class="form-select" required>
                                        <option value="">Seleccione un cliente</option>
                                        <?php foreach ($clientes as $c): ?>
                                        <option value="<?= $c['id_cliente'] ?>"><?= hescape($c['nombre']) ?> (<?= hescape($c['documento']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de emision</label>
                                        <input type="date" name="fecha_emision" class="form-control" value="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de vencimiento</label>
                                        <input type="date" name="fecha_vencimiento" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notas</label>
                                    <textarea name="notas" class="form-control" rows="2" placeholder="Opcional"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-list me-2 text-primary"></i>Items</span>
                                <button type="button" class="btn btn-success btn-sm" onclick="agregarItem()"><i class="fas fa-plus"></i> Agregar</button>
                            </div>
                            <div class="card-body">
                                <div class="table-wrap">
                                    <table class="table table-sm" id="tablaItems">
                                        <thead>
                                            <tr>
                                                <th style="width:50%">Descripcion</th>
                                                <th style="width:15%">Cant.</th>
                                                <th style="width:20%">Precio</th>
                                                <th style="width:15%">Subtotal</th>
                                                <th style="width:5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsBody">
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <div class="row text-end">
                                    <div class="col-md-6 offset-md-6">
                                        <p class="mb-1">Subtotal: <strong id="lblSubtotal">$0</strong></p>
                                        <p class="mb-1">IVA (19%): <strong id="lblIva">$0</strong></p>
                                        <p class="mb-0 fs-5">Total: <strong id="lblTotal">$0</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3 mb-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Guardar Factura</button>
                    <a href="index.php" class="btn btn-secondary btn-lg"><i class="fas fa-times me-2"></i>Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<script>
let itemCount = 0;

function agregarItem(desc = '', cant = 1, precio = 0) {
    itemCount++;
    const html = `<tr id="item_${itemCount}">
        <td><input type="text" class="form-control form-control-sm item-desc" value="${desc}" placeholder="Ej: Plan Internet 50MB" required></td>
        <td><input type="number" class="form-control form-control-sm item-cant" value="${cant}" min="1" onchange="calcItem(${itemCount})" onkeyup="calcItem(${itemCount})"></td>
        <td><input type="number" class="form-control form-control-sm item-precio" value="${precio}" min="0" step="0.01" onchange="calcItem(${itemCount})" onkeyup="calcItem(${itemCount})"></td>
        <td class="item-subtotal text-end pt-2">$0</td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();calcular()"><i class="fas fa-trash"></i></button></td>
    </tr>`;
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', html);
    if (desc) calcItem(itemCount);
    calcular();
}

function calcItem(id) {
    const row = document.getElementById(`item_${id}`);
    if (!row) return;
    const cant = parseFloat(row.querySelector('.item-cant').value) || 0;
    const precio = parseFloat(row.querySelector('.item-precio').value) || 0;
    const sub = cant * precio;
    row.querySelector('.item-subtotal').textContent = '$' + sub.toLocaleString('es-CO', {minimumFractionDigits:0});
    calcular();
}

function calcular() {
    const rows = document.querySelectorAll('#itemsBody tr');
    let subtotal = 0;
    rows.forEach(row => {
        const cant = parseFloat(row.querySelector('.item-cant').value) || 0;
        const precio = parseFloat(row.querySelector('.item-precio').value) || 0;
        subtotal += cant * precio;
    });
    const iva = subtotal * 0.19;
    const total = subtotal + iva;
    document.getElementById('lblSubtotal').textContent = '$' + Math.round(subtotal).toLocaleString('es-CO');
    document.getElementById('lblIva').textContent = '$' + Math.round(iva).toLocaleString('es-CO');
    document.getElementById('lblTotal').textContent = '$' + Math.round(total).toLocaleString('es-CO');
}

document.getElementById('formFactura').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('#itemsBody tr');
    const items = [];
    rows.forEach(row => {
        items.push({
            descripcion: row.querySelector('.item-desc').value,
            cantidad: parseInt(row.querySelector('.item-cant').value) || 1,
            precio: parseFloat(row.querySelector('.item-precio').value) || 0
        });
    });
    if (items.length === 0) {
        e.preventDefault();
        Swal.fire({icon:'error',title:'Error',text:'Agregue al menos un item a la factura'});
        return;
    }
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'items';
    input.value = JSON.stringify(items);
    this.appendChild(input);
});

agregarItem('Plan Internet 50MB', 1, 50000);
</script>

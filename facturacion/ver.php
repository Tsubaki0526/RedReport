<?php
include('../sesion.php');
include('../parte1.php');
require_once '../app/config/conexion.php';
require_once '../app/config/seguridad.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT f.*, c.nombre AS cliente_nombre, c.documento, c.direccion, c.telefono, c.email
                        FROM tb_facturas f
                        INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente
                        WHERE f.id_factura = ?");
$stmt->execute([$id]);
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$factura) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Factura no encontrada'}).then(()=>window.location='index.php');</script>";
    include('../parte2.php');
    exit;
}

$items = $pdo->prepare("SELECT * FROM tb_factura_items WHERE id_factura = ?");
$items->execute([$id]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

$badge = match($factura['estado']) {
    'pagada' => 'bg-success',
    'pendiente' => 'bg-warning text-dark',
    'vencida' => 'bg-danger',
    'anulada' => 'bg-secondary',
    default => 'bg-secondary'
};
?>
<link rel="stylesheet" href="../public/css/redreport.css">
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Factura <?= hescape($factura['numero_factura']) ?></h1>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="pdf.php?id=<?= $id ?>" class="btn btn-secondary btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
                    <?php if (in_array($factura['estado'], ['pendiente', 'vencida'])): ?>
                    <form method="POST" action="controles/pagar_factura.php" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_factura" value="<?= $id ?>">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Marcar Pagada</button>
                    </form>
                    <form method="POST" action="controles/anular_factura.php" class="d-inline" onsubmit="return confirm('Anular esta factura?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_factura" value="<?= $id ?>">
                        <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-ban"></i> Anular</button>
                    </form>
                                    <?php endif; ?>
                                    <a href="index.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content">
                        <div class="container-fluid">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h6 class="text-muted">Cliente</h6>
                                            <p class="mb-0 fw-bold"><?= hescape($factura['cliente_nombre']) ?></p>
                                            <p class="mb-0"><?= hescape($factura['documento']) ?></p>
                                            <p class="mb-0"><?= hescape($factura['direccion'] ?? '') ?></p>
                                            <p class="mb-0">Tel: <?= hescape($factura['telefono'] ?? '') ?></p>
                                            <p class="mb-0">Email: <?= hescape($factura['email'] ?? '') ?></p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h2 class="text-primary"><?= hescape($factura['numero_factura']) ?></h2>
                                            <p class="mb-0"><strong>Emision:</strong> <?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></p>
                                            <p class="mb-0"><strong>Vencimiento:</strong> <?= date('d/m/Y', strtotime($factura['fecha_vencimiento'])) ?></p>
                                            <?php if ($factura['fecha_pago']): ?>
                                            <p class="mb-0"><strong>Pagada:</strong> <?= date('d/m/Y', strtotime($factura['fecha_pago'])) ?></p>
                                            <?php endif; ?>
                                            <p class="mb-0 mt-2"><span class="badge <?= $badge ?> fs-6"><?= ucfirst($factura['estado']) ?></span></p>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Descripcion</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-end">Precio Unit.</th>
                                                    <th class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; foreach ($items as $item): ?>
                                                <tr>
                                                    <td><?= $i++ ?></td>
                                                    <td><?= hescape($item['descripcion']) ?></td>
                                                    <td class="text-center"><?= $item['cantidad'] ?></td>
                                                    <td class="text-end">$<?= number_format($item['precio_unitario'], 0) ?></td>
                                                    <td class="text-end">$<?= number_format($item['subtotal'], 0) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" class="text-end">Subtotal:</th>
                                                    <th class="text-end">$<?= number_format($factura['subtotal'], 0) ?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-end">IVA (19%):</th>
                                                    <th class="text-end">$<?= number_format($factura['iva'], 0) ?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="text-end fs-5">Total:</th>
                                                    <th class="text-end fs-5">$<?= number_format($factura['total'], 0) ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <?php if ($factura['notas']): ?>
                                    <div class="mt-3">
                                        <h6 class="text-muted">Notas</h6>
                                        <p><?= nl2br(hescape($factura['notas'])) ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php
                            $pagos = $pdo->prepare("SELECT p.*, u.nombre AS usuario_nombre FROM tb_pagos p LEFT JOIN tb_usuarios u ON p.id_usuario = u.id_usuario WHERE p.id_factura = ? ORDER BY p.id_pago DESC");
                            $pagos->execute([$id]);
                            $pagos = $pagos->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php if (!empty($pagos)): ?>
                            <div class="card mt-3">
                                <div class="card-header"><h3 class="card-title"><i class="fas fa-receipt me-2 text-success"></i>Historial de pagos</h3></div>
                                <div class="card-body p-0">
                                    <div class="table-container">
                                    <table class="table table-sm mb-0">
                                        <thead><tr><th>#</th><th>Monto</th><th>Metodo</th><th>Referencia</th><th>Fecha</th><th>Usuario</th><th>Comprobante</th></tr></thead>
                                        <tbody>
                                            <?php foreach ($pagos as $p): ?>
                                            <tr>
                                                <td><?= $p['id_pago'] ?></td>
                                                <td><strong>$<?= number_format($p['monto'], 0) ?></strong></td>
                                                <td><?= hescape($p['metodo_pago']) ?></td>
                                                <td><?= hescape($p['referencia'] ?? '-') ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($p['fecha_pago'])) ?></td>
                                                <td><?= hescape($p['usuario_nombre'] ?? '-') ?></td>
                                                <td><a href="pdf_comprobante.php?id=<?= $p['id_pago'] ?>" class="btn btn-sm btn-outline-success" target="_blank"><i class="fas fa-file-pdf"></i></a></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (in_array($factura['estado'], ['pendiente', 'vencida'])): ?>
                            <div class="mt-3">
                                <a href="registrar_pago.php?id=<?= $id ?>" class="btn btn-success"><i class="fas fa-check"></i> Registrar pago</a>
                                <form method="POST" action="controles/enviar_factura_email.php" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_factura" value="<?= $id ?>">
                                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-envelope"></i> Enviar por email</button>
                                </form>
                            </div>
                            <?php else: ?>
                            <div class="mt-3">
                                <form method="POST" action="controles/enviar_factura_email.php" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id_factura" value="<?= $id ?>">
                                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-envelope"></i> Enviar por email</button>
                                </form>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php include('../parte2.php'); ?>

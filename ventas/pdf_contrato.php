<?php
require_once '../app/config/conexion.php';
require_once '../vendor/autoload.php';

$id = intval($_GET['id'] ?? 0);
$ctr = $pdo->prepare("SELECT c.*, cl.nombre AS cliente_nombre, cl.documento, cl.direccion, cl.telefono, cl.email, p.nombre AS plan_nombre, p.precio, p.velocidad, u.nombre AS vendedor_nombre
    FROM tb_contratos c
    INNER JOIN tb_clientes cl ON c.id_cliente=cl.id_cliente
    INNER JOIN tb_planes p ON c.id_plan=p.id_plan
    INNER JOIN tb_usuarios u ON c.id_vendedor=u.id_usuario
    WHERE c.id_contrato=?");
$ctr->execute([$id]);
$c = $ctr->fetch(PDO::FETCH_ASSOC);
if (!$c) { die('Contrato no encontrado'); }

use Dompdf\Dompdf;
$dompdf = new Dompdf(['defaultFont' => 'Helvetica']);
ob_start();
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><style>
body{font-family:Helvetica,sans-serif;font-size:12px;padding:20px;}
h1{color:#2563eb;text-align:center;font-size:22px;}
.empresa{text-align:center;margin-bottom:20px;color:#64748b;}
.datos{margin-bottom:20px;}
.datos td{padding:4px 8px;}
.tabla{width:100%;border-collapse:collapse;margin:15px 0;}
.tabla th{background:#2563eb;color:#fff;padding:8px;text-align:left;}
.tabla td{padding:6px;border-bottom:1px solid #e2e8f0;}
.total{text-align:right;font-size:16px;font-weight:bold;margin-top:10px;}
.firma{margin-top:40px;text-align:center;}
.firma img{max-width:200px;max-height:60px;border:1px solid #e2e8f0;padding:8px;}
.firma p{font-size:11px;color:#64748b;margin-top:4px;}
.footer{text-align:center;margin-top:30px;font-size:10px;color:#94a3b8;}
</style></head><body>
<h1>CONTRATO DE SERVICIOS</h1>
<div class="empresa"><strong><?= hescape(APP_NAME) ?></strong><br>Contrato N° <?= $c['id_contrato'] ?></div>

<table class="datos">
<tr><td><strong>Cliente:</strong></td><td><?= hescape($c['cliente_nombre']) ?></td></tr>
<tr><td><strong>Documento:</strong></td><td><?= hescape($c['documento']) ?></td></tr>
<tr><td><strong>Dirección:</strong></td><td><?= hescape($c['direccion'] ?: '-') ?></td></tr>
<tr><td><strong>Teléfono:</strong></td><td><?= hescape($c['telefono'] ?: '-') ?></td></tr>
<tr><td><strong>Email:</strong></td><td><?= hescape($c['email'] ?: '-') ?></td></tr>
</table>

<table class="tabla">
<thead><tr><th>Plan</th><th>Velocidad</th><th>Valor mensual</th><th>Fecha inicio</th><th>Fecha fin</th></tr></thead>
<tbody><tr>
<td><?= hescape($c['plan_nombre']) ?></td><td><?= hescape($c['velocidad'] ?: '-') ?></td>
<td>$<?= number_format($c['precio'], 0) ?></td><td><?= $c['fecha_inicio'] ?></td><td><?= $c['fecha_fin'] ?: 'Indefinido' ?></td>
</tr></tbody></table>
<div class="total">Total: $<?= number_format($c['precio'], 0) ?>/mes</div>

<?php if ($c['notas']): ?><p><strong>Notas:</strong> <?= nl2br(hescape($c['notas'])) ?></p><?php endif; ?>

<p><strong>Vendedor:</strong> <?= hescape($c['vendedor_nombre']) ?></p>
<p><strong>Fecha de creación:</strong> <?= $c['fecha_inicio'] ?></p>

<div class="firma">
    <p><strong>Firma del cliente</strong></p>
    <?php if ($c['firma_path'] && file_exists('../' . $c['firma_path'])): ?>
        <img src="../<?= $c['firma_path'] ?>?t=<?= time() ?>" alt="Firma">
    <?php else: ?>
        <p style="color:#dc2626;">Pendiente de firma</p>
    <?php endif; ?>
    <p><?= hescape($c['cliente_nombre']) ?></p>
</div>

<div class="footer">
    Documento generado el <?= date('d/m/Y H:i') ?> - <?= hescape(APP_NAME) ?>
</div>
</body></html>
<?php
$html = ob_get_clean();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("contrato_{$c['id_contrato']}.pdf", ['Attachment' => false]);

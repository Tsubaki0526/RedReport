<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';
verificar_acceso([1, 2]);
require_once '../../vendor/autoload.php';

$tipo = $_GET['tipo'] ?? 'facturacion';
$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');
$filtro_id = $_GET['filtro_id'] ?? '';

use Dompdf\Dompdf;
$dompdf = new Dompdf(['defaultFont' => 'Helvetica']);
ob_start();
?>
<!DOCTYPE html><html><head><meta charset="utf-8"><style>
body{font-family:Helvetica,sans-serif;font-size:11px;}table{width:100%;border-collapse:collapse;margin-top:10px;}
th{background:#2563eb;color:#fff;padding:6px;text-align:left;font-size:10px;}td{padding:5px;border-bottom:1px solid #ddd;}
h1{font-size:16px;color:#1e293b;}h2{font-size:13px;color:#64748b;margin-top:-5px;}
.badge-pagada{color:#16a34a;font-weight:bold;}.badge-pendiente{color:#d97706;font-weight:bold;}
.badge-vencida{color:#dc2626;font-weight:bold;}.badge-anulada{color:#6b7280;font-weight:bold;}
.text-right{text-align:right;}.mt-4{margin-top:20px;}
</style></head><body>
<h1>Informe de <?= ucfirst($tipo) ?></h1>
<h2>Del <?= $fecha_desde ?> al <?= $fecha_hasta ?></h2>

<?php if ($tipo == 'facturacion'): ?>
<table><thead><tr><th># Factura</th><th>Cliente</th><th>Emisión</th><th>Vencimiento</th><th>Total</th><th>Estado</th></tr></thead><tbody>
<?php $total=0; $stmt = $pdo->prepare("SELECT f.*,c.nombre AS cn FROM tb_facturas f LEFT JOIN tb_clientes c ON f.id_cliente=c.id_cliente WHERE f.fecha_emision BETWEEN :d AND :h ORDER BY f.fecha_emision DESC"); $stmt->execute(['d'=>$fecha_desde,'h'=>$fecha_hasta]); while($r=$stmt->fetch()): $total+=$r['total']; ?>
<tr><td><?= hescape($r['numero_factura']) ?></td><td><?= hescape($r['cn']??'-') ?></td><td><?= $r['fecha_emision'] ?></td><td><?= $r['fecha_vencimiento'] ?></td><td class="text-right">$<?= number_format($r['total'],0) ?></td><td class="badge-<?= $r['estado'] ?>"><?= $r['estado'] ?></td></tr>
<?php endwhile; ?>
<tr style="font-weight:bold;background:#f1f5f9;"><td colspan="4">TOTAL</td><td class="text-right">$<?= number_format($total,0) ?></td><td></td></tr></tbody></table>

<?php elseif ($tipo == 'ventas'): ?>
<table><thead><tr><th># Venta</th><th>Cliente</th><th>Vendedor</th><th>Plan</th><th>Monto</th><th>Tipo</th><th>Fecha</th></tr></thead><tbody>
<?php $total=0; $sql="SELECT v.*,c.nombre AS cn,u.nombre AS vn,p.nombre AS pn FROM tb_ventas v LEFT JOIN tb_clientes c ON v.id_cliente=c.id_cliente LEFT JOIN tb_usuarios u ON v.id_vendedor=u.id_usuario LEFT JOIN tb_contratos ct ON v.id_contrato=ct.id_contrato LEFT JOIN tb_planes p ON ct.id_plan=p.id_plan WHERE v.fecha BETWEEN :d AND :h ORDER BY v.fecha DESC"; $params=['d'=>$fecha_desde,'h'=>$fecha_hasta]; if($filtro_id){$sql=str_replace('WHERE v.fecha','WHERE v.id_vendedor=:f AND v.fecha',$sql);$params['f']=$filtro_id;} $stmt=$pdo->prepare($sql);$stmt->execute($params); while($r=$stmt->fetch()): $total+=$r['monto']; ?>
<tr><td><?= $r['id_venta'] ?></td><td><?= hescape($r['cn']??'-') ?></td><td><?= hescape($r['vn']??'-') ?></td><td><?= hescape($r['pn']??'-') ?></td><td class="text-right">$<?= number_format($r['monto'],0) ?></td><td><?= $r['tipo'] ?></td><td><?= $r['fecha'] ?></td></tr>
<?php endwhile; ?>
<tr style="font-weight:bold;background:#f1f5f9;"><td colspan="4">TOTAL</td><td class="text-right">$<?= number_format($total,0) ?></td><td colspan="2"></td></tr></tbody></table>

<?php elseif ($tipo == 'instalaciones'): ?>
<table><thead><tr><th>Cliente</th><th>Dirección</th><th>Técnico</th><th>Fecha</th></tr></thead><tbody>
<?php $sql="SELECT c.nombre AS cn,c.direccion,u.nombre AS tn,c.fecha_instalacion FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador=u.id_usuario WHERE c.fecha_instalacion BETWEEN :d AND :h ORDER BY c.fecha_instalacion DESC"; $params=['d'=>$fecha_desde,'h'=>$fecha_hasta]; if($filtro_id){$sql=str_replace('WHERE c.fecha_instalacion','WHERE c.id_instalador=:f AND c.fecha_instalacion',$sql);$params['f']=$filtro_id;} $stmt=$pdo->prepare($sql);$stmt->execute($params); while($r=$stmt->fetch()): ?>
<tr><td><?= hescape($r['cn']??'-') ?></td><td><?= hescape($r['direccion']??'-') ?></td><td><?= hescape($r['tn']??'-') ?></td><td><?= $r['fecha_instalacion'] ?></td></tr>
<?php endwhile; ?></tbody></table>

<?php elseif ($tipo == 'tickets'): ?>
<table><thead><tr><th>Ticket</th><th>Cliente</th><th>Asunto</th><th>Categoría</th><th>Prioridad</th><th>Estado</th><th>Fecha</th></tr></thead><tbody>
<?php $stmt=$pdo->prepare("SELECT t.*,c.nombre AS cn FROM tb_tickets t LEFT JOIN tb_clientes c ON t.id_cliente=c.id_cliente WHERE t.fecha_creacion BETWEEN :d AND :h ORDER BY t.fecha_creacion DESC"); $stmt->execute(['d'=>$fecha_desde,'h'=>$fecha_hasta]); while($r=$stmt->fetch()): ?>
<tr><td><?= hescape($r['numero_ticket']) ?></td><td><?= hescape($r['cn']??'-') ?></td><td><?= hescape(mb_substr($r['asunto'],0,40)) ?></td><td><?= $r['categoria'] ?></td><td><?= $r['prioridad'] ?></td><td><?= $r['estado'] ?></td><td><?= $r['fecha_creacion'] ?></td></tr>
<?php endwhile; ?></tbody></table>

<?php elseif ($tipo == 'cartera'): ?>
<table><thead><tr><th>Cliente</th><th>Teléfono</th><th>Facturas vencidas</th><th>Deuda total</th><th>Días mora</th></tr></thead><tbody>
<?php $total=0; $stmt=$pdo->query("SELECT c.nombre,c.telefono,COUNT(f.id_factura) AS fv,SUM(f.total) AS dt,DATEDIFF(CURDATE(),MIN(f.fecha_vencimiento)) AS dm FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente=c.id_cliente WHERE f.estado IN ('pendiente','vencida') GROUP BY f.id_cliente ORDER BY dm DESC"); while($r=$stmt->fetch()): $total+=$r['dt']; ?>
<tr><td><?= hescape($r['nombre']) ?></td><td><?= hescape($r['telefono']) ?></td><td class="text-right"><?= $r['fv'] ?></td><td class="text-right">$<?= number_format($r['dt'],0) ?></td><td><?= $r['dm'] ?> días</td></tr>
<?php endwhile; ?>
<tr style="font-weight:bold;background:#f1f5f9;"><td colspan="3">TOTAL CARTERA</td><td class="text-right">$<?= number_format($total,0) ?></td><td></td></tr></tbody></table>
<?php endif; ?>

<div class="mt-4"><p>Generado el <?= date('d/m/Y H:i') ?> por <?= hescape($_SESSION['usuario']??'Sistema') ?></p></div>
</body></html>
<?php
$html = ob_get_clean();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("informe_$tipo.pdf", ['Attachment' => true]);

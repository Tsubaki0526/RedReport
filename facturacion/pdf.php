<?php
session_start();
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexion.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT f.*, c.nombre AS cliente_nombre, c.documento, c.direccion, c.telefono, c.email
                        FROM tb_facturas f
                        INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente
                        WHERE f.id_factura = ?");
$stmt->execute([$id]);
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$factura) {
    die('Factura no encontrada');
}

$items = $pdo->prepare("SELECT * FROM tb_factura_items WHERE id_factura = ?");
$items->execute([$id]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

$estadoLabel = match($factura['estado']) {
    'pagada' => 'PAGADA',
    'pendiente' => 'PENDIENTE',
    'vencida' => 'VENCIDA',
    'anulada' => 'ANULADA',
    default => strtoupper($factura['estado'])
};

require_once __DIR__ . '/../vendor/dompdf/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 20mm; }
    body { font-family: "DejaVu Sans", sans-serif; font-size: 10pt; color: #333; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
    .header h1 { color: #2563eb; margin: 0; font-size: 18pt; }
    .header p { margin: 2px 0; color: #666; font-size: 8pt; }
    .badge-estado { display: inline-block; padding: 3px 12px; border-radius: 10px; font-size: 9pt; font-weight: bold; }
    .pagada { background: #d1fae5; color: #065f46; }
    .pendiente { background: #fef3c7; color: #92400e; }
    .vencida { background: #fee2e2; color: #991b1b; }
    .anulada { background: #e5e7eb; color: #374151; }
    .info-cliente { margin-bottom: 20px; }
    .info-cliente td { vertical-align: top; padding: 2px 0; }
    table.items { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.items th { background: #2563eb; color: white; padding: 6px 8px; text-align: left; font-size: 9pt; }
    table.items th.right { text-align: right; }
    table.items th.center { text-align: center; }
    table.items td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
    table.items td.right { text-align: right; }
    table.items td.center { text-align: center; }
    table.totals { width: 100%; margin-top: 10px; }
    table.totals td { padding: 3px 8px; }
    table.totals .label { text-align: right; font-weight: bold; }
    table.totals .value { text-align: right; width: 150px; }
    .total-row { font-size: 13pt; border-top: 2px solid #2563eb; }
    .footer { margin-top: 30px; text-align: center; color: #999; font-size: 7pt; border-top: 1px solid #ddd; padding-top: 8px; }
</style>
</head>
<body>
<div class="header">
    <h1>' . APP_NAME . '</h1>
    <p>NIT: 000.000.000-0 | ' . APP_URL . '</p>
    <p>Factura de Venta</p>
</div>
<p style="text-align:right"><span class="badge-estado ' . $factura['estado'] . '">' . $estadoLabel . '</span></p>
<table class="info-cliente" width="100%">
    <tr>
        <td width="50%">
            <strong>FACTURA No:</strong> ' . htmlspecialchars($factura['numero_factura']) . '<br>
            <strong>Fecha Emision:</strong> ' . date('d/m/Y', strtotime($factura['fecha_emision'])) . '<br>
            <strong>Fecha Vencimiento:</strong> ' . date('d/m/Y', strtotime($factura['fecha_vencimiento'])) . '<br>
            ' . ($factura['fecha_pago'] ? '<strong>Fecha Pago:</strong> ' . date('d/m/Y', strtotime($factura['fecha_pago'])) : '') . '
        </td>
        <td width="50%">
            <strong>Cliente:</strong> ' . htmlspecialchars($factura['cliente_nombre']) . '<br>
            <strong>Documento:</strong> ' . htmlspecialchars($factura['documento']) . '<br>
            <strong>Direccion:</strong> ' . htmlspecialchars($factura['direccion'] ?? '') . '<br>
            <strong>Telefono:</strong> ' . htmlspecialchars($factura['telefono'] ?? '') . '
        </td>
    </tr>
</table>
<table class="items">
    <tr>
        <th style="width:5%">#</th>
        <th>Descripcion</th>
        <th class="center" style="width:10%">Cant.</th>
        <th class="right" style="width:20%">Precio Unit.</th>
        <th class="right" style="width:20%">Subtotal</th>
    </tr>';
    $i = 1;
    foreach ($items as $item) {
        $html .= '<tr>
            <td>' . $i++ . '</td>
            <td>' . htmlspecialchars($item['descripcion']) . '</td>
            <td class="center">' . $item['cantidad'] . '</td>
            <td class="right">$' . number_format($item['precio_unitario'], 0) . '</td>
            <td class="right">$' . number_format($item['subtotal'], 0) . '</td>
        </tr>';
    }
    $html .= '
</table>
<table class="totals">
    <tr><td class="label">Subtotal:</td><td class="value">$' . number_format($factura['subtotal'], 0) . '</td></tr>
    <tr><td class="label">IVA (19%):</td><td class="value">$' . number_format($factura['iva'], 0) . '</td></tr>
    <tr class="total-row"><td class="label">TOTAL:</td><td class="value">$' . number_format($factura['total'], 0) . '</td></tr>
</table>';
if ($factura['notas']) {
    $html .= '<p style="margin-top:15px"><strong>Notas:</strong><br>' . nl2br(htmlspecialchars($factura['notas'])) . '</p>';
}
$html .= '
<div class="footer">
    ' . APP_NAME . ' - ' . APP_URL . ' | Generado el ' . date('d/m/Y H:i') . '
</div>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('factura_' . $factura['numero_factura'] . '.pdf', ['Attachment' => false]);

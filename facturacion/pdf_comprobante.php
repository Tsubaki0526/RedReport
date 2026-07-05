<?php
session_start();
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexion.php';

$id_pago = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT p.*, f.numero_factura, f.total, f.fecha_emision, f.fecha_vencimiento,
                        c.nombre AS cliente_nombre, c.documento, c.direccion
                        FROM tb_pagos p
                        INNER JOIN tb_facturas f ON p.id_factura = f.id_factura
                        INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente
                        WHERE p.id_pago = ?");
$stmt->execute([$id_pago]);
$pago = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pago) die('Pago no encontrado');

require_once __DIR__ . '/../vendor/dompdf/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
    @page { margin: 15mm; }
    body { font-family: "DejaVu Sans", sans-serif; font-size: 10pt; color: #333; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #16a34a; padding-bottom: 10px; }
    .header h1 { color: #16a34a; margin: 0; font-size: 18pt; }
    .recibo { text-align: center; font-size: 16pt; font-weight: bold; color: #16a34a; margin: 10px 0; }
    .info { margin-bottom: 20px; }
    .info td { padding: 4px 0; vertical-align: top; }
    .detalle { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .detalle th { background: #16a34a; color: white; padding: 6px 8px; text-align: left; }
    .detalle td { padding: 5px 8px; border-bottom: 1px solid #d1d5db; }
    .detalle .right { text-align: right; }
    .footer { margin-top: 30px; text-align: center; color: #999; font-size: 7pt; border-top: 1px solid #ddd; padding-top: 8px; }
    .sello { text-align: center; margin-top: 30px; }
    .sello span { display: inline-block; border: 2px solid #16a34a; color: #16a34a; padding: 8px 30px; border-radius: 5px; font-size: 14pt; font-weight: bold; }
</style></head><body>
<div class="header"><h1>' . APP_NAME . '</h1></div>
<div class="recibo">COMPROBANTE DE PAGO</div>
<p style="text-align:right;color:#16a34a;font-weight:bold;">No. ' . str_pad($pago['id_pago'], 6, '0', STR_PAD_LEFT) . '</p>
<table class="info" width="100%">
    <tr><td width="50%">
        <strong>Cliente:</strong> ' . htmlspecialchars($pago['cliente_nombre']) . '<br>
        <strong>Documento:</strong> ' . htmlspecialchars($pago['documento']) . '<br>
        <strong>Direccion:</strong> ' . htmlspecialchars($pago['direccion'] ?? '') . '
    </td><td width="50%">
        <strong>Factura:</strong> ' . htmlspecialchars($pago['numero_factura']) . '<br>
        <strong>Fecha pago:</strong> ' . date('d/m/Y H:i', strtotime($pago['fecha_pago'])) . '<br>
        <strong>Metodo:</strong> ' . htmlspecialchars($pago['metodo_pago']) . '
        ' . ($pago['referencia'] ? '<br><strong>Referencia:</strong> ' . htmlspecialchars($pago['referencia']) : '') . '
    </td></tr>
</table>
<table class="detalle">
    <tr><th>Concepto</th><th class="right">Monto</th></tr>
    <tr><td>Cancelacion factura ' . htmlspecialchars($pago['numero_factura']) . '</td><td class="right">$' . number_format($pago['monto'], 0) . '</td></tr>
    <tr><td><strong>Total pagado</strong></td><td class="right"><strong>$' . number_format($pago['monto'], 0) . '</strong></td></tr>
</table>
' . ($pago['notas'] ? '<p style="margin-top:10px"><strong>Notas:</strong> ' . nl2br(htmlspecialchars($pago['notas'])) . '</p>' : '') . '
<div class="sello"><span>CANCELADO</span></div>
<div class="footer">' . APP_NAME . ' - Generado el ' . date('d/m/Y H:i') . '</div>
</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('comprobante_pago_' . $pago['id_pago'] . '.pdf', ['Attachment' => true]);

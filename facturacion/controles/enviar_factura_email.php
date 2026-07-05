<?php
session_start();
require_once '../../app/config/config.php';
require_once '../../app/config/conexion.php';
require_once '../../app/config/seguridad.php';
require_once '../../app/controles/email_queue.php';
require_once '../../autoload.inc.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

csrf_verify($_POST['_csrf_token'] ?? '');
$id_factura = intval($_POST['id_factura'] ?? 0);

$stmt = $pdo->prepare("SELECT f.*, c.nombre AS cliente_nombre, c.email, c.documento, c.direccion, c.telefono
                        FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente
                        WHERE f.id_factura = ?");
$stmt->execute([$id_factura]);
$factura = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$factura || empty($factura['email'])) {
    echo "<script>Swal.fire({icon:'error',title:'Error',text:'Cliente sin email o factura no encontrada'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
    exit;
}

$items = $pdo->prepare("SELECT * FROM tb_factura_items WHERE id_factura = ?");
$items->execute([$id_factura]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;padding:20px;">
<h2 style="color:#2563eb;"><?= APP_NAME ?></h2>
<p>Hola <strong><?= hescape($factura['cliente_nombre']) ?></strong>,</p>
<p>Adjuntamos la factura <strong><?= hescape($factura['numero_factura']) ?></strong> por un total de <strong>$<?= number_format($factura['total'], 0) ?></strong>.</p>
<p><strong>Vencimiento:</strong> <?= date('d/m/Y', strtotime($factura['fecha_vencimiento'])) ?></p>
<p><strong>Estado:</strong> <?= ucfirst($factura['estado']) ?></p>
<hr>
<table style="width:100%;border-collapse:collapse;">
<tr style="background:#f3f4f6;"><th style="padding:8px;text-align:left;">Descripcion</th><th style="padding:8px;text-align:center;">Cant</th><th style="padding:8px;text-align:right;">Precio</th><th style="padding:8px;text-align:right;">Subtotal</th></tr>
<?php foreach ($items as $item): ?>
<tr><td style="padding:6px;"><?= hescape($item['descripcion']) ?></td><td style="padding:6px;text-align:center;"><?= $item['cantidad'] ?></td><td style="padding:6px;text-align:right;">$<?= number_format($item['precio_unitario'], 0) ?></td><td style="padding:6px;text-align:right;">$<?= number_format($item['subtotal'], 0) ?></td></tr>
<?php endforeach; ?>
</table>
<hr>
<p style="text-align:right;"><strong>Total: $<?= number_format($factura['total'], 0) ?></strong></p>
<p style="color:#6b7280;font-size:12px;"><?= APP_NAME ?> - Gracias por su preferencia</p>
</body></html>
<?php
$htmlBody = ob_get_clean();

require_once __DIR__ . '/../../vendor/dompdf/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$dompdfOptions = new Options();
$dompdfOptions->set('isRemoteEnabled', true);
$dompdf = new Dompdf($dompdfOptions);
$dompdf->loadHtml(mb_convert_encoding($htmlBody, 'HTML-ENTITIES', 'UTF-8'));
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfContent = $dompdf->output();

// Save PDF temporarily for queue attachment
$tmpDir = __DIR__ . '/../../temp';
if (!is_dir($tmpDir)) mkdir($tmpDir, 0755, true);
$pdfFile = $tmpDir . '/Factura_' . $factura['numero_factura'] . '.pdf';
file_put_contents($pdfFile, $pdfContent);

try {
    $asunto = "Factura " . $factura['numero_factura'] . " - " . APP_NAME;
    $id_cola = EmailQueue::addStatic($pdo, $factura['email'], $asunto, $htmlBody,
        [['path' => $pdfFile, 'name' => 'Factura_' . $factura['numero_factura'] . '.pdf']],
        $factura['id_cliente'], $id_factura);

    if ($id_cola > 0) {
        // Try to process queue immediately (best-effort)
        $result = EmailQueue::processStatic($pdo, 5);
        $sent = $result['sent'] ?? 0;

        if ($sent > 0) {
            bitacora($pdo, $_SESSION['id_usuario'], 'Envio email', 'tb_facturas', $id_factura,
                     "Factura {$factura['numero_factura']} enviada a {$factura['email']} via cola");
            echo "<script>Swal.fire({icon:'success',title:'Enviada',text:'Factura enviada a {$factura['email']}'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
        } else {
            bitacora($pdo, $_SESSION['id_usuario'], 'Cola email', 'tb_facturas', $id_factura,
                     "Factura {$factura['numero_factura']} añadida a la cola para {$factura['email']}");
            echo "<script>Swal.fire({icon:'info',title:'En cola',text:'Factura añadida a la cola de envio'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
        }
    } else {
        throw new Exception('No se pudo añadir a la cola');
    }
} catch (Exception $e) {
    // Fallback: send directly
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($factura['email'], $factura['cliente_nombre']);
        $mail->Subject = "Factura " . $factura['numero_factura'] . " - " . APP_NAME;
        $mail->Body    = $htmlBody;
        $mail->isHTML(true);
        $mail->addStringAttachment($pdfContent, "Factura_" . $factura['numero_factura'] . ".pdf");

        $mail->send();

        bitacora($pdo, $_SESSION['id_usuario'], 'Envio email', 'tb_facturas', $id_factura,
                 "Factura {$factura['numero_factura']} enviada a {$factura['email']} (fallback directo)");

        echo "<script>Swal.fire({icon:'success',title:'Enviada',text:'Factura enviada a {$factura['email']}'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
    } catch (Exception $e2) {
        echo "<script>Swal.fire({icon:'error',title:'Error',text:'Error al enviar: {$e2->getMessage()}'}).then(()=>window.location='../ver.php?id=$id_factura');</script>";
    }
}

// Cleanup temp file
if (file_exists($pdfFile)) @unlink($pdfFile);

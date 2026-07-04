<?php
require '../../vendor/autoload.php'; // Cargar DOMPDF
use Dompdf\Dompdf;
use Dompdf\Options;

// Configuración DOMPDF (para acentos y UTF-8)
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Conexión BD
require '../../app/config/conexion.php';

// Recibir id desde GET
$id = $_GET['id'] ?? 0;

// Consulta con JOIN
$sql = "SELECT 
            c.id_azteka_registrado,
            c.radicado,
            c.operador,
            c.cliente,
            c.ciudad,
            c.telefono,
            c.fecha,
            c.hora,
            c.dano_reportado,
            f.fecha_hora_finalizado,
            f.horas_totales,
            f.horas_real_dano,
            f.tipo_de_dano,
            f.parada_reloj,
            f.hora_parada_inicio,
            f.hora_parada_fin,
            f.horas_parada,
            f.observaciones_final
        FROM tb_azteka c
        LEFT JOIN tb_azteka_finalizacion f 
            ON c.id_azteka_registrado = f.id_azteka_registrado
        WHERE c.id_azteka_registrado = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("No se encontró el reporte.");
}

// Variables
$radicado = $data['radicado'];
$cliente = $data['cliente'];
$ciudad = $data['ciudad'];
$telefono = $data['telefono'];
$operador = $data['operador'];
$fecha = $data['fecha'];
$hora = $data['hora'];
$dano = $data['dano_reportado'];

$fecha_fin = $data['fecha_hora_finalizado'];
$horas_totales = $data['horas_totales'];
$horas_real = $data['horas_real_dano'];
$tipo_dano = $data['tipo_de_dano'];
$parada = $data['parada_reloj'] == 1 ? "Sí" : "No";
$parada_inicio = $data['hora_parada_inicio'];
$parada_fin = $data['hora_parada_fin'];
$horas_parada = $data['horas_parada'];
$obs = $data['observaciones_final'];

// Plantilla HTML tipo factura
$html = "
<html>
<head>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 15px; }
    .section { margin-bottom: 15px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    .footer { text-align: center; margin-top: 25px; font-size: 10px; }
    .observaciones { 
        border: 1px solid #000; 
        padding: 6px; 
        text-align: justify; 
        word-wrap: break-word; 
        white-space: pre-wrap; 
        max-width: 100%; 
        page-break-inside: avoid;
    }
  </style>
</head>
<body>

<div class='header'>
  <h2>Informe Técnico - Azteca</h2>
  <p><strong>Radicado:</strong> $radicado</p>
</div>

<div class='section'>
  <h3>Datos del Cliente</h3>
  <table>
    <tr><th>Cliente</th><td>$cliente</td></tr>
    <tr><th>Teléfono</th><td>$telefono</td></tr>
  </table>
</div>

<div class='section'>
  <h3>Detalles del Reporte</h3>
  <table>
    <tr><th>Ciudad</th><td>$ciudad</td></tr>
    <tr><th>Fecha / Hora Reporte</th><td>$fecha $hora</td></tr>
    <tr><th>Daño Reportado</th><td>$dano</td></tr>
  </table>
</div>

<div class='section'>
  <h3>Finalización</h3>
  <table>
    <tr><th>Fecha / Hora Finalizado</th><td>$fecha_fin</td></tr>
    <tr><th>Horas Totales</th><td>$horas_totales</td></tr>
    <tr><th>Horas Reales Daño</th><td>$horas_real</td></tr>
    <tr><th>Tipo de Daño</th><td>$tipo_dano</td></tr>
  </table>
</div>

<div class='section'>
  <h3>Parada de Reloj</h3>
  <table>
    <tr><th>Parada Reloj</th><td>$parada</td></tr>
    <tr><th>Inicio</th><td>$parada_inicio</td></tr>
    <tr><th>Fin</th><td>$parada_fin</td></tr>
    <tr><th>Horas Parada</th><td>$horas_parada</td></tr>
  </table>
</div>

<div class='section'>
  <h3>Observaciones</h3>
  <div class='observaciones'>$obs</div>
</div>

<div class='footer'>
  <p>© 2025 Azteca</p>
</div>

</body>
</html>
";

// Cargar y renderizar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("informe_$radicado.pdf", ["Attachment" => true]);

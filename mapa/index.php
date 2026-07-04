<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

// Get all clients with coordinates
$sql = "SELECT c.id_cliente, c.nombre, c.direccion, c.telefono, c.lat, c.lng, c.fecha_instalacion,
               u.nombre AS instalador
        FROM tb_clientes c
        LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario
        WHERE c.lat IS NOT NULL AND c.lng IS NOT NULL";
$clientes = $pdo->query($sql)->fetchAll();

// Get coverage zones
$zonas = $pdo->query("SELECT * FROM tb_cobertura_zonas WHERE activo = 1")->fetchAll();
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Mapa de Cobertura</h1></div>
                <div class="col-sm-6 text-end"><span id="fechaHora" class="text-muted"></span></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Clientes geolocalizados</h3>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height:600px;width:100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([4.7110, -74.0721], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

const clientes = <?= json_encode($clientes) ?>;
clientes.forEach(c => {
    if (c.lat && c.lng) {
        const color = c.fecha_instalacion ? 'green' : 'orange';
        L.circleMarker([parseFloat(c.lat), parseFloat(c.lng)], {
            radius: 8, fillColor: color, color: '#333', weight: 1, fillOpacity: 0.8
        }).addTo(map).bindPopup(`
            <b>${c.nombre}</b><br>
            ${c.direccion || ''}<br>
            Tel: ${c.telefono}<br>
            ${c.instalador ? 'Instalador: ' + c.instalador : 'Sin instalador'}<br>
            ${c.fecha_instalacion ? 'Instalado: ' + c.fecha_instalacion : 'Pendiente'}
        `);
    }
});

const zonas = <?= json_encode($zonas) ?>;
zonas.forEach(z => {
    try {
        const coords = JSON.parse(z.coordenadas);
        L.polygon(coords, { color: z.color, fillOpacity: 0.1 }).addTo(map)
            .bindPopup('<b>' + z.nombre + '</b>');
    } catch(e) {}
});
</script>

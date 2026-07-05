<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

$id_rol = $_SESSION['id_rol'] ?? 0;

// Get all clients with coordinates
$sql = "SELECT c.id_cliente, c.nombre, c.direccion, c.telefono, c.lat, c.lng, c.fecha_instalacion, c.estado_servicio,
               u.nombre AS instalador
        FROM tb_clientes c
        LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario
        WHERE c.lat IS NOT NULL AND c.lng IS NOT NULL";
$clientes = $pdo->query($sql)->fetchAll();

// Get coverage zones
$zonas = $pdo->query("SELECT * FROM tb_cobertura_zonas WHERE activo = 1")->fetchAll();

// Get company data
$empresa = $pdo->query("SELECT * FROM tb_empresa WHERE id_empresa = 1")->fetch(PDO::FETCH_ASSOC);

// Build heatmap data from client locations + zone centroids
$heatPoints = [];
foreach ($clientes as $c) {
    $lat = (float)$c['lat']; $lng = (float)$c['lng'];
    if (!$lat || !$lng) continue;
    // Weight based on service status
    $estado = $c['estado_servicio'] ?? '';
    $w = ($c['fecha_instalacion']) ? 1.0 : (($estado === 'Activo') ? 0.8 : (($estado === 'Suspendido') ? 0.5 : 0.3));
    $heatPoints[] = [$lat, $lng, $w];
}
// Add zone polygon centroids as medium-weight heat points
foreach ($zonas as $z) {
    try {
        $coords = json_decode($z['coordenadas'], true);
        if (!is_array($coords) || count($coords) < 3) continue;
        $sumLat = 0; $sumLng = 0;
        foreach ($coords as $pt) { $sumLat += $pt[0]; $sumLng += $pt[1]; }
        $n = count($coords);
        $heatPoints[] = [$sumLat/$n, $sumLng/$n, 0.5];
        // Also add polygon edge points
        foreach ($coords as $pt) { $heatPoints[] = [(float)$pt[0], (float)$pt[1], 0.3]; }
    } catch (\Exception $e) {}
}
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
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title mb-0">Clientes geolocalizados</h3>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active" id="btnZonas"><i class="fas fa-draw-polygon"></i> Zonas</button>
                    <button class="btn btn-outline-primary" id="btnHeatmap"><i class="fas fa-fire"></i> Calor</button>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
const empresa = <?= json_encode($empresa) ?>;
const hasEmpresa = empresa && empresa.lat && empresa.lng;
const centerLat = hasEmpresa ? parseFloat(empresa.lat) : 4.7110;
const centerLng = hasEmpresa ? parseFloat(empresa.lng) : -74.0721;
const map = L.map('map').setView([centerLat, centerLng], hasEmpresa ? 12 : 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Company marker
if (hasEmpresa) {
    const icon = L.divIcon({
        html: '<i class="fas fa-building fa-2x" style="color:#2563eb;"></i>',
        iconSize: [30, 30],
        className: 'bg-transparent'
    });
    L.marker([centerLat, centerLng], { icon }).addTo(map)
        .bindPopup('<b>' + empresa.nombre + '</b><br>' + (empresa.direccion || '') + '<br>Tel: ' + (empresa.telefono || ''));
}

// Client markers
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

// Coverage zones (polygons)
const zonas = <?= json_encode($zonas) ?>;
const zoneLayers = [];
zonas.forEach(z => {
    try {
        const coords = JSON.parse(z.coordenadas);
        const layer = L.polygon(coords, { color: z.color, fillOpacity: 0.1 }).addTo(map)
            .bindPopup('<b>' + z.nombre + '</b>');
        zoneLayers.push(layer);
    } catch(e) {}
});

// Heatmap layer
const heatPoints = <?= json_encode($heatPoints) ?>;
const heatLayer = L.heatLayer(heatPoints, {
    radius: 30, blur: 20, maxZoom: 16, max: 1.0, gradient: {0.2:'blue',0.4:'cyan',0.6:'lime',0.8:'yellow',1.0:'red'}
}).addTo(map);

// Toggle buttons
let showZones = true, showHeat = true;
document.getElementById('btnZonas').addEventListener('click', function() {
    showZones = !showZones;
    this.classList.toggle('active');
    zoneLayers.forEach(l => { if (showZones) map.addLayer(l); else map.removeLayer(l); });
});
document.getElementById('btnHeatmap').addEventListener('click', function() {
    showHeat = !showHeat;
    this.classList.toggle('active');
    if (showHeat) map.addLayer(heatLayer); else map.removeLayer(heatLayer);
});
</script>

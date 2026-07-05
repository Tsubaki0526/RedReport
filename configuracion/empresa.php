<?php
include('../sesion.php');
include('../parte1.php');
if ($_SESSION['id_rol'] != 1) {
    echo "<script>alert('Acceso denegado'); window.location='../index.php';</script>";
    exit;
}
require_once '../app/config/conexion.php';
$empresa = $pdo->query("SELECT * FROM tb_empresa WHERE id_empresa = 1")->fetch(PDO::FETCH_ASSOC);
if (!$empresa) {
    $pdo->exec("INSERT INTO tb_empresa (nombre) VALUES ('Mi Empresa')");
    $empresa = $pdo->query("SELECT * FROM tb_empresa WHERE id_empresa = 1")->fetch(PDO::FETCH_ASSOC);
}
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Datos de la Empresa</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-building me-2 text-primary"></i>Información general</h3></div>
                <div class="card-body">
                    <form method="POST" action="controles/guardar_empresa.php">
                        <?php require_once('../app/config/seguridad.php'); echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre de la empresa <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" value="<?= hescape($empresa['nombre']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">NIT / Documento</label>
                                    <input type="text" name="documento" class="form-control" value="<?= hescape($empresa['documento']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" name="direccion" class="form-control" value="<?= hescape($empresa['direccion']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="<?= hescape($empresa['telefono']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= hescape($empresa['email']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Ubicación en el mapa</h5>
                                <p class="text-muted">Haz clic en el mapa para marcar la ubicación de la empresa</p>
                                <div class="mb-3">
                                    <label class="form-label">Latitud</label>
                                    <input type="text" name="lat" id="lat" class="form-control" value="<?= $empresa['lat'] ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Longitud</label>
                                    <input type="text" name="lng" id="lng" class="form-control" value="<?= $empresa['lng'] ?>" readonly>
                                </div>
                                <div id="map" style="height:300px;border:1px solid #ddd;border-radius:4px;"></div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Guardar</button>
                            <a href="../configuracion/index.php" class="btn btn-secondary btn-lg"><i class="fas fa-times me-2"></i>Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const lat = <?= $empresa['lat'] ? $empresa['lat'] : '4.7110' ?>;
const lng = <?= $empresa['lng'] ? $empresa['lng'] : '-74.0721' ?>;
const map = L.map('map').setView([lat, lng], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);
const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
marker.on('dragend', function(e) {
    const pos = marker.getLatLng();
    document.getElementById('lat').value = pos.lat.toFixed(7);
    document.getElementById('lng').value = pos.lng.toFixed(7);
});
map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    document.getElementById('lat').value = e.latlng.lat.toFixed(7);
    document.getElementById('lng').value = e.latlng.lng.toFixed(7);
});
</script>

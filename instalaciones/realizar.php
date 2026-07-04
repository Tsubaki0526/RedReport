<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

$id_cliente = (int)($_GET['id'] ?? 0);
$sql = "SELECT c.*, u.nombre AS instalador_nombre FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario WHERE c.id_cliente = :id";
$cliente = $pdo->prepare($sql);
$cliente->execute([':id' => $id_cliente]);
$cliente = $cliente->fetch();

if (!$cliente) { echo "<script>alert('Cliente no encontrado');window.location='index.php';</script>"; exit; }

// Only assigned installer or admin can complete
if ($_SESSION['id_rol'] != 1 && $cliente['id_instalador'] != $_SESSION['id_usuario']) {
    echo "<script>alert('No tienes permiso');window.location='index.php';</script>"; exit;
}

$tipos = $pdo->query("SELECT * FROM tb_tipos_equipo")->fetchAll();
$equipos_asignados = $pdo->prepare("SELECT e.*, t.nombre AS tipo FROM tb_equipos e INNER JOIN tb_tipos_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo WHERE e.id_cliente = :id");
$equipos_asignados->execute([':id' => $id_cliente]);
$equipos_asignados = $equipos_asignados->fetchAll();
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Realizar Instalación</h1></div>
                <div class="col-sm-6 text-end"><span id="fechaHora" class="text-muted"></span></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header"><h3 class="card-title">Datos del cliente</h3></div>
                        <div class="card-body">
                            <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre']) ?></p>
                            <p><strong>Dirección:</strong> <?= htmlspecialchars($cliente['direccion'] ?? '-') ?></p>
                            <p><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono'] ?? '-') ?></p>
                            <p><strong>Documento:</strong> <?= htmlspecialchars($cliente['documento'] ?? '-') ?></p>
                        </div>
                    </div>

                    <div class="card card-success">
                        <div class="card-header"><h3 class="card-title">Ubicación</h3></div>
                        <div class="card-body">
                            <div id="mapInstalacion" style="height:250px;"></div>
                            <form id="coordForm" class="mt-2">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label">Latitud</label>
                                        <input type="text" id="lat" name="lat" class="form-control form-control-sm" value="<?= $cliente['lat'] ?? '' ?>" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Longitud</label>
                                        <input type="text" id="lng" name="lng" class="form-control form-control-sm" value="<?= $cliente['lng'] ?? '' ?>" readonly>
                                    </div>
                                </div>
                                <button type="button" id="btnGeolocate" class="btn btn-sm btn-info mt-2"><i class="fas fa-crosshairs"></i> Obtener mi ubicación</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <form action="controles/completar_instalacion.php" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_cliente" value="<?= $id_cliente ?>">
                        <input type="hidden" name="lat" id="lat_hidden" value="<?= $cliente['lat'] ?? '' ?>">
                        <input type="hidden" name="lng" id="lng_hidden" value="<?= $cliente['lng'] ?? '' ?>">

                        <div class="card card-warning">
                            <div class="card-header"><h3 class="card-title">Equipos instalados</h3></div>
                            <div class="card-body">
                                <div id="equiposContainer">
                                    <div class="row equipo-row mb-2">
                                        <div class="col-4">
                                            <select name="id_tipo_equipo[]" class="form-control form-control-sm" required>
                                                <option value="">Tipo</option>
                                                <?php foreach ($tipos as $t): ?>
                                                <option value="<?= $t['id_tipo_equipo'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" name="serial[]" class="form-control form-control-sm" placeholder="Serial" required>
                                        </div>
                                        <div class="col-4">
                                            <input type="text" name="marca[]" class="form-control form-control-sm" placeholder="Marca">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="addEquipo" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Agregar equipo</button>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Finalizar instalación</h3></div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check"></i> Completar instalación</button>
                            </div>
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
const map = L.map('mapInstalacion').setView([<?= $cliente['lat'] ?? '4.7110' ?>, <?= $cliente['lng'] ?? '-74.0721' ?>], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let marker = null;
<?php if ($cliente['lat'] && $cliente['lng']): ?>
marker = L.marker([<?= $cliente['lat'] ?>, <?= $cliente['lng'] ?>]).addTo(map);
<?php endif; ?>

document.getElementById('btnGeolocate').addEventListener('click', function() {
    if (!navigator.geolocation) { alert('Geolocalización no soportada'); return; }
    navigator.geolocation.getCurrentPosition(function(pos) {
        const lat = pos.coords.latitude, lng = pos.coords.longitude;
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('lat_hidden').value = lat;
        document.getElementById('lng_hidden').value = lng;
        map.setView([lat, lng], 17);
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);
    }, function() { alert('No se pudo obtener ubicación'); });
});

document.getElementById('addEquipo').addEventListener('click', function() {
    const row = document.querySelector('.equipo-row').cloneNode(true);
    row.querySelectorAll('input').forEach(i => i.value = '');
    row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    document.getElementById('equiposContainer').appendChild(row);
});
</script>

<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');
if ($_SESSION['id_rol'] != 1) { echo "<script>alert('Acceso denegado');window.location='../index.php';</script>"; exit; }

$zonas = $pdo->query("SELECT * FROM tb_cobertura_zonas ORDER BY fecha_creacion DESC")->fetchAll();
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Administrar Zonas de Cobertura</h1></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Nueva Zona</h3></div>
                        <div class="card-body">
                            <form action="controles/guardar_zona.php" method="POST">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label">Nombre de la zona</label>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Color</label>
                                    <input type="color" name="color" class="form-control form-control-color" value="#3388ff">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Coordenadas (JSON)</label>
                                    <textarea name="coordenadas" class="form-control" rows="5" placeholder='[[4.711,-74.072],[4.712,-74.070],...]' required></textarea>
                                    <small class="text-muted">Dibuja en el mapa y copia las coordenadas</small>
                                </div>
                                <div id="mapPreview" style="height:200px;" class="mb-3"></div>
                                <button type="submit" class="btn btn-primary">Guardar zona</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Zonas existentes</h3></div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead><tr><th>Nombre</th><th>Color</th><th>Activo</th><th>Acción</th></tr></thead>
                                <tbody>
                                    <?php foreach ($zonas as $z): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($z['nombre']) ?></td>
                                        <td><span style="display:inline-block;width:20px;height:20px;background:<?= htmlspecialchars($z['color']) ?>;border-radius:4px;"></span></td>
                                        <td><?= $z['activo'] ? 'Sí' : 'No' ?></td>
                                        <td>
                                            <form action="controles/toggle_zona.php" method="POST" style="display:inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= $z['id_zona'] ?>">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <?= $z['activo'] ? 'Desactivar' : 'Activar' ?>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('mapPreview').setView([4.7110, -74.0721], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let drawnPolygon = null;
map.on('click', function(e) {
    if (!drawnPolygon) {
        drawnPolygon = L.polygon([e.latlng], { color: '#3388ff' }).addTo(map);
    } else {
        let coords = drawnPolygon.getLatLngs()[0];
        coords.push(e.latlng);
        drawnPolygon.setLatLngs([coords]);
    }
    if (drawnPolygon) {
        const coords = drawnPolygon.getLatLngs()[0].map(c => [c.lat, c.lng]);
        document.querySelector('textarea[name="coordenadas"]').value = JSON.stringify(coords);
    }
});
</script>

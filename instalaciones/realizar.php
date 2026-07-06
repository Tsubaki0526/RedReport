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
                            <div class="card-header"><h3 class="card-title">Fotos / Evidencias</h3></div>
                            <div class="card-body">
                                <div id="dropZone" class="border border-2 border-dashed rounded p-4 text-center mb-3" style="border-style: dashed !important; cursor:pointer; background:#f8f9fa;">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                    <p class="mb-1 text-muted">Arrastra una foto aquí o haz clic para seleccionar</p>
                                    <input type="file" id="fotoInput" name="foto" class="d-none" accept="image/*">
                                </div>
                                <div id="previewContainer" class="mb-3 d-none">
                                    <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                        <img id="previewImg" src="#" alt="Preview" style="height:60px;width:60px;object-fit:cover;border-radius:4px;">
                                        <div class="flex-grow-1">
                                            <input type="text" id="fotoDesc" class="form-control form-control-sm" placeholder="Descripcion (opcional)">
                                        </div>
                                        <button id="btnUploadFoto" class="btn btn-sm btn-info"><i class="fas fa-upload"></i></button>
                                        <button id="btnCancelPreview" class="btn btn-sm btn-outline-secondary"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                                <div id="uploadProgress" class="d-none mb-2"><div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:100%">Subiendo...</div></div></div>
                                <div id="fotosContainer" class="row">
                                    <?php
                                    $fotos = $pdo->prepare("SELECT * FROM tb_instalacion_fotos WHERE id_cliente = ? ORDER BY fecha_subida DESC");
                                    $fotos->execute([$cliente['id_cliente']]);
                                    $fotos = $fotos->fetchAll();
                                    foreach ($fotos as $f): ?>
                                    <div class="col-4 col-md-3 mb-2" data-foto="<?= hescape($f['nombre_archivo']) ?>">
                                        <a href="<?= APP_URL ?>public/uploads/<?= hescape($f['nombre_archivo']) ?>" target="_blank">
                                            <img src="<?= APP_URL ?>public/uploads/<?= hescape($f['nombre_archivo']) ?>" class="img-thumbnail" style="height:80px;object-fit:cover;">
                                        </a>
                                        <?php if ($f['descripcion']): ?><small class="d-block text-muted"><?= hescape($f['descripcion']) ?></small><?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
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

// Photo upload with drag-and-drop and AJAX
const dropZone = document.getElementById('dropZone');
const fotoInput = document.getElementById('fotoInput');
const previewContainer = document.getElementById('previewContainer');
const previewImg = document.getElementById('previewImg');
const fotoDesc = document.getElementById('fotoDesc');
const uploadProgress = document.getElementById('uploadProgress');
const fotosContainer = document.getElementById('fotosContainer');
const csrfToken = document.querySelector('input[name="_csrf_token"]')?.value;

dropZone.addEventListener('click', () => fotoInput.click());
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.background = '#e9ecef'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.background = '#f8f9fa'; });
dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.style.background = '#f8f9fa'; handleFile(e.dataTransfer.files[0]); });
fotoInput.addEventListener('change', () => { if (fotoInput.files[0]) handleFile(fotoInput.files[0]); });

function handleFile(file) {
    if (!file.type.startsWith('image/')) { alert('Solo imágenes'); return; }
    const reader = new FileReader();
    reader.onload = e => {
        previewImg.src = e.target.result;
        previewContainer.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
}

document.getElementById('btnUploadFoto').addEventListener('click', function() {
    const file = fotoInput.files[0];
    if (!file) return;
    const form = new FormData();
    form.append('foto', file);
    form.append('descripcion', fotoDesc.value);
    form.append('id_cliente', '<?= $id_cliente ?>');
    form.append('_csrf_token', csrfToken);
    uploadProgress.classList.remove('d-none');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'controles/subir_foto.php');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
        uploadProgress.classList.add('d-none');
        if (xhr.status === 200) {
            try {
                const r = JSON.parse(xhr.responseText);
                if (r.ok) {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3 mb-2';
                    col.innerHTML = '<a href="' + r.url + '" target="_blank"><img src="' + r.url + '" class="img-thumbnail" style="height:80px;object-fit:cover;"></a>' +
                        (r.descripcion ? '<small class="d-block text-muted">' + r.descripcion + '</small>' : '');
                    fotosContainer.prepend(col);
                    previewContainer.classList.add('d-none');
                    fotoInput.value = '';
                    fotoDesc.value = '';
                } else { alert(r.error || 'Error al subir'); }
            } catch(e) { alert('Error al procesar respuesta'); }
        } else { alert('Error del servidor'); }
    };
    xhr.send(form);
});

document.getElementById('btnCancelPreview').addEventListener('click', function() {
    previewContainer.classList.add('d-none');
    fotoInput.value = '';
    fotoDesc.value = '';
});
</script>

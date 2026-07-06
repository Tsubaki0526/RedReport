<?php
$titulo = 'Nueva Instalación';
$seccion = 'instalacion';
require_once __DIR__ . '/../header.php';
if (!$movil_user || !$es_empleado) { header('Location: ../login.php'); exit; }

$tipos_equipo = $pdo->query("SELECT * FROM tb_tipos_equipo ORDER BY nombre")->fetchAll();
$equipos_disp = $pdo->query("SELECT * FROM tb_equipos WHERE estado='Disponible' ORDER BY serial")->fetchAll();
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-wifi me-2"></i>Nueva Instalación</h6></div>
    <a href="/RedReport/movil/index.php" class="btn btn-sm btn-outline-light"><i class="fas fa-times"></i></a>
  </div>
</div>

<div class="container-fluid px-3 py-3">
  <form method="POST" action="instalacion_guardar.php" enctype="multipart/form-data" id="formInstalacion">
    <input type="hidden" name="_csrf_token" value="<?= hescape($_SESSION['_csrf_token'] ?? '') ?>">

    <div class="card p-3">
      <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:12px"><i class="fas fa-user me-1"></i>Cliente</h6>
      <div class="mb-2">
        <input type="text" id="buscarCliente" class="form-control form-control-mobile" placeholder="Buscar cliente por nombre o documento..." autocomplete="off">
        <div id="resultadosCliente" style="display:none"></div>
      </div>
      <input type="hidden" name="id_cliente" id="id_cliente" required>
      <div id="clienteInfo" class="p-3 rounded" style="background:#f8fafc;border:1px solid var(--border);display:none">
        <p class="mb-1"><strong id="cliNombre"></strong></p>
        <p class="mb-1" style="font-size:13px;color:var(--text-muted)"><i class="fas fa-map-marker-alt me-1"></i><span id="cliDireccion"></span></p>
        <p class="mb-0" style="font-size:13px;color:var(--text-muted)"><i class="fas fa-phone me-1"></i><span id="cliTelefono"></span></p>
      </div>
    </div>

    <div class="card p-3">
      <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:12px"><i class="fas fa-map-marker-alt me-1"></i>Ubicación</h6>
      <div class="mb-2">
        <button type="button" class="btn btn-outline-primary btn-mobile w-100" onclick="getLocation()"><i class="fas fa-location-dot me-2"></i>Obtener ubicación actual</button>
      </div>
      <div class="row g-2 mb-2">
        <div class="col-6"><input type="text" name="lat" id="lat" class="form-control form-control-mobile" placeholder="Latitud" readonly></div>
        <div class="col-6"><input type="text" name="lng" id="lng" class="form-control form-control-mobile" placeholder="Longitud" readonly></div>
      </div>
      <div id="mapaPreview" style="height:200px;border-radius:10px;display:none"></div>
    </div>

    <div class="card p-3">
      <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:12px"><i class="fas fa-camera me-1"></i>Fotos</h6>
      <input type="file" name="fotos[]" accept="image/*" capture="environment" multiple class="form-control form-control-mobile mb-2">
      <small class="text-muted">Toma fotos del equipo instalado y la ubicación</small>
    </div>

    <div class="card p-3">
      <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:12px"><i class="fas fa-microchip me-1"></i>Equipo instalado</h6>
      <div class="mb-2">
        <select name="id_tipo_equipo" id="id_tipo_equipo" class="form-select form-control-mobile" required>
          <option value="">Tipo de equipo</option>
          <?php foreach ($tipos_equipo as $t): ?><option value="<?= $t['id_tipo_equipo'] ?>"><?= hescape($t['nombre']) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2"><input type="text" name="serial" class="form-control form-control-mobile" placeholder="Serial del equipo" required></div>
      <div class="row g-2 mb-2">
        <div class="col-6"><input type="text" name="marca" class="form-control form-control-mobile" placeholder="Marca"></div>
        <div class="col-6"><input type="text" name="modelo" class="form-control form-control-mobile" placeholder="Modelo"></div>
      </div>
    </div>

    <div class="card p-3">
      <h6 style="font-size:13px;font-weight:600;color:var(--text-muted);margin-bottom:12px"><i class="fas fa-pen me-1"></i>Notas</h6>
      <textarea name="notas" class="form-control form-control-mobile" rows="3" placeholder="Observaciones de la instalación..."></textarea>
    </div>

    <button type="submit" class="btn btn-success btn-mobile w-100 mb-3" id="btnGuardar">
      <i class="fas fa-check me-2"></i>Finalizar Instalación
    </button>
  </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Client search
const searchInput = document.getElementById('buscarCliente');
const resultsDiv = document.getElementById('resultadosCliente');
let searchTimeout;

searchInput.addEventListener('input', function() {
  clearTimeout(searchTimeout);
  const q = this.value.trim();
  if (q.length < 2) { resultsDiv.style.display = 'none'; return; }
  searchTimeout = setTimeout(() => {
    fetch('../controles/buscar_cliente.php?q=' + encodeURIComponent(q))
      .then(r => r.json())
      .then(data => {
        resultsDiv.innerHTML = data.map(c =>
          `<div class="list-item" onclick="seleccionarCliente(${c.id_cliente},'${c.nombre.replace(/'/g,"\\'")}','${(c.direccion||'').replace(/'/g,"\\'")}','${(c.telefono||'').replace(/'/g,"\\'")}')">
            <div class="body"><div class="title">${c.nombre}</div><div class="sub">${c.documento || ''} · ${c.direccion || ''}</div></div>
          </div>`
        ).join('');
        resultsDiv.style.display = data.length ? 'block' : 'none';
      });
  }, 300);
});

function seleccionarCliente(id, nombre, direccion, telefono) {
  document.getElementById('id_cliente').value = id;
  document.getElementById('cliNombre').textContent = nombre;
  document.getElementById('cliDireccion').textContent = direccion || 'Sin dirección';
  document.getElementById('cliTelefono').textContent = telefono || 'Sin teléfono';
  document.getElementById('clienteInfo').style.display = 'block';
  resultsDiv.style.display = 'none';
  searchInput.value = nombre;
}

// GPS
function getLocation() {
  if (!navigator.geolocation) { alert('GPS no disponible'); return; }
  document.getElementById('lat').value = 'Obteniendo...';
  document.getElementById('lng').value = 'Obteniendo...';
  navigator.geolocation.getCurrentPosition(pos => {
    const lat = pos.coords.latitude.toFixed(7);
    const lng = pos.coords.longitude.toFixed(7);
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;

    const mapDiv = document.getElementById('mapaPreview');
    mapDiv.style.display = 'block';
    const map = L.map(mapDiv).setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19}).addTo(map);
    L.marker([lat, lng]).addTo(map);
    setTimeout(() => map.invalidateSize(), 300);
  }, () => {
    alert('No se pudo obtener la ubicación. Verifica el GPS.');
    document.getElementById('lat').value = '';
    document.getElementById('lng').value = '';
  }, { enableHighAccuracy: true, timeout: 15000 });
}
</script>
<?php require_once __DIR__ . '/../footer.php'; ?>

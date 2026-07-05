<?php
include('../sesion.php');
include('../parte1.php');
$url = APP_URL;
?>
<style>
  #dropZone {
    border: 2px dashed var(--border-color, #cbd5e1);
    border-radius: 12px;
    padding: 2.5rem 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: var(--transition, all 0.2s ease);
    background: var(--input-bg, #fff);
    position: relative;
  }
  #dropZone:hover,
  #dropZone.dragover {
    border-color: var(--primary, #2563eb);
    background: rgba(37, 99, 235, 0.04);
  }
  #dropZone.has-file {
    border-color: var(--success, #16a34a);
    background: rgba(22, 163, 74, 0.04);
  }
  #dropZone .drop-icon {
    font-size: 3rem;
    color: var(--text-muted, #94a3b8);
    margin-bottom: 0.75rem;
  }
  #dropZone.has-file .drop-icon {
    color: var(--success, #16a34a);
  }
  #dropZone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
  }
  .mapping-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
  }
  .mapping-row:last-child {
    border-bottom: none;
  }
  .mapping-row .csv-col {
    font-weight: 600;
    min-width: 140px;
    color: var(--text-primary, #1e293b);
  }
  .mapping-row .arrow {
    color: var(--text-muted, #94a3b8);
    font-size: 1.1rem;
  }
  .mapping-row select {
    min-width: 180px;
  }
  .preview-table {
    font-size: 0.85rem;
  }
  .preview-table th {
    white-space: nowrap;
  }
  .stat-import {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.85rem;
  }
  .stat-import.success { background: #dcfce7; color: #166534; }
  .stat-import.danger { background: #fee2e2; color: #991b1b; }

  #previewSection { display: none; }
  #mappingSection { display: none; }
  #btnImport { display: none; }
</style>

<div class="content-wrapper">

  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-file-csv me-2 text-primary"></i>Importar Clientes desde CSV</h1>
        </div>
        <div class="col-sm-6 text-end">
          <a href="vistas/lista.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Volver a Clientes
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <!-- Step 1: Upload -->
          <div class="card" id="stepUpload">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-upload me-2 text-primary"></i>1. Seleccionar archivo CSV</h3>
            </div>
            <div class="card-body">
              <div id="dropZone">
                <div class="drop-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <p class="mb-1 fw-semibold">Arrastra y suelta tu archivo CSV aquí</p>
                <p class="text-muted small mb-2">o haz clic para seleccionarlo</p>
                <p class="text-muted small mb-0">Formatos aceptados: <code>.csv</code> — Tamaño máximo: <strong>5 MB</strong></p>
                <input type="file" id="csvFile" accept=".csv" title="Seleccionar archivo CSV">
              </div>
              <div class="mt-3 d-flex align-items-center gap-3" id="fileInfo" style="display:none !important;">
                <span class="stat-import success"><i class="fas fa-check-circle"></i> <span id="fileName"></span></span>
                <span class="text-muted small" id="fileSize"></span>
                <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="btnRemoveFile">
                  <i class="fas fa-times"></i> Quitar
                </button>
              </div>
            </div>
          </div>

          <!-- Step 2: Preview -->
          <div class="card" id="previewSection">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-eye me-2 text-primary"></i>2. Vista previa (primeras 5 filas)</h3>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover preview-table mb-0" id="previewTable">
                  <thead id="previewHead"></thead>
                  <tbody id="previewBody"></tbody>
                </table>
              </div>
              <div class="p-3 text-muted small" id="previewCount"></div>
            </div>
          </div>

          <!-- Step 3: Column Mapping -->
          <div class="card" id="mappingSection">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-random me-2 text-primary"></i>3. Asignación de columnas</h3>
              <div class="card-tools">
                <span class="badge bg-info" id="mappedCount">0 / 6 campos mapeados</span>
              </div>
            </div>
            <div class="card-body">
              <p class="text-muted small mb-3">
                <i class="fas fa-info-circle me-1"></i>
                Asigna cada columna del CSV al campo correspondiente de la base de datos.
                Los campos <strong>nombre</strong> y <strong>documento</strong> son obligatorios.
              </p>
              <div id="mappingList"></div>
            </div>
          </div>

          <!-- Step 4: Import -->
          <div class="card" id="stepImport">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-play me-2 text-primary"></i>4. Importar</h3>
            </div>
            <div class="card-body text-center">
              <p class="text-muted mb-3" id="importReadyMsg">Revisa la asignación de columnas y haz clic en Importar.</p>
              <button type="button" class="btn btn-primary btn-lg px-5" id="btnImport" disabled>
                <i class="fas fa-file-import me-2"></i>Importar Clientes
              </button>
              <div id="importProgress" style="display:none;" class="mt-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                Importando...
              </div>
            </div>
          </div>

          <!-- Results -->
          <div class="card" id="resultSection" style="display:none;">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-clipboard-check me-2"></i>Resultado de la importación</h3>
            </div>
            <div class="card-body">
              <div class="row text-center mb-4">
                <div class="col-6">
                  <div class="stat-import success fs-5 px-4 py-2" id="resultSuccess">0</div>
                  <small class="text-muted">Importados</small>
                </div>
                <div class="col-6">
                  <div class="stat-import danger fs-5 px-4 py-2" id="resultErrors">0</div>
                  <small class="text-muted">Con errores</small>
                </div>
              </div>
              <div id="resultDetails" style="display:none;">
                <hr>
                <h6><i class="fas fa-exclamation-triangle text-warning me-1"></i> Detalle de errores</h6>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered preview-table">
                    <thead><tr><th># Fila</th><th>Error</th></tr></thead>
                    <tbody id="resultErrorList"></tbody>
                  </table>
                </div>
              </div>
              <div class="text-center mt-3">
                <a href="vistas/lista.php" class="btn btn-primary"><i class="fas fa-users me-1"></i>Ver Clientes</a>
                <button type="button" class="btn btn-outline-secondary ms-2" id="btnImportAnother">
                  <i class="fas fa-upload me-1"></i>Importar otro archivo
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

</div>

<script>
const TARGET_FIELDS = [
  { id: 'nombre', label: 'Nombre', required: true },
  { id: 'documento', label: 'Documento / NIT', required: true },
  { id: 'email', label: 'Correo electrónico', required: false },
  { id: 'telefono', label: 'Teléfono', required: false },
  { id: 'direccion', label: 'Dirección', required: false },
  { id: 'ciudad', label: 'Ciudad', required: false },
];

let csvHeaders = [];
let csvRows = [];
let csvRaw = '';

function humanSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / 1048576).toFixed(1) + ' MB';
}

function parseCSV(text) {
  const lines = [];
  let current = '';
  let inQuotes = false;
  for (let i = 0; i < text.length; i++) {
    const ch = text[i];
    if (inQuotes) {
      if (ch === '"') {
        if (i + 1 < text.length && text[i + 1] === '"') {
          current += '"';
          i++;
        } else {
          inQuotes = false;
        }
      } else {
        current += ch;
      }
    } else {
      if (ch === '"') {
        inQuotes = true;
      } else if (ch === ',') {
        lines.push(current);
        current = '';
      } else if (ch === '\n' || ch === '\r') {
        if (ch === '\r' && i + 1 < text.length && text[i + 1] === '\n') i++;
        lines.push(current);
        current = '';
        lines.push(null);
      } else {
        current += ch;
      }
    }
  }
  if (current.length > 0) lines.push(current);

  const rows = [];
  let row = [];
  for (const val of lines) {
    if (val === null) {
      if (row.length > 0) rows.push(row);
      row = [];
    } else {
      row.push(val.trim());
    }
  }
  if (row.length > 0) rows.push(row);
  return rows;
}

function renderPreview(headers, data) {
  const preview = data.slice(0, 5);
  const thead = document.getElementById('previewHead');
  const tbody = document.getElementById('previewBody');

  let hHtml = '<tr>';
  headers.forEach(h => { hHtml += '<th>' + hescape(h) + '</th>'; });
  hHtml += '</tr>';
  thead.innerHTML = hHtml;

  if (preview.length === 0) {
    tbody.innerHTML = '<tr><td colspan="' + headers.length + '" class="text-center text-muted py-4">El archivo no contiene datos</td></tr>';
  } else {
    let bHtml = '';
    preview.forEach(row => {
      bHtml += '<tr>';
      headers.forEach((_, i) => {
        bHtml += '<td>' + hescape(row[i] ?? '') + '</td>';
      });
      bHtml += '</tr>';
    });
    tbody.innerHTML = bHtml;
  }

  document.getElementById('previewCount').textContent = 'Total de filas: ' + data.length + ' (mostrando primeras ' + Math.min(5, data.length) + ')';
  document.getElementById('previewSection').style.display = 'block';
}

function renderMapping(headers) {
  const container = document.getElementById('mappingList');
  let html = '';
  TARGET_FIELDS.forEach(field => {
    const autoMatch = headers.find(h => h.toLowerCase().replace(/[\s_-]/g, '') === field.id.toLowerCase());
    html += '<div class="mapping-row">';
    html += '<span class="csv-col">' + hescape(field.label) + (field.required ? ' <span class="text-danger">*</span>' : '') + '</span>';
    html += '<span class="arrow"><i class="fas fa-arrow-right"></i></span>';
    html += '<select class="form-select form-select-sm" data-target="' + hescape(field.id) + '" data-required="' + field.required + '">';
    html += '<option value="">— No importar —</option>';
    headers.forEach((h, i) => {
      const sel = autoMatch === h ? ' selected' : '';
      html += '<option value="' + i + '"' + sel + '>' + hescape(h) + '</option>';
    });
    html += '</select>';
    if (field.required) {
      html += '<small class="text-danger ms-1 required-mark">*</small>';
    }
    html += '</div>';
  });
  container.innerHTML = html;

  updateMappingCount();
}

function updateMappingCount() {
  const selects = document.querySelectorAll('#mappingList select');
  let mapped = 0;
  selects.forEach(sel => {
    if (sel.value !== '') mapped++;
  });
  document.getElementById('mappedCount').textContent = mapped + ' / ' + TARGET_FIELDS.length + ' campos mapeados';

  // Enable/disable import button
  const requiredMapped = Array.from(selects).every(sel => {
    if (sel.dataset.required === 'true') return sel.value !== '';
    return true;
  });
  document.getElementById('btnImport').disabled = !requiredMapped;
  document.getElementById('importReadyMsg').textContent = requiredMapped
    ? 'Todo listo para importar ' + csvRows.length + ' registros.'
    : 'Asigna las columnas obligatorias (marcadas con *) para poder importar.';
}

function resetImport() {
  document.getElementById('resultSection').style.display = 'none';
  document.getElementById('stepUpload').style.display = 'block';
  document.getElementById('previewSection').style.display = 'none';
  document.getElementById('mappingSection').style.display = 'none';
  document.getElementById('stepImport').style.display = 'block';
  document.getElementById('btnImport').style.display = 'none';
  document.getElementById('btnImport').disabled = true;
  document.getElementById('importProgress').style.display = 'none';
  const dropZone = document.getElementById('dropZone');
  dropZone.classList.remove('has-file');
  document.getElementById('fileInfo').style.display = 'none';
  document.getElementById('csvFile').value = '';
  csvHeaders = [];
  csvRows = [];
  csvRaw = '';
}

// --- Event handlers ---

document.getElementById('csvFile').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  if (file.size > 5 * 1048576) {
    Swal.fire({ icon: 'error', title: 'Archivo muy grande', text: 'El tamaño máximo permitido es 5 MB.' });
    this.value = '';
    return;
  }
  if (!file.name.toLowerCase().endsWith('.csv')) {
    Swal.fire({ icon: 'error', title: 'Formato incorrecto', text: 'Solo se aceptan archivos .csv' });
    this.value = '';
    return;
  }

  document.getElementById('fileName').textContent = file.name;
  document.getElementById('fileSize').textContent = '(' + humanSize(file.size) + ')';
  document.getElementById('fileInfo').style.display = 'flex';
  document.getElementById('dropZone').classList.add('has-file');

  const reader = new FileReader();
  reader.onload = function(ev) {
    csvRaw = ev.target.result;
    const rows = parseCSV(csvRaw);
    if (rows.length < 2) {
      Swal.fire({ icon: 'error', title: 'CSV vacío', text: 'El archivo no contiene datos válidos.' });
      return;
    }
    csvHeaders = rows[0];
    csvRows = rows.slice(1).filter(r => r.some(c => c !== ''));

    renderPreview(csvHeaders, csvRows);
    renderMapping(csvHeaders);
    document.getElementById('mappingSection').style.display = 'block';
    document.getElementById('btnImport').style.display = 'inline-block';
    updateMappingCount();
  };
  reader.readAsText(file);
});

document.getElementById('dropZone').addEventListener('dragover', function(e) {
  e.preventDefault();
  this.classList.add('dragover');
});
document.getElementById('dropZone').addEventListener('dragleave', function(e) {
  e.preventDefault();
  this.classList.remove('dragover');
});
document.getElementById('dropZone').addEventListener('drop', function(e) {
  e.preventDefault();
  this.classList.remove('dragover');
  const file = e.dataTransfer.files[0];
  if (file) {
    document.getElementById('csvFile').files = e.dataTransfer.files;
    document.getElementById('csvFile').dispatchEvent(new Event('change'));
  }
});

document.getElementById('btnRemoveFile').addEventListener('click', function() {
  resetImport();
});

document.getElementById('mappingList').addEventListener('change', function(e) {
  if (e.target.matches('select[data-target]')) {
    updateMappingCount();
  }
});

document.getElementById('btnImport').addEventListener('click', function() {
  if (this.disabled) return;
  const selects = document.querySelectorAll('#mappingList select');
  const mapping = {};
  let valid = true;
  selects.forEach(sel => {
    const target = sel.dataset.target;
    const val = sel.value;
    if (sel.dataset.required === 'true' && val === '') {
      valid = false;
    }
    mapping[target] = val !== '' ? parseInt(val, 10) : null;
  });
  if (!valid) {
    Swal.fire({ icon: 'warning', title: 'Campos obligatorios', text: 'Debes asignar las columnas obligatorias (Nombre y Documento).' });
    return;
  }

  const formData = new FormData();
  formData.append('_csrf_token', '<?= csrf_token() ?>');
  formData.append('mapping', JSON.stringify(mapping));
  formData.append('csv', csvRaw);

  document.getElementById('btnImport').disabled = true;
  document.getElementById('importProgress').style.display = 'inline-block';

  fetch('controles/importar_csv.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(res => {
    document.getElementById('importProgress').style.display = 'none';
    document.getElementById('resultSuccess').textContent = res.success;
    document.getElementById('resultErrors').textContent = res.errors;
    document.getElementById('resultSection').style.display = 'block';
    document.getElementById('btnImport').style.display = 'none';

    if (res.error_details && res.error_details.length > 0) {
      let html = '';
      res.error_details.forEach(err => {
        html += '<tr><td>' + err.row + '</td><td>' + hescape(err.message) + '</td></tr>';
      });
      document.getElementById('resultErrorList').innerHTML = html;
      document.getElementById('resultDetails').style.display = 'block';
    }

    const icon = res.errors > 0 ? (res.success > 0 ? 'warning' : 'error') : 'success';
    Swal.fire({
      icon: icon,
      title: res.errors > 0 ? 'Importación completada con errores' : 'Importación exitosa',
      text: res.errors > 0
        ? res.success + ' registros importados, ' + res.errors + ' errores.'
        : res.success + ' clientes importados correctamente.',
      confirmButtonText: 'Aceptar'
    });
  })
  .catch(err => {
    document.getElementById('importProgress').style.display = 'none';
    document.getElementById('btnImport').disabled = false;
    Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo completar la importación.' });
  });
});

document.getElementById('btnImportAnother').addEventListener('click', function() {
  resetImport();
});
</script>

<?php include('../parte2.php'); ?>

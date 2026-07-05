  <!-- Main Footer -->
  <footer class="main-footer d-flex justify-content-between align-items-center">
    <strong>akai tsubaki</strong>
    <span><?= htmlspecialchars($_SESSION['usuario'] ?? 'Invitado') ?></span>
  </footer>

  </div>
</div><!-- /.wrapper -->

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (required by DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables BS5 -->
<script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.11/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.10/vfs_fonts.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Sidebar toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function(e) {
  e.preventDefault();
  document.getElementById('mainSidebar')?.classList.toggle('show');
  document.getElementById('sidebarOverlay')?.classList.toggle('show');
});

document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
  document.getElementById('mainSidebar')?.classList.remove('show');
  this.classList.remove('show');
});

// Logout
$(document).on('click', '#btnLogout, #btnLogoutNav', function(e) {
  e.preventDefault();
  $.ajax({
    url: '<?= APP_URL ?>app/controles/controles_login/logout.php',
    type: 'POST',
    success: function(response) {
      const res = JSON.parse(response);
      if (res.success) {
        Swal.fire({
          icon: 'success',
          title: 'Sesion cerrada',
          text: 'Has cerrado sesion correctamente',
          confirmButtonText: 'Aceptar'
        }).then(() => {
          window.location.replace('<?= APP_URL ?>login/login.php');
        });
      }
    },
    error: function() {
      Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cerrar la sesion' });
    }
  });
});

// Clock
function actualizarFechaHora() {
  const el = document.getElementById('fechaHora');
  if (!el) return;
  const now = new Date();
  el.textContent = now.toLocaleString('es-CO', {
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true,
    timeZone: 'America/Bogota'
  });
}
setInterval(actualizarFechaHora, 1000);
actualizarFechaHora();

// HTML escape helper
function hescape(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

// Fullscreen toggle
document.querySelector('[data-bs-toggle="fullscreen"]')?.addEventListener('click', function(e) {
  e.preventDefault();
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
  } else {
    document.exitFullscreen();
  }
});

// Auto-hide alerts
document.querySelectorAll('.alert-auto').forEach(function(el) {
  setTimeout(function() { el.style.display = 'none'; }, 5000);
});

// Notificaciones: contador no leidas
function actualizarNotif() {
  fetch('<?= APP_URL ?>notificaciones/controles/contar_no_leidas.php')
    .then(r => r.json())
    .then(d => {
      const badge = document.querySelector('.notif-badge');
      if (badge) {
        if (d.count > 0) { badge.style.display = 'inline'; badge.textContent = d.count; }
        else { badge.style.display = 'none'; }
      }
    }).catch(() => {});
}
actualizarNotif();
setInterval(actualizarNotif, 30000);
</script>

<!-- Global Search Modal -->
<div class="modal fade" id="globalSearchModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header pb-2">
        <div class="input-group">
          <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-muted"></i></span>
          <input type="text" class="form-control form-control-lg border-0 ps-0" id="globalSearchInput" placeholder="Buscar clientes, facturas, tickets, órdenes...">
          <span class="input-group-text bg-transparent border-0"><kbd class="bg-light text-muted border">ESC</kbd></span>
        </div>
      </div>
      <div class="modal-body pt-2" id="globalSearchResults">
        <div class="text-center text-muted py-4"><i class="fas fa-search me-2"></i>Escribe para buscar...</div>
      </div>
    </div>
  </div>
</div>

<!-- Global Search Logic -->
<script>
(function() {
  const btn = document.getElementById('globalSearchBtn');
  const modal = new bootstrap.Modal(document.getElementById('globalSearchModal'));
  const input = document.getElementById('globalSearchInput');
  const results = document.getElementById('globalSearchResults');
  let timer;

  btn?.addEventListener('click', function(e) { e.preventDefault(); modal.show(); setTimeout(() => input?.focus(), 300); });

  document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); modal.show(); setTimeout(() => input?.focus(), 300); }
    if (e.key === 'Escape' && document.getElementById('globalSearchModal')?.classList.contains('show')) { modal.hide(); }
  });

  input?.addEventListener('input', function() {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) { results.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-search me-2"></i>Escribe para buscar...</div>'; return; }
    timer = setTimeout(function() {
      results.innerHTML = '<div class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Buscando...</div>';
      fetch('<?= APP_URL ?>app/controles/busqueda_global.php?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(d => {
          if (!d.length) { results.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-times-circle me-2"></i>Sin resultados</div>'; return; }
          let html = '<div class="list-group list-group-flush">';
          d.forEach(function(item) {
            html += '<a href="' + item.url + '" class="list-group-item list-group-item-action d-flex align-items-center gap-3 border-0 rounded-3 mb-1">';
            html += '<span class="badge bg-' + item.badge + ' rounded-pill" style="width:70px;flex-shrink:0;">' + item.type + '</span>';
            html += '<div class="flex-grow-1"><div class="fw-semibold">' + item.label + '</div>';
            if (item.sub) html += '<small class="text-muted">' + item.sub + '</small>';
            html += '</div></a>';
          });
          html += '</div>';
          results.innerHTML = html;
        })
        .catch(function() { results.innerHTML = '<div class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle me-2"></i>Error de búsqueda</div>'; });
    }, 300);
  });
})();
</script>

</body>
</html>

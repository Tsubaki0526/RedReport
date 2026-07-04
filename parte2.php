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
</script>

</body>
</html>

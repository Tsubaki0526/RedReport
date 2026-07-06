<?php
$titulo = 'Reportar Falla';
$seccion = 'tickets';
require_once __DIR__ . '/../header.php';
if (!$movil_user || !$es_cliente) { header('Location: ../login.php'); exit; }
?>
<div class="topbar">
  <div class="d-flex justify-content-between align-items-center">
    <div><h6><i class="fas fa-exclamation-triangle me-2"></i>Reportar Falla</h6></div>
    <a href="tickets.php" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left"></i></a>
  </div>
</div>

<div class="container-fluid px-3 py-3">
  <form method="POST" action="ticket_guardar.php">
    <input type="hidden" name="_csrf_token" value="<?= hescape($_SESSION['_csrf_token'] ?? '') ?>">

    <div class="card p-3">
      <div class="mb-3">
        <label class="form-label" style="font-size:13px;font-weight:600">Asunto</label>
        <input type="text" name="asunto" class="form-control form-control-mobile" placeholder="Ej: No tengo internet" required>
      </div>
      <div class="mb-3">
        <label class="form-label" style="font-size:13px;font-weight:600">Categoría</label>
        <select name="categoria" class="form-select form-control-mobile" required>
          <option value="Fallo de conexion">Fallo de conexión</option>
          <option value="Equipo">Problema con equipo</option>
          <option value="Facturacion">Facturación</option>
          <option value="Otro">Otro</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label" style="font-size:13px;font-weight:600">Descripción</label>
        <textarea name="descripcion" class="form-control form-control-mobile" rows="5" placeholder="Describe el problema en detalle..." required></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-mobile w-100"><i class="fas fa-paper-plane me-2"></i>Enviar reporte</button>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../footer.php'; ?>

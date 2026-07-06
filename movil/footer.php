
<?php if ($movil_user): ?>
<nav class="bottombar">
  <a href="/RedReport/movil/index.php" class="<?= $seccion=='dashboard'?'active':'' ?>"><i class="fas fa-home"></i>Inicio</a>
  <?php if ($es_empleado): ?>
  <a href="/RedReport/movil/instalador/ordenes.php" class="<?= $seccion=='ordenes'?'active':'' ?>"><i class="fas fa-clipboard-list"></i>Órdenes</a>
  <?php else: ?>
  <a href="/RedReport/movil/cliente/facturas.php" class="<?= $seccion=='facturas'?'active':'' ?>"><i class="fas fa-file-invoice"></i>Facturas</a>
  <?php endif; ?>
  <?php if ($es_empleado): ?>
  <a href="/RedReport/movil/instalador/instalacion.php" class="<?= $seccion=='instalacion'?'active':'' ?>"><i class="fas fa-plus-circle"></i>Instalar</a>
  <?php else: ?>
  <a href="/RedReport/movil/cliente/tickets.php" class="<?= $seccion=='tickets'?'active':'' ?>"><i class="fas fa-headset"></i>Soporte</a>
  <?php endif; ?>
  <a href="/RedReport/movil/logout.php"><i class="fas fa-sign-out-alt"></i>Salir</a>
</nav>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

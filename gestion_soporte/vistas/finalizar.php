<?php
include('../sesion.php');
include('../parte1.php');
include('../../app/config/conexion.php');

$id_r_registrado = isset($_GET['id_r_registrado']) ? (int) $_GET['id_r_registrado'] : 0;

// Buscar datos del Reporte
$sqlReporte = "SELECT * FROM tb_reportes_registrador WHERE id_r_registrado = :id_r_registrado LIMIT 1";
$queryReporte = $pdo->prepare($sqlReporte);
$queryReporte->execute([':id_r_registrado' => $id_r_registrado]);
$reporte = $queryReporte->fetch(PDO::FETCH_ASSOC);

if (!$reporte) {
  echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Reporte no encontrado',
            text: 'El reporte con ID " . $id_r_registrado . " no existe o fue eliminado.',
            confirmButtonText: 'Volver'
        }).then(() => {
            window.location = 'lista_gestion.php';
        });
    </script>";
  exit;
}

?>


<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">Finalizar Reporte</h1>
        </div>
        <div class="col-sm-12 text-end">
          <span id="fechaHora" class="text-muted"></span>
        </div>
      </div>
    </div>
  </div>
  

  
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Columna del formulario -->
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Formulario de Finalización</h3>
              
            </div>

            <div class="card-body">
              <form method="POST" action="../controles/finalizar_reportes_controles.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id_r_registrado" value="<?= $reporte['id_r_registrado'] ?>">

                <div class="row">
                  <!-- Datos bloqueados -->
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Radicado</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['radicado']) ?>" readonly>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['empresa']) ?>" readonly>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Operador</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['operador']) ?>" readonly>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['nombre']) ?>" readonly>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['telefono']) ?>" readonly>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['direccion']) ?>" readonly>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Forma de contacto</label>
                    <select name="forma" class="form-control" disabled>
                      <option value="Correo" <?= (strtolower($reporte['forma'] ?? '') === 'correo') ? 'selected' : '' ?>>Correo</option>
                      <option value="Llamada" <?= (strtolower($reporte['forma'] ?? '') === 'llamada') ? 'selected' : '' ?>>Llamada</option>
                      <option value="Whatsapp" <?= (strtolower($reporte['forma'] ?? '') === 'whatsapp') ? 'selected' : '' ?>>WhatsApp</option>
                    </select>
                    <!-- Campo oculto para enviar el valor al servidor -->
                    <input type="hidden" name="forma" value="<?= htmlspecialchars($reporte['forma']) ?>">
                  </div>


                  <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Reporte</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['fecha']) ?>" readonly>
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Hora Reporte</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['hora']) ?>" readonly>
                  </div>

                  <!-- Campos de finalización -->
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Finalizado</label>
                    <input type="date" name="fecha_finalizado" class="form-control" value="<?= date('Y-m-d') ?>">
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Hora Finalizado</label>
                    <input type="time" name="hora_finalizado" class="form-control" value="<?= date('H:i') ?>">
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Personal Encargado</label>
                    <input type="text" name="personal_encargado" class="form-control" required>
                  </div>

                  <div class="col-md-12 mb-3">
                    <label class="form-label">Observaciones Finalización</label>
                    <textarea name="observaciones_final" class="form-control" rows="3" required></textarea>
                  </div>
                </div>

                <div class="text-end">
                  <button type="submit" class="btn btn-success">Finalizar Reporte</button>
                  <a href="lista_gestion.php" class="btn btn-secondary">Cancelar</a>
                </div>
              </form>
            </div> 
          </div> 
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../parte2.php'); ?>
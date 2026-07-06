<?php
include('../../sesion.php');
include('../../parte1.php');
include('../../app/config/conexion.php');

$id_cliente = $_GET['id_cliente'] ?? 0;

// Buscar datos del cliente
$sqlCliente = "SELECT * FROM tb_clientes WHERE id_cliente = :id_cliente";
$queryCliente = $pdo->prepare($sqlCliente);
$queryCliente->execute(['id_cliente' => $id_cliente]);
$cliente = $queryCliente->fetch(PDO::FETCH_ASSOC);

// Buscar IPs
$sqlIps = "SELECT * FROM tb_ips WHERE id_cliente = :id_cliente";
$queryIps = $pdo->prepare($sqlIps);
$queryIps->execute(['id_cliente' => $id_cliente]);
$ips = $queryIps->fetchAll(PDO::FETCH_ASSOC);

// Buscar Red
$sqlRed = "SELECT * FROM tb_red WHERE id_cliente = :id_cliente";
$queryRed = $pdo->prepare($sqlRed);
$queryRed->execute(['id_cliente' => $id_cliente]);
$redes = $queryRed->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">Editar Cliente</h1>
        </div>
        <div class="col-sm-12 text-end">
          <span class="text-muted">
            <span id="fechaHora" class="text-muted"></span>
          </span>
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
              <h3 class="card-title">Formulario de Edición</h3>
              
            </div>

            <div class="card-body">
              <form method="POST" action="../controles/editar_clientes_controles.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">

                <!-- Datos principales -->
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($cliente['nombre']) ?>">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Documento</label>
                    <input type="text" name="documento" class="form-control"
                           value="<?= htmlspecialchars($cliente['documento']) ?>">
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="<?= htmlspecialchars($cliente['telefono']) ?>">
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control"
                           value="<?= htmlspecialchars($cliente['direccion']) ?>">
                  </div>

                  <div class="col-md-4 mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" name="email" class="form-control"
                           value="<?= htmlspecialchars($cliente['email']) ?>">
                  </div>
                </div>

                <hr>

                <!-- IPs -->
                <h5>IPs Contratadas</h5>
                <div class="table-responsive mb-3">
                  <table class="table table-sm table-bordered text-center">
                    <thead class="table-light">
                      <tr>
                        <th>IP</th>
                        <th>Megas</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($ips as $ip): ?>
                      <tr>
                        <td>
                          <input type="text" name="ips[<?= hescape($ip['id_ip']) ?>][ip_principal]"
                                 class="form-control form-control-sm"
                                 value="<?= hescape($ip['ip_principal']) ?>">
                        </td>
                        <td>
                          <input type="text" name="ips[<?= hescape($ip['id_ip']) ?>][megas]"
                                 class="form-control form-control-sm"
                                 value="<?= hescape($ip['megas_contratadas']) ?>">
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <hr>

                <!-- Red -->
                <h5>Red Asociada</h5>
                <div class="table-responsive mb-3">
                  <table class="table table-sm table-bordered text-center">
                    <thead class="table-light">
                      <tr>
                        <th>Switch</th>
                        <th>IP</th>
                        <th>Puerto</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($redes as $red): ?>
                      <tr>
                        <td>
                          <input type="text" name="red[<?= hescape($red['id_red']) ?>][switch]"
                                 class="form-control form-control-sm"
                                 value="<?= hescape($red['switch']) ?>">
                        </td>
                        <td>
                          <input type="text" name="red[<?= hescape($red['id_red']) ?>][ip]"
                                 class="form-control form-control-sm"
                                 value="<?= hescape($red['ip']) ?>">
                        </td>
                        <td>
                          <input type="text" name="red[<?= hescape($red['id_red']) ?>][puerto]"
                                 class="form-control form-control-sm"
                                 value="<?= hescape($red['puerto']) ?>">
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="text-end">
                  <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
              </form>
            </div> 
          </div> 
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('../../parte2.php'); ?>

<?php
include('../../sesion.php');
verificar_acceso([1, 2]);
include('../../parte1.php');
?>


<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">Registro De Clientes</h1>
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
              <h3 class="card-title">Registro De Clientes</h3>
              
            </div>

            <div class="card-body">
              <form action="../controles/crear_clientes_controles.php" method="POST">
                <?= csrf_field() ?>
                <div class="row">

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Nombres</label>
                      <input type="text" class="form-control" name="nombre" placeholder="Ingresar El Nombre Del Cliente" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Documento / Nit</label>
                      <input type="text" class="form-control" name="documento" placeholder="Ingresar El Documento o Nit Del Cliente" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Teléfono</label>
                      <input type="text" class="form-control" name="telefono" placeholder="Ingresar El Teléfono Del Cliente" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Dirección</label>
                      <input type="text" class="form-control" name="direccion" placeholder="Ingresar La Dirección Del Cliente" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Correo Electrónico</label>
                      <input type="email" class="form-control" name="email" placeholder="Ingresar El Correo Electrónico Del Cliente" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Estado del servicio</label>
                      <select class="form-control" name="estado_servicio">
                        <option value="Activo">Activo</option>
                        <option value="Suspendido">Suspendido</option>
                        <option value="Cortado">Cortado</option>
                      </select>
                    </div>
                  </div>

                </div>
                <hr>
                <button type="submit" class="btn btn-primary">Registrar</button>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  
</div>


<?php include('../../parte2.php'); ?>

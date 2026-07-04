<?php
include('../sesion.php');
include('../parte1.php');

?>


<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-12">
          <h1 class="m-0">Registro De Usuarios</h1>
        </div>
        <div class="col-sm-12 text-end">
          <span class="text-muted">
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
              <h3 class="card-title">Registro De Usuarios</h3>
            </div>

            <div class="card-body">
              <form action="crear_usuarios_controles.php" method="POST">
                <?= csrf_field() ?>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Nombres</label>
                      <input type="text" class="form-control" name="nombre" placeholder="Ingresar El Nombre Del Usuario" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Documento</label>
                      <input type="text" class="form-control" name="documento" placeholder="Ingresar El Documento Del Usuario" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Teléfono</label>
                      <input type="text" class="form-control" name="telefono" placeholder="Ingresar El Teléfono Del Usuario" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Correo Electrónico</label>
                      <input type="email" class="form-control" name="email" placeholder="Ingresar El Correo" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Rol</label>
                      <select class="form-control" name="id_rol" required>
                        <option value="">Seleccione un rol</option>
                        <?php
                        include('../app/config/conexion.php');
                        $sql_rol = "SELECT * FROM tb_rol";
                        $query_rol = $pdo->prepare($sql_rol);
                        $query_rol->execute();
                        $roles = $query_rol->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($roles as $rol) { ?>
                          <option value="<?= $rol['id_rol']; ?>"><?= htmlspecialchars($rol['nombre_rol']); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Contraseña</label>
                      <input type="password" class="form-control" name="password" placeholder="Ingresar La Contraseña" autocomplete="new-password" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Confirmar Contraseña</label>
                      <input type="password" class="form-control" name="confirmar" placeholder="Confirmar Contraseña" autocomplete="new-password" required>
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



<?php include('../parte2.php'); ?>
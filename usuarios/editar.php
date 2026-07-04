<?php
include('../sesion.php');
include('../parte1.php');
include('ver_usuarios_controles.php');
?>


<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Editar Datos De Los Usuarios</h1>
        </div>
        <div class="col-sm-6 text-end">
          <span id="fechaHora" class="text-muted"></span>
        </div>
      </div>
    </div>
  </div>

  
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Editar Datos Del Usuario</h3>
            </div>

            <div class="card-body">
              <form action="actualizar_usuarios_controles.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario_get); ?>">

                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Nombres</label>
                      <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($nombre) ?>" placeholder="Ingresar El Nombre Del Usuario" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Documento</label>
                      <input type="text" class="form-control" name="documento" value="<?= htmlspecialchars($documento) ?>" placeholder="Ingresar El Documento Del Usuario" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Teléfono</label>
                      <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($telefono) ?>" placeholder="Ingresar El Teléfono Del Usuario" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Correo Electrónico</label>
                      <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Ingresar El Correo" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="rol">Rol de Usuario</label>
                      <select name="id_rol" id="nombre_rol" class="form-control" required>
                        <option value="">-- Seleccione Rol --</option>
                        <?php foreach ($roles_datos as $rol_item): ?>
                          <option value="<?= $rol_item['id_rol'] ?>" <?= ($rol_item['id_rol'] == $id_rol) ? 'selected' : '' ?>>
                            <?= $rol_item['nombre_rol'] ?>
                          </option>
                        <?php endforeach; ?>
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
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Editar</button>
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['mensaje'])): ?>
  <script>
    <?php if ($_GET['mensaje'] == 'editado'): ?>
      Swal.fire({
        icon: 'success',
        title: 'Usuario editado correctamente',
        showConfirmButton: false,
        timer: 2000
      });
    <?php elseif ($_GET['mensaje'] == 'error'): ?>
      Swal.fire({
        icon: 'error',
        title: 'Error al editar el usuario',
        text: 'Inténtalo de nuevo.',
        showConfirmButton: true
      });
    <?php elseif ($_GET['mensaje'] == 'error_password'): ?>
      Swal.fire({
        icon: 'warning',
        title: 'Las contraseñas no coinciden',
        text: 'Verifica los campos e intenta otra vez.',
        showConfirmButton: true
      });
    <?php endif; ?>
  </script>
<?php endif; ?>
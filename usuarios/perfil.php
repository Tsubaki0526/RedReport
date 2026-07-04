<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT u.*, r.nombre_rol FROM tb_usuarios u INNER JOIN tb_rol r ON u.id_rol = r.id_rol WHERE u.id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$user = $stmt->fetch();
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Mi Perfil</h1>
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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Datos personales</h3>
                        </div>
                        <div class="card-body">
                            <form action="actualizar_perfil.php" method="POST">
                                <?php require_once('../app/config/seguridad.php'); echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Documento</label>
                                    <input type="text" name="documento" class="form-control" value="<?= htmlspecialchars($user['documento']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($user['telefono']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rol</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['nombre_rol']) ?>" readonly>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Cambiar contraseña</h3>
                        </div>
                        <div class="card-body">
                            <form action="cambiar_password.php" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label">Contraseña actual</label>
                                    <input type="password" name="password_actual" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nueva contraseña</label>
                                    <input type="password" name="password_nueva" class="form-control" minlength="6" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirmar nueva contraseña</label>
                                    <input type="password" name="password_confirmar" class="form-control" minlength="6" required>
                                </div>
                                <button type="submit" class="btn btn-warning">Cambiar contraseña</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>

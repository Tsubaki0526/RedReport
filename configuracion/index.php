<?php
include('../sesion.php');
include('../parte1.php');
if ($_SESSION['id_rol'] != 1) {
    echo "<script>alert('Acceso denegado'); window.location='../index.php';</script>";
    exit;
}
$envFile = __DIR__ . '/../.env';
$envContent = file_exists($envFile) ? file_get_contents($envFile) : '';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Configuración</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Configuración del sistema</h3></div>
                <div class="card-body">
                    <form action="guardar.php" method="POST">
                        <?php require_once('../app/config/seguridad.php'); echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Base de datos</h5>
                                <div class="mb-3">
                                    <label class="form-label">DB Host</label>
                                    <input type="text" name="DB_HOST" class="form-control" value="<?= htmlspecialchars($_ENV['DB_HOST'] ?? DB_HOST) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">DB Name</label>
                                    <input type="text" name="DB_NAME" class="form-control" value="<?= htmlspecialchars($_ENV['DB_NAME'] ?? DB_NAME) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">DB User</label>
                                    <input type="text" name="DB_USER" class="form-control" value="<?= htmlspecialchars($_ENV['DB_USER'] ?? DB_USER) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">DB Password</label>
                                    <input type="password" name="DB_PASS" class="form-control" value="<?= htmlspecialchars($_ENV['DB_PASS'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Correo SMTP</h5>
                                <div class="mb-3">
                                    <label class="form-label">SMTP Host</label>
                                    <input type="text" name="SMTP_HOST" class="form-control" value="<?= htmlspecialchars($_ENV['SMTP_HOST'] ?? SMTP_HOST) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">SMTP Port</label>
                                    <input type="number" name="SMTP_PORT" class="form-control" value="<?= htmlspecialchars($_ENV['SMTP_PORT'] ?? SMTP_PORT) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">SMTP User</label>
                                    <input type="text" name="SMTP_USER" class="form-control" value="<?= htmlspecialchars($_ENV['SMTP_USER'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">SMTP Password</label>
                                    <input type="password" name="SMTP_PASS" class="form-control" value="<?= htmlspecialchars($_ENV['SMTP_PASS'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">APP URL</label>
                                    <input type="text" name="APP_URL" class="form-control" value="<?= htmlspecialchars($_ENV['APP_URL'] ?? APP_URL) ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-check-label">
                                        <input type="checkbox" name="APP_DEBUG" value="true" <?= (isset($_ENV['APP_DEBUG']) ? $_ENV['APP_DEBUG'] : 'true') === 'true' ? 'checked' : '' ?>> Modo debug
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">Guardar configuración</button>
                    </form>
                </div>
            </div>

            <!-- SMTP Test Card -->
            <div class="card mt-4">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-envelope me-2 text-primary"></i>Prueba de Email</h3></div>
                <div class="card-body">
                    <form id="formPruebaEmail">
                        <?= csrf_field() ?>
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Correo destinatario</label>
                                <input type="email" name="destinatario" id="destinatarioEmail" class="form-control" value="<?= hescape($_SESSION['email'] ?? '') ?>" placeholder="correo@ejemplo.com">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100" id="btnProbarEmail"><i class="fas fa-paper-plane me-1"></i>Enviar prueba</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<script>
$(document).ready(function() {
    $('#formPruebaEmail').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnProbarEmail').prop('disabled', true);
        btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Enviando...');
        $.ajax({
            url: 'controles/probar_email.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json'
        }).done(function(res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Enviado', text: res.message });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message });
            }
        }).fail(function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error de conexión con el servidor' });
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Enviar prueba');
        });
    });
});
</script>

<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
require_once('../app/config/2fa.php');

$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT u.*, r.nombre_rol FROM tb_usuarios u INNER JOIN tb_rol r ON u.id_rol = r.id_rol WHERE u.id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$user = $stmt->fetch();

$totp = new TOTP();
$hasSecret = !empty($user['google2fa_secret']);

if (!$hasSecret) {
    $newSecret = $totp->generateSecret();
    $qrUrl = $totp->getGoogleQRCodeUrl($newSecret, $user['email']);
    $otpauthUrl = $totp->getQRCodeUrl($newSecret, $user['email']);
}
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Autenticaci&oacute;n en dos pasos (2FA)</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <span id="fechaHora" class="text-muted"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php if ($hasSecret): ?>
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-shield-alt me-2"></i>2FA activado</h3>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle text-success" style="font-size:4rem;"></i>
                            <p class="mt-3 fs-5">La autenticaci&oacute;n en dos pasos est&aacute; activa en tu cuenta.</p>
                            <p class="text-muted">Al iniciar sesi&oacute;n se te solicitar&aacute; un c&oacute;digo adicional desde tu aplicación Google Authenticator.</p>
                            <hr>
                            <button type="button" class="btn btn-danger" id="btnDesactivar2FA">
                                <i class="fas fa-unlock me-1"></i> Desactivar 2FA
                            </button>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-qrcode me-2"></i>Configurar 2FA</h3>
                        </div>
                        <div class="card-body">
                            <ol class="mb-4">
                                <li>Instala <strong>Google Authenticator</strong> en tu tel&eacute;fono.</li>
                                <li>Abre la app y selecciona <strong>"A&ntilde;adir c&oacute;digo"</strong> &rarr; <strong>"Escanear c&oacute;digo QR"</strong>.</li>
                                <li>Escanea el c&oacute;digo QR de abajo o ingresa la clave manualmente.</li>
                                <li>Ingresa el c&oacute;digo de 6 d&iacute;gitos generado y haz clic en <strong>"Verificar y activar"</strong>.</li>
                            </ol>

                            <div class="text-center mb-3">
                                <img src="<?= hescape($qrUrl) ?>" alt="QR Code" class="img-fluid border p-2 rounded" style="max-width:220px;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Clave secreta (ingresa manualmente si no puedes escanear):</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-center font-monospace" id="secretKey" value="<?= hescape($newSecret) ?>" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="copySecret"><i class="fas fa-copy"></i></button>
                                </div>
                            </div>

                            <hr>

                            <form id="frmVerificar2FA">
                                <?= csrf_field() ?>
                                <input type="hidden" name="secret" value="<?= hescape($newSecret) ?>">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">C&oacute;digo de verificaci&oacute;n:</label>
                                    <input type="text" name="code" class="form-control text-center font-monospace fs-4" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="off" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="btnVerificar">
                                    <i class="fas fa-check me-1"></i> Verificar y activar
                                </button>
                            </form>
                            <div id="msg2fa" class="mt-3"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.getElementById('copySecret');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const input = document.getElementById('secretKey');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                Swal.fire({ icon: 'success', title: 'Copiado', text: 'Clave secreta copiada al portapapeles', timer: 1500, showConfirmButton: false, heightAuto: false });
            }).catch(() => {
                document.execCommand('copy');
                Swal.fire({ icon: 'success', title: 'Copiado', timer: 1500, showConfirmButton: false, heightAuto: false });
            });
        });
    }

    const frm = document.getElementById('frmVerificar2FA');
    if (frm) {
        frm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnVerificar');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verificando...';
            const msgDiv = document.getElementById('msg2fa');
            msgDiv.innerHTML = '';

            const formData = new FormData(frm);
            fetch('controles/2fa_guardar.php', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: '2FA activado', text: 'A partir de ahora se te solicitar&aacute; un c&oacute;digo al iniciar sesi&oacute;n.', heightAuto: false }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        msgDiv.innerHTML = '<div class="alert alert-danger mb-0">' + data.message + '</div>';
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check me-1"></i> Verificar y activar';
                    }
                })
                .catch(() => {
                    msgDiv.innerHTML = '<div class="alert alert-danger mb-0">Error de conexi&oacute;n. Intenta de nuevo.</div>';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check me-1"></i> Verificar y activar';
                });
        });
    }

    const btnDesactivar = document.getElementById('btnDesactivar2FA');
    if (btnDesactivar) {
        btnDesactivar.addEventListener('click', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Desactivar 2FA',
                text: 'Se eliminar&aacute; la autenticaci&oacute;n en dos pasos de tu cuenta. &iquest;Continuar?',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'S&iacute;, desactivar',
                cancelButtonText: 'Cancelar',
                heightAuto: false
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    <?php
                    $token = csrf_token();
                    ?>
                    formData.append('_csrf_token', '<?= $token ?>');
                    fetch('controles/2fa_eliminar.php', { method: 'POST', body: formData })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({ icon: 'success', title: '2FA desactivado', heightAuto: false }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: data.message, heightAuto: false });
                            }
                        });
                }
            });
        });
    }
});
</script>
<?php include('../parte2.php'); ?>

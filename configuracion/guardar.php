<?php
session_start();
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}
if ($_SESSION['id_rol'] != 1) {
    die("Acceso denegado");
}

$keys = ['DB_HOST','DB_NAME','DB_USER','DB_PASS','SMTP_HOST','SMTP_PORT','SMTP_USER','SMTP_PASS','APP_URL','APP_NAME','APP_ENV','APP_DEBUG'];
$lines = [];
foreach ($keys as $k) {
    $v = $_POST[$k] ?? '';
    if ($k === 'APP_DEBUG') {
        $v = isset($_POST['APP_DEBUG']) ? 'true' : 'false';
    }
    $lines[] = "$k=$v";
}
$content = implode("\n", $lines) . "\n";
$envFile = __DIR__ . '/../.env';
file_put_contents($envFile, $content);

bitacora($pdo, $_SESSION['id_usuario'], 'ACTUALIZAR', 'configuracion', null, "Configuración del sistema actualizada");

echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
    Swal.fire({icon:'success',title:'Configuración guardada',text:'Reinicia la aplicación para aplicar cambios.',confirmButtonText:'OK'})
    .then(() => window.location='index.php');
</script>";
?>

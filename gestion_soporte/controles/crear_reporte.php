<?php
include('../../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

// ================= GENERAR RADICADO =================
$fecha = date("dmY"); // Ej: 10092025

// Buscar el último radicado registrado (sin importar fecha)
$sql = "SELECT radicado FROM tb_reportes_registrador 
        ORDER BY radicado DESC LIMIT 1";
$query = $pdo->prepare($sql);
$query->execute();
$ultimo = $query->fetch(PDO::FETCH_ASSOC);

if ($ultimo) {
    // Extraer consecutivo del último radicado (últimos 4 dígitos)
    $consecutivo = intval(substr($ultimo['radicado'], -4)) + 1;
} else {
    // Si no hay registros, empieza desde 0001
    $consecutivo = 1;
}

// Formatear a 4 dígitos
$consecutivo = str_pad($consecutivo, 4, "0", STR_PAD_LEFT);

// Generar radicado con fecha actual + consecutivo global
$radicado = $fecha . $consecutivo;


// ===================================================

$empresa       = $_POST['empresa'] ?? '';
$operador      = $_POST['operador'] ?? '';
$nombre        = $_POST['nombre'] ?? '';
$telefono      = $_POST['telefono'] ?? '';
$direccion     = $_POST['direccion'] ?? '';
$forma         = $_POST['forma'] ?? '';
$fechaForm     = $_POST['fecha'] ?? '';
$hora          = $_POST['hora'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$estado        = "pendiente";

try {
    $sql = "INSERT INTO tb_reportes_registrador 
            (radicado, empresa, operador, nombre, telefono, direccion, fecha, hora, observaciones, estado, forma)
        VALUES 
            (:radicado, :empresa, :operador, :nombre, :telefono, :direccion, :fecha, :hora, :observaciones, :estado, :forma)";


    $query = $pdo->prepare($sql);
    $query->bindValue(':radicado', $radicado);
    $query->bindValue(':empresa', $empresa);
    $query->bindValue(':operador', $operador);
    $query->bindValue(':nombre', $nombre);
    $query->bindValue(':telefono', $telefono);
    $query->bindValue(':direccion', $direccion);
    $query->bindValue(':forma', $forma);
    $query->bindValue(':fecha', $fechaForm);
    $query->bindValue(':hora', $hora);
    $query->bindValue(':observaciones', $observaciones);
    $query->bindValue(':estado', $estado);
    $ejecutado = $query->execute();
    if ($ejecutado) {
        $nuevo_id = $pdo->lastInsertId();
        bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_reportes_registrador', $nuevo_id, "Reporte registrado, Radicado: $radicado");
    }
} catch (PDOException $e) {
    error_log("Error crear reporte: " . $e->getMessage());
    $ejecutado = false;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        <?php if ($ejecutado): ?>
            Swal.fire({
                icon: 'success',
                title: 'Reporte registrado',
                text: '<?= json_encode("El reporte fue registrado correctamente con radicado " . $radicado) ?>',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location = '../vistas/lista_gestion.php';
            });
        <?php else: ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= json_encode("No se pudo registrar el reporte") ?>',
                confirmButtonText: 'Intentar de nuevo'
            }).then(() => {
                window.location = '../vistas/registrar_reporte.php';
            });
        <?php endif; ?>
    </script>
</body>

</html>
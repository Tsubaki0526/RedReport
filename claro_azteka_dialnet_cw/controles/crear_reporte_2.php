<?php
include('../../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

// ================== CLIENTE ==================
$cliente = $_POST['cliente'] ?? '';

switch ($cliente) {
    case 'claro':   $tabla = 'tb_claro'; break;
    case 'azteca':  $tabla = 'tb_azteka'; break;
    case 'dialnet': $tabla = 'tb_dialnet'; break;
    case 'liberty': $tabla = 'tb_liberty'; break;
    default: die("Cliente no válido");
}

// ================= GENERAR RADICADO =================
$fecha = date("dmY"); // Ej: 10092025

// Buscar el último radicado registrado (sin importar fecha)
$sql = "SELECT radicado FROM $tabla
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

// ================== DATOS DEL FORM ==================
$operador      = $_POST['operador'] ?? '';
$cliente       = $_POST['cliente'] ?? '';
$telefono      = $_POST['telefono'] ?? '';
$ciudad       = $_POST['ciudad'] ?? '';
$fechaForm     = $_POST['fecha'] ?? '';
$hora          = $_POST['hora'] ?? '';
$dano_reportado = $_POST['dano_reportado'] ?? '';
$estado        = "pendiente";

try {
    $sql = "INSERT INTO $tabla 
            (radicado, operador, cliente, telefono, ciudad, fecha, hora, dano_reportado, estado)
        VALUES 
            (:radicado, :operador, :cliente, :telefono, :ciudad, :fecha, :hora, :dano_reportado, :estado)";

    $query = $pdo->prepare($sql);
    $query->bindValue(':radicado', $radicado);
    $query->bindValue(':operador', $operador);
    $query->bindValue(':cliente', $cliente);
    $query->bindValue(':telefono', $telefono);
    $query->bindValue(':ciudad', $ciudad);
    $query->bindValue(':fecha', $fechaForm);
    $query->bindValue(':hora', $hora);
    $query->bindValue(':dano_reportado', $dano_reportado);
    $query->bindValue(':estado', $estado);
    $ejecutado = $query->execute();
    if ($ejecutado) {
        $nuevo_id = $pdo->lastInsertId();
        bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', $tabla, $nuevo_id, "Reporte registrado, Cliente: $cliente, Radicado: $radicado");
    }
} catch (PDOException $e) {
    error_log("Error crear reporte claro: " . $e->getMessage());
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
                window.location = '../../index.php';
            });
        <?php else: ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= json_encode("No se pudo registrar el reporte") ?>',
                confirmButtonText: 'Intentar de nuevo'
            }).then(() => {
                window.location = '../vistas/registrar_daño_2.php';
            });
        <?php endif; ?>
    </script>
</body>
</html>

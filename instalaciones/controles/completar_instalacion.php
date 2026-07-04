<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_cliente = (int)($_POST['id_cliente'] ?? 0);

// Permission check: only admin or assigned installer
$check = $pdo->prepare("SELECT id_instalador FROM tb_clientes WHERE id_cliente = :id");
$check->execute([':id' => $id_cliente]);
$cliente = $check->fetch();
$es_admin = $_SESSION['id_rol'] == 1;
$es_asignado = $cliente && $cliente['id_instalador'] == $_SESSION['id_usuario'];
if (!$es_admin && !$es_asignado) die("Acceso denegado");
$lat = $_POST['lat'] ?? null;
$lng = $_POST['lng'] ?? null;
$observaciones = $_POST['observaciones'] ?? '';

try {
    $pdo->beginTransaction();

    // Update client with installation data
    $sql = "UPDATE tb_clientes SET fecha_instalacion = NOW(), lat = COALESCE(:lat, lat), lng = COALESCE(:lng, lng) WHERE id_cliente = :id";
    $pdo->prepare($sql)->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id_cliente]);

    // Register equipment
    $tipos = $_POST['id_tipo_equipo'] ?? [];
    $serials = $_POST['serial'] ?? [];
    $marcas = $_POST['marca'] ?? [];

    $sqlEq = "INSERT INTO tb_equipos (id_tipo_equipo, serial, marca, estado, id_cliente, fecha_asignado) VALUES (:tipo, :serial, :marca, 'Asignado', :cliente, NOW())";
    $stmtEq = $pdo->prepare($sqlEq);

    for ($i = 0; $i < count($tipos); $i++) {
        if (!empty($tipos[$i]) && !empty($serials[$i])) {
            $stmtEq->execute([
                ':tipo' => $tipos[$i],
                ':serial' => $serials[$i],
                ':marca' => $marcas[$i] ?? '',
                ':cliente' => $id_cliente
            ]);
        }
    }

    $pdo->commit();
    bitacora($pdo, $_SESSION['id_usuario'], 'COMPLETAR_INSTALACION', 'tb_clientes', $id_cliente, "Instalación completada para cliente ID: $id_cliente");

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Instalación completada',text:'Equipos registrados correctamente'}).then(()=>window.location='index.php');</script>";
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("completar_instalacion error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrió un error al procesar la instalación.'}).then(()=>window.location='realizar.php?id=$id_cliente');</script>";
}
?>

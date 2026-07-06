<?php
session_start();
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if (!isset($_SESSION['movil_user']) || $_SESSION['movil_user']['tipo'] !== 'empleado') {
    header('Location: ../login.php'); exit;
}

if (!csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_tecnico = $_SESSION['movil_user']['id'];
$id_cliente = intval($_POST['id_cliente'] ?? 0);
$lat = $_POST['lat'] ?? '';
$lng = $_POST['lng'] ?? '';
$notas = trim($_POST['notas'] ?? '');
$id_tipo_equipo = intval($_POST['id_tipo_equipo'] ?? 0);
$serial = trim($_POST['serial'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');

if (!$id_cliente || !$serial) {
    echo "<script>alert('Faltan datos requeridos');history.back();</script>";
    exit;
}

try {
    $pdo->beginTransaction();

    // Update client location and installation date
    $upd = $pdo->prepare("UPDATE tb_clientes SET lat=?, lng=?, fecha_instalacion=NOW(), estado_servicio='Activo', id_instalador=? WHERE id_cliente=?");
    $upd->execute([$lat ?: null, $lng ?: null, $id_tecnico, $id_cliente]);

    // Create service order
    $num_orden = 'ORD-' . strtoupper(substr(uniqid(), -6));
    $ord = $pdo->prepare("INSERT INTO tb_ordenes (numero_orden, id_cliente, id_tecnico, tipo, descripcion, estado, fecha_asignacion, fecha_completada, solucion) VALUES (?,?,?,'Instalacion',?,'Completada',NOW(),NOW(),?)");
    $ord->execute([$num_orden, $id_cliente, $id_tecnico, $notas, $notas]);
    $id_orden = $pdo->lastInsertId();

    // Register equipment
    $eq = $pdo->prepare("INSERT INTO tb_equipos (id_tipo_equipo, serial, marca, modelo, estado, id_cliente, fecha_asignado) VALUES (?,?,?,?,'Asignado',?,NOW())");
    $eq->execute([$id_tipo_equipo, $serial, $marca, $modelo, $id_cliente]);
    $id_equipo = $pdo->lastInsertId();

    // Upload photos
    if (!empty($_FILES['fotos']['name'][0])) {
        $fotoDir = __DIR__ . '/../../public/uploads/instalaciones/';
        if (!is_dir($fotoDir)) mkdir($fotoDir, 0755, true);
        foreach ($_FILES['fotos']['tmp_name'] as $i => $tmp) {
            if ($_FILES['fotos']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext = pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION);
            $name = 'inst_' . $id_cliente . '_' . time() . '_' . $i . '.' . $ext;
            move_uploaded_file($tmp, $fotoDir . $name);
            $fp = $pdo->prepare("INSERT INTO tb_instalacion_fotos (id_cliente, nombre_archivo, descripcion) VALUES (?,?,?)");
            $fp->execute([$id_cliente, $name, 'Instalación móvil - ' . date('Y-m-d H:i')]);
        }
    }

    $pdo->commit();
    bitacora($pdo, $id_tecnico, 'Instalacion movil', 'tb_clientes', $id_cliente, "Instalación completada via móvil. Orden: $num_orden, Equipo: $serial");
    echo "<script>alert('Instalación completada exitosamente');window.location='index.php';</script>";

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error instalación móvil: " . $e->getMessage());
    echo "<script>alert('Error al guardar la instalación');history.back();</script>";
}

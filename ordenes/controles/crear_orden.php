<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_cliente = intval($_POST['id_cliente'] ?? 0);
$id_tecnico = intval($_POST['id_tecnico'] ?? 0) ?: null;
$tipo = $_POST['tipo'] ?? 'Soporte';
$prioridad = $_POST['prioridad'] ?? 'Media';
$descripcion = trim($_POST['descripcion'] ?? '');

if ($id_cliente <= 0 || empty($descripcion)) { header('Location: ../index.php'); exit; }

try {
    $num = $pdo->query("SELECT COUNT(*)+1 FROM tb_ordenes")->fetchColumn();
    $numero = 'ORD-' . str_pad($num, 5, '0', STR_PAD_LEFT);

    $stmt = $pdo->prepare("INSERT INTO tb_ordenes (numero_orden, id_cliente, id_tecnico, tipo, prioridad, descripcion, estado, fecha_asignacion) VALUES (?,?,?,?,?,?,'Abierta',?)");
    $stmt->execute([$numero, $id_cliente, $id_tecnico, $tipo, $prioridad, $descripcion, $id_tecnico ? date('Y-m-d H:i:s') : null]);
    $id_orden = $pdo->lastInsertId();

    bitacora($pdo, $id_usuario, 'CREAR_ORDEN', 'tb_ordenes', $id_orden, "Orden $numero creada para cliente #$id_cliente");
    header('Location: ../index.php');
} catch (Exception $e) {
    error_log("crear_orden error: " . $e->getMessage());
    header('Location: ../index.php');
}

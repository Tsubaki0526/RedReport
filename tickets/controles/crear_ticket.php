<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

verificar_acceso([1, 2]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_cliente = intval($_POST['id_cliente'] ?? 0);
$asunto = trim($_POST['asunto'] ?? '');
$categoria = $_POST['categoria'] ?? 'Otro';
$prioridad = $_POST['prioridad'] ?? 'Media';
$id_asignado = intval($_POST['id_usuario'] ?? 0) ?: null;
$descripcion = trim($_POST['descripcion'] ?? '');

if ($id_cliente <= 0 || empty($asunto) || empty($descripcion)) { header('Location: ../index.php'); exit; }

try {
    $num = $pdo->query("SELECT COUNT(*)+1 FROM tb_tickets")->fetchColumn();
    $numero = 'TCK-' . str_pad($num, 5, '0', STR_PAD_LEFT);

    $stmt = $pdo->prepare("INSERT INTO tb_tickets (numero_ticket, id_cliente, id_usuario, asunto, descripcion, categoria, prioridad, estado, fecha_asignacion) VALUES (?,?,?,?,?,?,?,'Abierto',?)");
    $stmt->execute([$numero, $id_cliente, $id_asignado, $asunto, $descripcion, $categoria, $prioridad, $id_asignado ? date('Y-m-d H:i:s') : null]);
    $id_ticket = $pdo->lastInsertId();

    bitacora($pdo, $id_usuario, 'CREAR_TICKET', 'tb_tickets', $id_ticket, "Ticket $numero creado para cliente #$id_cliente: $asunto");
    header('Location: ../index.php');
} catch (Exception $e) {
    error_log("crear_ticket error: " . $e->getMessage());
    header('Location: ../index.php');
}

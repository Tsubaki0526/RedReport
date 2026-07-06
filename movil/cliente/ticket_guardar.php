<?php
session_start();
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if (!isset($_SESSION['movil_user']) || $_SESSION['movil_user']['tipo'] !== 'cliente') {
    header('Location: ../login.php'); exit;
}

if (!csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_cliente = $_SESSION['movil_user']['id'];
$asunto = trim($_POST['asunto'] ?? '');
$categoria = $_POST['categoria'] ?? 'Otro';
$descripcion = trim($_POST['descripcion'] ?? '');

if (!$asunto || !$descripcion) {
    echo "<script>alert('Completa todos los campos');history.back();</script>";
    exit;
}

$num = 'TK-' . strtoupper(substr(uniqid(), -6));
$stmt = $pdo->prepare("INSERT INTO tb_tickets (numero_ticket, id_cliente, asunto, descripcion, categoria, estado, fecha_creacion) VALUES (?,?,?,?,?,'Abierto',NOW())");
$stmt->execute([$num, $id_cliente, $asunto, $descripcion, $categoria]);
$id_ticket = $pdo->lastInsertId();

bitacora($pdo, $id_cliente, 'Ticket creado', 'tb_tickets', $id_ticket, "Ticket $num creado via móvil: $asunto");

echo "<script>alert('Ticket #$num creado exitosamente. Te contactaremos pronto.');window.location='tickets.php';</script>";

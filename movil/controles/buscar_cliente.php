<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';

if (!isset($_SESSION['movil_user'])) { http_response_code(401); exit; }

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo '[]'; exit; }

$stmt = $pdo->prepare("SELECT id_cliente, nombre, documento, direccion, telefono, lat, lng FROM tb_clientes WHERE nombre LIKE ? OR documento LIKE ? LIMIT 15");
$like = "%$q%";
$stmt->execute([$like, $like]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

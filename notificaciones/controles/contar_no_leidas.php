<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tb_notificaciones WHERE (id_usuario = ? OR id_usuario IS NULL) AND leida = 0");
$stmt->execute([$id_usuario]);
echo json_encode(['count' => intval($stmt->fetchColumn())]);

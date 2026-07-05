<?php
session_start();
require_once '../../app/config/conexion.php';

$usuario = trim($_POST['usuario'] ?? '');
$password = trim($_POST['password'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM tb_clientes WHERE (email=? OR telefono=?) AND password IS NOT NULL");
$stmt->execute([$usuario, $usuario]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cliente && password_verify($password, $cliente['password'])) {
    $_SESSION['portal_cliente'] = $cliente;
    header('Location: ../dashboard.php');
} else {
    header('Location: ../index.php?error=Credenciales incorrectas');
}

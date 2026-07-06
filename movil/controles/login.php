<?php
session_start();
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';

$tipo    = $_POST['tipo'] ?? 'empleado';
$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($tipo === 'empleado') {
    $stmt = $pdo->prepare("SELECT * FROM tb_usuarios WHERE (nombre=? OR email=?)");
    $stmt->execute([$usuario, $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['movil_user'] = [
            'tipo'    => 'empleado',
            'id'      => $user['id_usuario'],
            'nombre'  => $user['nombre'],
            'usuario' => $user['usuario'],
            'id_rol'  => $user['id_rol']
        ];
        header('Location: ../index.php');
        exit;
    }
} else {
    $stmt = $pdo->prepare("SELECT * FROM tb_clientes WHERE (email=? OR telefono=?) AND password IS NOT NULL");
    $stmt->execute([$usuario, $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['movil_user'] = [
            'tipo'    => 'cliente',
            'id'      => $user['id_cliente'],
            'nombre'  => $user['nombre'],
            'email'   => $user['email'],
            'estado_servicio' => $user['estado_servicio']
        ];
        header('Location: ../index.php');
        exit;
    }
}

header('Location: ../login.php?error=Credenciales incorrectas');

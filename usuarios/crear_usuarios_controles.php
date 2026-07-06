<?php
include('../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../app/config/seguridad.php');
verificar_acceso([1]);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}
?><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']);
    $documento = trim($_POST['documento']);
    $telefono  = trim($_POST['telefono']);
    $email     = trim($_POST['email']);
    $password  = trim($_POST['password']);
    $confirmar = trim($_POST['confirmar']);
    $id_rol    = intval($_POST['id_rol']); // ← Nuevo campo capturado del formulario

    // Validar campos vacíos
    if (empty($nombre) || empty($documento) || empty($telefono) || empty($email) || empty($password) || empty($confirmar) || empty($id_rol)) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'warning',
                title: 'Campos vacíos',
                text: 'Todos los campos son obligatorios, incluyendo el rol',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        exit();
    }

    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'warning',
                title: 'Correo inválido',
                text: 'Por favor ingresa un correo electrónico válido',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        exit();
    }

    // Validar fortaleza de contrasena
    if (strlen($password) < 6) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'warning',
                title: 'Contrasena debil',
                text: 'La contrasena debe tener al menos 6 caracteres',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        exit();
    }

    // Validar coincidencia de contraseñas
    if ($password !== $confirmar) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Las contraseñas no coinciden',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        exit();
    }

    // Verificar si el correo ya está registrado
    $sql_check = "SELECT COUNT(*) FROM tb_usuarios WHERE email = :email";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([':email' => $email]);
    $existe = $stmt_check->fetchColumn();

    if ($existe > 0) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'warning',
                title: 'Correo duplicado',
                text: 'Este correo ya está registrado',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        exit();
    }

    // Encriptar contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en la base de datos
    try {
        $sql = "INSERT INTO tb_usuarios (nombre, documento, telefono, email, password, id_rol) 
                VALUES (:nombre, :documento, :telefono, :email, :password, :id_rol)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'  => $nombre,
            ':documento' => $documento,
            ':telefono'  => $telefono,
            ':email'    => $email,
            ':password' => $passwordHash,
            ':id_rol'   => $id_rol
        ]);

        $nuevo_id = $pdo->lastInsertId();
        bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_usuarios', $nuevo_id, "Usuario: $nombre, Email: $email");

        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'Registro exitoso',
                text: 'El usuario ha sido registrado correctamente',
            }).then(() => {
                window.location.href = 'lista.php';
            });
        }
        </script>";
    } catch (PDOException $e) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'error',
                title: 'Error al registrar',
                text: 'Ocurrió un problema al guardar el usuario',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        // Descomenta si quieres ver el error en desarrollo:
        // error_log($e->getMessage());
    }
}
?>

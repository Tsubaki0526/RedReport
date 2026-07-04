<?php
include('../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}
?><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = intval($_POST['id_usuario']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $documento = trim($_POST['documento']);
    $email = trim($_POST['email']);
    $id_rol = intval($_POST['id_rol']); // nuevo campo
    $password = trim($_POST['password']);
    $confirmar = trim($_POST['confirmar']);

    // Validar campos vacíos
    if (empty($nombre) || empty($email)) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'warning',
                title: 'Campos vacíos',
                text: 'Nombre y correo electrónico son obligatorios',
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

    // Validar contraseña si se está cambiando
    $actualizarPassword = false;
    if (!empty($password) || !empty($confirmar)) {
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
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $actualizarPassword = true;
    }

    try {
        if ($actualizarPassword) {
            $sql = "UPDATE tb_usuarios 
                    SET nombre = :nombre, telefono = :telefono, documento = :documento, email = :email, password = :password, id_rol = :id_rol 
                    WHERE id_usuario = :id_usuario";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre'   => $nombre,
                ':telefono' => $telefono,
                ':documento'=> $documento,
                ':email'     => $email,
                ':password'  => $passwordHash,
                ':id_rol'    => $id_rol,
                ':id_usuario'=> $id_usuario
            ]);
        } else {
            $sql = "UPDATE tb_usuarios 
                    SET nombre = :nombre, telefono = :telefono, documento = :documento, email = :email, id_rol = :id_rol 
                    WHERE id_usuario = :id_usuario";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre'   => $nombre,
                ':telefono' => $telefono,
                ':documento'=> $documento,
                ':email'     => $email,
                ':id_rol'    => $id_rol,
                ':id_usuario'=> $id_usuario
            ]);
        }

        bitacora($pdo, $_SESSION['id_usuario'], 'ACTUALIZAR', 'tb_usuarios', $id_usuario, "Usuario actualizado: $nombre");

        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'success',
                title: 'Actualización exitosa',
                text: 'El usuario ha sido actualizado correctamente',
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
                title: 'Error al actualizar',
                text: 'Ocurrió un problema al actualizar el usuario',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        // Para desarrollo puedes habilitar esto:
        // error_log($e->getMessage());
    }
}
?>

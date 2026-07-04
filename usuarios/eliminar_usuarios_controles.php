<?php
include('../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}
?><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script><?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = intval($_POST['id_usuario'] ?? 0);

    if ($id_usuario <= 0) {
        echo "<script>
            alert('ID de usuario inválido');
            window.history.back();
        </script>";
        exit();
    }

    try {
        $sql = "DELETE FROM tb_usuarios WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);

        if ($stmt->rowCount() > 0) {
            bitacora($pdo, $_SESSION['id_usuario'], 'ELIMINAR', 'tb_usuarios', $id_usuario, "Usuario eliminado ID: $id_usuario");

            echo "
            <script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Usuario eliminado',
                    text: 'El usuario fue eliminado correctamente',
                }).then(() => {
                    window.location.href = 'lista.php';
                });
            }
            </script>";
        } else {
            echo "
            <script>
            window.onload = function() {
                Swal.fire({
                    icon: 'info',
                    title: 'No se encontró el usuario',
                    text: 'Verifica el ID recibido: $id_usuario',
                }).then(() => {
                    window.history.back();
                });
            }
            </script>";
        }
    } catch (PDOException $e) {
        echo "
        <script>
        window.onload = function() {
            Swal.fire({
                icon: 'error',
                title: 'Error al eliminar',
                text: 'Ocurrió un problema técnico',
            }).then(() => {
                window.history.back();
            });
        }
        </script>";
        // error_log($e->getMessage());
    }
}
?>

<?php
include('../../app/config/conexion.php');
session_start();
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}
    $nombre     = $_POST['nombre'] ?? '';
    $documento  = $_POST['documento'] ?? '';
    $telefono   = $_POST['telefono'] ?? '';
    $direccion  = $_POST['direccion'] ?? '';
    $email      = $_POST['email'] ?? '';
    $estado_servicio = $_POST['estado_servicio'] ?? 'Activo';

    if (!empty($nombre) && !empty($telefono) && !empty($direccion)) {
        try {
            $sql = "INSERT INTO tb_clientes (nombre, documento, telefono, direccion, email, estado_servicio) 
                    VALUES (:nombre, :documento, :telefono, :direccion, :email, :estado)";
            $query = $pdo->prepare($sql);
            $query->bindParam(':nombre', $nombre);
            $query->bindParam(':documento', $documento);
            $query->bindParam(':telefono', $telefono);
            $query->bindParam(':direccion', $direccion);
            $query->bindParam(':email', $email);
            $query->bindParam(':estado', $estado_servicio);
            $query->execute();
            $nuevo_id = $pdo->lastInsertId();
            bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_clientes', $nuevo_id, "Cliente: $nombre");

            // Estructura HTML + SweetAlert2
            echo "
            <!DOCTYPE html>
            <html lang='es'>
            <head>
              <meta charset='UTF-8'>
              <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
              <script>
                Swal.fire({
                  icon: 'success',
                  title: 'Cliente registrado',
                  text: '✅ El cliente fue registrado correctamente',
                  confirmButtonText: 'Aceptar'
                }).then(() => {
                  window.location.href = '../vistas/lista.php';
                });
              </script>
            </body>
            </html>";
        } catch (PDOException $e) {
            echo "
            <!DOCTYPE html>
            <html lang='es'>
            <head>
              <meta charset='UTF-8'>
              <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
              <script>
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: '❌ Error al registrar el cliente',
                  confirmButtonText: 'Cerrar'
                }).then(() => {
                  window.history.back();
                });
              </script>
            </body>
            </html>";
        }
    } else {
        echo "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
          <meta charset='UTF-8'>
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
          <script>
            Swal.fire({
              icon: 'warning',
              title: 'Campos incompletos',
              text: '❌ Debes completar todos los campos obligatorios',
              confirmButtonText: 'Volver'
            }).then(() => {
              window.history.back();
            });
          </script>
        </body>
        </html>";
    }
?>

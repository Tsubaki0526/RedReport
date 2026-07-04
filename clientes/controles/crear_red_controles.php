<?php
include('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!csrf_verify($_POST['_csrf_token'] ?? '')) { csrf_die(); }
    $id_cliente = $_POST['id_cliente'] ?? 0;
    $switch = $_POST['switch'] ?? '';
    $ip = $_POST['ip'] ?? '';
    $puerto = $_POST['puerto'] ?? '';

    if (!empty($id_cliente)  && !empty($ip) && !empty($switch) && !empty($puerto)) {
        try {
            $sql = "INSERT INTO tb_red (id_cliente, switch, ip, puerto) 
                    VALUES (:id_cliente, :switch, :ip, :puerto)";
            $query = $pdo->prepare($sql);
            $query->bindParam(':id_cliente', $id_cliente);
            $query->bindParam(':switch', $switch);
            $query->bindParam(':ip', $ip);
            $query->bindParam(':puerto', $puerto);
            $query->execute();

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
                  title: 'RED registrada',
                  text: '✅ La RED fue registrada correctamente',
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
                  text: '❌ Error al registrar la RED',
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
} else {
    header('Location: ../vistas/lista.php');
    exit();
}
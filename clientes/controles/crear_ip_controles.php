<?php
include('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
verificar_acceso([1, 2]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!csrf_verify($_POST['_csrf_token'] ?? '')) { csrf_die(); }
    $id_cliente = $_POST['id_cliente'] ?? 0;
    $ip_principal = $_POST['ip_principal'] ?? '';
    $megas = $_POST['megas_contratadas'] ?? '';

    if (!empty($id_cliente) && !empty($ip_principal) && !empty($megas)) {
        try {
            $sql = "INSERT INTO tb_ips (id_cliente, ip_principal, megas_contratadas) 
                    VALUES (:id_cliente, :ip_principal, :megas)";
            $query = $pdo->prepare($sql);
            $query->bindParam(':id_cliente', $id_cliente);
            $query->bindParam(':ip_principal', $ip_principal);
            $query->bindParam(':megas', $megas);
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
                  title: 'IP registrada',
                  text: '✅ La IP fue registrada correctamente',
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
} else {
    header('Location: ../vistas/lista.php');
    exit();
}
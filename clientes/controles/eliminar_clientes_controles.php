<?php
include('../../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../app/config/seguridad.php');
verificar_acceso([1, 2]);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_cliente'])) {
    $id_cliente = intval($_POST['id_cliente']);

    try {
        $pdo->beginTransaction();

        // 1. Eliminar las redes asociadas
        $sqlRed = "DELETE FROM tb_red WHERE id_cliente = :id_cliente";
        $stmtRed = $pdo->prepare($sqlRed);
        $stmtRed->execute(['id_cliente' => $id_cliente]);

        // 2. Eliminar las IPs asociadas
        $sqlIp = "DELETE FROM tb_ips WHERE id_cliente = :id_cliente";
        $stmtIp = $pdo->prepare($sqlIp);
        $stmtIp->execute(['id_cliente' => $id_cliente]);

        // 3. Eliminar el cliente
        $sqlCliente = "DELETE FROM tb_clientes WHERE id_cliente = :id_cliente";
        $stmtCliente = $pdo->prepare($sqlCliente);
        $stmtCliente->execute(['id_cliente' => $id_cliente]);

        $pdo->commit();
        bitacora($pdo, $_SESSION['id_usuario'], 'ELIMINAR', 'tb_clientes', $id_cliente, "Cliente eliminado ID: $id_cliente");

        // ✅ Mensaje de éxito
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
              title: 'Cliente eliminado',
              text: '✅ El cliente y sus datos asociados fueron eliminados correctamente',
              confirmButtonText: 'Aceptar'
            }).then(() => {
              window.location.href = '../vistas/lista.php';
            });
          </script>
        </body>
        </html>";
    } catch (PDOException $e) {
        $pdo->rollBack();
        // ❌ Mensaje de error
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
              text: '❌ No se pudo eliminar el cliente',
              confirmButtonText: 'Cerrar'
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

<?php
include('../../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // ============================
        // ACTUALIZAR CLIENTE
        // ============================
        $sqlCliente = "UPDATE tb_clientes 
                       SET nombre=:nombre, documento=:documento, telefono=:telefono, direccion=:direccion, email=:email 
                       WHERE id_cliente=:id_cliente";
        $stmt = $pdo->prepare($sqlCliente);
        $stmt->execute([
            'nombre' => $_POST['nombre'],
            'documento' => $_POST['documento'],
            'telefono' => $_POST['telefono'],
            'direccion' => $_POST['direccion'],
            'email' => $_POST['email'],
            'id_cliente' => intval($_POST['id_cliente'])
        ]);

        // ============================
        // ACTUALIZAR IPS
        // ============================
        if (!empty($_POST['ips'])) {
            foreach ($_POST['ips'] as $id_ip => $ipData) {
                $sqlIp = "UPDATE tb_ips 
                          SET ip_principal=:ip, megas_contratadas=:megas 
                          WHERE id_ip=:id_ip";
                $stmtIp = $pdo->prepare($sqlIp);
                $stmtIp->execute([
                    'ip' => $ipData['ip_principal'],
                    'megas' => $ipData['megas'],
                    'id_ip' => $id_ip
                ]);
            }
        }

        // ============================
        // ACTUALIZAR RED
        // ============================
        if (!empty($_POST['red'])) {
            foreach ($_POST['red'] as $id_red => $redData) {
                $sqlRed = "UPDATE tb_red 
                           SET switch=:switch, ip=:ip, puerto=:puerto 
                           WHERE id_red=:id_red";
                $stmtRed = $pdo->prepare($sqlRed);
                $stmtRed->execute([
                    'switch' => $redData['switch'],
                    'ip' => $redData['ip'],
                    'puerto' => $redData['puerto'],
                    'id_red' => $id_red
                ]);
            }
        }

        $pdo->commit();
        $id_cliente = intval($_POST['id_cliente']);
        bitacora($pdo, $_SESSION['id_usuario'], 'ACTUALIZAR', 'tb_clientes', $id_cliente, "Cliente actualizado: " . $_POST['nombre']);

        // ============================
        // SWEETALERT DE ÉXITO
        // ============================
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
              title: 'Cliente actualizado',
              text: '✅ Los datos del cliente se actualizaron correctamente',
              confirmButtonText: 'Aceptar'
            }).then(() => {
              window.location.href = '../vistas/lista.php';
            });
          </script>
        </body>
        </html>";
    } catch (PDOException $e) {
        $pdo->rollBack();
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
               text: '❌ Error al actualizar el cliente',
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

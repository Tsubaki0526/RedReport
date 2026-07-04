<?php
include('../../app/config/conexion.php');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_liberty_registrado   = $_POST['id_liberty_registrado'];
    $fecha_finalizado      = $_POST['fecha_finalizado'];
    $hora_finalizado       = $_POST['hora_finalizado'];
    $horas_totales         = $_POST['horas_totales'];
    $horas_real_dano       = $_POST['horas_real_dano'];
    $tipo_dano             = $_POST['tipo_de_dano'] ?? null;
    $observaciones_final   = $_POST['observaciones_finales'] ?? '';

    // Solo guardar hora de parada si se marcó la parada
    $parada_reloj        = isset($_POST['usar_parada']) ? 1 : 0;
    $hora_parada_inicio  = $parada_reloj ? $_POST['hora_parada_inicio'] : null;
    $hora_parada_fin     = $parada_reloj ? $_POST['hora_parada_fin'] : null;
    $horas_parada        = $parada_reloj ? $_POST['horas_parada'] : null;

    $fecha_hora_finalizado = $fecha_finalizado . ' ' . $hora_finalizado;

    try {
        $pdo->beginTransaction();

        // Insertar finalización
        $sql = "INSERT INTO tb_liberty_finalizacion 
                (id_liberty_registrado, fecha_hora_finalizado, horas_totales, horas_real_dano, tipo_de_dano, parada_reloj, hora_parada_inicio, hora_parada_fin, horas_parada, observaciones_final) 
                VALUES 
                (:id_liberty_registrado, :fecha_hora_finalizado, :horas_totales, :horas_real_dano, :tipo_dano, :parada_reloj, :hora_parada_inicio, :hora_parada_fin, :horas_parada, :observaciones_final)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_liberty_registrado'   => $id_liberty_registrado,
            ':fecha_hora_finalizado' => $fecha_hora_finalizado,
            ':horas_totales'         => $horas_totales,
            ':horas_real_dano'       => $horas_real_dano,
            ':tipo_dano'             => $tipo_dano,
            ':parada_reloj'          => $parada_reloj,
            ':hora_parada_inicio'    => $hora_parada_inicio,
            ':hora_parada_fin'       => $hora_parada_fin,
            ':horas_parada'          => $horas_parada,
            ':observaciones_final'   => $observaciones_final,
        ]);

        // Actualizar estado del reporte
        $sqlUpdate = "UPDATE tb_liberty SET estado = 'Finalizado' WHERE id_liberty_registrado = :id";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([':id' => $id_liberty_registrado]);

        $pdo->commit();

        bitacora($pdo, $_SESSION['id_usuario'], 'ACTUALIZAR', 'tb_liberty', $id_liberty_registrado, "Reporte finalizado ID: $id_liberty_registrado");
        $id_finalizado = $pdo->lastInsertId();
        bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_liberty_finalizacion', $id_finalizado, "Finalización creada para reporte ID: $id_liberty_registrado");

        // Mensaje de éxito
        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Reporte Finalizado',
                text: 'El reporte se finalizó correctamente',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location = '../vistas_liberty/lista_liberty.php';
            });
        </script>
        </body>
        </html>";
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        // Mensaje de error
        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error: ". addslashes($e->getMessage()) ."',
                confirmButtonText: 'OK'
            }).then(() => {
                window.history.back();
            });
        </script>
        </body>
        </html>";
        exit;
    }
}
?>

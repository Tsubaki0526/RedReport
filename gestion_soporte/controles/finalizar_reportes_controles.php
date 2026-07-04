<?php
include('../../sesion.php');
include('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_r_registrado = (int)$_POST['id_r_registrado'];
    $fecha_finalizado = $_POST['fecha_finalizado'];
    $hora_finalizado = $_POST['hora_finalizado'];
    $personal_encargado = $_POST['personal_encargado'];
    $observaciones = $_POST['observaciones_final'];

    $estado_finalizado = 'Finalizado';

    // 1. Insertar en tb_reportes_finalizados
    $sqlInsert = "INSERT INTO tb_reportes_finalizados 
                  (id_r_registrado, fecha_finalizado, hora_finalizado, personal_encargado, observaciones)
                  VALUES 
                  (:id_r_registrado, :fecha_finalizado, :hora_finalizado, :personal_encargado, :observaciones)";
    
    $queryInsert = $pdo->prepare($sqlInsert);
    $queryInsert->execute([
        ':id_r_registrado' => $id_r_registrado,
        ':fecha_finalizado' => $fecha_finalizado,
        ':hora_finalizado' => $hora_finalizado,
        ':personal_encargado' => $personal_encargado,
        ':observaciones' => $observaciones,
    ]);

    // 2. Actualizar el estado en tb_reportes_registrador
    $sqlUpdate = "UPDATE tb_reportes_registrador
                  SET estado = :estado
                  WHERE id_r_registrado = :id_r_registrado";
    
    $queryUpdate = $pdo->prepare($sqlUpdate);
    $queryUpdate->execute([
        ':estado' => $estado_finalizado,
        ':id_r_registrado' => $id_r_registrado
    ]);

    bitacora($pdo, $_SESSION['id_usuario'], 'ACTUALIZAR', 'tb_reportes_registrador', $id_r_registrado, "Reporte finalizado ID: $id_r_registrado");
    $id_finalizado = $pdo->lastInsertId();
    bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_reportes_finalizados', $id_finalizado, "Finalización creada para reporte ID: $id_r_registrado");

    echo '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: "success",
    title: "Reporte finalizado",
    text: "El reporte se ha finalizado correctamente.",
    confirmButtonText: "Aceptar"
}).then(() => {
    window.location = "../vistas/lista_gestion.php";
});
</script>
</body>
</html>';

}
?>

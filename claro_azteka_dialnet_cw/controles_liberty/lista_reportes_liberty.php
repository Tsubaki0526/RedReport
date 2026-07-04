<?php
include('../../app/config/conexion.php');

$sql_reportes = "SELECT id_liberty_registrado, radicado, operador, cliente, ciudad, telefono, fecha, hora, dano_reportado, estado
                 FROM tb_liberty 
                 WHERE estado != 'Finalizado'
                 ORDER BY fecha DESC, hora DESC";

$q_reportes = $pdo->prepare($sql_reportes);
$q_reportes->execute();
$reportes = $q_reportes->fetchAll(PDO::FETCH_ASSOC);
?>

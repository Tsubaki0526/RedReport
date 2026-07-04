<?php
include('../../app/config/conexion.php'); // ajusta la ruta si es diferente

// =================== REPORTES ===================
$sql_reportes = "SELECT id_dialnet_registrado, radicado, operador, cliente, ciudad, telefono, fecha, hora, dano_reportado, estado
                 FROM tb_dialnet 
                 WHERE estado != 'Finalizado'
                 ORDER BY fecha DESC, hora DESC";

$q_reportes = $pdo->prepare($sql_reportes);
$q_reportes->execute();
$reportes = $q_reportes->fetchAll(PDO::FETCH_ASSOC);

?>

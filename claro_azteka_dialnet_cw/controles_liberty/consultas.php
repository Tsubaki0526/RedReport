<?php
require_once('../../app/config/conexion.php');

$sqlUltimo = "SELECT radicado FROM tb_liberty ORDER BY radicado DESC LIMIT 1";
$queryUltimo = $pdo->prepare($sqlUltimo);
$queryUltimo->execute();
$ultimoRadicado = $queryUltimo->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT radicado, fecha, estado, cliente 
        FROM tb_liberty 
        WHERE estado != 'finalizado'";
$query = $pdo->prepare($sql);
$query->execute();
$pendientes = $query->fetchAll(PDO::FETCH_ASSOC);

$alertas = [];
?>

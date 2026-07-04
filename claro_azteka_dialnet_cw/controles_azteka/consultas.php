<?php
require_once('../../app/config/conexion.php');


// Buscar el último radicado generado
$sqlUltimo = "SELECT radicado FROM tb_azteka ORDER BY radicado DESC LIMIT 1";
$queryUltimo = $pdo->prepare($sqlUltimo);
$queryUltimo->execute();
$ultimoRadicado = $queryUltimo->fetch(PDO::FETCH_ASSOC);



// Obtener todos los reportes no finalizados
$sql = "SELECT radicado, fecha, estado, cliente 
        FROM tb_azteka
        WHERE estado != 'finalizado'";
$query = $pdo->prepare($sql);
$query->execute();
$pendientes = $query->fetchAll(PDO::FETCH_ASSOC);

$alertas = [];



?>
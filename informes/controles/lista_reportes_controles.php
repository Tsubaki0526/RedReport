<?php
include(__DIR__ . '/../../app/config/conexion.php');

$condiciones = [];
$params = [];

if (!empty($_GET['fecha_inicio'])) {
    $condiciones[] = "r.fecha >= :fecha_inicio";
    $params[':fecha_inicio'] = $_GET['fecha_inicio'];
}
if (!empty($_GET['fecha_fin'])) {
    $condiciones[] = "r.fecha <= :fecha_fin";
    $params[':fecha_fin'] = $_GET['fecha_fin'];
}

$sql_reportes = "SELECT r.id_r_registrado, r.empresa, r.radicado, r.operador, r.nombre, 
                        r.direccion, r.forma, r.telefono, r.fecha, r.hora, r.observaciones,
                        r.estado,
                        f.fecha_finalizado, f.hora_finalizado, f.personal_encargado, 
                        f.observaciones AS solucion
                 FROM tb_reportes_registrador r
                 LEFT JOIN tb_reportes_finalizados f 
                 ON r.id_r_registrado = f.id_r_registrado";

if ($condiciones) {
    $sql_reportes .= " WHERE " . implode(' AND ', $condiciones);
}

$sql_reportes .= " ORDER BY r.fecha DESC, r.hora DESC";

$q_reportes = $pdo->prepare($sql_reportes);
$q_reportes->execute($params);
$reportes = $q_reportes->fetchAll(PDO::FETCH_ASSOC);

?>


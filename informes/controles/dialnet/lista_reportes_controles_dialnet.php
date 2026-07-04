<?php
include(__DIR__ . '/../../../app/config/conexion.php');

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

$sql_reportes = "SELECT r.id_dialnet_registrado, r.radicado, r.cliente, r.ciudad, r.telefono, r.fecha, r.hora, r.dano_reportado, r.estado,
                        f.fecha_hora_finalizado, f.horas_totales, f.horas_real_dano, f.tipo_de_dano, f.parada_reloj, f.hora_parada_inicio, f.hora_parada_fin, f.horas_parada, 
                        f.observaciones_final AS solucion
                 FROM tb_dialnet r
                 LEFT JOIN tb_dialnet_finalizacion f 
                 ON r.id_dialnet_registrado = f.id_dialnet_registrado";

if ($condiciones) {
    $sql_reportes .= " WHERE " . implode(' AND ', $condiciones);
}

$sql_reportes .= " ORDER BY r.fecha DESC, r.hora DESC";

$q_reportes = $pdo->prepare($sql_reportes);
$q_reportes->execute($params);
$reportes = $q_reportes->fetchAll(PDO::FETCH_ASSOC);

?>

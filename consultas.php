<?php
include('app/config/conexion.php');

// Consultar total de clientes
$sqlClientes = "SELECT COUNT(*) AS total FROM tb_clientes";
$queryClientes = $pdo->prepare($sqlClientes);
$queryClientes->execute();
$totalClientes = $queryClientes->fetch(PDO::FETCH_ASSOC)['total'];

// Consultar total de reportes
$sqlReportes = "SELECT COUNT(*) AS total FROM tb_reportes_registrador";
$queryReportes = $pdo->prepare($sqlReportes);
$queryReportes->execute();
$totalReportes = $queryReportes->fetch(PDO::FETCH_ASSOC)['total'];

// Consultar total de reportes Claro
$sqlClaro = "SELECT COUNT(*) AS total FROM tb_claro";
$queryClaro = $pdo->prepare($sqlClaro);
$queryClaro->execute();
$totalReportes_claro = $queryClaro->fetch(PDO::FETCH_ASSOC)['total'];

// Consultar total de reportes Azteka
$sqlAzteka = "SELECT COUNT(*) AS total FROM tb_azteka";
$queryAzteka = $pdo->prepare($sqlAzteka);
$queryAzteka->execute();
$totalReportes_azteka = $queryAzteka->fetch(PDO::FETCH_ASSOC)['total'];

// Obtener mes actual
$mesActual = date('m');
$anioActual = date('Y');

// Consulta: contar reportes por cliente en el mes actual
$sql = "SELECT c.nombre, COUNT(r.id_r_registrado) AS total_reportes
        FROM tb_reportes_registrador r
        INNER JOIN tb_clientes c ON r.nombre = c.nombre
        WHERE MONTH(r.fecha) = :mes AND YEAR(r.fecha) = :anio
        GROUP BY c.nombre
        ORDER BY total_reportes DESC
        LIMIT 5"; // Top 5 clientes

$stmt = $pdo->prepare($sql);
$stmt->execute(['mes' => $mesActual, 'anio' => $anioActual]);
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pasar datos a arrays para Chart.js
$clientes = [];
$reportes = [];

foreach ($resultado as $fila) {
    $clientes[] = $fila['nombre'];
    $reportes[] = (int)$fila['total_reportes'];
}
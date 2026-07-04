<?php
include('../../app/config/conexion.php');

$term = $_GET['term'] ?? '';

$sql = "SELECT * FROM tb_clientes 
        WHERE nombre LIKE :term OR telefono LIKE :term 
        LIMIT 10";

$stmt = $pdo->prepare($sql);
$stmt->execute([':term' => "%$term%"]);
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($clientes);

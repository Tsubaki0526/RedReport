<?php
include('../app/config/conexion.php');// ajusta la ruta si es diferente

$sql_usuarios = "
SELECT u.*, r.nombre_rol
FROM tb_usuarios u
JOIN tb_rol r ON u.id_rol = r.id_rol
WHERE u.nombre != 'administrador'
";
$query_usuarios = $pdo->prepare($sql_usuarios);
$query_usuarios->execute();
$usuarios_datos = $query_usuarios->fetchAll(PDO::FETCH_ASSOC);



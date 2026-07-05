<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_equipo = (int)($_POST['id_equipo'] ?? 0);
if ($id_equipo <= 0) die("Equipo invalido");

try {
    $stmt = $pdo->prepare("UPDATE tb_equipos SET estado = 'Disponible', id_cliente = NULL, id_instalacion = NULL, fecha_asignado = NULL WHERE id_equipo = :id");
    $stmt->execute([':id' => $id_equipo]);
    bitacora($pdo, $_SESSION['id_usuario'], 'DEVOLVER_EQUIPO', 'tb_equipos', $id_equipo, "Equipo #$id_equipo devuelto a inventario");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Equipo devuelto',text:'El equipo ya está disponible en inventario'}).then(()=>window.location='../index.php');</script>";
} catch (Exception $e) {
    error_log("devolver_equipo error: " . $e->getMessage());
    echo "<script>alert('Error al devolver el equipo');window.location='../index.php';</script>";
}

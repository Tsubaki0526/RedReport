<?php
session_start();
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre = $_POST['nombre'] ?? '';
$documento = $_POST['documento'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$email = $_POST['email'] ?? '';

try {
    $sql = "UPDATE tb_usuarios SET nombre = :nombre, documento = :documento, telefono = :telefono, email = :email WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nombre' => $nombre, ':documento' => $documento, ':telefono' => $telefono, ':email' => $email, ':id' => $id_usuario]);
    $_SESSION['usuario'] = $nombre;
    bitacora($pdo, $id_usuario, 'ACTUALIZAR', 'tb_usuarios', $id_usuario, "Usuario actualizó su perfil");
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Perfil actualizado',confirmButtonText:'OK'}).then(()=>window.location='perfil.php');</script>";
} catch (Exception $e) {
    error_log("actualizar_perfil error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrió un error al actualizar el perfil.',confirmButtonText:'OK'}).then(()=>window.location='perfil.php');</script>";
}
?>

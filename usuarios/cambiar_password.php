<?php
session_start();
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$id_usuario = $_SESSION['id_usuario'];
$password_actual = $_POST['password_actual'] ?? '';
$password_nueva = $_POST['password_nueva'] ?? '';
$password_confirmar = $_POST['password_confirmar'] ?? '';

if ($password_nueva !== $password_confirmar) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Las contraseñas no coinciden',confirmButtonText:'OK'}).then(()=>window.location='perfil.php');</script>";
    exit;
}

if (strlen($password_nueva) < 6) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Mínimo 6 caracteres',confirmButtonText:'OK'}).then(()=>window.location='perfil.php');</script>";
    exit;
}

$sql = "SELECT password FROM tb_usuarios WHERE id_usuario = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$user = $stmt->fetch();

if (!$user || !password_verify($password_actual, $user['password'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Contraseña actual incorrecta',confirmButtonText:'OK'}).then(()=>window.location='perfil.php');</script>";
    exit;
}

$hash = password_hash($password_nueva, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE tb_usuarios SET password = :pass WHERE id_usuario = :id");
$stmt->execute([':pass' => $hash, ':id' => $id_usuario]);
bitacora($pdo, $id_usuario, 'CAMBIAR_PASSWORD', 'tb_usuarios', $id_usuario, "Usuario cambió su contraseña");
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>Swal.fire({icon:'success',title:'Contraseña cambiada',confirmButtonText:'OK'}).then(()=>window.location='perfil.php');</script>";
?>

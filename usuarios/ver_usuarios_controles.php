<?php
include('../app/config/conexion.php');

$id_usuario_get = $_GET['id_usuario'] ?? null;

if ($id_usuario_get) {
    // Obtener datos del usuario incluyendo el rol
    $sql_usuarios = "
        SELECT u.*, r.nombre_rol
        FROM tb_usuarios u
        JOIN tb_rol r ON u.id_rol = r.id_rol
        WHERE u.id_usuario = :id_usuario
    ";
    $query_usuarios = $pdo->prepare($sql_usuarios);
    $query_usuarios->execute([':id_usuario' => $id_usuario_get]);
    $usuarios_datos = $query_usuarios->fetch(PDO::FETCH_ASSOC);

    if ($usuarios_datos) {
        $nombre = $usuarios_datos['nombre'];
        $documento = $usuarios_datos['documento'];
        $telefono = $usuarios_datos['telefono'];
        $email = $usuarios_datos['email'];
        $id_rol = $usuarios_datos['id_rol'];
        $rol_nombre = $usuarios_datos['nombre_rol'];
    } else {
        echo "Usuario no encontrado.";
        exit;
    }

    // Cargar lista de roles
    $sql_roles = "SELECT * FROM tb_rol";
    $query_roles = $pdo->prepare($sql_roles);
    $query_roles->execute();
    $roles_datos = $query_roles->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "ID de usuario no proporcionado.";
    exit;
}

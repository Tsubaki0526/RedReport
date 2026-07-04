<?php
include("../app/config/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <link rel="icon" href="../public/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['password'], $_POST['id_usuario'], $_POST['token'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $token      = $_POST['token'];
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validar que el token coincida y no haya expirado
    $sqlCheck = "SELECT id_usuario FROM tb_usuarios WHERE id_usuario = ? AND token_reset = ? AND token_expira > NOW()";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$id_usuario, $token]);
    if (!$stmtCheck->fetch()) {
        echo "<script>Swal.fire({icon:'error',title:'Enlace inválido',text:'El enlace ha expirado o es inválido.'}).then(()=>window.location.href='login.php');</script>";
        exit;
    }

    $sql = "UPDATE tb_usuarios 
            SET password = ?, token_reset = NULL, token_expira = NULL 
            WHERE id_usuario = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$password, $id_usuario])) {
        // ✅ Éxito
        echo "
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Contraseña actualizada!',
                text: 'Ahora puedes iniciar sesión con tu nueva contraseña.',
                confirmButtonText: 'Ir al login',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                window.location.href = 'login.php';
            });
        </script>";
    } else {
        // ❌ Error en DB
        echo "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al actualizar la contraseña. Intenta de nuevo.',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#d33'
            }).then(() => {
                window.location.href = 'recuperar.php?token=" . urlencode($_POST['token'] ?? '') . "';
            });
        </script>";
    }
} else {
    // ⚠️ Acceso inválido
    echo "
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Acceso inválido',
            text: 'Debes ingresar desde el formulario de restablecimiento.',
            confirmButtonText: 'Ir al login',
            confirmButtonColor: '#f39c12'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>";
}
?>

</body>
</html>

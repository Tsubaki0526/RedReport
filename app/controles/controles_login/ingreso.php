<?php
session_start();
include('../../config/conexion.php');
require_once('../../config/seguridad.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    csrf_die();
}

$nombre = $_POST['nombre'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($nombre) || empty($password)) {
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Datos incompletos',
                text: 'Debe ingresar usuario y contraseña',
                confirmButtonText: 'Intentar de nuevo',
                allowOutsideClick: false,
                heightAuto: false
            }).then(() => {
                window.location.href = '../../../login/login.php';
            });
        </script>
    </body>
    </html>";
    exit();
}

$sql = "SELECT u.*, r.nombre_rol 
        FROM tb_usuarios u
        INNER JOIN tb_rol r ON u.id_rol = r.id_rol
        WHERE u.nombre = :nombre";
$query = $pdo->prepare($sql);
$query->execute([':nombre' => $nombre]);
$usuario = $query->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    $bloqueo = verificar_bloqueo($pdo, $usuario['id_usuario']);
    if ($bloqueo) {
        echo "
        <html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Cuenta bloqueada',
                    text: '$bloqueo',
                    confirmButtonText: 'Entendido',
                    allowOutsideClick: false,
                    heightAuto: false
                }).then(() => {
                    window.location.href = '../../../login/login.php';
                });
            </script>
        </body>
        </html>";
        exit();
    }
}

if ($usuario && password_verify($password, $usuario['password'])) {
    registrar_intento($pdo, $usuario['id_usuario'], true);

    if (!empty($usuario['google2fa_secret'])) {
        $_SESSION['tentativa_2fa_user_id'] = $usuario['id_usuario'];
        $_SESSION['tentativa_2fa_secret']  = $usuario['google2fa_secret'];
        session_regenerate_id(true);
        header("Location: " . APP_URL . "login/verificar_2fa.php");
        exit();
    }

    session_regenerate_id(true);
    $_SESSION['usuario']     = $usuario['nombre'];
    $_SESSION['id_usuario']  = $usuario['id_usuario'];
    $_SESSION['id_rol']      = $usuario['id_rol'];
    $_SESSION['nombre_rol']  = $usuario['nombre_rol'];
    $_SESSION['_ultimo_acceso'] = time();

    bitacora($pdo, $usuario['id_usuario'], 'INICIO_SESION', 'tb_usuarios', $usuario['id_usuario'], "Usuario $nombre inici&oacute; sesi&oacute;n");

    $rutas = [
        'Administrador' => '../../../index.php',
        'Gestion'       => '../../../index.php',
    ];

    $destino = $rutas[$usuario['nombre_rol']] ?? '../../../login/login.php';
    header("Location: $destino");
    exit();

} else {
    if ($usuario) {
        registrar_intento($pdo, $usuario['id_usuario'], false);
    }
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Acceso denegado',
                text: 'Usuario o contraseña incorrectos',
                confirmButtonText: 'Intentar de nuevo',
                allowOutsideClick: false,
                heightAuto: false
            }).then(() => {
                window.location.href = '../../../login/login.php';
            });
        </script>
    </body>
    </html>";
    exit();
}
?>

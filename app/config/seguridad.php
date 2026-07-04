<?php
if (defined('SEGURIDAD_CARGADO')) return;
define('SEGURIDAD_CARGADO', true);

require_once __DIR__ . '/conexion.php';

define('SESSION_TIMEOUT', 1800);
define('MAX_INTENTOS', 5);
define('BLOQUEO_MINUTOS', 15);

function csrf_token() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify($token) {
    if (empty($_SESSION['_csrf_token']) || empty($token)) return false;
    return hash_equals($_SESSION['_csrf_token'], $token);
}

function csrf_die() {
    http_response_code(419);
    die('
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: "error",
            title: "Sesión expirada",
            text: "Por seguridad, recarga la página e intenta de nuevo.",
            confirmButtonText: "Recargar"
        }).then(() => { window.location.reload(); });
    </script>');
}

function bitacora($pdo, $id_usuario, $accion, $tabla, $id_registro = null, $detalle = null) {
    try {
        $sql = "INSERT INTO tb_bitacora (id_usuario, accion, tabla_afectada, id_registro_afectado, detalle, direccion_ip)
                VALUES (:id_usuario, :accion, :tabla, :id_registro, :detalle, :ip)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':accion' => $accion,
            ':tabla' => $tabla,
            ':id_registro' => $id_registro,
            ':detalle' => $detalle,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);
    } catch (Exception $e) {
        $logDir = __DIR__ . '/../../logs';
        if (!is_dir($logDir)) mkdir($logDir, 0755, true);
        file_put_contents("$logDir/app.log", date('Y-m-d H:i:s') . " [BITACORA] " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

function verificar_sesion() {
    if (empty($_SESSION['id_usuario'])) {
        return false;
    }
    if (isset($_SESSION['_ultimo_acceso'])) {
        $inactivo = time() - $_SESSION['_ultimo_acceso'];
        if ($inactivo > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            return false;
        }
    }
    $_SESSION['_ultimo_acceso'] = time();
    return true;
}

function verificar_bloqueo($pdo, $id_usuario) {
    $sql = "SELECT intentos_fallidos, bloqueado_hasta FROM tb_usuarios WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $user = $stmt->fetch();

    if (!$user) return null;

    if ($user['bloqueado_hasta'] && strtotime($user['bloqueado_hasta']) > time()) {
        $restante = ceil((strtotime($user['bloqueado_hasta']) - time()) / 60);
        return "Cuenta bloqueada. Intenta de nuevo en $restante minuto(s).";
    }

    if ($user['bloqueado_hasta'] && strtotime($user['bloqueado_hasta']) <= time()) {
        $sqlReset = "UPDATE tb_usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id_usuario = :id";
        $pdo->prepare($sqlReset)->execute([':id' => $id_usuario]);
    }

    return null;
}

function registrar_intento($pdo, $id_usuario, $exitosa) {
    if ($exitosa) {
        $sql = "UPDATE tb_usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id_usuario = :id";
        $pdo->prepare($sql)->execute([':id' => $id_usuario]);
    } else {
        $sql = "UPDATE tb_usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id_usuario = :id";
        $pdo->prepare($sql)->execute([':id' => $id_usuario]);

        $sqlCheck = "SELECT intentos_fallidos FROM tb_usuarios WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sqlCheck);
        $stmt->execute([':id' => $id_usuario]);
        $user = $stmt->fetch();

        if ($user && $user['intentos_fallidos'] >= MAX_INTENTOS) {
            $bloqueo = date('Y-m-d H:i:s', time() + (BLOQUEO_MINUTOS * 60));
            $sqlBlock = "UPDATE tb_usuarios SET bloqueado_hasta = :bloqueo WHERE id_usuario = :id";
            $pdo->prepare($sqlBlock)->execute([':bloqueo' => $bloqueo, ':id' => $id_usuario]);
        }
    }
}
?>

<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

header('Content-Type: application/json');

if ($_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    http_response_code(419);
    echo json_encode(['success' => false, 'message' => 'Sesión expirada. Recarga la página.']);
    exit;
}

$id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
$nombre = trim($_POST['nombre'] ?? '');
$asunto = trim($_POST['asunto'] ?? '');
$cuerpo = trim($_POST['cuerpo'] ?? '');

if ($nombre === '' || $asunto === '' || $cuerpo === '') {
    echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben ser llenados.']);
    exit;
}

try {
    if ($id) {
        $sql = "UPDATE tb_plantillas_email SET nombre = :nombre, asunto = :asunto, cuerpo = :cuerpo, updated_at = NOW() WHERE id_plantilla = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nombre' => $nombre, ':asunto' => $asunto, ':cuerpo' => $cuerpo, ':id' => $id]);
        bitacora($pdo, $_SESSION['id_usuario'], 'EDITAR', 'tb_plantillas_email', $id, "Plantilla actualizada: $nombre");
        echo json_encode(['success' => true, 'message' => 'Plantilla actualizada correctamente.']);
    } else {
        $sql = "INSERT INTO tb_plantillas_email (nombre, asunto, cuerpo, variables, created_at, updated_at) VALUES (:nombre, :asunto, :cuerpo, :variables, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':asunto' => $asunto,
            ':cuerpo' => $cuerpo,
            ':variables' => '{nombre_cliente}, {numero_factura}, {monto}, {fecha_vencimiento}, {empresa_nombre}, {url_pago}'
        ]);
        $insertId = $pdo->lastInsertId();
        bitacora($pdo, $_SESSION['id_usuario'], 'CREAR', 'tb_plantillas_email', $insertId, "Plantilla creada: $nombre");
        echo json_encode(['success' => true, 'message' => 'Plantilla creada correctamente.']);
    }
} catch (Exception $e) {
    error_log("guardar_plantilla error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ocurrió un error al guardar la plantilla.']);
}

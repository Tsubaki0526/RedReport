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

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT nombre FROM tb_plantillas_email WHERE id_plantilla = ?");
    $stmt->execute([$id]);
    $plantilla = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$plantilla) {
        echo json_encode(['success' => false, 'message' => 'La plantilla no existe.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM tb_plantillas_email WHERE id_plantilla = ?");
    $stmt->execute([$id]);

    bitacora($pdo, $_SESSION['id_usuario'], 'ELIMINAR', 'tb_plantillas_email', $id, "Plantilla eliminada: {$plantilla['nombre']}");
    echo json_encode(['success' => true, 'message' => 'Plantilla eliminada correctamente.']);
} catch (Exception $e) {
    error_log("eliminar_plantilla error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ocurrió un error al eliminar la plantilla.']);
}

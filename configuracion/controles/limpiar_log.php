<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) {
    http_response_code(419);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido']);
    exit;
}

$logCandidates = [
    ini_get('error_log'),
    'C:/xampp/php/logs/php_error_log',
    'C:/xampp/apache/logs/error.log',
    __DIR__ . '/../../error_log',
    __DIR__ . '/../../logs/app.log',
    __DIR__ . '/../../logs/error.log',
    __DIR__ . '/../../logs/php_error_log',
];

$logFile = null;
foreach ($logCandidates as $path) {
    if ($path && file_exists($path) && is_file($path) && is_readable($path)) {
        $logFile = $path;
        break;
    }
}

if (!$logFile) {
    echo json_encode(['success' => false, 'message' => 'No se encontró ningún archivo de log para limpiar']);
    exit;
}

try {
    file_put_contents($logFile, '');
    bitacora($pdo, $_SESSION['id_usuario'], 'LIMPIAR_LOG', 'logs', 0, 'Archivo de log limpiado: ' . basename($logFile));
    echo json_encode(['success' => true, 'message' => 'Archivo de log limpiado correctamente']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al limpiar el log: ' . $e->getMessage()]);
}

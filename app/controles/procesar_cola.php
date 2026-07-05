<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/seguridad.php';
require_once __DIR__ . '/email_queue.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$action = $_GET['action'] ?? 'process';

try {
    switch ($action) {
        case 'process':
            $limit = intval($_GET['limit'] ?? 10);
            $result = EmailQueue::processStatic($pdo, $limit);
            bitacora($pdo, $_SESSION['id_usuario'], 'Procesar cola', 'tb_email_queue', null,
                "Cola procesada: {$result['sent']} enviados, {$result['failed']} fallos");
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'retry':
            $count = EmailQueue::retryErrorsStatic($pdo);
            bitacora($pdo, $_SESSION['id_usuario'], 'Reintentar cola', 'tb_email_queue', null,
                "Reintentos: $count items marcados como pendientes");
            echo json_encode(['success' => true, 'data' => ['reintentados' => $count]]);
            break;

        case 'status':
            $status = EmailQueue::statusStatic($pdo);
            echo json_encode(['success' => true, 'data' => $status]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Accion no valida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

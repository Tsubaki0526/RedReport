<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexion.php';

// API Key validation
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? '');
if (empty($api_key) || $api_key !== ($_ENV['API_KEY'] ?? 'RedReport2024API')) {
    http_response_code(401);
    echo json_encode(['error' => 'API key invalida']);
    exit;
}

$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$id = intval($_GET['id'] ?? 0);

try {
    switch ($endpoint) {
        case 'clientes':
            require __DIR__ . '/controles/clientes.php';
            break;
        case 'facturas':
            require __DIR__ . '/controles/facturas.php';
            break;
        case 'contratos':
            require __DIR__ . '/controles/contratos.php';
            break;
        case 'planes':
            if ($method == 'GET') {
                $planes = $pdo->query("SELECT id_plan, nombre, velocidad, precio, descripcion FROM tb_planes WHERE activo = 1")->fetchAll();
                echo json_encode(['data' => $planes]);
            }
            break;
        case 'dashboard':
            if ($method == 'GET') {
                $total = $pdo->query("SELECT COUNT(*) FROM tb_clientes")->fetchColumn();
                $activos = $pdo->query("SELECT COUNT(*) FROM tb_clientes WHERE estado_servicio = 'Activo'")->fetchColumn();
                $contratos = $pdo->query("SELECT COUNT(*) FROM tb_contratos WHERE estado = 'activo'")->fetchColumn();
                $deuda = $pdo->query("SELECT COALESCE(SUM(total),0) FROM tb_facturas WHERE estado IN ('pendiente','vencida')")->fetchColumn();
                echo json_encode(['data' => compact('total','activos','contratos','deuda')]);
            }
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint no encontrado', 'endpoints' => ['clientes', 'facturas', 'contratos', 'planes', 'dashboard']]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

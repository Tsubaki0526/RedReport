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

$action = $_POST['action'] ?? '';
if (!in_array($action, ['optimize', 'repair', 'analyze'])) {
    echo json_encode(['success' => false, 'message' => 'Acción inválida']);
    exit;
}

try {
    $dbName = DB_NAME;
    $tables = $pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = " . $pdo->quote($dbName))->fetchAll(PDO::FETCH_COLUMN);

    $results = [];
    $errors = 0;

    foreach ($tables as $table) {
        try {
            $sql = '';
            switch ($action) {
                case 'optimize':
                    $sql = "OPTIMIZE TABLE `$table`";
                    break;
                case 'repair':
                    $sql = "REPAIR TABLE `$table`";
                    break;
                case 'analyze':
                    $sql = "ANALYZE TABLE `$table`";
                    break;
            }
            $stmt = $pdo->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = $row ? ($row['Msg_type'] === 'error' ? 'ERROR: ' . ($row['Msg_text'] ?? '') : 'OK') : 'OK';
            if ($row && $row['Msg_type'] === 'error') $errors++;
            $results[] = ['table' => $table, 'status' => $status];
        } catch (Exception $e) {
            $errors++;
            $results[] = ['table' => $table, 'status' => 'ERROR: ' . $e->getMessage()];
        }
    }

    $actionLabels = ['optimize' => 'OPTIMIZAR', 'repair' => 'REPARAR', 'analyze' => 'ANALIZAR'];
    $total = count($tables);
    $ok = $total - $errors;
    bitacora($pdo, $_SESSION['id_usuario'], $actionLabels[$action], 'tb_mantenimiento_bd', 0, "{$actionLabels[$action]}: {$ok}/{$total} tablas OK");

    echo json_encode([
        'success' => $errors === 0,
        'message' => "{$actionLabels[$action]} completado. {$ok}/{$total} tablas procesadas correctamente.",
        'results' => $results
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error general: ' . $e->getMessage()]);
}

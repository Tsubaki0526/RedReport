<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';
verificar_acceso([1, 2]);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => 0, 'errors' => 1, 'error_details' => [['row' => 0, 'message' => 'Método no permitido']]]);
    exit;
}

$token = $_POST['_csrf_token'] ?? '';
if (!csrf_verify($token)) {
    http_response_code(419);
    echo json_encode(['success' => 0, 'errors' => 1, 'error_details' => [['row' => 0, 'message' => 'Token CSRF inválido. Recarga la página.']]]);
    exit;
}

$csvRaw = $_POST['csv'] ?? '';
$mappingJson = $_POST['mapping'] ?? '';

if (empty($csvRaw)) {
    echo json_encode(['success' => 0, 'errors' => 1, 'error_details' => [['row' => 0, 'message' => 'No se recibió el contenido del CSV.']]]);
    exit;
}

$mapping = json_decode($mappingJson, true);
if (!is_array($mapping)) {
    echo json_encode(['success' => 0, 'errors' => 1, 'error_details' => [['row' => 0, 'message' => 'Mapeo de columnas inválido.']]]);
    exit;
}

if (empty($mapping['nombre']) || empty($mapping['documento'])) {
    echo json_encode(['success' => 0, 'errors' => 1, 'error_details' => [['row' => 0, 'message' => 'Los campos Nombre y Documento son obligatorios.']]]);
    exit;
}

// Parse CSV
$lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $csvRaw));
$rows = [];
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;
    $row = str_getcsv($line, ',', '"', '');
    $rows[] = $row;
}

if (count($rows) < 2) {
    echo json_encode(['success' => 0, 'errors' => 1, 'error_details' => [['row' => 0, 'message' => 'El CSV debe contener un encabezado y al menos una fila de datos.']]]);
    exit;
}

$headers = $rows[0];
$dataRows = array_slice($rows, 1);

$success = 0;
$errors = 0;
$errorDetails = [];

$id_usuario = $_SESSION['id_usuario'] ?? 0;

foreach ($dataRows as $idx => $row) {
    $rowNum = $idx + 2; // +2 because 1-indexed and header is row 1

    $get = function($field) use ($mapping, $row) {
        $colIndex = $mapping[$field] ?? null;
        if ($colIndex === null || $colIndex === '') return '';
        return trim($row[$colIndex] ?? '');
    };

    $nombre    = $get('nombre');
    $documento = $get('documento');
    $email     = $get('email');
    $telefono  = $get('telefono');
    $direccion = $get('direccion');
    $ciudad    = $get('ciudad');

    // Combine ciudad into direccion if both provided
    if ($ciudad !== '' && $direccion !== '') {
        $direccion = $ciudad . ', ' . $direccion;
    } elseif ($ciudad !== '') {
        $direccion = $ciudad;
    }

    // Validate required fields
    if ($nombre === '') {
        $errors++;
        $errorDetails[] = ['row' => $rowNum, 'message' => 'El campo Nombre está vacío.'];
        continue;
    }
    if ($documento === '') {
        $errors++;
        $errorDetails[] = ['row' => $rowNum, 'message' => 'El campo Documento está vacío.'];
        continue;
    }

    // Validate email format if provided
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors++;
        $errorDetails[] = ['row' => $rowNum, 'message' => "Correo electrónico inválido: " . hescape($email)];
        continue;
    }

    // Check for duplicate documento
    try {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM tb_clientes WHERE documento = :doc");
        $stmtCheck->execute([':doc' => $documento]);
        if ($stmtCheck->fetchColumn() > 0) {
            $errors++;
            $errorDetails[] = ['row' => $rowNum, 'message' => "Documento duplicado: " . hescape($documento)];
            continue;
        }
    } catch (PDOException $e) {
        $errors++;
        $errorDetails[] = ['row' => $rowNum, 'message' => 'Error al verificar documento.'];
        continue;
    }

    // Insert
    try {
        $sql = "INSERT INTO tb_clientes (nombre, documento, email, telefono, direccion, fecha_registro)
                VALUES (:nombre, :documento, :email, :telefono, :direccion, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'    => $nombre,
            ':documento' => $documento,
            ':email'     => $email,
            ':telefono'  => $telefono,
            ':direccion' => $direccion,
        ]);
        $nuevoId = $pdo->lastInsertId();
        $success++;
        bitacora($pdo, $id_usuario, 'IMPORTAR', 'tb_clientes', $nuevoId, "Cliente importado: $nombre ($documento)");
    } catch (PDOException $e) {
        $errors++;
        $errorDetails[] = ['row' => $rowNum, 'message' => 'Error al insertar: ' . $e->getMessage()];
    }
}

echo json_encode([
    'success'       => $success,
    'errors'        => $errors,
    'error_details' => $errorDetails,
]);

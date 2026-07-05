<?php
session_start();
require_once __DIR__ . '/../../app/config/config.php';
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

if ($_SESSION['id_rol'] != 1) { die('Acceso denegado'); }

$logCandidates = [
    ini_get('error_log'),
    'C:/xampp/php/logs/php_error_log',
    'C:/xampp/apache/logs/error.log',
    __DIR__ . '/../../error_log',
    __DIR__ . '/../../logs/app.log',
    __DIR__ . '/../../logs/error.log',
    __DIR__ . '/../../logs/php_error.log',
];

$logFile = null;
foreach ($logCandidates as $path) {
    if ($path && file_exists($path) && is_file($path) && is_readable($path)) {
        $logFile = $path;
        break;
    }
}

if (!$logFile) {
    http_response_code(404);
    die('Archivo de log no encontrado');
}

bitacora($pdo, $_SESSION['id_usuario'], 'DESCARGAR_LOG', 'logs', 0, 'Descargó el archivo: ' . basename($logFile));

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . basename($logFile) . '.txt"');
header('Content-Length: ' . filesize($logFile));
header('Cache-Control: no-cache');
readfile($logFile);

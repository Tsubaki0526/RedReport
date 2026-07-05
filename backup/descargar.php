<?php
include('../sesion.php');
$file = basename($_GET['file'] ?? '');
$path = __DIR__ . '/' . $file;
if (!file_exists($path) || !str_starts_with($file, 'backup_') || !str_ends_with($file, '.sql')) {
    die('Archivo no válido');
}
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));
readfile($path);

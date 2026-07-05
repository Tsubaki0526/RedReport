<?php
include('../sesion.php');
$file = basename($_GET['file'] ?? '');
$path = __DIR__ . '/' . $file;
if (file_exists($path) && str_starts_with($file, 'backup_') && str_ends_with($file, '.sql')) {
    unlink($path);
}
header('Location: index.php');

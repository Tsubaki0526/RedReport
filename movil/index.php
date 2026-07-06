<?php
require_once __DIR__ . '/header.php';
if (!$movil_user) { header('Location: login.php'); exit; }

$titulo = 'Inicio';
$seccion = 'dashboard';
$contenido = $es_empleado ? __DIR__ . '/instalador/index.php' : __DIR__ . '/cliente/index.php';
require $contenido;
require __DIR__ . '/footer.php';

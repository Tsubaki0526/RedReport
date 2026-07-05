<?php
session_start();
if (!isset($_SESSION['portal_cliente'])) { header('Location: index.php'); exit; }
require_once '../app/config/conexion.php';
$c = $_SESSION['portal_cliente'];
$facturas = $pdo->prepare("SELECT * FROM tb_facturas WHERE id_cliente=? ORDER BY fecha_emision DESC");
$facturas->execute([$c['id_cliente']]);
$facturas = $facturas->fetchAll();
include 'dashboard.php';

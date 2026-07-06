<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header("Cache-Control: no-store, no-cache, must-revalidate");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/conexion.php';
require_once __DIR__ . '/../app/config/seguridad.php';

$movil_user = $_SESSION['movil_user'] ?? null;
$es_empleado = $movil_user && ($movil_user['tipo'] === 'empleado');
$es_cliente  = $movil_user && ($movil_user['tipo'] === 'cliente');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
<meta name="theme-color" content="#2563eb">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<link rel="manifest" href="/RedReport/movil/manifest.json">
<link rel="icon" href="/RedReport/public/img/favicon.png" type="image/png">
<title><?= $titulo ?? 'RedReport' ?> - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root{--navy:#0f172a;--blue:#2563eb;--blue-hover:#1d4ed8;--card-bg:#fff;--body-bg:#f1f5f9;--text:#1e293b;--text-muted:#64748b;--border:#e2e8f0}
body{background:var(--body-bg);font-family:system-ui,-apple-system,sans-serif;font-size:15px;color:var(--text);padding-bottom:70px;-webkit-tap-highlight-color:transparent}
.topbar{background:var(--navy);color:#fff;padding:12px 16px;position:sticky;top:0;z-index:1000}
.topbar h6{margin:0;font-weight:600}.topbar .sub{font-size:12px;color:#94a3b8}
.card{border:none;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.06);margin-bottom:12px}
.card-title{font-size:14px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.card-value{font-size:24px;font-weight:700}
.list-item{border-bottom:1px solid var(--border);padding:14px 16px;display:flex;align-items:center;gap:12px;cursor:pointer;transition:background .15s}
.list-item:last-child{border-bottom:none}
.list-item:active{background:#f8fafc}
.list-item .icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.list-item .body{flex:1;min-width:0}.list-item .body .title{font-weight:600;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.list-item .body .sub{font-size:12px;color:var(--text-muted)}
.list-item .right{text-align:right;flex-shrink:0}
.bottombar{background:#fff;border-top:1px solid var(--border);position:fixed;bottom:0;left:0;right:0;z-index:1000;display:flex;padding:6px 0;padding-bottom:env(safe-area-inset-bottom)}
.bottombar a{flex:1;text-align:center;padding:6px 0;color:var(--text-muted);text-decoration:none;font-size:11px;transition:color .15s}
.bottombar a i{display:block;font-size:20px;margin-bottom:2px}
.bottombar a.active,.bottombar a:active{color:var(--blue)}
.btn-mobile{border-radius:10px;padding:14px;font-weight:600;font-size:15px}
.form-control-mobile{border-radius:10px;padding:12px 14px;font-size:15px;border:1.5px solid var(--border)}
.form-control-mobile:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.15)}
.badge-mobile{padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500}
.alert-mobile{border-radius:10px;padding:12px 14px;font-size:14px}
.empty-state{padding:40px 20px;text-align:center;color:var(--text-muted)}
.empty-state i{font-size:48px;margin-bottom:12px;opacity:.4}
.loading{text-align:center;padding:40px;color:var(--text-muted)}
.loading i{font-size:32px;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
@media(prefers-color-scheme:dark){:root{--body-bg:#0f172a;--card-bg:#1e293b;--text:#e2e8f0;--text-muted:#94a3b8;--border:#334155}.bottombar{background:#1e293b;border-top-color:#334155}.card{background:var(--card-bg)}.list-item{border-bottom-color:var(--border)}.list-item:active{background:#334155}.topbar{background:#020617}.form-control-mobile{background:#334155;border-color:#475569;color:#fff}.form-control-mobile:focus{background:#334155}}
</style>
</head>
<body>

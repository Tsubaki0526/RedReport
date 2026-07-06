<?php
require_once __DIR__ . '/app/config/config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$url = APP_URL;
$id_rol = $id_rol ?? 0;

// Determinar el módulo actual basado en la URL
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];
// Extraer la parte relativa después del nombre del script base
$relPath = str_replace('/RedReport/', '', $requestUri);
$relPath = strtok($relPath, '?'); // quitar query string
$parts = explode('/', trim($relPath, '/'));
$currentModule = $parts[0] ?? '';
// Si es la raíz, es dashboard
if ($currentModule === '' || $currentModule === 'index.php') {
    $currentModule = 'dashboard';
}
// Módulos que no tienen submenú (direct-link)
$directModules = ['ordenes', 'tickets', 'informes', 'monitoreo'];
// Módulos con submenú
$collapseModules = [
    'clientes'      => 'menuClientes',
    'ventas'        => 'menuVentas',
    'facturacion'   => 'menuFacturacion',
    'instalaciones' => 'menuInstalaciones',
    'inventario'    => 'menuInventario',
    'mapa'          => 'menuMapa',
    'usuarios'      => 'menuUsuarios',
    'auditoria'     => 'menuUsuarios',
    'configuracion' => 'menuConfig',
    'backup'        => 'menuConfig',
    'api'           => 'menuConfig',
];
$activeCollapse = '';
foreach ($collapseModules as $prefix => $menuId) {
    if (strpos($relPath, $prefix) === 0) {
        $activeCollapse = $menuId;
        break;
    }
}
function isActive($prefix) {
    global $currentModule;
    return $currentModule === $prefix ? 'active' : '';
}
function isCollapseShow($menuId) {
    global $activeCollapse;
    return $activeCollapse === $menuId ? 'show' : '';
}
function isAriaExpanded($menuId) {
    global $activeCollapse;
    return $activeCollapse === $menuId ? 'true' : 'false';
}
function navActive($prefix) {
    global $currentModule, $directModules;
    if (in_array($prefix, $directModules)) {
        return $currentModule === $prefix ? 'active' : '';
    }
    // Para collapses, el active se añade si el submenú está abierto
    global $collapseModules;
    $menuId = $collapseModules[$prefix] ?? '';
    global $activeCollapse;
    return $activeCollapse === $menuId ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= APP_NAME ?></title>

  <!-- Favicon -->
  <link rel="icon" href="<?= $url ?>public/img/favicon.png" type="image/png">

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- DataTables BS5 -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

  <!-- RedReport CSS -->
  <link rel="stylesheet" href="<?= $url ?>public/css/redreport.css">

  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- Sidebar overlay (mobile) -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- Main Sidebar -->
  <aside class="main-sidebar" id="mainSidebar">
    <div class="sidebar">

      <!-- Brand -->
      <a href="<?= $url ?>index.php" class="brand-link">
        <img src="<?= $url ?>public/img/favicon.png" alt="Logo">
        <span class="fw-bold fs-5"><?= APP_NAME ?></span>
      </a>

      <!-- Menu -->
      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column">

          <!-- Clientes -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('clientes') ?>" data-bs-toggle="collapse" data-bs-target="#menuClientes" aria-expanded="<?= isAriaExpanded('menuClientes') ?>">
              <i class="fas fa-users nav-icon"></i>
              <span>Clientes</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuClientes') ?>" id="menuClientes">
              <li class="nav-item">
                <a href="<?= $url ?>clientes/vistas/lista.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado De Clientes
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>clientes/vistas/registrar.php" class="nav-link">
                  <i class="fas fa-user-plus"></i> Registro De Clientes
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>clientes/importar.php" class="nav-link">
                  <i class="fas fa-file-csv"></i> Importar CSV
                </a>
              </li>
            </ul>
          </li>

          <?php if ($id_rol != 3): ?>
          <!-- Ventas -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('ventas') ?>" data-bs-toggle="collapse" data-bs-target="#menuVentas" aria-expanded="<?= isAriaExpanded('menuVentas') ?>">
              <i class="fas fa-chart-line nav-icon"></i>
              <span>Ventas</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuVentas') ?>" id="menuVentas">
              <li class="nav-item">
                <a href="<?= $url ?>ventas/index.php" class="nav-link">
                  <i class="fas fa-chart-pie"></i> Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>ventas/planes.php" class="nav-link">
                  <i class="fas fa-tags"></i> Planes
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>ventas/contratos.php" class="nav-link">
                  <i class="fas fa-file-contract"></i> Contratos
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>ventas/ventas.php" class="nav-link">
                  <i class="fas fa-cart-plus"></i> Ventas
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

          <!-- Facturacion -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('facturacion') ?>" data-bs-toggle="collapse" data-bs-target="#menuFacturacion" aria-expanded="<?= isAriaExpanded('menuFacturacion') ?>">
              <i class="fas fa-file-invoice nav-icon"></i>
              <span>Facturacion</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuFacturacion') ?>" id="menuFacturacion">
              <li class="nav-item">
                <a href="<?= $url ?>facturacion/index.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado de Facturas
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>facturacion/crear.php" class="nav-link">
                  <i class="fas fa-plus"></i> Crear Factura
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>facturacion/recurrente.php" class="nav-link">
                  <i class="fas fa-sync-alt"></i> Facturación recurrente
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>facturacion/cartera.php" class="nav-link">
                  <i class="fas fa-exclamation-triangle"></i> Cartera
                </a>
              </li>
            </ul>
          </li>

          <!-- Ordenes de servicio -->
          <li class="nav-item">
            <a href="<?= $url ?>ordenes/index.php" class="nav-link <?= isActive('ordenes') ?>">
              <i class="fas fa-clipboard nav-icon"></i>
              <span>Ordenes de servicio</span>
            </a>
          </li>

          <!-- Tickets de soporte -->
          <li class="nav-item">
            <a href="<?= $url ?>tickets/index.php" class="nav-link <?= isActive('tickets') ?>">
              <i class="fas fa-headset nav-icon"></i>
              <span>Tickets de soporte</span>
            </a>
          </li>

          <?php if ($id_rol != 2): ?>
          <!-- Instalaciones -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('instalaciones') ?>" data-bs-toggle="collapse" data-bs-target="#menuInstalaciones" aria-expanded="<?= isAriaExpanded('menuInstalaciones') ?>">
              <i class="fas fa-hard-hat nav-icon"></i>
              <span>Instalaciones</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuInstalaciones') ?>" id="menuInstalaciones">
              <li class="nav-item">
                <a href="<?= $url ?>instalaciones/index.php" class="nav-link">
                  <i class="fas fa-list"></i> Gestionar Instalaciones
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

          <?php if ($id_rol != 3): ?>
          <!-- Inventario -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('inventario') ?>" data-bs-toggle="collapse" data-bs-target="#menuInventario" aria-expanded="<?= isAriaExpanded('menuInventario') ?>">
              <i class="fas fa-boxes nav-icon"></i>
              <span>Inventario de Equipos</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuInventario') ?>" id="menuInventario">
              <li class="nav-item">
                <a href="<?= $url ?>inventario/index.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado de Equipos
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>inventario/registrar.php" class="nav-link">
                  <i class="fas fa-plus"></i> Registrar Equipo
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

          <!-- Mapa de Cobertura -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('mapa') ?>" data-bs-toggle="collapse" data-bs-target="#menuMapa" aria-expanded="<?= isAriaExpanded('menuMapa') ?>">
              <i class="fas fa-map-marked-alt nav-icon"></i>
              <span>Mapa de Cobertura</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuMapa') ?>" id="menuMapa">
              <li class="nav-item">
                <a href="<?= $url ?>mapa/index.php" class="nav-link">
                  <i class="fas fa-map"></i> Ver Mapa
                </a>
              </li>
              <?php if ($id_rol == 1): ?>
              <li class="nav-item">
                <a href="<?= $url ?>mapa/admin.php" class="nav-link">
                  <i class="fas fa-cog"></i> Administrar Zonas
                </a>
              </li>
              <?php endif; ?>
            </ul>
          </li>

          <!-- Monitoreo SNMP -->
          <li class="nav-item">
            <a href="<?= $url ?>monitoreo/index.php" class="nav-link <?= isActive('monitoreo') ?>">
              <i class="fas fa-network-wired nav-icon"></i>
              <span>Monitoreo SNMP</span>
            </a>
          </li>

          <!-- Informes -->
          <li class="nav-item">
            <a href="<?= $url ?>informes/index.php" class="nav-link <?= isActive('informes') ?>">
              <i class="fas fa-chart-bar nav-icon"></i>
              <span>Informes</span>
            </a>
          </li>

          <!-- App Móvil -->
          <li class="nav-item">
            <a href="<?= $url ?>movil/" class="nav-link" target="_blank">
              <i class="fas fa-mobile-alt nav-icon"></i>
              <span>App Móvil</span>
            </a>
          </li>

          <li class="nav-item"><hr class="sidebar-divider"></li>

          <!-- Usuarios y Seguridad -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('usuarios') ?>" data-bs-toggle="collapse" data-bs-target="#menuUsuarios" aria-expanded="<?= isAriaExpanded('menuUsuarios') ?>">
              <i class="fas fa-shield-alt nav-icon"></i>
              <span>Usuarios y Seguridad</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuUsuarios') ?>" id="menuUsuarios">
              <li class="nav-item">
                <a href="<?= $url ?>usuarios/lista.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado De Usuarios
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>usuarios/crear.php" class="nav-link">
                  <i class="fas fa-user-plus"></i> Registro De Usuarios
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>auditoria/index.php" class="nav-link">
                  <i class="fas fa-history"></i> Auditoría
                </a>
              </li>
            </ul>
          </li>

          <?php if ($id_rol == 1): ?>
          <!-- Configuracion -->
          <li class="nav-item">
            <a href="#" class="nav-link <?= navActive('configuracion') ?>" data-bs-toggle="collapse" data-bs-target="#menuConfig" aria-expanded="<?= isAriaExpanded('menuConfig') ?>">
              <i class="fas fa-cog nav-icon"></i>
              <span>Configuracion</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse <?= isCollapseShow('menuConfig') ?>" id="menuConfig">
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/index.php" class="nav-link">
                  <i class="fas fa-sliders-h"></i> Sistema
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/empresa.php" class="nav-link">
                  <i class="fas fa-building"></i> Datos de la Empresa
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/permisos.php" class="nav-link">
                  <i class="fas fa-shield-alt"></i> Permisos por rol
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>backup/index.php" class="nav-link">
                  <i class="fas fa-database"></i> Backup BD
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/plantillas_email.php" class="nav-link">
                  <i class="fas fa-envelope"></i> Plantillas Email
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/cola_email.php" class="nav-link">
                  <i class="fas fa-envelope-open-text"></i> Cola de Correos
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/health.php" class="nav-link">
                  <i class="fas fa-heartbeat"></i> Health Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/mantenimiento_bd.php" class="nav-link">
                  <i class="fas fa-database"></i> Mantenimiento BD
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>configuracion/logs.php" class="nav-link">
                  <i class="fas fa-exclamation-triangle"></i> Visor de Logs
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>api/documentacion.php" class="nav-link">
                  <i class="fas fa-code"></i> API Docs
                </a>
              </li>
            </ul>
          </li>
          <?php endif; ?>

        </ul>
      </nav>

      <!-- Logout -->
      <div class="mt-auto p-3">
        <button id="btnLogout" class="btn btn-danger w-100 btn-sm">
          <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </button>
      </div>

    </div>
  </aside>

  <!-- Content area -->
  <div class="content-wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white bg-white">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#" id="sidebarToggle" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="nav-item d-none d-md-block">
          <a class="nav-link" href="#" id="globalSearchBtn" role="button" title="Buscar (Ctrl+K)">
            <i class="fas fa-search"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link position-relative" href="<?= $url ?>notificaciones/index.php" title="Notificaciones">
            <i class="fas fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notif-badge" style="font-size:9px;display:none;">0</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle"></i> <span class="d-none d-sm-inline"><?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuario') ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= $url ?>usuarios/perfil.php"><i class="fas fa-id-card me-2"></i>Mi Perfil</a></li>
            <?php if ($id_rol == 1): ?>
            <li><a class="dropdown-item" href="<?= $url ?>configuracion/index.php"><i class="fas fa-cog me-2"></i>Configuración</a></li>
            <?php endif; ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" id="btnLogoutNav"><i class="fas fa-sign-out-alt me-2"></i>Cerrar sesión</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-bs-toggle="fullscreen">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
      </ul>
    </nav>

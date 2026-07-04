<?php
require_once __DIR__ . '/../app/config/config.php';
$url = APP_URL;
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

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <aside class="main-sidebar" id="mainSidebar">
    <div class="sidebar">

      <a href="<?= $url ?>gestion_soporte/index.php" class="brand-link">
        <img src="<?= $url ?>public/img/favicon.png" alt="Logo">
        <span class="fw-bold fs-5"><?= APP_NAME ?></span>
      </a>

      <nav class="mt-2">
        <ul class="nav nav-sidebar flex-column">

          <li class="nav-item">
            <a href="#" class="nav-link active" data-bs-toggle="collapse" data-bs-target="#menuReportesG" aria-expanded="false">
              <i class="fas fa-file-alt nav-icon"></i>
              <span>Reportes</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse" id="menuReportesG">
              <li class="nav-item">
                <a href="<?= $url ?>gestion_soporte/vistas/lista_gestion.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado De Reportes
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>gestion_soporte/vistas/registrar_reporte.php" class="nav-link">
                  <i class="fas fa-user-plus"></i> Registro De Reportes
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link active" data-bs-toggle="collapse" data-bs-target="#menuClaroG" aria-expanded="false">
              <i class="fas fa-file-alt nav-icon"></i>
              <span>Reportes C/A/D/L</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse" id="menuClaroG">
              <li class="nav-item">
                <a href="<?= $url ?>claro_azteka_dialnet_cw/vistas/lista_claro.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado Claro
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>claro_azteka_dialnet_cw/vistas_azteka/lista_azteka.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado Azteca
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>claro_azteka_dialnet_cw/vistas_dialnet/lista_dialnet.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado Dialnet
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>claro_azteka_dialnet_cw/vistas_liberty/lista_liberty.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado Liberty
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>claro_azteka_dialnet_cw/vistas/registrar_daño.php" class="nav-link">
                  <i class="fas fa-user-plus"></i> Registro De Reportes
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="#" class="nav-link active" data-bs-toggle="collapse" data-bs-target="#menuInformesG" aria-expanded="false">
              <i class="fas fa-file-alt nav-icon"></i>
              <span>Informes</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse" id="menuInformesG">
              <li class="nav-item">
                <a href="<?= $url ?>informes/vistas/informe_reportes.php" class="nav-link">
                  <i class="fas fa-list"></i> Listado De Informes
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>informes/vistas/informe_claro.php" class="nav-link">
                  <i class="fas fa-list"></i> Informes Claro
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>informes/vistas/informe_azteca.php" class="nav-link">
                  <i class="fas fa-list"></i> Informes Azteca
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>informes/vistas/informe_dialnet.php" class="nav-link">
                  <i class="fas fa-list"></i> Informes Dialnet
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= $url ?>informes/vistas/informe_liberty.php" class="nav-link">
                  <i class="fas fa-list"></i> Informes Liberty
                </a>
              </li>
            </ul>
          </li>

          <!-- Mapa de Cobertura -->
          <li class="nav-item">
            <a href="#" class="nav-link active" data-bs-toggle="collapse" data-bs-target="#menuMapaG" aria-expanded="false">
              <i class="fas fa-map-marked-alt nav-icon"></i>
              <span>Mapa de Cobertura</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse" id="menuMapaG">
              <li class="nav-item">
                <a href="<?= $url ?>mapa/index.php" class="nav-link">
                  <i class="fas fa-map"></i> Ver Mapa
                </a>
              </li>
            </ul>
          </li>

          <!-- Inventario -->
          <li class="nav-item">
            <a href="#" class="nav-link active" data-bs-toggle="collapse" data-bs-target="#menuInventarioG" aria-expanded="false">
              <i class="fas fa-boxes nav-icon"></i>
              <span>Inventario de Equipos</span>
              <i class="fas fa-angle-right right ms-auto"></i>
            </a>
            <ul class="nav nav-treeview collapse" id="menuInventarioG">
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

        </ul>
      </nav>

      <div class="mt-auto p-3">
        <button id="btnLogout" class="btn btn-danger w-100 btn-sm">
          <i class="fas fa-sign-out-alt"></i> Cerrar sesion
        </button>
      </div>

    </div>
  </aside>

  <div class="content-wrapper">

    <nav class="main-header navbar navbar-expand navbar-white bg-white">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="#" id="sidebarToggle" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle"></i> <span class="d-none d-sm-inline"><?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuario') ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= $url ?>usuarios/perfil.php"><i class="fas fa-id-card me-2"></i>Mi Perfil</a></li>
            <?php if ($_SESSION['id_rol'] == 1): ?>
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

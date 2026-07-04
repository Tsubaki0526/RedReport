<?php
include('sesion.php');
include('parte1.php');
include('consultas.php');

// Additional stats
$dialnet = $pdo->query("SELECT COUNT(*) AS total FROM tb_dialnet")->fetch(PDO::FETCH_ASSOC)['total'];
$liberty = $pdo->query("SELECT COUNT(*) AS total FROM tb_liberty")->fetch(PDO::FETCH_ASSOC)['total'];
$instalaciones_pendientes = $pdo->query("SELECT COUNT(*) AS total FROM tb_clientes WHERE fecha_instalacion IS NULL")->fetch(PDO::FETCH_ASSOC)['total'];
$instalaciones_completadas = $pdo->query("SELECT COUNT(*) AS total FROM tb_clientes WHERE fecha_instalacion IS NOT NULL")->fetch(PDO::FETCH_ASSOC)['total'];
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Dashboard</h1>
        </div>
        <div class="col-sm-6 text-end">
          <span id="fechaHora" class="text-muted" style="font-size:0.9rem;"></span>
        </div>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">
      <!-- Welcome -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-0 bg-white">
            <div class="card-body d-flex align-items-center gap-3 py-3">
              <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                <i class="fas fa-wave-square text-primary fs-4"></i>
              </div>
              <div>
                <h5 class="mb-0 fw-bold">Bienvenido de nuevo, <?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuario') ?></h5>
                <p class="text-muted mb-0 small">Sistema de gestion ISP — RedReport v2.0</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="row">
        <div class="col-lg-3 col-md-6">
          <div class="stat-card blue">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div>
              <div class="stat-value"><?= $totalClientes ?></div>
              <div class="stat-label">Clientes registrados</div>
            </div>
            <a href="clientes/vistas/lista.php" class="stat-link">Ver todos <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="stat-card green">
            <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            <div>
              <div class="stat-value"><?= $totalReportes + $totalReportes_claro + $totalReportes_azteka + $dialnet + $liberty ?></div>
              <div class="stat-label">Total reportes</div>
            </div>
            <a href="informes/vistas/informe_reportes_2.php" class="stat-link">Ver informes <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="stat-card orange">
            <div class="stat-icon"><i class="fas fa-wrench"></i></div>
            <div>
              <div class="stat-value"><?= $instalaciones_pendientes ?></div>
              <div class="stat-label">Instalaciones pendientes</div>
            </div>
            <a href="instalaciones/index.php" class="stat-link">Gestionar <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="stat-card teal">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div>
              <div class="stat-value"><?= $instalaciones_completadas ?></div>
              <div class="stat-label">Instalaciones completadas</div>
            </div>
            <a href="instalaciones/index.php" class="stat-link">Ver detalles <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Second row: report breakdown -->
      <div class="row">
        <div class="col-lg-3 col-md-6">
          <div class="stat-card purple">
            <div class="stat-icon"><i class="fas fa-bolt"></i></div>
            <div>
              <div class="stat-value"><?= $totalReportes_claro ?></div>
              <div class="stat-label">Reportes Claro</div>
            </div>
            <a href="informes/vistas/informe_claro_2.php" class="stat-link">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="stat-card red">
            <div class="stat-icon"><i class="fas fa-satellite-dish"></i></div>
            <div>
              <div class="stat-value"><?= $totalReportes_azteka ?></div>
              <div class="stat-label">Reportes Azteca</div>
            </div>
            <a href="informes/vistas/informe_azteca_2.php" class="stat-link">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="stat-card pink">
            <div class="stat-icon"><i class="fas fa-globe"></i></div>
            <div>
              <div class="stat-value"><?= $dialnet ?></div>
              <div class="stat-label">Reportes Dialnet</div>
            </div>
            <a href="informes/vistas/informe_dialnet_2.php" class="stat-link">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="stat-card indigo">
            <div class="stat-icon"><i class="fas fa-network-wired"></i></div>
            <div>
              <div class="stat-value"><?= $liberty ?></div>
              <div class="stat-label">Reportes Liberty</div>
            </div>
            <a href="informes/vistas/informe_liberty_2.php" class="stat-link">Ver <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span><i class="fas fa-chart-bar me-2 text-primary"></i>Top 5 clientes con mas reportes</span>
              <span class="badge bg-primary bg-opacity-10 text-primary"><?= date('F Y') ?></span>
            </div>
            <div class="card-body">
              <canvas id="graficoReportes" height="220"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-header"><i class="fas fa-info-circle me-2 text-info"></i>Resumen rapido</div>
            <div class="card-body p-0">
              <table class="table table-borderless mb-0">
                <tbody>
                  <tr><td class="ps-3">Clientes</td><td class="fw-bold text-end pe-3"><?= $totalClientes ?></td></tr>
                  <tr><td class="ps-3">Reportes este mes</td><td class="fw-bold text-end pe-3"><?= array_sum($reportes) ?></td></tr>
                  <tr><td class="ps-3">Instalaciones pendientes</td><td class="fw-bold text-end pe-3"><?= $instalaciones_pendientes ?></td></tr>
                  <tr><td class="ps-3">Completadas</td><td class="fw-bold text-end pe-3"><?= $instalaciones_completadas ?></td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card">
            <div class="card-body text-center py-4">
              <a href="mapa/index.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-map-marked-alt me-1"></i>Ver mapa de cobertura</a>
              <a href="inventario/index.php" class="btn btn-outline-success btn-sm ms-1"><i class="fas fa-boxes me-1"></i>Inventario</a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include('parte2.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("graficoReportes");
  if (!ctx) return;

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: <?= json_encode($clientes) ?>,
      datasets: [{
        label: "Reportes en el mes",
        data: <?= json_encode($reportes) ?>,
        backgroundColor: [
          'rgba(37, 99, 235, 0.7)',
          'rgba(22, 163, 74, 0.7)',
          'rgba(245, 158, 11, 0.7)',
          'rgba(239, 68, 68, 0.7)',
          'rgba(139, 92, 246, 0.7)'
        ],
        borderColor: [
          '#2563eb', '#16a34a', '#f59e0b', '#ef4444', '#8b5cf6'
        ],
        borderWidth: 0,
        borderRadius: 6,
        barPercentage: 0.6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision: 0, font: { size: 12 } },
          grid: { color: '#f1f5f9' }
        },
        x: {
          grid: { display: false },
          ticks: { font: { size: 11 } }
        }
      }
    }
  });
});
</script>

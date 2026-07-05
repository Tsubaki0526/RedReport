<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

$tipo_filtro = $_GET['tipo'] ?? '';
$estado_filtro = $_GET['estado'] ?? '';

$sql = "SELECT e.*, t.nombre AS tipo_equipo, c.nombre AS cliente_nombre
        FROM tb_equipos e
        INNER JOIN tb_tipos_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo
        LEFT JOIN tb_clientes c ON e.id_cliente = c.id_cliente
        WHERE 1=1";
$params = [];
if ($tipo_filtro) { $sql .= " AND e.id_tipo_equipo = :tipo"; $params[':tipo'] = $tipo_filtro; }
if ($estado_filtro) { $sql .= " AND e.estado = :estado"; $params[':estado'] = $estado_filtro; }
$sql .= " ORDER BY e.fecha_registro DESC";
$equipos = $pdo->prepare($sql);
$equipos->execute($params);
$equipos = $equipos->fetchAll();

$tipos = $pdo->query("SELECT * FROM tb_tipos_equipo")->fetchAll();

// Stock minimo alertas
$stock_bajo = $pdo->query("SELECT t.nombre, COUNT(e.id_equipo) AS disponibles, MIN(e.stock_minimo) AS minimo
    FROM tb_tipos_equipo t
    LEFT JOIN tb_equipos e ON e.id_tipo_equipo = t.id_tipo_equipo AND e.estado = 'Disponible'
    GROUP BY t.id_tipo_equipo
    HAVING disponibles < minimo OR (minimo > 0 AND disponibles IS NULL)")->fetchAll();
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Inventario de Equipos</h1></div>
                <div class="col-sm-6 text-end"><span id="fechaHora" class="text-muted"></span></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Listado de equipos</h3>
                    <a href="registrar.php" class="btn btn-sm btn-success ms-auto"><i class="fas fa-plus"></i> Nuevo equipo</a>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3">
                        <div class="col-auto">
                            <select name="tipo" class="form-select form-select-sm">
                                <option value="">Todos los tipos</option>
                                <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t['id_tipo_equipo'] ?>" <?= $tipo_filtro == $t['id_tipo_equipo'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-auto">
                            <select name="estado" class="form-select form-select-sm">
                                <option value="">Todos los estados</option>
                                <option value="Disponible" <?= $estado_filtro == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                                <option value="Asignado" <?= $estado_filtro == 'Asignado' ? 'selected' : '' ?>>Asignado</option>
                                <option value="Dañado" <?= $estado_filtro == 'Dañado' ? 'selected' : '' ?>>Dañado</option>
                                <option value="Garantia" <?= $estado_filtro == 'Garantia' ? 'selected' : '' ?>>Garantía</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                        </div>
                    </form>
                    <?php if (!empty($stock_bajo)): ?>
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i><strong>Stock bajo:</strong>
                        <?php foreach ($stock_bajo as $s): ?><span class="badge bg-warning text-dark me-2"><?= hescape($s['nombre']) ?>: <?= intval($s['disponibles']) ?> disp. (mín: <?= intval($s['minimo']) ?>)</span><?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="table-container">
                    <table id="tablaEquipos" class="table table-bordered table-striped table-sm">
                        <thead><tr><th>Serial</th><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Estado</th><th>Cliente</th><th>Registro</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach ($equipos as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['serial']) ?></td>
                                <td><?= htmlspecialchars($e['tipo_equipo']) ?></td>
                                <td><?= htmlspecialchars($e['marca'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($e['modelo'] ?? '-') ?></td>
                                <td><span class="badge bg-<?= $e['estado'] == 'Disponible' ? 'success' : ($e['estado'] == 'Asignado' ? 'info' : ($e['estado'] == 'Dañado' ? 'danger' : 'warning')) ?>"><?= $e['estado'] ?></span></td>
                                <td><?= htmlspecialchars($e['cliente_nombre'] ?? '-') ?></td>
                                <td><?= $e['fecha_registro'] ?></td>
                                <td>
                                    <?php if ($e['estado'] == 'Asignado'): ?>
                                    <form method="POST" action="controles/devolver_equipo.php" class="d-inline" onsubmit="return confirm('Devolver este equipo a inventario?')">
                                        <?php require_once('../app/config/seguridad.php'); echo csrf_field(); ?>
                                        <input type="hidden" name="id_equipo" value="<?= $e['id_equipo'] ?>">
                                        <button type="submit" class="btn btn-sm btn-warning" title="Devolver a inventario"><i class="fas fa-undo"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../parte2.php'); ?>
<script>$(function(){$('#tablaEquipos').DataTable({responsive:true,autoWidth:false,language:{url:'//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json'},columnDefs:[{orderable:false,targets:-1}]});});</script>

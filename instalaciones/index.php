<?php
include('../sesion.php');
include('../parte1.php');
require_once('../app/config/conexion.php');
require_once('../app/config/seguridad.php');

$es_instalador = ($_SESSION['id_rol'] == 3);
$es_admin = ($_SESSION['id_rol'] == 1);

$sql = "SELECT c.id_cliente, c.nombre, c.direccion, c.telefono, c.lat, c.lng,
               c.fecha_instalacion, c.id_instalador,
               u.nombre AS instalador_nombre,
               co.id_contrato, p.nombre AS plan_nombre
        FROM tb_clientes c
        LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario
        LEFT JOIN tb_contratos co ON co.id_cliente = c.id_cliente AND co.estado = 'activo'
        LEFT JOIN tb_planes p ON co.id_plan = p.id_plan
        WHERE 1=1";
$params = [];
if ($es_instalador) {
    $sql .= " AND c.id_instalador = :id";
    $params[':id'] = $_SESSION['id_usuario'];
}
if (!$es_admin && !$es_instalador) {
    $sql .= " AND 1=0";
}
$sql .= " ORDER BY c.fecha_instalacion IS NULL DESC, c.nombre ASC";
$clientes = $pdo->prepare($sql);
$clientes->execute($params);
$clientes = $clientes->fetchAll();

// Get equipment types for assignment form
$tipos = $pdo->query("SELECT * FROM tb_tipos_equipo")->fetchAll();
// Get installers for admin
$instaladores = [];
if ($es_admin) {
    $instaladores = $pdo->query("SELECT id_usuario, nombre FROM tb_usuarios WHERE id_rol = 3 OR id_rol = 1 ORDER BY nombre")->fetchAll();
}
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Instalaciones</h1></div>
                <div class="col-sm-6 text-end"><span id="fechaHora" class="text-muted"></span></div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $es_instalador ? 'Mis instalaciones' : 'Listado de instalaciones' ?></h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                    <table id="tablaInst" class="table table-bordered table-striped table-sm">
                        <thead><tr><th>Cliente</th><th>Dirección</th><th>Teléfono</th><th>Plan</th><th>Instalador</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach ($clientes as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['nombre']) ?></td>
                                <td><?= htmlspecialchars($c['direccion'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['plan_nombre'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($c['instalador_nombre'] ?? 'Sin asignar') ?></td>
                                <td>
                                    <?php if ($c['fecha_instalacion']): ?>
                                        <span class="badge bg-success">Instalado <?= date('d/m/Y', strtotime($c['fecha_instalacion'])) ?></span>
                                    <?php elseif ($c['id_instalador']): ?>
                                        <span class="badge bg-warning text-dark">Asignado</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($es_admin && !$c['id_instalador']): ?>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#asignarModal" data-cliente-id="<?= $c['id_cliente'] ?>" data-cliente-nombre="<?= htmlspecialchars($c['nombre']) ?>">
                                        <i class="fas fa-user-plus"></i> Asignar
                                    </button>
                                    <?php endif; ?>
                                    <?php if ($es_instalador && $c['id_instalador'] == $_SESSION['id_usuario'] && !$c['fecha_instalacion']): ?>
                                    <a href="realizar.php?id=<?= $c['id_cliente'] ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-tools"></i> Realizar instalación
                                    </a>
                                    <?php endif; ?>
                                    <a href="../mapa/index.php?cliente=<?= $c['id_cliente'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </a>
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

<!-- Assign Modal -->
<?php if ($es_admin): ?>
<div class="modal fade" id="asignarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="controles/asignar_instalador.php" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header"><h5 class="modal-title">Asignar instalador</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="id_cliente" id="asignarClienteId">
                    <p>Cliente: <strong id="asignarClienteNombre"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Instalador</label>
                        <select name="id_instalador" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($instaladores as $inst): ?>
                            <option value="<?= $inst['id_usuario'] ?>"><?= htmlspecialchars($inst['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Asignar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include('../parte2.php'); ?>
<script>
$(function(){$('#tablaInst').DataTable({responsive:true,autoWidth:false,language:{url:'//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json'},columnDefs:[{orderable:false,targets:6}]});});
<?php if ($es_admin): ?>
var modal = document.getElementById('asignarModal');
modal.addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    document.getElementById('asignarClienteId').value = btn.dataset.clienteId;
    document.getElementById('asignarClienteNombre').textContent = btn.dataset.clienteNombre;
});
<?php endif; ?>
</script>

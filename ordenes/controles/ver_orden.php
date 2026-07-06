<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';
verificar_acceso([1, 2, 3]);

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT o.*, c.nombre AS cliente_nombre, c.documento, c.telefono, c.direccion, c.email,
                        u.nombre AS tecnico_nombre FROM tb_ordenes o
                        LEFT JOIN tb_clientes c ON o.id_cliente = c.id_cliente
                        LEFT JOIN tb_usuarios u ON o.id_tecnico = u.id_usuario
                        WHERE o.id_orden = ?");
$stmt->execute([$id]);
$o = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$o) { echo '<p class="text-muted">Orden no encontrada</p>'; exit; }

$estados = ['Abierta' => 'warning', 'En Proceso' => 'info', 'Completada' => 'success', 'Cancelada' => 'secondary'];
$prioridades = ['Baja' => 'success', 'Media' => 'warning', 'Alta' => 'danger', 'Urgente' => 'danger'];
?>
<div class="table-responsive">
    <table class="table table-sm">
        <tr><td><strong>Orden:</strong></td><td><?= hescape($o['numero_orden']) ?></td></tr>
        <tr><td><strong>Cliente:</strong></td><td><?= hescape($o['cliente_nombre'] ?? '-') ?> (<?= hescape($o['documento'] ?? '') ?>)</td></tr>
        <tr><td><strong>Telefono:</strong></td><td><?= hescape($o['telefono'] ?? '-') ?></td></tr>
        <tr><td><strong>Direccion:</strong></td><td><?= hescape($o['direccion'] ?? '-') ?></td></tr>
        <tr><td><strong>Tipo:</strong></td><td><?= hescape($o['tipo']) ?></td></tr>
        <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-<?= $prioridades[$o['prioridad']] ?? 'secondary' ?>"><?= $o['prioridad'] ?></span></td></tr>
        <tr><td><strong>Estado:</strong></td><td><span class="badge bg-<?= $estados[$o['estado']] ?? 'secondary' ?>"><?= $o['estado'] ?></span></td></tr>
        <tr><td><strong>Tecnico:</strong></td><td><?= hescape($o['tecnico_nombre'] ?? 'Sin asignar') ?></td></tr>
        <tr><td><strong>Descripcion:</strong></td><td><?= nl2br(hescape($o['descripcion'] ?? '')) ?></td></tr>
        <?php if ($o['solucion']): ?><tr><td><strong>Solucion:</strong></td><td><?= nl2br(hescape($o['solucion'])) ?></td></tr><?php endif; ?>
        <tr><td><strong>Creada:</strong></td><td><?= date('d/m/Y H:i', strtotime($o['fecha_creacion'])) ?></td></tr>
        <?php if ($o['fecha_completada']): ?><tr><td><strong>Completada:</strong></td><td><?= date('d/m/Y H:i', strtotime($o['fecha_completada'])) ?></td></tr><?php endif; ?>
    </table>
</div>

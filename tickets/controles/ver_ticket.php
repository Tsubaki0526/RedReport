<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT t.*, c.nombre AS cliente_nombre, c.documento, c.telefono, c.direccion, c.email,
    u.nombre AS usuario_nombre FROM tb_tickets t
    LEFT JOIN tb_clientes c ON t.id_cliente = c.id_cliente
    LEFT JOIN tb_usuarios u ON t.id_usuario = u.id_usuario
    WHERE t.id_ticket = ?");
$stmt->execute([$id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$t) { echo '<p class="text-muted">Ticket no encontrado</p>'; exit; }

$estados = ['Abierto' => 'warning', 'En Proceso' => 'info', 'Resuelto' => 'success', 'Cerrado' => 'secondary'];
$prioridades = ['Baja' => 'success', 'Media' => 'warning', 'Alta' => 'danger', 'Urgente' => 'danger'];
?>
<div class="table-responsive">
    <table class="table table-sm">
        <tr><td><strong>Ticket:</strong></td><td><?= hescape($t['numero_ticket']) ?></td></tr>
        <tr><td><strong>Cliente:</strong></td><td><?= hescape($t['cliente_nombre'] ?? '-') ?> (<?= hescape($t['documento'] ?? '') ?>)</td></tr>
        <tr><td><strong>Telefono:</strong></td><td><?= hescape($t['telefono'] ?? '-') ?></td></tr>
        <tr><td><strong>Asunto:</strong></td><td><strong><?= hescape($t['asunto']) ?></strong></td></tr>
        <tr><td><strong>Categoria:</strong></td><td><?= hescape($t['categoria']) ?></td></tr>
        <tr><td><strong>Prioridad:</strong></td><td><span class="badge bg-<?= $prioridades[$t['prioridad']] ?? 'secondary' ?>"><?= $t['prioridad'] ?></span></td></tr>
        <tr><td><strong>Estado:</strong></td><td><span class="badge bg-<?= $estados[$t['estado']] ?? 'secondary' ?>"><?= $t['estado'] ?></span></td></tr>
        <tr><td><strong>Asignado a:</strong></td><td><?= hescape($t['usuario_nombre'] ?? 'Sin asignar') ?></td></tr>
        <tr><td><strong>Descripcion:</strong></td><td><?= nl2br(hescape($t['descripcion'] ?? '')) ?></td></tr>
        <?php if ($t['solucion']): ?><tr><td><strong>Solucion:</strong></td><td><?= nl2br(hescape($t['solucion'])) ?></td></tr><?php endif; ?>
        <tr><td><strong>Creado:</strong></td><td><?= date('d/m/Y H:i', strtotime($t['fecha_creacion'])) ?></td></tr>
        <?php if ($t['fecha_resolucion']): ?><tr><td><strong>Resuelto:</strong></td><td><?= date('d/m/Y H:i', strtotime($t['fecha_resolucion'])) ?></td></tr><?php endif; ?>
    </table>
</div>

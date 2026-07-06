<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

verificar_acceso([1, 2]);

if (!csrf_verify($_POST['_csrf_token'] ?? '')) { echo json_encode(['success' => false, 'error' => 'CSRF']); exit; }

$id_usuario_sesion = $_SESSION['id_usuario'] ?? 0;
$id_ticket = intval($_POST['id_ticket'] ?? 0);
$estado = $_POST['estado'] ?? '';
$solucion = trim($_POST['solucion'] ?? '');
$id_usuario_asignar = intval($_POST['id_usuario'] ?? 0) ?: null;

if ($id_ticket <= 0 || !in_array($estado, ['Abierto','En Proceso','Resuelto','Cerrado'])) { header('Location: ../index.php'); exit; }

try {
    if ($estado == 'Resuelto') {
        $stmt = $pdo->prepare("UPDATE tb_tickets SET estado = ?, solucion = ?, fecha_resolucion = NOW() WHERE id_ticket = ?");
        $stmt->execute([$estado, $solucion, $id_ticket]);
    } elseif ($estado == 'En Proceso' && $id_usuario_asignar) {
        $stmt = $pdo->prepare("UPDATE tb_tickets SET estado = ?, id_usuario = ?, fecha_asignacion = NOW() WHERE id_ticket = ?");
        $stmt->execute([$estado, $id_usuario_asignar, $id_ticket]);
    } else {
        $stmt = $pdo->prepare("UPDATE tb_tickets SET estado = ? WHERE id_ticket = ?");
        $stmt->execute([$estado, $id_ticket]);
    }

    bitacora($pdo, $id_usuario_sesion, 'ESTADO_TICKET', 'tb_tickets', $id_ticket, "Ticket #$id_ticket cambio a $estado");

    $stmt = $pdo->prepare("SELECT t.numero_ticket FROM tb_tickets t WHERE t.id_ticket = ?");
    $stmt->execute([$id_ticket]);
    $t = $stmt->fetch();

    require_once __DIR__ . '/../../notificaciones/controles/crear_notificacion.php';
    crear_notificacion($pdo, $estado == 'Resuelto' ? 'success' : ($estado == 'Cerrado' ? 'secondary' : 'info'),
        "Ticket $t[numero_ticket]: $estado",
        "El ticket de soporte $t[numero_ticket] cambio a estado: $estado.",
        APP_URL . '/tickets/index.php');

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("cambiar_estado_ticket error: " . $e->getMessage());
    echo json_encode(['success' => false]);
}

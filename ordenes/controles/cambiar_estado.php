<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

verificar_acceso([1, 2, 3]);

if (!csrf_verify($_POST['_csrf_token'] ?? '')) { echo json_encode(['success' => false, 'error' => 'CSRF']); exit; }

$id_usuario = $_SESSION['id_usuario'] ?? 0;
$id_orden = intval($_POST['id_orden'] ?? 0);
$estado = $_POST['estado'] ?? '';
$solucion = trim($_POST['solucion'] ?? '');

if ($id_orden <= 0 || !in_array($estado, ['Abierta','En Proceso','Completada','Cancelada'])) { header('Location: ../index.php'); exit; }

try {
    if ($estado == 'Completada') {
        $stmt = $pdo->prepare("UPDATE tb_ordenes SET estado = ?, solucion = ?, fecha_completada = NOW() WHERE id_orden = ?");
        $stmt->execute([$estado, $solucion, $id_orden]);
    } else {
        $stmt = $pdo->prepare("UPDATE tb_ordenes SET estado = ? WHERE id_orden = ?");
        $stmt->execute([$estado, $id_orden]);
    }

    bitacora($pdo, $id_usuario, 'ESTADO_ORDEN', 'tb_ordenes', $id_orden, "Orden #$id_orden cambio a $estado");

    $stmt = $pdo->prepare("SELECT o.numero_orden FROM tb_ordenes o WHERE o.id_orden = ?");
    $stmt->execute([$id_orden]);
    $o = $stmt->fetch();

    require_once __DIR__ . '/../../notificaciones/controles/crear_notificacion.php';
    crear_notificacion($pdo, $estado == 'Completada' ? 'success' : ($estado == 'Cancelada' ? 'danger' : 'info'),
        "Orden $o[numero_orden]: $estado",
        "La orden de servicio $o[numero_orden] cambio a estado: $estado.",
        APP_URL . '/ordenes/index.php');

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("cambiar_estado_orden error: " . $e->getMessage());
    echo json_encode(['success' => false]);
}

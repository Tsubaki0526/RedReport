<?php
function crear_notificacion($pdo, $tipo, $titulo, $mensaje, $url = null, $id_usuario = null) {
    $stmt = $pdo->prepare("INSERT INTO tb_notificaciones (id_usuario, tipo, titulo, mensaje, url) VALUES (?,?,?,?,?)");
    $stmt->execute([$id_usuario, $tipo, $titulo, $mensaje, $url]);
}

function notificar_instalacion_pendiente($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM tb_clientes WHERE id_instalador IS NOT NULL AND fecha_instalacion IS NULL");
    $pendientes = $stmt->fetchColumn();
    if ($pendientes > 0) {
        crear_notificacion($pdo, 'warning', "Instalaciones pendientes ($pendientes)",
            "Hay $pendientes cliente(s) esperando instalacion.",
            APP_URL . '/instalaciones/index.php');
    }
}

function notificar_facturas_vencidas($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM tb_facturas WHERE estado = 'vencida'");
    $vencidas = $stmt->fetchColumn();
    if ($vencidas > 0) {
        crear_notificacion($pdo, 'danger', "Facturas vencidas ($vencidas)",
            "Hay $vencidas factura(s) vencidas sin pagar.",
            APP_URL . '/facturacion/cartera.php');
    }
}

function notificar_stock_bajo($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM tb_equipos e
        INNER JOIN tb_tipos_equipo t ON e.id_tipo_equipo = t.id_tipo_equipo AND t.nombre IS NOT NULL
        WHERE e.stock_minimo > 0 AND e.estado = 'Disponible'
        GROUP BY e.id_tipo_equipo
        HAVING COUNT(*) <= MAX(e.stock_minimo)");
    $bajos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($bajos) > 0) {
        crear_notificacion($pdo, 'warning', "Stock bajo en inventario",
            "Hay equipos con stock por debajo del minimo.",
            APP_URL . '/inventario/index.php');
    }
}

function generar_notificaciones_automaticas($pdo) {
    $pdo->query("DELETE FROM tb_notificaciones WHERE fecha_creacion < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    notificar_instalacion_pendiente($pdo);
    notificar_facturas_vencidas($pdo);
    notificar_stock_bajo($pdo);
}

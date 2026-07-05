<?php
session_start();
require_once('../../app/config/conexion.php');
require_once('../../app/config/seguridad.php');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$id_cliente = (int)($_POST['id_cliente'] ?? 0);

// Permission check: only admin or assigned installer
$check = $pdo->prepare("SELECT id_instalador FROM tb_clientes WHERE id_cliente = :id");
$check->execute([':id' => $id_cliente]);
$cliente = $check->fetch();
$es_admin = $_SESSION['id_rol'] == 1;
$es_asignado = $cliente && $cliente['id_instalador'] == $_SESSION['id_usuario'];
if (!$es_admin && !$es_asignado) die("Acceso denegado");
$lat = $_POST['lat'] ?? null;
$lng = $_POST['lng'] ?? null;
$observaciones = $_POST['observaciones'] ?? '';

try {
    $pdo->beginTransaction();

    // Update client with installation data
    $sql = "UPDATE tb_clientes SET fecha_instalacion = NOW(), lat = COALESCE(:lat, lat), lng = COALESCE(:lng, lng) WHERE id_cliente = :id";
    $pdo->prepare($sql)->execute([':lat' => $lat, ':lng' => $lng, ':id' => $id_cliente]);

    // Register equipment
    $tipos = $_POST['id_tipo_equipo'] ?? [];
    $serials = $_POST['serial'] ?? [];
    $marcas = $_POST['marca'] ?? [];

    $sqlEq = "INSERT INTO tb_equipos (id_tipo_equipo, serial, marca, estado, id_cliente, fecha_asignado) VALUES (:tipo, :serial, :marca, 'Asignado', :cliente, NOW())";
    $stmtEq = $pdo->prepare($sqlEq);

    for ($i = 0; $i < count($tipos); $i++) {
        if (!empty($tipos[$i]) && !empty($serials[$i])) {
            $stmtEq->execute([
                ':tipo' => $tipos[$i],
                ':serial' => $serials[$i],
                ':marca' => $marcas[$i] ?? '',
                ':cliente' => $id_cliente
            ]);
        }
    }

    // Auto-generate first invoice from active contract
    $contrato = $pdo->prepare("
        SELECT co.id_contrato, co.id_plan, p.nombre AS plan_nombre, p.precio
        FROM tb_contratos co
        INNER JOIN tb_planes p ON co.id_plan = p.id_plan
        WHERE co.id_cliente = :cli AND co.estado = 'activo'
        ORDER BY co.id_contrato ASC LIMIT 1
    ");
    $contrato->execute([':cli' => $id_cliente]);
    $contrato = $contrato->fetch();

    if ($contrato) {
        $stmt = $pdo->query("SELECT COALESCE(MAX(id_factura), 0) + 1 AS next FROM tb_facturas");
        $next = $stmt->fetch(PDO::FETCH_ASSOC)['next'];
        $numero = 'FAC-' . str_pad($next, 5, '0', STR_PAD_LEFT);

        $subtotal = $contrato['precio'];
        $iva = round($subtotal * 0.19, 2);
        $total = round($subtotal + $iva, 2);

        $sqlF = "INSERT INTO tb_facturas (numero_factura, id_cliente, fecha_emision, fecha_vencimiento, subtotal, iva, total, notas, fecha_creacion)
                 VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), ?, ?, ?, ?, NOW())";
        $pdo->prepare($sqlF)->execute([
            $numero, $id_cliente, $subtotal, $iva, $total,
            'Factura inicial - Contrato #' . $contrato['id_contrato'] . ' (' . $contrato['plan_nombre'] . ')'
        ]);
        $id_factura = $pdo->lastInsertId();

        $sqlI = "INSERT INTO tb_factura_items (id_factura, descripcion, cantidad, precio_unitario, subtotal) VALUES (?, ?, 1, ?, ?)";
        $pdo->prepare($sqlI)->execute([$id_factura, 'Plan ' . $contrato['plan_nombre'], $subtotal, $subtotal]);

        $factura_creada = true;
    } else {
        $factura_creada = false;
    }

    $pdo->commit();
    $msg = 'Instalación completada, equipos registrados correctamente.';
    if ($factura_creada) $msg .= ' Factura inicial generada automáticamente.';
    bitacora($pdo, $_SESSION['id_usuario'], 'COMPLETAR_INSTALACION', 'tb_clientes', $id_cliente, $msg);

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'success',title:'Instalación completada',text:'$msg'}).then(()=>window.location='index.php');</script>";
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("completar_instalacion error: " . $e->getMessage());
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>Swal.fire({icon:'error',title:'Error',text:'Ocurrió un error al procesar la instalación.'}).then(()=>window.location='realizar.php?id=$id_cliente');</script>";
}

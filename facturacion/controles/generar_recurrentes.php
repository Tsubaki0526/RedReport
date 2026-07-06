<?php
session_start();
require_once __DIR__ . '/../../app/config/conexion.php';
require_once __DIR__ . '/../../app/config/seguridad.php';

verificar_acceso([1, 2]);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify($_POST['_csrf_token'] ?? '')) csrf_die();

$contratos = $pdo->query("
    SELECT co.id_contrato, c.id_cliente, c.nombre AS cliente, p.nombre AS plan_nombre, p.precio
    FROM tb_contratos co
    INNER JOIN tb_clientes c ON co.id_cliente = c.id_cliente
    INNER JOIN tb_planes p ON co.id_plan = p.id_plan
    WHERE co.estado = 'activo'
")->fetchAll();

$generadas = 0;
foreach ($contratos as $co) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM tb_facturas WHERE id_cliente = :cli AND MONTH(fecha_emision) = MONTH(CURDATE()) AND YEAR(fecha_emision) = YEAR(CURDATE())");
    $check->execute([':cli' => $co['id_cliente']]);
    if ($check->fetchColumn() > 0) continue;

    try {
        $pdo->beginTransaction();

        $next = $pdo->query("SELECT COALESCE(MAX(id_factura), 0) + 1 AS next FROM tb_facturas")->fetch(PDO::FETCH_ASSOC)['next'];
        $numero = 'FAC-' . str_pad($next, 5, '0', STR_PAD_LEFT);

        $subtotal = $co['precio'];
        $iva = round($subtotal * 0.19, 2);
        $total = round($subtotal + $iva, 2);

        $sqlF = "INSERT INTO tb_facturas (numero_factura, id_cliente, id_contrato, fecha_emision, fecha_vencimiento, subtotal, iva, total, notas, fecha_creacion)
                 VALUES (?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), ?, ?, ?, ?, NOW())";
        $pdo->prepare($sqlF)->execute([
            $numero, $co['id_cliente'], $co['id_contrato'],
            $subtotal, $iva, $total,
            'Factura recurrente - ' . $co['plan_nombre']
        ]);
        $id_factura = $pdo->lastInsertId();

        $sqlI = "INSERT INTO tb_factura_items (id_factura, descripcion, cantidad, precio_unitario, subtotal) VALUES (?, ?, 1, ?, ?)";
        $pdo->prepare($sqlI)->execute([$id_factura, 'Plan ' . $co['plan_nombre'], $subtotal, $subtotal]);

        $pdo->commit();
        $generadas++;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("generar_recurrentes error: " . $e->getMessage());
    }
}

bitacora($pdo, $_SESSION['id_usuario'], 'FACTURACION_RECURRENTE', 'tb_facturas', 0, "Se generaron $generadas facturas recurrentes");
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>Swal.fire({icon:'success',title:'Facturación recurrente',text:'Se generaron $generadas facturas'}).then(()=>window.location='../recurrente.php');</script>";

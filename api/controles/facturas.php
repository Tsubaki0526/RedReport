<?php
switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT f.*, c.nombre AS cliente_nombre, c.documento FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente WHERE f.id_factura = ?");
            $stmt->execute([$id]);
            $factura = $stmt->fetch();
            if (!$factura) { http_response_code(404); echo json_encode(['error' => 'Factura no encontrada']); exit; }
            $items = $pdo->prepare("SELECT * FROM tb_factura_items WHERE id_factura = ?");
            $items->execute([$id]);
            $factura['items'] = $items->fetchAll();
            echo json_encode(['data' => $factura]);
        } else {
            $estado = $_GET['estado'] ?? '';
            $sql = "SELECT f.*, c.nombre AS cliente_nombre FROM tb_facturas f INNER JOIN tb_clientes c ON f.id_cliente = c.id_cliente";
            $params = [];
            if ($estado) { $sql .= " WHERE f.estado = ?"; $params[] = $estado; }
            $sql .= " ORDER BY f.fecha_emision DESC LIMIT 100";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['data' => $stmt->fetchAll()]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['id_cliente']) || empty($input['items'])) {
            http_response_code(400); echo json_encode(['error' => 'id_cliente e items requeridos']); exit;
        }
        $pdo->beginTransaction();
        try {
            $next = $pdo->query("SELECT COALESCE(MAX(CAST(SUBSTRING(numero_factura,5) AS UNSIGNED)),0)+1 FROM tb_facturas")->fetchColumn();
            $numero = 'FAC-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            $subtotal = 0;
            foreach ($input['items'] as $item) { $subtotal += ($item['precio_unitario'] ?? 0) * ($item['cantidad'] ?? 1); }
            $iva = $subtotal * 0.19;
            $total = $subtotal + $iva;
            $stmt = $pdo->prepare("INSERT INTO tb_facturas (numero_factura, id_cliente, subtotal, iva, total, estado, fecha_emision, fecha_vencimiento) VALUES (?,?,?,?,?,'pendiente',CURDATE(),DATE_ADD(CURDATE(), INTERVAL 30 DAY))");
            $stmt->execute([$numero, $input['id_cliente'], $subtotal, $iva, $total]);
            $id_factura = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO tb_factura_items (id_factura, descripcion, cantidad, precio_unitario, subtotal) VALUES (?,?,?,?,?)");
            foreach ($input['items'] as $item) {
                $sub = ($item['precio_unitario'] ?? 0) * ($item['cantidad'] ?? 1);
                $stmt->execute([$id_factura, $item['descripcion'], $item['cantidad'] ?? 1, $item['precio_unitario'] ?? 0, $sub]);
            }
            $pdo->commit();
            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $id_factura, 'numero' => $numero]);
        } catch (Exception $e) { $pdo->rollBack(); throw $e; }
        break;
}

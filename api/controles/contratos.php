<?php
switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT ct.*, c.nombre AS cliente_nombre, p.nombre AS plan_nombre, p.precio, u.nombre AS vendedor_nombre
                FROM tb_contratos ct
                INNER JOIN tb_clientes c ON ct.id_cliente = c.id_cliente
                INNER JOIN tb_planes p ON ct.id_plan = p.id_plan
                LEFT JOIN tb_usuarios u ON ct.id_vendedor = u.id_usuario
                WHERE ct.id_contrato = ?");
            $stmt->execute([$id]);
            $contrato = $stmt->fetch();
            if (!$contrato) { http_response_code(404); echo json_encode(['error' => 'Contrato no encontrado']); exit; }
            echo json_encode(['data' => $contrato]);
        } else {
            $cliente_id = intval($_GET['id_cliente'] ?? 0);
            $sql = "SELECT ct.*, c.nombre AS cliente_nombre, p.nombre AS plan_nombre, p.precio, u.nombre AS vendedor_nombre
                FROM tb_contratos ct
                INNER JOIN tb_clientes c ON ct.id_cliente = c.id_cliente
                INNER JOIN tb_planes p ON ct.id_plan = p.id_plan
                LEFT JOIN tb_usuarios u ON ct.id_vendedor = u.id_usuario";
            $params = [];
            if ($cliente_id) { $sql .= " WHERE ct.id_cliente = ?"; $params[] = $cliente_id; }
            $sql .= " ORDER BY ct.fecha_inicio DESC";
            $stmt = $pdo->prepare($sql); $stmt->execute($params);
            echo json_encode(['data' => $stmt->fetchAll()]);
        }
        break;
}

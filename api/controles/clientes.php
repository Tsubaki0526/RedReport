<?php
switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT c.*, u.nombre AS instalador_nombre FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario WHERE c.id_cliente = ?");
            $stmt->execute([$id]);
            $cliente = $stmt->fetch();
            if (!$cliente) { http_response_code(404); echo json_encode(['error' => 'Cliente no encontrado']); exit; }
            echo json_encode(['data' => $cliente]);
        } else {
            $clientes = $pdo->query("SELECT c.*, u.nombre AS instalador_nombre FROM tb_clientes c LEFT JOIN tb_usuarios u ON c.id_instalador = u.id_usuario ORDER BY c.nombre")->fetchAll();
            echo json_encode(['data' => $clientes]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['nombre']) || empty($input['documento'])) {
            http_response_code(400); echo json_encode(['error' => 'nombre y documento requeridos']); exit;
        }
        $stmt = $pdo->prepare("INSERT INTO tb_clientes (nombre, documento, telefono, direccion, email, estado_servicio, lat, lng) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $input['nombre'], $input['documento'], $input['telefono'] ?? '', $input['direccion'] ?? '',
            $input['email'] ?? '', $input['estado_servicio'] ?? 'Activo',
            $input['lat'] ?? null, $input['lng'] ?? null
        ]);
        http_response_code(201);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requerido']); exit; }
        $input = json_decode(file_get_contents('php://input'), true);
        $fields = []; $params = [];
        foreach (['nombre','documento','telefono','direccion','email','estado_servicio','lat','lng'] as $f) {
            if (isset($input[$f])) { $fields[] = "$f = ?"; $params[] = $input[$f]; }
        }
        if (empty($fields)) { http_response_code(400); echo json_encode(['error' => 'Sin campos']); exit; }
        $params[] = $id;
        $pdo->prepare("UPDATE tb_clientes SET " . implode(', ', $fields) . " WHERE id_cliente = ?")->execute($params);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requerido']); exit; }
        $pdo->prepare("DELETE FROM tb_clientes WHERE id_cliente = ?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;
}

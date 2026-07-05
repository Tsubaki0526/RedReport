<?php
session_start();
if (!isset($_SESSION['portal_cliente'])) { header('Location: index.php'); exit; }
require_once '../app/config/conexion.php';
require_once '../app/config/seguridad.php';
$c = $_SESSION['portal_cliente'];

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $asunto = trim($_POST['asunto'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    $num = $pdo->query("SELECT COALESCE(MAX(id_ticket),0)+1 FROM tb_tickets")->fetchColumn();
    $numero = 'TCK-' . str_pad($num, 5, '0', STR_PAD_LEFT);

    $stmt = $pdo->prepare("INSERT INTO tb_tickets (numero_ticket, id_cliente, asunto, categoria, descripcion, prioridad, estado, fecha_creacion) VALUES (?,?,?,?,?,'Media','Abierto',NOW())");
    $stmt->execute([$numero, $c['id_cliente'], $asunto, $categoria, $descripcion]);

    $pdo->prepare("INSERT INTO tb_bitacora (id_usuario, accion, tabla_afectada, id_registro_afectado, detalle, direccion_ip, fecha_hora) VALUES (1,'CREAR','tb_tickets',?,'Portal cliente: $numero',?,NOW())")->execute([$pdo->lastInsertId(), $_SERVER['REMOTE_ADDR']??'127.0.0.1']);
    $mensaje = 'Ticket creado: ' . $numero;
}
csrf_field();
$categorias = ['Fallo conexion' => 'Fallo de conexión', 'Equipo' => 'Problema con equipo', 'Facturacion' => 'Facturación', 'Otro' => 'Otro'];
?>
<div class="container py-4">
    <?php if ($mensaje): ?><div class="alert alert-success"><?= hescape($mensaje) ?></div><?php endif; ?>
    <div class="card">
        <div class="card-header"><h6 class="m-0"><i class="fas fa-plus me-2 text-info"></i>Nuevo ticket de soporte</h6></div>
        <div class="card-body">
            <form method="POST">
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Asunto</label>
                    <input type="text" name="asunto" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria" class="form-select" required>
                        <?php foreach ($categorias as $v=>$l): ?>
                        <option value="<?= $v ?>"><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-info text-white"><i class="fas fa-paper-plane me-2"></i>Enviar ticket</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

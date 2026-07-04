<?php
include('../../sesion.php');
include('../../parte1.php');
include('../../app/config/conexion.php');

$id_cliente = $_GET['id_cliente'] ?? 0;

// Buscar datos del cliente
$sql = "SELECT * FROM tb_clientes WHERE id_cliente = :id_cliente LIMIT 1";
$query = $pdo->prepare($sql);
$query->bindParam(':id_cliente', $id_cliente);
$query->execute();
$cliente = $query->fetch(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Asignar IP a Cliente</h1>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($cliente['nombre']) ?></p>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nueva IP</h3>
                </div>

                <div class="card-body">
                    <form action="../controles/crear_ip_controles.php" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_cliente" value="<?= htmlspecialchars($cliente['id_cliente']) ?>">

                        <div class="form-group">
                            <label>Dirección IP</label>
                            <input type="text" class="form-control" name="ip_principal" placeholder="Ej: 192.168.1.10" required>
                        </div>

                        <div class="form-group">
                            <label>Megas Contratadas</label>
                            <input type="number" class="form-control" name="megas_contratadas" placeholder="Ej: 50" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="lista.php" class="btn btn-secondary">Volver</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../parte2.php'); ?>

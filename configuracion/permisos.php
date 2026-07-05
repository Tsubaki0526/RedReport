<?php
include('../sesion.php');
require_once '../app/config/conexion.php';
include('../parte1.php');

if ($_SESSION['id_rol'] != 1) { echo "<script>window.location='../index.php';</script>"; exit; }

$roles = $pdo->query("SELECT * FROM tb_rol ORDER BY id_rol")->fetchAll();
$modulos = $pdo->query("SELECT * FROM tb_modulos ORDER BY orden")->fetchAll();

// Guardar permisos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['_csrf_token'] ?? '')) {
    $id_rol = intval($_POST['id_rol'] ?? 0);
    if ($id_rol > 0) {
        $pdo->prepare("DELETE FROM tb_permisos WHERE id_rol = ?")->execute([$id_rol]);
        $stmt = $pdo->prepare("INSERT INTO tb_permisos (id_modulo, id_rol, leer, escribir, editar, eliminar) VALUES (?,?,?,?,?,?)");
        foreach ($modulos as $m) {
            $leer = intval($_POST["leer_{$m['id_modulo']}"] ?? 0);
            $escribir = intval($_POST["escribir_{$m['id_modulo']}"] ?? 0);
            $editar = intval($_POST["editar_{$m['id_modulo']}"] ?? 0);
            $eliminar = intval($_POST["eliminar_{$m['id_modulo']}"] ?? 0);
            if ($leer) $stmt->execute([$m['id_modulo'], $id_rol, $leer, $escribir, $editar, $eliminar]);
        }
        echo "<script>Swal.fire({icon:'success',title:'Permisos actualizados'}).then(()=>window.location='permisos.php');</script>";
    }
}

$permisos = $pdo->query("SELECT * FROM tb_permisos")->fetchAll(PDO::FETCH_ASSOC);

// Build lookup
$permLookup = [];
foreach ($permisos as $p) {
    $permLookup[$p['id_rol']][$p['id_modulo']] = $p;
}
?>
<div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1 class="m-0"><i class="fas fa-shield-alt me-2"></i>Permisos por rol</h1></div></div></div></div>
    <div class="content"><div class="container-fluid">
        <?php foreach ($roles as $rol): ?>
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title"><?= hescape($rol['nombre_rol']) ?></h3></div>
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_rol" value="<?= $rol['id_rol'] ?>">
                <div class="card-body p-0">
                    <div class="table-container">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Modulo</th><th class="text-center">Leer</th><th class="text-center">Escribir</th><th class="text-center">Editar</th><th class="text-center">Eliminar</th></tr></thead>
                        <tbody>
                            <?php foreach ($modulos as $m):
                                $p = $permLookup[$rol['id_rol']][$m['id_modulo']] ?? null;
                            ?>
                            <tr>
                                <td><i class="<?= hescape($m['icono']) ?> me-2"></i><?= hescape($m['nombre']) ?></td>
                                <td class="text-center"><input type="checkbox" name="leer_<?= $m['id_modulo'] ?>" value="1" <?= ($p['leer'] ?? 0) ? 'checked' : '' ?>></td>
                                <td class="text-center"><input type="checkbox" name="escribir_<?= $m['id_modulo'] ?>" value="1" <?= ($p['escribir'] ?? 0) ? 'checked' : '' ?>></td>
                                <td class="text-center"><input type="checkbox" name="editar_<?= $m['id_modulo'] ?>" value="1" <?= ($p['editar'] ?? 0) ? 'checked' : '' ?>></td>
                                <td class="text-center"><input type="checkbox" name="eliminar_<?= $m['id_modulo'] ?>" value="1" <?= ($p['eliminar'] ?? 0) ? 'checked' : '' ?>></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="card-footer"><button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Guardar permisos</button></div>
            </form>
        </div>
        <?php endforeach; ?>
    </div></div>
</div>
<?php include('../parte2.php'); ?>

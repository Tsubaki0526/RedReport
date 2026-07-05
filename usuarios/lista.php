<?php
include('../sesion.php');
include('../parte1.php');

?>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Lista de Usuarios</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <span id="fechaHora" class="text-muted"></span>
                </div>
            </div>
        </div>
    </div>
    

    
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Usuarios Registrados</h3>
                    </div>

                    <div class="card-body">
                    <div class="table-container">
                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Documento</th>
                                    <th>Celular</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include('lista_usuarios_controles.php');
                                foreach ($usuarios_datos as $usuario) {
                                    $id_usuario = $usuario['id_usuario']; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                        <td><?= htmlspecialchars($usuario['documento']) ?></td>
                                        <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td><?= htmlspecialchars($usuario['nombre_rol']) ?></td>
                                        <td>
                                            <center>
                                                <div class="btn-group" role="group" aria-label="Acciones">
                                                    <a href="editar.php?id_usuario=<?php echo $id_usuario; ?>" type="button" class="btn btn-primary btn-sm me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form method="POST" action="eliminar_usuarios_controles.php" class="form-eliminar d-inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                                                        <button type="button" class="btn btn-danger btn-sm btn-confirmar-eliminar">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>

                                                </div>

                                            </center>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                   <th>Nombres</th>
                                    <th>Documento</th>
                                    <th>Celular</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../parte2.php'); ?>

<script>
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "language": { "url": "//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json" },
            buttons: [{
                    extend: 'collection',
                    text: 'Reportes',
                    orientation: 'landscape',
                    buttons: [{
                        text: 'Copiar',
                        extend: 'copy'
                    }, {
                        extend: 'pdf',
                    }, {
                        extend: 'csv',
                    }, {
                        extend: 'excel',
                    }, {
                        text: 'Imprimir',
                        extend: 'print'
                    }]
                },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas'
                }
            ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-confirmar-eliminar').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const form = this.closest('form');

        Swal.fire({
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

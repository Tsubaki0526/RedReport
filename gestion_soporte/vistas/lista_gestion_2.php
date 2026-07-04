<?php
include('../../sesion.php');
include('../../parte1.php');
include('../controles/consulta.php');

?>

<link rel="stylesheet" href="../../public/css/redreport.css">


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Lista de Reportes</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <span id="fechaHora" class="text-muted"></span>
                </div>
            </div>
        </div>
    </div>
    

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-start-primary">
            <div class="card-body py-2 px-5">
                <small class="text-muted">Último Radicado Generado</small><br>
                <span class="h6 text-primary fw-bold mb-0">
                    <?= $ultimoRadicado ? htmlspecialchars($ultimoRadicado['radicado']) : "Aún no hay radicados"; ?>
                </span>
            </div>
        </div>
    </div>



    
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Reportes Registrados</h3>
                        
                    </div>

                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Empresa</th>
                                    <th>Operador</th>
                                    <th>Nombre</th>
                                    <th>Direccion</th>
                                    <th>Forma</th>
                                    <th>Telefono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Observaciones</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include('../controles/lista_reportes_controles.php');
                                foreach ($reportes as $reporte) {
                                    $id_r_registrado = $reporte['id_r_registrado'];

                                    // limpiar número de teléfono
                                    $telefono = preg_replace('/\D/', '', $reporte['telefono']);

                                    // mensaje dinámico
                                      $mensaje = "Hola " . $reporte['nombre'] .
                                        ", se informa que su reporte con radicado " . $reporte['radicado'] .
                                        " está en estado: " . $reporte['estado'] . " para mas informacion comunicarse con gestion";
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($reporte['radicado']) ?></td>
                                        <td><?= htmlspecialchars($reporte['empresa']) ?></td>
                                        <td><?= htmlspecialchars($reporte['operador']) ?></td>
                                        <td><?= htmlspecialchars($reporte['nombre']) ?></td>
                                        <td><?= htmlspecialchars($reporte['direccion']) ?></td>
                                        <td><?= htmlspecialchars($reporte['forma']) ?></td>
                                        <td style="padding:0;">
                                            <a href="https://wa.me/57<?= $telefono ?>?text=<?= urlencode($mensaje) ?>"
                                                target="_blank"
                                                class="btn btn-success btn-sm d-block w-100 h-100 text-center"
                                                style="border-radius:0;">
                                                <i class="fab fa-whatsapp"></i> <?= htmlspecialchars($reporte['telefono']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($reporte['fecha']) ?></td>
                                        <td><?= htmlspecialchars($reporte['hora']) ?></td>
                                        <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= htmlspecialchars(mb_strimwidth($reporte['observaciones'], 0, 10, "...")) ?>
                                            <a href="#" class="btn btn-link btn-sm" onclick="verSolucion('<?= htmlspecialchars(addslashes($reporte['observaciones'])) ?>')">Ver más</a>
                                        </td>
                                        <td><?= htmlspecialchars($reporte['estado']) ?></td>
                                        <td class="acciones" style="text-align:center;">
                                            <a href="finalizar_2.php?id_r_registrado=<?= $id_r_registrado ?>" class="btn btn-warning btn-sm">
                                                Finalizar <i class="fas fa-hourglass-half"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Empresa</th>
                                    <th>Operador</th>
                                    <th>Nombre</th>
                                    <th>Direccion</th>
                                    <th>Forma</th>
                                    <th>Telefono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Observaciones</th>
                                    <th>Estado</th>
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

<?php include('../../parte2.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // DataTables
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            buttons: [{
                    extend: 'collection',
                    text: 'Reportes',
                    orientation: 'landscape',
                    buttons: [{
                            text: 'Copiar',
                            extend: 'copy'
                        },
                        {
                            extend: 'pdf'
                        },
                        {
                            extend: 'csv'
                        },
                        {
                            extend: 'excel'
                        },
                        {
                            text: 'Imprimir',
                            extend: 'print'
                        }
                    ]
                },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas'
                }
            ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    // Fecha y hora en vivo
    function actualizarFechaHora() {
        const now = new Date();
        const opciones = {
            dateStyle: 'short',
            timeStyle: 'medium'
        };
        document.getElementById('fechaHora').textContent = now.toLocaleString('es-CO', opciones);
    }
    setInterval(actualizarFechaHora, 1000);
    actualizarFechaHora();

    
      function verSolucion(texto) {
    Swal.fire({
        title: 'Detalle de la Solución',
            html: '<div style="text-align:left; white-space:pre-wrap;">' + hescape(texto) + '</div>',
        width: 600,
        confirmButtonText: 'Cerrar'
    });

}
</script>
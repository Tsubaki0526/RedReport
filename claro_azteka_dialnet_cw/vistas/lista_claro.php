<?php
include('../../gestion_soporte/sesion.php');
include('../../gestion_soporte/parte1.php');
include('../controles/consultas.php');

?>

<link rel="stylesheet" href="../../public/css/redreport.css">


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Lista de Reportes De Claro</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <span id="fechaHora" class="text-muted"></span>
                </div>
            </div>
        </div>
    </div>
    

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm border-left-primary">
            <div class="card-body py-2 px-5">
                <small class="text-muted">Último Radicado Generado Del Cliente Claro</small><br>
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
                        <h3 class="card-title">Reportes Registrados De Claro</h3>
                        
                    </div>

                    <div class="card-body">
                        <div class="mb-2 small text-muted d-flex flex-wrap gap-3">
                            <span><i class="fas fa-whatsapp text-success"></i> WhatsApp</span>
                            <span><i class="fas fa-hourglass-half text-warning"></i> Finalizar</span>
                        </div>
                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Operador</th>
                                    <th>Cliente</th>
                                    <th>Ciudad</th>
                                    <th>Telefono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Daño Reportado</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include('../controles/lista_reportes_claro.php');
                                foreach ($reportes as $reporte) {
                                    $id_claro_registrado = $reporte['id_claro_registrado'];

                                    // limpiar número de teléfono
                                    $telefono = preg_replace('/\D/', '', $reporte['telefono']);

                                    // mensaje dinámico
                                      $mensaje = "Hola " . $reporte['cliente'] .
                                        ", se informa que su reporte con radicado " . $reporte['radicado'] .
                                        " está en estado: " . $reporte['estado'] . " para mas informacion comunicarse con gestion";
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($reporte['radicado']) ?></td>
                                        <td><?= htmlspecialchars($reporte['operador']) ?></td>
                                        <td><?= htmlspecialchars($reporte['cliente']) ?></td>
                                        <td><?= htmlspecialchars($reporte['ciudad']) ?></td>
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
                                        <td title="<?= htmlspecialchars($reporte['dano_reportado']) ?>">
                                            <?= substr($reporte['dano_reportado'], 0, 50) ?>...
                                        </td>
                                        <td><?= htmlspecialchars($reporte['estado']) ?></td>
                                        <td class="acciones" style="text-align:center;">
                                            <a href="finalizar_claro.php?id_claro_registrado=<?= $id_claro_registrado ?>" class="btn btn-warning btn-sm">
                                                Finalizar <i class="fas fa-hourglass-half"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Operador</th>
                                    <th>Cliente</th>
                                    <th>Ciudad</th>
                                    <th>Telefono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Daño Reportado</th>
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

<?php include('../../gestion_soporte/parte2.php'); ?>

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
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
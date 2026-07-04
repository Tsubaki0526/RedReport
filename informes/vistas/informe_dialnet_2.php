<?php
include('../../sesion.php');
include('../../parte1.php');

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
    

    
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Reportes Registrados</h3>
                        
                    </div>

                    <div class="card-body">


                        <form method="GET" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-auto">
                                    <label for="fecha_inicio" class="form-label">Inicio</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control form-control-sm"
                                        value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                                </div>
                                <div class="col-auto">
                                    <label for="fecha_fin" class="form-label">Fin</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control form-control-sm"
                                        value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="../vistas/informe_reportes_2.php" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </form>


                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Cliente</th>
                                    <th>Ciudad</th>
                                    <th>Telefono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Daño Reportado</th>
                                    <th>Fecha y Hora De finalizacion</th>
                                    <th>Hora Totales</th>
                                    <th>Horas Reales Del Daño</th>
                                    <th>Tipo De Daño</th>
                                    <th>Parada De Reloj</th>
                                    <th>Horas Paradas Inicio</th>
                                    <th>Horas Paradas Fin</th>
                                    <th>Horas Paradas</th>
                                    <th>Solucion</th>
                                    <th>Estado</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include('../controles/dialnet/lista_reportes_controles_dialnet.php');
                                foreach ($reportes as $reporte) {
                                    // limpiar número de teléfono
                                    $telefono = preg_replace('/\D/', '', $reporte['telefono']);

                                    // mensaje dinámico para WhatsApp
                                    $mensaje = "Hola " . $reporte['cliente'] .
                                        ", se informa que su reporte con radicado " . $reporte['radicado'] .
                                        " está en estado: " . $reporte['estado'] . " para mas informacion comunicarse con gestion";
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($reporte['radicado']) ?></td>
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
                                        <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= htmlspecialchars(mb_strimwidth($reporte['dano_reportado'], 0, 10, "...")) ?>
                                            <a href="#" class="btn btn-link btn-sm" onclick="verSolucion('<?= htmlspecialchars(addslashes($reporte['dano_reportado'])) ?>')">Ver más</a>
                                        </td>
                                        <td><?= htmlspecialchars($reporte['fecha_hora_finalizado'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($reporte['horas_totales'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($reporte['horas_real_dano'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($reporte['tipo_de_dano'] ?? '-') ?></td>
                                        <td>
                                            <?php
                                            if (isset($reporte['parada_reloj'])) {
                                                echo $reporte['parada_reloj'] == 1 ? 'Sí' : 'No';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($reporte['hora_parada_inicio'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($reporte['hora_parada_fin'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($reporte['horas_parada'] ?? '-') ?></td>
                                        <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= htmlspecialchars(mb_strimwidth($reporte['solucion'], 0, 50, "...")) ?>
                                            <a href="#" class="btn btn-link btn-sm" onclick="verSolucion('<?= htmlspecialchars(addslashes($reporte['solucion'])) ?>')">Ver más</a>
                                        </td>
                                        <td><?= htmlspecialchars($reporte['estado']) ?></td>
                                        <td>
                                            <a href="informe_dialnet_pdf.php?id=<?= $reporte['id_dialnet_registrado'] ?>"
                                                class="btn btn-sm btn-primary"
                                                target="_blank">
                                                Descargar Informe En PDF
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th>Radicado</th>
                                    <th>Cliente</th>
                                    <th>Ciudad</th>
                                    <th>Telefono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Daño Reportado</th>
                                    <th>Fecha y Hora De finalizacion</th>
                                    <th>Hora Totales</th>
                                    <th>Horas Reales Del Daño</th>
                                    <th>Tipo De Daño</th>
                                    <th>Parada De Reloj</th>
                                    <th>Horas Paradas Inicio</th>
                                    <th>Horas Paradas Fin</th>
                                    <th>Horas Paradas</th>
                                    <th>Solucion</th>
                                    <th>Estado</th>
                                    <th>Accion</th>
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

function hescape(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
</script>

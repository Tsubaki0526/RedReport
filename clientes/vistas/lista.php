<?php
include('../../sesion.php');
include('../../parte1.php');
include('../controles/lista_clientes_controles.php');
?>


<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Lista de Clientes</h1>
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
                        <h3 class="card-title">Clientes Registrados</h3>
                        
                    </div>

                    <div class="card-body">
                        <!-- Leyenda de iconos -->
                        <div class="mb-2 small text-muted d-flex flex-wrap gap-3">
                            <span><i class="fas fa-eye text-info"></i> Ver más</span>
                            <span><i class="fas fa-network-wired text-success"></i> IPs</span>
                            <span><i class="fas fa-network-wired text-primary"></i> Red</span>
                            <span><i class="fas fa-edit text-warning"></i> Editar</span>
                            <span><i class="fas fa-trash text-danger"></i> Borrar</span>
                        </div>
                        <!-- Tabla principal -->
                        <div class="table-container">
                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Nombres</th>
                                    <th>Documento</th>
                                    <th>Celular</th>
                                    <th>Dirección</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                    <th style="display:none;">Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clientes as $cliente): ?>
                                    <tr data-cliente-id="<?= $cliente['id_cliente'] ?>">
                                        <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                                        <td><?= htmlspecialchars($cliente['documento']) ?></td>
                                        <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                                        <td><?= htmlspecialchars($cliente['direccion']) ?></td>
                                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                                        <td><span class="badge bg-<?= $cliente['estado_servicio'] == 'Activo' ? 'success' : ($cliente['estado_servicio'] == 'Suspendido' ? 'warning text-dark' : 'danger') ?>"><?= $cliente['estado_servicio'] ?></span></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <button class="btn btn-info btn-sm btn-ver-detalle" title="Ver más">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="ficha.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-secondary btn-sm" title="Ficha completa">
                                                    <i class="fas fa-address-card"></i>
                                                </a>
                                                <a href="../vistas/ips_clientes.php?id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-success btn-sm" title="IPs">
                                                    <i class="fas fa-network-wired"></i>
                                                </a>
                                                <a href="../vistas/red_clientes.php?id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-primary btn-sm" title="Red">
                                                    <i class="fas fa-network-wired"></i>
                                                </a>
                                                <a href="../vistas/editar_clientes.php?id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="../controles/eliminar_clientes_controles.php" method="POST" style="display:inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id_cliente" value="<?= $cliente['id_cliente'] ?>">
                                                    <button type="button" class="btn btn-danger btn-sm btn-confirmar-eliminar" title="Borrar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        <td style="display:none;" class="detalles">
                                            <?php
                                            $detalleText = "<strong>IPs</strong><br>";
                                            if (!empty($ips_by_cliente[$cliente['id_cliente']])) {
                                                $detalleText .= "<table border='1' style='border-collapse:collapse; width:100%; font-size:10px;'>
                                                        <thead><tr><th>IP</th><th>Megas</th></tr></thead><tbody>";
                                                foreach ($ips_by_cliente[$cliente['id_cliente']] as $ip) {
                                                    $detalleText .= "<tr><td>" . htmlspecialchars($ip['ip_principal']) . "</td><td>" . htmlspecialchars($ip['megas_contratadas']) . " Mbps</td></tr>";
                                                }
                                                $detalleText .= "</tbody></table>";
                                            } else {
                                                $detalleText .= "Sin IPs registradas<br>";
                                            }

                                            $detalleText .= "<br><strong>Red</strong><br>";
                                            if (!empty($red_by_cliente[$cliente['id_cliente']])) {
                                                $detalleText .= "<table border='1' style='border-collapse:collapse; width:100%; font-size:10px;'>
                                                        <thead><tr><th>Switch</th><th>IP</th><th>Puerto</th></tr></thead><tbody>";
                                                foreach ($red_by_cliente[$cliente['id_cliente']] as $red) {
                                                    $detalleText .= "<tr><td>" . htmlspecialchars($red['switch']) . "</td><td>" . htmlspecialchars($red['ip']) . "</td><td>" . htmlspecialchars($red['puerto']) . "</td></tr>";
                                                }
                                                $detalleText .= "</tbody></table>";
                                            } else {
                                                $detalleText .= "Sin red registrada";
                                            }
                                            echo $detalleText;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Divs ocultos con detalle (para Ver más en pantalla) -->
                        <?php foreach ($clientes as $cliente): ?>
                            <div id="detalle-<?= $cliente['id_cliente'] ?>" class="d-none">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>IPs y Plan</h6>
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>IP</th>
                                                    <th>Megas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($ips_by_cliente[$cliente['id_cliente']])): ?>
                                                    <?php foreach ($ips_by_cliente[$cliente['id_cliente']] as $ip): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($ip['ip_principal']) ?></td>
                                                            <td><?= htmlspecialchars($ip['megas_contratadas']) ?> Mbps</td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="2">Sin IPs registradas</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Red</h6>
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Switch / Antena</th>
                                                    <th>IP</th>
                                                    <th>Puerto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($red_by_cliente[$cliente['id_cliente']])): ?>
                                                    <?php foreach ($red_by_cliente[$cliente['id_cliente']] as $red): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($red['switch']) ?></td>
                                                            <td><?= htmlspecialchars($red['ip']) ?></td>
                                                            <td><?= htmlspecialchars($red['puerto']) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="3">Sin red registrada</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../parte2.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var table = $("#example1").DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.11/i18n/es-ES.json' },
            columnDefs: [{
                    targets: -1,
                    visible: false
                }, // ocultar columna detalles
                {
                    targets: -2,
                    responsivePriority: 1,
                    className: 'all'
                } // Acciones siempre visible
            ],
            buttons: [{
                    extend: 'collection',
                    text: 'Reportes',
                    buttons: [{
                            extend: 'excel',
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function(data, row, column, node) {
                                        return agregarDetalles(data, row, column, node, 'excel');
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function(data, row, column, node) {
                                        return agregarDetalles(data, row, column, node, 'pdf');
                                    }
                                }
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: ':visible:not(:last-child)',
                                format: {
                                    body: function(data, row, column, node) {
                                        var $tr = $(node).closest('tr');
                                        var detalles = $tr.next('tr.child').find('td').text().trim();

                                        if (detalles) {
                                            return data + " | Detalles: " + detalles;
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'copy',
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function(data, row, column, node) {
                                        return agregarDetalles(data, row, column, node, 'copy');
                                    }
                                }
                            }
                        },
                        {
                            extend: 'csv',
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function(data, row, column, node) {
                                        return agregarDetalles(data, row, column, node, 'csv');
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas'
                }
            ]
        });

        table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        // Función para agregar detalles dependiendo del tipo de export
        function agregarDetalles(data, row, column, node, tipoExport) {
            if (!node) return data; // seguridad

            var $tr = $(node).closest('tr');
            var detallesHTML = $tr.find('td.detalles').html();
            var detallesTexto = $tr.find('td.detalles').text().trim();

            if (!detallesHTML) return data;

            if (tipoExport === 'pdf' || tipoExport === 'print') {
                return data + "\n" + detallesTexto; // usar texto plano (mejor compatibilidad)
            } else {
                return data + " | " + detallesTexto;
            }
        }


        // Mostrar fila hijo en pantalla
        $('#example1 tbody').on('click', '.btn-ver-detalle', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            if (!row.child || typeof row.child.isShown !== 'function') return;
            var clienteId = tr.data('cliente-id');
            var contenido = $('#detalle-' + clienteId).html() || 'No hay detalles';
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(contenido).show();
                tr.addClass('shown');
            }
        });

        // SweetAlert para eliminar
        document.querySelectorAll('.btn-confirmar-eliminar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const form = this.closest('form');
                Swal.fire({
                    title: '¿Eliminar cliente?',
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
    });
</script>
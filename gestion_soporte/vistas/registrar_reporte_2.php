<?php
date_default_timezone_set('America/Bogota');
include('../../sesion.php');
include('../../parte1.php');

?>



<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Registro De Reportes</h1>
                </div>
                <div class="col-sm-12 text-end">
                    <span class="text-muted">
                        <span id="fechaHora" class="text-muted"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
    

    
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Columna del formulario -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Registro De Reportes</h3>
                            
                        </div>

                        <div class="card-body">
                            <form action="../controles/crear_reporte_2.php" method="POST">
                                <?= csrf_field() ?>
                                <div class="row">

                                    <div class="col-md-8 mb-3">
                                        <label for="cliente">Buscar Cliente</label>
                                        <input type="text" id="cliente" class="form-control" placeholder="Escriba el nombre del cliente..." autocomplete="off">
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Radicado</label>
                                            <input type="text" class="form-control" value="<?php echo date('dmY') . '****'; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Empresa</label>
                                            <input type="text" class="form-control" name="empresa" placeholder="Ingresar La Empresa" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Operador</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="operador"
                                                value="<?= htmlspecialchars($_SESSION['usuario'] ?? 'Invitado') ?>"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingresar El Nombre Del Cliente" autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Ingresar El Teléfono Del Cliente" autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Dirección</label>
                                            <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Ingresar La Dirección Del Cliente" autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="forma">Forma de contacto</label>
                                            <select id="forma" name="forma" class="form-control" required>
                                                <option value="" disabled selected>Seleccione la forma</option>
                                                <option value="correo">Correo</option>
                                                <option value="llamada">Llamada</option>
                                                <option value="whatsapp">WhatsApp</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Fecha</label>
                                            <input
                                                type="date"
                                                class="form-control"
                                                name="fecha"
                                                value="<?php echo date('Y-m-d'); ?>"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Hora</label>
                                            <input
                                                type="time"
                                                class="form-control"
                                                name="hora"
                                                value="<?php echo date('H:i'); ?>"
                                                readonly>
                                        </div>
                                    </div>


                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Observaciones</label>
                                            <input type="text" class="form-control" name="observaciones" placeholder="Ingresar Observaciones" autocomplete="off" required>
                                        </div>
                                    </div>

                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">Registrar</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    
</div>


<?php include('../../parte2.php'); ?>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script>
$(function() {
    $("#cliente").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "../controles/buscar_cliente.php",
                dataType: "json",
                data: { term: request.term },
                success: function(data) {
                    response(data.map(cliente => ({
                        label: cliente.nombre + " (" + cliente.telefono + ")",
                        value: cliente.nombre,
                        data: cliente
                    })));
                }
            });
        },
        select: function(event, ui) {
            // Al seleccionar, llenar los campos automáticamente
            $("#nombre").val(ui.item.data.nombre);
            $("#telefono").val(ui.item.data.telefono);
            $("#direccion").val(ui.item.data.direccion);
        },
        minLength: 2
    });
});
</script>

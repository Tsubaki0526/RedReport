<?php
date_default_timezone_set('America/Bogota');
include('../../gestion_soporte/sesion.php');
include('../../gestion_soporte/parte1.php');

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
                            <button type="button" class="btn btn-sm" data-bs-toggle="collapse" data-bs-target="#collapseCard">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>

                        <div class="card-body collapse show" id="collapseCard">
                            <form action="../controles/crear_reporte.php" method="POST">
                                <?= csrf_field() ?>
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Radicado</label>
                                            <input type="text" class="form-control" value="<?php echo date('dmY') . '****'; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Operador</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name="operador"
                                                value="<?= hescape($_SESSION['usuario'] ?? 'Invitado') ?>"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Cliente</label>
                                            <select class="form-control" name="cliente" required>
                                                <option value="">Seleccione...</option>
                                                <option value="claro">Claro</option>
                                                <option value="azteca">Azteca</option>
                                                <option value="dialnet">Dialnet</option>
                                                <option value="liberty">Liberty</option>
                                            </select>
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
                                            <label>Ciudad Donde Se Reporta El Daño</label>
                                            <input type="text" class="form-control" name="ciudad" id="ciudad" placeholder="Ingresar La Ciudad Del Cliente" autocomplete="off" required>
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


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Daño Reportado</label>
                                            <input type="text" class="form-control" name="dano_reportado" placeholder="Ingresar Daño Reportado" autocomplete="off" required>
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


<?php include('../../gestion_soporte/parte2.php'); ?>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


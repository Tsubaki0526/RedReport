<?php
include('../../sesion.php');
include('../../parte1.php');
include('../../app/config/conexion.php');

$id_liberty_registrado = isset($_GET['id_liberty_registrado']) ? (int) $_GET['id_liberty_registrado'] : 0;

// Buscar datos del Reporte
$sqlReporte = "SELECT * FROM tb_liberty WHERE id_liberty_registrado = :id_liberty_registrado LIMIT 1";
$queryReporte = $pdo->prepare($sqlReporte);
$queryReporte->execute([':id_liberty_registrado' => $id_liberty_registrado]);
$reporte = $queryReporte->fetch(PDO::FETCH_ASSOC);

if (!$reporte) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Reporte no encontrado',
            text: 'El reporte con ID " . $id_liberty_registrado . " no existe o fue eliminado.',
            confirmButtonText: 'Volver'
        }).then(() => {
            window.location = 'lista_liberty_2.php';
        });
    </script>";
    exit;
}
?>

<link rel="stylesheet" href="../../public/css/redreport.css">


<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0">Finalizar Reporte</h1>
                </div>
                <div class="col-sm-12 text-end">
                    <span id="fechaHora" class="text-muted"></span>
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
                            <h3 class="card-title">Formulario de Finalización</h3>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="../controles_liberty/finalizar_reporte_liberty_2.php" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id_liberty_registrado" value="<?= $reporte['id_liberty_registrado'] ?>">

                                <div class="row">
                                    <!-- Datos bloqueados -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Radicado</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['radicado']) ?>" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Operador</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['operador']) ?>" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Cliente</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['cliente']) ?>" readonly>
                                        <input type="hidden" name="cliente" value="<?= htmlspecialchars($reporte['cliente']) ?>">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['telefono']) ?>" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Ciudad</label>
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($reporte['ciudad']) ?>" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Fecha Reporte</label>
                                        <input type="text" id="fecha_inicio" class="form-control" value="<?= htmlspecialchars($reporte['fecha']) ?>" readonly>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Hora Reporte</label>
                                        <input type="text" id="hora_inicio" class="form-control" value="<?= htmlspecialchars($reporte['hora']) ?>" readonly>
                                    </div>

                                    <!-- Campos de finalización -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Fecha Finalizado</label>
                                        <input type="date" id="fecha_finalizado" name="fecha_finalizado" class="form-control" value="<?= date('Y-m-d') ?>">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Hora Finalizado</label>
                                        <input type="time" id="hora_finalizado" name="hora_finalizado" class="form-control" value="<?= date('H:i') ?>">
                                    </div>

                                    <!-- Horas Totales -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Horas Totales</label>
                                        <input type="text" id="horas_totales" name="horas_totales" class="form-control" readonly>
                                    </div>

                                    <!-- Total Real de Horas de Daño -->
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Total Real de Horas de Daño</label>
                                        <input type="text" id="horas_real_dano" name="horas_real_dano" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Tipo de Daño</label>
                                            <select class="form-control" name="tipo_de_dano" required>
                                                <option value="">Seleccione...</option>
                                                <option value="corte_de_fibra">Corte de Fibra</option>
                                                <option value="atenuacion">Atenuación</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Checkbox para parada -->
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="usar_parada" name="usar_parada">
                                            <label class="form-check-label">¿Hubo parada de reloj?</label>
                                        </div>
                                    </div>

                                    <!-- Campos de parada (ocultos por defecto) -->
                                    <div id="parada_section" style="display:none;">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Inicio Parada</label>
                                                <input type="time" id="hora_parada_inicio" name="hora_parada_inicio" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Fin Parada</label>
                                                <input type="time" id="hora_parada_fin" name="hora_parada_fin" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Horas Parada</label>
                                                <input type="text" id="horas_parada" name="horas_parada" class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Observaciones Finalización</label>
                                        <textarea name="observaciones_finales" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">Finalizar Reporte</button>
                                    <a href="lista_liberty_2.php" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div> 
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script de cálculo -->
<script>
function calcularHorasTotales() {
    let fechaInicio = document.getElementById("fecha_inicio")?.value;
    let horaInicio = document.getElementById("hora_inicio")?.value;
    let fechaFin = document.getElementById("fecha_finalizado")?.value;
    let horaFin = document.getElementById("hora_finalizado")?.value;
    if (!fechaInicio || !horaInicio || !fechaFin || !horaFin) return;

    let inicio = new Date(`${fechaInicio}T${horaInicio}`);
    let fin = new Date(`${fechaFin}T${horaFin}`);
    if (fin < inicio) fin.setDate(fin.getDate() + 1);

    let diffMs = fin - inicio;
    let diffMin = Math.floor(diffMs / 60000);
    let horas = Math.floor(diffMin / 60);
    let minutos = diffMin % 60;
    let horasTotalesStr = `${horas.toString().padStart(2,'0')}:${minutos.toString().padStart(2,'0')}`;
    document.getElementById("horas_totales").value = horasTotalesStr;

    // Calcular horas efectivas descontando parada
    let minutosParada = 0;
    if (document.getElementById("usar_parada").checked) {
        let hParada = document.getElementById("horas_parada").value;
        if (hParada) {
            let partes = hParada.split(":");
            minutosParada = parseInt(partes[0]) * 60 + parseInt(partes[1]);
        }
    }

    let minutosEfectivos = diffMin - minutosParada;
    if (minutosEfectivos < 0) minutosEfectivos = 0;
    let horasEfectivas = Math.floor(minutosEfectivos / 60);
    let minutosRestantes = minutosEfectivos % 60;
    document.getElementById("horas_real_dano").value =
        `${horasEfectivas.toString().padStart(2,'0')}:${minutosRestantes.toString().padStart(2,'0')}`;
}

function calcularHorasParada() {
    let inicio = document.getElementById("hora_parada_inicio")?.value;
    let fin = document.getElementById("hora_parada_fin")?.value;
    if (!inicio || !fin) return;

    let fecha = document.getElementById("fecha_finalizado").value || new Date().toISOString().split('T')[0];
    let inicioParada = new Date(`${fecha}T${inicio}`);
    let finParada = new Date(`${fecha}T${fin}`);
    if (finParada < inicioParada) finParada.setDate(finParada.getDate() + 1);

    let diffMs = finParada - inicioParada;
    let diffMin = Math.floor(diffMs / 60000);
    let horas = Math.floor(diffMin / 60);
    let minutos = diffMin % 60;
    document.getElementById("horas_parada").value =
        `${horas.toString().padStart(2,'0')}:${minutos.toString().padStart(2,'0')}`;

    calcularHorasTotales();
}

// Mostrar/ocultar sección parada
document.getElementById("usar_parada").addEventListener("change", function() {
    document.getElementById("parada_section").style.display = this.checked ? "block" : "none";
    calcularHorasTotales();
});

// Eventos
document.getElementById("hora_finalizado").addEventListener("input", calcularHorasTotales);
document.getElementById("fecha_finalizado").addEventListener("input", calcularHorasTotales);
document.getElementById("hora_parada_inicio").addEventListener("input", calcularHorasParada);
document.getElementById("hora_parada_fin").addEventListener("input", calcularHorasParada);

// Calcular al cargar
window.addEventListener("load", calcularHorasTotales);
</script>

<?php include('../../parte2.php'); ?>

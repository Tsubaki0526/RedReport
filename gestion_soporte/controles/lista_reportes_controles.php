<?php
include('../../app/config/conexion.php'); // ajusta la ruta si es diferente

// =================== REPORTES ===================
$sql_reportes = "SELECT id_r_registrado, empresa, radicado, operador, nombre, direccion, telefono, fecha, hora, observaciones, estado, forma
                 FROM tb_reportes_registrador 
                 WHERE estado != 'Finalizado'
                 ORDER BY fecha DESC, hora DESC";

$q_reportes = $pdo->prepare($sql_reportes);
$q_reportes->execute();
$reportes = $q_reportes->fetchAll(PDO::FETCH_ASSOC);

// =================== FUNCIÓN DÍAS HÁBILES ===================
function diasHabiles($fecha) {
    $inicio = new DateTime($fecha);
    $hoy = new DateTime();
    $diasHabiles = 0;

    while ($inicio <= $hoy) {
        $diaSemana = $inicio->format('N'); // 1 = lunes ... 7 = domingo
        if ($diaSemana >= 1 && $diaSemana <= 5) {
            $diasHabiles++;
        }
        $inicio->modify('+1 day');
    }
    return $diasHabiles - 1; // restamos el mismo día
}

// =================== ALERTAS ===================
$alert_count = 0;
$alertas = [];

foreach ($reportes as $rep) {
    $dias = diasHabiles($rep['fecha']);
    if (strtolower(trim($rep['estado'])) === 'pendiente' && $dias >= 3) {
        $alert_count++;
        $alertas[] = "⚠️ Radicado <strong>{$rep['radicado']}</strong> del cliente <strong>{$rep['nombre']}</strong> lleva <strong>{$dias} días hábiles</strong> pendiente.";
    }
}

// =================== GENERAR ALERTA SWAL ===================
if ($alert_count > 0) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: '🚨 Reportes atrasados',
            html: '<ul style=\"text-align:left;\">";
                foreach ($alertas as $a) { echo "<li>".$a."</li>"; }
    echo       "</ul>',
            confirmButtonText: 'Revisar'
        });
    </script>";
}
?>

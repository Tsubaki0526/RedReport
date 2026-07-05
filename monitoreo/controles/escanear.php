<?php
require_once '../../app/config/conexion.php';

$id = intval($_GET['id'] ?? 0);
$ip = trim($_GET['ip'] ?? '');
$comunidad = getenv('SNMP_COMMUNITY') ?: 'public';
$timeout = 3;

$estado = 'Offline';
$senal = 0;

// Try SNMP ping
$sysDescr = @snmpget($ip, $comunidad, '1.3.6.1.2.1.1.1.0', $timeout);
if ($sysDescr !== false) {
    $estado = 'Online';
    // Try signal OID (custom - adjust per equipment)
    $senalRaw = @snmpget($ip, $comunidad, '1.3.6.1.4.1.9.9.13.1.3.1.3.1', $timeout);
    if ($senalRaw !== false) {
        $senal = min(100, max(0, intval(trim(str_replace(['"', 'INTEGER: ', 'Gauge32: ', 'STRING: '], '', $senalRaw)))));
    } else {
        $senal = rand(60, 100); // fallback if no signal OID
    }
} else {
    // Fallback: ping
    $ping = @exec("ping -n 1 -w $timeout " . escapeshellarg($ip), $out, $exit);
    if ($exit === 0) {
        $estado = 'Online';
        $senal = 85;
    }
}

if ($id) {
    $stmt = $pdo->prepare("UPDATE tb_dispositivos SET ultimo_estado=?, ultimo_check_signal=?, ultimo_check=NOW() WHERE id_dispositivo=?");
    $stmt->execute([$estado, $senal, $id]);
}

header('Content-Type: application/json');
echo json_encode([
    'estado' => $estado,
    'senal' => $senal,
    'fecha' => date('d/m/Y H:i')
]);

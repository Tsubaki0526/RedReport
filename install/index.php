<?php
session_start();
$step = $_POST['step'] ?? 1;
$error = '';
$success = '';

$envFile = __DIR__ . '/../.env';
$sqlFile = __DIR__ . '/../redreport.sql';

$appUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/RedReport/';

function checkExt($ext) {
    return extension_loaded($ext);
}

function testDB($host, $name, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$name", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        return ['ok' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        // Try without db name (might not exist yet)
        try {
            $pdo = new PDO("mysql:host=$host", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$name`");
            return ['ok' => true, 'pdo' => $pdo];
        } catch (PDOException $e2) {
            return ['ok' => false, 'error' => $e2->getMessage()];
        }
    }
}

function importSQL($pdo, $file) {
    $sql = file_get_contents($file);
    if (!$sql) return "No se pudo leer el archivo SQL.";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $statements = explode(';', $sql);
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if (!empty($stmt)) {
            try {
                $pdo->exec($stmt);
            } catch (PDOException $e) {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                return "Error en sentencia SQL: " . $e->getMessage();
            }
        }
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    return '';
}

function writeEnv($file, $data) {
    $content = "# RedReport - Configuracion\n";
    $content .= "# Generado automaticamente por el instalador\n";
    $content .= "# Fecha: " . date('Y-m-d H:i:s') . "\n\n";
    $content .= "DB_HOST={$data['DB_HOST']}\n";
    $content .= "DB_NAME={$data['DB_NAME']}\n";
    $content .= "DB_USER={$data['DB_USER']}\n";
    $content .= "DB_PASS={$data['DB_PASS']}\n";
    $content .= "SMTP_HOST=smtp.gmail.com\n";
    $content .= "SMTP_PORT=587\n";
    $content .= "SMTP_USER=\n";
    $content .= "SMTP_PASS=\n";
    $content .= "SMTP_FROM=no-reply@redreport.com\n";
    $content .= "SMTP_FROM_NAME=RedReport\n";
    $content .= "APP_URL={$data['APP_URL']}\n";
    $content .= "APP_NAME=RedReport\n";
    $content .= "APP_ENV=production\n";
    $content .= "APP_DEBUG=false\n";
    $content .= "API_KEY=\n";
    return file_put_contents($file, $content) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'test_db') {
        $result = testDB($_POST['DB_HOST'], $_POST['DB_NAME'], $_POST['DB_USER'], $_POST['DB_PASS']);
        echo json_encode($result);
        exit;
    }

    if ($action === 'install') {
        $dbHost = $_POST['DB_HOST'] ?? 'localhost';
        $dbName = $_POST['DB_NAME'] ?? 'redreport';
        $dbUser = $_POST['DB_USER'] ?? 'root';
        $dbPass = $_POST['DB_PASS'] ?? '';
        $adminUser = $_POST['admin_user'] ?? 'administrador';
        $adminPass = $_POST['admin_pass'] ?? '';
        $adminEmail = $_POST['admin_email'] ?? '';
        $appUrl = $_POST['APP_URL'] ?? $appUrl;

        if (empty($adminPass) || strlen($adminPass) < 4) {
            echo json_encode(['ok' => false, 'error' => 'La contraseña del admin debe tener al menos 4 caracteres']);
            exit;
        }

        // Test DB
        $test = testDB($dbHost, $dbName, $dbUser, $dbPass);
        if (!$test['ok']) {
            echo json_encode(['ok' => false, 'error' => $test['error']]);
            exit;
        }
        $pdo = $test['pdo'];

        // Import SQL
        if (!file_exists($sqlFile)) {
            echo json_encode(['ok' => false, 'error' => 'No se encuentra redreport.sql en la raiz del proyecto']);
            exit;
        }
        $err = importSQL($pdo, $sqlFile);
        if ($err) {
            echo json_encode(['ok' => false, 'error' => $err]);
            exit;
        }

        // Update admin password
        $hash = password_hash($adminPass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE tb_usuarios SET password = ?, email = ? WHERE id_rol = 1 LIMIT 1");
        $stmt->execute([$hash, $adminEmail]);
        if ($stmt->rowCount() === 0) {
            // If no admin exists, create one
            $stmt = $pdo->prepare("INSERT INTO tb_usuarios (nombre, usuario, password, email, id_rol) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$adminUser, $adminUser, $hash, $adminEmail]);
        }

        // Write .env
        $envData = [
            'DB_HOST' => $dbHost,
            'DB_NAME' => $dbName,
            'DB_USER' => $dbUser,
            'DB_PASS' => $dbPass,
            'APP_URL' => rtrim($appUrl, '/') . '/',
        ];
        if (!writeEnv($envFile, $envData)) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo escribir el archivo .env. Verifica permisos.']);
            exit;
        }

        echo json_encode(['ok' => true]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Instalación - RedReport</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
body { background: #f0f2f5; font-family: 'Segoe UI', system-ui, sans-serif; }
.install-box { max-width: 720px; margin: 40px auto; }
.step-indicator { display: flex; justify-content: center; gap: 8px; margin-bottom: 30px; }
.step-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; background: #dee2e6; color: #6c757d; }
.step-dot.active { background: #0d6efd; color: #fff; }
.step-dot.done { background: #198754; color: #fff; }
.card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
.card-header { background: #fff; border-bottom: 1px solid #e9ecef; border-radius: 12px 12px 0 0 !important; padding: 1rem 1.5rem; }
.card-body { padding: 1.5rem; }
.requirement-pass { color: #198754; }
.requirement-fail { color: #dc3545; }
</style>
</head>
<body>
<div class="container install-box">
    <div class="text-center mb-4">
        <h2><i class="fas fa-tachometer-alt text-primary"></i> RedReport</h2>
        <p class="text-muted">Asistente de instalación</p>
    </div>

    <?php if (file_exists($envFile) && !isset($_POST['force'])): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
            <h3 class="mt-3">RedReport ya está instalado</h3>
            <p class="text-muted">El archivo <code>.env</code> ya existe en el servidor.</p>
            <p>Si deseas reinstalar, elimina el archivo <code>.env</code> manualmente o continúa con precaución.</p>
            <form method="POST">
                <input type="hidden" name="force" value="1">
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Reinstalar? Se perderán los datos actuales.');">
                    <i class="fas fa-redo"></i> Reinstalar
                </button>
            </form>
            <div class="mt-3">
                <a href="../index.php" class="btn btn-primary"><i class="fas fa-arrow-right"></i> Ir al inicio de sesión</a>
            </div>
        </div>
    </div>
    <?php else: ?>

    <div class="step-indicator" id="stepIndicator">
        <div class="step-dot active" id="dot1">1</div>
        <div class="step-dot" id="dot2">2</div>
        <div class="step-dot" id="dot3">3</div>
        <div class="step-dot" id="dot4">4</div>
    </div>

    <!-- Step 1: Requirements -->
    <div class="card" id="step1">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-server me-2"></i>Requisitos del servidor</h5></div>
        <div class="card-body">
            <?php
            $reqs = [
                ['PHP 7.4+', PHP_VERSION_ID >= 70400, PHP_VERSION],
                ['PDO Extension', checkExt('pdo'), ''],
                ['MySQL PDO', checkExt('pdo_mysql'), ''],
                ['GD Extension', checkExt('gd'), ''],
                ['MBString', checkExt('mbstring'), ''],
                ['DOM Extension', checkExt('dom'), ''],
                ['cURL Extension', checkExt('curl'), ''],
                ['JSON Extension', checkExt('json'), ''],
            ];
            $allPass = true;
            ?>
            <table class="table table-borderless mb-3">
                <?php foreach ($reqs as $r): $pass = $r[1]; if (!$pass) $allPass = false; ?>
                <tr>
                    <td><?= $r[0] ?></td>
                    <td class="text-end"><?= $r[2] ? "<code>{$r[2]}</code>" : '' ?></td>
                    <td class="text-end"><?= $pass ? '<span class="requirement-pass"><i class="fas fa-check-circle"></i></span>' : '<span class="requirement-fail"><i class="fas fa-times-circle"></i> No disponible</span>' ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td>redreport.sql</td>
                    <td></td>
                    <td class="text-end"><?= file_exists($sqlFile) ? '<span class="requirement-pass"><i class="fas fa-check-circle"></i></span>' : '<span class="requirement-fail"><i class="fas fa-times-circle"></i> No encontrado</span>' ?></td>
                </tr>
            </table>
            <button class="btn btn-primary" onclick="goStep(2)" <?= !$allPass ? 'disabled' : '' ?>>
                Continuar <i class="fas fa-arrow-right"></i>
            </button>
            <?php if (!$allPass): ?>
                <p class="text-danger mt-2 small">Corrige los requisitos faltantes antes de continuar.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Step 2: Database -->
    <div class="card d-none" id="step2">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-database me-2"></i>Configuración de base de datos</h5></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Host <small class="text-muted">(normalmente localhost)</small></label>
                <input type="text" id="DB_HOST" class="form-control" value="localhost">
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre de base de datos</label>
                <input type="text" id="DB_NAME" class="form-control" value="redreport">
            </div>
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" id="DB_USER" class="form-control" value="root">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" id="DB_PASS" class="form-control">
            </div>
            <div id="dbTestResult" class="mb-3"></div>
            <button class="btn btn-outline-secondary" onclick="goStep(1)"><i class="fas fa-arrow-left"></i> Atrás</button>
            <button class="btn btn-primary" onclick="testDBConn()" id="btnTestDB"><i class="fas fa-plug"></i> Probar conexión</button>
            <button class="btn btn-success" id="btnDBNext" disabled onclick="goStep(3)">Continuar <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- Step 3: Admin + URL -->
    <div class="card d-none" id="step3">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Administrador y URL</h5></div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">URL del sistema</label>
                <input type="text" id="APP_URL" class="form-control" value="<?= htmlspecialchars($appUrl, ENT_QUOTES, 'UTF-8') ?>">
                <small class="text-muted">Ej: https://midominio.com/RedReport/</small>
            </div>
            <hr>
            <div class="mb-3">
                <label class="form-label">Usuario administrador</label>
                <input type="text" id="admin_user" class="form-control" value="administrador">
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="text" id="admin_pass" class="form-control" value="admin" required>
                <small class="text-muted">Mínimo 4 caracteres. Cámbiala después del primer inicio.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Email del administrador</label>
                <input type="email" id="admin_email" class="form-control" value="admin@example.com">
            </div>
            <button class="btn btn-outline-secondary" onclick="goStep(2)"><i class="fas fa-arrow-left"></i> Atrás</button>
            <button class="btn btn-success" onclick="runInstall()" id="btnInstall"><i class="fas fa-rocket"></i> Instalar</button>
        </div>
    </div>

    <!-- Step 4: Done -->
    <div class="card d-none" id="step4">
        <div class="card-body text-center py-5">
            <div id="installResult">
                <i class="fas fa-spinner fa-pulse text-primary" style="font-size: 48px;"></i>
                <h4 class="mt-3">Instalando...</h4>
                <p class="text-muted">Importando base de datos y configurando el sistema.</p>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<script>
function goStep(n) {
    for (let i = 1; i <= 4; i++) {
        document.getElementById('step' + i).classList.add('d-none');
        document.getElementById('dot' + i).className = 'step-dot';
    }
    document.getElementById('step' + n).classList.remove('d-none');
    if (n < 4) document.getElementById('dot' + n).classList.add('active');
    for (let i = 1; i < n; i++) document.getElementById('dot' + i).classList.add('done');
}

function testDBConn() {
    const btn = document.getElementById('btnTestDB');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Probando...';
    document.getElementById('dbTestResult').innerHTML = '';

    const form = new FormData();
    form.append('action', 'test_db');
    form.append('DB_HOST', document.getElementById('DB_HOST').value);
    form.append('DB_NAME', document.getElementById('DB_NAME').value);
    form.append('DB_USER', document.getElementById('DB_USER').value);
    form.append('DB_PASS', document.getElementById('DB_PASS').value);

    fetch('index.php', { method: 'POST', body: form })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                document.getElementById('dbTestResult').innerHTML = '<div class="alert alert-success py-2 mb-0"><i class="fas fa-check-circle"></i> Conexión exitosa</div>';
                document.getElementById('btnDBNext').disabled = false;
            } else {
                document.getElementById('dbTestResult').innerHTML = '<div class="alert alert-danger py-2 mb-0"><i class="fas fa-times-circle"></i> ' + d.error + '</div>';
                document.getElementById('btnDBNext').disabled = true;
            }
        })
        .catch(e => {
            document.getElementById('dbTestResult').innerHTML = '<div class="alert alert-danger py-2 mb-0">Error de conexión</div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plug"></i> Probar conexión';
        });
}

function runInstall() {
    goStep(4);
    const resultDiv = document.getElementById('installResult');

    const form = new FormData();
    form.append('action', 'install');
    form.append('DB_HOST', document.getElementById('DB_HOST').value);
    form.append('DB_NAME', document.getElementById('DB_NAME').value);
    form.append('DB_USER', document.getElementById('DB_USER').value);
    form.append('DB_PASS', document.getElementById('DB_PASS').value);
    form.append('APP_URL', document.getElementById('APP_URL').value);
    form.append('admin_user', document.getElementById('admin_user').value);
    form.append('admin_pass', document.getElementById('admin_pass').value);
    form.append('admin_email', document.getElementById('admin_email').value);

    fetch('index.php', { method: 'POST', body: form })
        .then(r => r.json())
        .then(d => {
            if (d.ok) {
                resultDiv.innerHTML = `
                    <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                    <h3 class="mt-3 text-success">Instalación completada</h3>
                    <p>RedReport está listo para usar.</p>
                    <div class="alert alert-info text-start">
                        <strong>Usuario:</strong> ${document.getElementById('admin_user').value}<br>
                        <strong>Contraseña:</strong> (la que configuraste)
                    </div>
                    <a href="../index.php" class="btn btn-primary btn-lg mt-2">
                        <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                    </a>
                `;
            } else {
                resultDiv.innerHTML = `
                    <i class="fas fa-times-circle text-danger" style="font-size: 64px;"></i>
                    <h4 class="mt-3 text-danger">Error en la instalación</h4>
                    <p class="text-danger">${d.error}</p>
                    <button class="btn btn-outline-secondary mt-2" onclick="goStep(1)">Volver al inicio</button>
                `;
            }
        })
        .catch(e => {
            resultDiv.innerHTML = `<i class="fas fa-times-circle text-danger" style="font-size: 64px;"></i>
                <h4 class="mt-3 text-danger">Error de conexión</h4>
                <p>${e.message}</p>
                <button class="btn btn-outline-secondary mt-2" onclick="goStep(1)">Volver al inicio</button>`;
        });
}

// He escape for PHP output
function hescape(s) {
    if (!s) return '';
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
</script>
</body>
</html>

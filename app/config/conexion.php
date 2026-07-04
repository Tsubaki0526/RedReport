<?php
require_once __DIR__ . '/config.php';

try {
    $servidor = "mysql:dbname=" . DB_NAME . ";host=" . DB_HOST;
    $pdo = new PDO($servidor, DB_USER, DB_PASS, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) mkdir($logDir, 0755, true);
    file_put_contents("$logDir/app.log", date('Y-m-d H:i:s') . " [DB] " . $e->getMessage() . "\n", FILE_APPEND);

    if (APP_DEBUG) {
        echo "Error de conexion a la base de datos. Verifica las credenciales en el archivo .env";
    } else {
        echo "Error interno del servidor. Por favor contacte al administrador.";
    }
    exit();
}

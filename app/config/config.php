<?php
if (defined('APP_NAME')) return;

date_default_timezone_set('America/Bogota');

$dotenvFile = __DIR__ . '/../../.env';
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Fallback to getenv() for Railway / hosting env vars
foreach (['DB_HOST','DB_NAME','DB_USER','DB_PASS','APP_URL','APP_NAME','APP_ENV','APP_DEBUG'] as $k) {
    if (empty($_ENV[$k]) && getenv($k) !== false) {
        $_ENV[$k] = getenv($k);
    }
}

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'redreport');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com');
define('SMTP_PORT', intval($_ENV['SMTP_PORT'] ?? 587));
define('SMTP_USER', $_ENV['SMTP_USER'] ?? '');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? '');
define('SMTP_FROM', $_ENV['SMTP_FROM'] ?? 'no-reply@redreport.com');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'RedReport');

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if (str_ends_with($basePath, '\\') || str_ends_with($basePath, '/')) {
    $basePath = rtrim($basePath, '/\\');
}
define('APP_URL', rtrim($_ENV['APP_URL'] ?? "$protocol://$host$basePath", '/') . '/');
define('APP_NAME', $_ENV['APP_NAME'] ?? 'RedReport');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));

function app_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) return;
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) mkdir($logDir, 0755, true);
    $logLine = date('Y-m-d H:i:s') . " [$severity] $message in $file:$line\n";
    file_put_contents("$logDir/app.log", $logLine, FILE_APPEND);
    if (APP_DEBUG) {
        echo "<div style='background:#fdd;padding:10px;margin:10px;border:1px solid #c00;border-radius:4px;'><strong>Error:</strong> " . htmlspecialchars($message) . " en <code>$file:$line</code></div>";
    }
}

function app_exception_handler($e) {
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) mkdir($logDir, 0755, true);
    $logLine = date('Y-m-d H:i:s') . " [EXCEPTION] {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}\n{$e->getTraceAsString()}\n";
    file_put_contents("$logDir/app.log", $logLine, FILE_APPEND);
    if (APP_DEBUG) {
        echo "<div style='background:#fdd;padding:10px;margin:10px;border:1px solid #c00;border-radius:4px;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo "<h1>Error interno del servidor</h1><p>Por favor contacte al administrador.</p>";
    }
}

if (APP_ENV === 'production') {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

set_error_handler('app_error_handler');
set_exception_handler('app_exception_handler');

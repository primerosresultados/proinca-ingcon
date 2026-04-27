<?php

$configPath = __DIR__ . '/../config.php';
if (!file_exists($configPath)) {
    header('Location: /install/');
    exit;
}

require $configPath;
require __DIR__ . '/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/csrf.php';
require __DIR__ . '/mail.php';
require __DIR__ . '/migrate.php';

// Errores: log a archivo, nunca mostrar en producción.
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
$logDir = __DIR__ . '/../uploads/logs';
if (!is_dir($logDir)) @mkdir($logDir, 0750, true);
ini_set('error_log', $logDir . '/php-error.log');

// Timezone configurable vía settings (con fallback antes de tener DB disponible).
date_default_timezone_set(defined('APP_TIMEZONE') ? APP_TIMEZONE : 'America/Argentina/Buenos_Aires');

ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);

if (!empty($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', '1');
}

session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);

session_start();

// Idle timeout: invalida sesión si pasó SESSION_LIFETIME desde el último hit.
if (!empty($_SESSION['user_id'])) {
    $now = time();
    if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > SESSION_LIFETIME) {
        logout();
    } else {
        $_SESSION['last_activity'] = $now;
    }
}

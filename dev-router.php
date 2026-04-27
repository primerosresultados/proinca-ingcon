<?php
// Router para `php -S` en desarrollo local.
// Devuelve `false` para archivos estáticos existentes (assets, imágenes...)
// para que el server los sirva con su MIME correcto. Cualquier otra cosa
// la maneja index.php.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';

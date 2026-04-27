<?php

function clientIp(): string {
    // Hostinger/Cloudflare: preferir cabeceras seteadas por el edge.
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP'] as $h) {
        if (!empty($_SERVER[$h])) {
            $ip = trim(explode(',', $_SERVER[$h])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function flashSet(string $key, string $msg): void {
    $_SESSION['_flash'][$key] = $msg;
}

function flashGet(string $key): ?string {
    $msg = $_SESSION['_flash'][$key] ?? null;
    if ($msg !== null) unset($_SESSION['_flash'][$key]);
    return $msg;
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function slugify(string $text): string {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $text));
    return trim($text, '-');
}

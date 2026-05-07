<?php

/**
 * Stubs para preview local sin MySQL.
 * Solo se carga si DEV_PREVIEW=1.
 */

if (!function_exists('csrfToken')) {
    function csrfToken(): string { return 'dev-preview-token'; }
    function csrfCheck(): void {}

    function getSetting(string $k, ?string $d = null): ?string {
        $map = [
            'site_name'     => 'Alexis Bello — PROINCA · INGEPUCON',
            'contact_phone' => '+56 9 1234 5678',
            'contact_email' => 'contacto@alexisbello.cl',
        ];
        return $map[$k] ?? $d;
    }

    function redirect(string $to): void { header('Location: ' . $to); exit; }

    function slugify(string $s): string {
        $s = strtolower(trim($s));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);
        return trim($s, '-');
    }

    function getDB(): PDO { throw new RuntimeException('DB disabled in preview'); }
    function clientIp(): string { return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'; }
    function notifyLeadCreated(array $l): void { error_log('[preview] lead: ' . json_encode($l)); }
    function sendLeadAutoReply(array $l): void {}
}

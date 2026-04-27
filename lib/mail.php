<?php

// Notificación por email usando mail() nativo (Hostinger lo habilita por default).
// Para SMTP autenticado, reemplazar por PHPMailer.

function sendMail(string $to, string $subject, string $body, ?string $fromOverride = null): bool {
    $to = trim($to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

    $from = $fromOverride ?: getSetting('notification_from', '');
    if (!$from || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $from = 'no-reply@' . preg_replace('/^www\./', '', $host);
    }

    $siteName = getSetting('site_name', 'Mi Sitio');
    $headers  = [];
    $headers[] = 'From: ' . mb_encode_mimeheader($siteName) . ' <' . $from . '>';
    $headers[] = 'Reply-To: ' . $from;
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    return @mail($to, $subject, $body, implode("\r\n", $headers), '-f' . $from);
}

function notifyLeadCreated(array $lead): void {
    $to = trim((string) getSetting('notification_email', ''));
    if (!$to) return;

    $siteName = getSetting('site_name', 'Mi Sitio');
    $subject  = "[$siteName] Nuevo lead: " . $lead['name'];
    $body  = "Nuevo lead recibido desde $siteName\n\n";
    $body .= "Nombre:   " . $lead['name'] . "\n";
    $body .= "Email:    " . $lead['email'] . "\n";
    $body .= "Teléfono: " . ($lead['phone'] ?: '—') . "\n";
    $body .= "Source:   " . ($lead['source'] ?: 'website') . "\n";
    $body .= "Fecha:    " . date('Y-m-d H:i') . "\n\n";
    $body .= "Mensaje:\n" . ($lead['message'] ?: '—') . "\n\n";
    $body .= "Ver en admin: " . (($_SERVER['HTTPS'] ?? '') ? 'https' : 'http') . '://'
           . ($_SERVER['HTTP_HOST'] ?? '') . '/admin/?id=' . (int) ($lead['id'] ?? 0) . "\n";

    sendMail($to, $subject, $body);
}

function sendLeadAutoReply(array $lead): void {
    if (getSetting('autoreply_enabled', '0') !== '1') return;
    $subject = (string) getSetting('autoreply_subject', 'Recibimos tu mensaje');
    $tpl     = (string) getSetting('autoreply_body', '');
    $body    = strtr($tpl, [
        '{{name}}'  => $lead['name'],
        '{{email}}' => $lead['email'],
    ]);
    sendMail($lead['email'], $subject, $body);
}

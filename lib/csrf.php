<?php

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfCheck(): void {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(403);
        exit('CSRF token inválido');
    }
}

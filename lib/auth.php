<?php

function currentUser(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $stmt = getDB()->prepare('SELECT id, email FROM users WHERE id = ? AND is_active = 1');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin(): void {
    if (!currentUser()) {
        header('Location: /admin/');
        exit;
    }
}

// Rate-limit por IP y por email en DB (resistente a borrado de cookies).
// Ventana: 15 min. Máx: 10 intentos por IP, 5 por email.
function loginRateLimitOk(string $email, string $ip): bool {
    $db = getDB();
    $db->exec('DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY)');

    $stmt = $db->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)'
    );
    $stmt->execute([$ip]);
    if ((int) $stmt->fetchColumn() >= 10) return false;

    if ($email !== '') {
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE email = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)'
        );
        $stmt->execute([$email]);
        if ((int) $stmt->fetchColumn() >= 5) return false;
    }
    return true;
}

function loginRateLimitRecord(string $email, string $ip): void {
    $stmt = getDB()->prepare('INSERT INTO login_attempts (ip_address, email) VALUES (?, ?)');
    $stmt->execute([$ip, $email ?: null]);
}

function loginRateLimitClear(string $email, string $ip): void {
    $stmt = getDB()->prepare('DELETE FROM login_attempts WHERE ip_address = ? OR email = ?');
    $stmt->execute([$ip, $email]);
}

function login(string $email, string $password): string {
    $ip = clientIp();
    if (!loginRateLimitOk($email, $ip)) return 'rate_limited';

    $stmt = getDB()->prepare('SELECT id, password_hash, is_active FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash']) || !(int) $user['is_active']) {
        loginRateLimitRecord($email, $ip);
        return 'invalid';
    }

    session_regenerate_id(true);
    $_SESSION['user_id']    = (int) $user['id'];
    $_SESSION['login_time'] = time();
    loginRateLimitClear($email, $ip);
    return 'ok';
}

function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function changePassword(int $userId, string $current, string $new): bool {
    $stmt = getDB()->prepare('SELECT password_hash FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($current, $user['password_hash'])) {
        return false;
    }
    $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = getDB()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $stmt->execute([$hash, $userId]);
    session_regenerate_id(true);
    return true;
}

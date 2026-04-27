<?php

// Corre todos los /migrations/NNN_*.sql que no estén registrados.
// Idempotente + cache por sesión (evita re-escanear en cada pageload).
// Archivos que empiezan con `_` se IGNORAN (útil para templates/ejemplos).
function runMigrations(): array {
    $files = glob(__DIR__ . '/../migrations/[!_]*.sql') ?: [];
    sort($files);
    if (empty($files)) return [];

    $hash = md5(implode('|', array_map('basename', $files)));
    $hasSession = session_status() === PHP_SESSION_ACTIVE;

    // Fast path: si en esta sesión ya corrimos con este set de archivos, salir
    if ($hasSession && ($_SESSION['_migrations_hash'] ?? null) === $hash) {
        return [];
    }

    $db = getDB();

    $db->exec(
        'CREATE TABLE IF NOT EXISTS migrations (
            filename VARCHAR(255) PRIMARY KEY,
            applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $applied = $db->query('SELECT filename FROM migrations')->fetchAll(PDO::FETCH_COLUMN);

    $ran = [];
    foreach ($files as $file) {
        $name = basename($file);
        if (in_array($name, $applied, true)) continue;

        $sql = file_get_contents($file);
        if ($sql === false || trim($sql) === '') continue;

        $db->exec($sql);

        $stmt = $db->prepare('INSERT INTO migrations (filename) VALUES (?)');
        $stmt->execute([$name]);
        $ran[] = $name;
    }

    if ($hasSession) {
        $_SESSION['_migrations_hash'] = $hash;
    }
    return $ran;
}

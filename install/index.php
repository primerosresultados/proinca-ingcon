<?php

$configPath  = __DIR__ . '/../config.php';
$installLock = __DIR__ . '/installed.lock';

if (file_exists($installLock) || file_exists($configPath)) {
    http_response_code(403);
    exit('Instalación bloqueada. Borrá /install/ del servidor.');
}

$step  = 'form';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host  = trim($_POST['db_host']     ?? '');
    $name  = trim($_POST['db_name']     ?? '');
    $user  = trim($_POST['db_user']     ?? '');
    $pass  = $_POST['db_pass']          ?? '';
    $email = trim($_POST['admin_email'] ?? '');
    $pw    = $_POST['admin_pw']         ?? '';

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pw) < 8) {
        $error = 'Email válido y contraseña (mín. 8 caracteres) son requeridos.';
    }

    if (!$error) {
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$name;charset=utf8mb4",
                $user, $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            error_log('Install DB error: ' . $e->getMessage());
            $error = 'No se pudo conectar a la base de datos. Revisá host, nombre, usuario y contraseña.';
        }
    }

    if (!$error) {
        $configContent = "<?php\n\n"
            . "define('DB_HOST', "     . var_export($host, true) . ");\n"
            . "define('DB_NAME', "     . var_export($name, true) . ");\n"
            . "define('DB_USER', "     . var_export($user, true) . ");\n"
            . "define('DB_PASS', "     . var_export($pass, true) . ");\n"
            . "define('DB_CHARSET', 'utf8mb4');\n\n"
            . "define('SITE_URL', (isset(\$_SERVER['HTTPS']) ? 'https://' : 'http://') . (\$_SERVER['HTTP_HOST'] ?? 'localhost'));\n"
            . "define('SESSION_LIFETIME', 7200);\n"
            . "define('APP_TIMEZONE', 'America/Argentina/Buenos_Aires');\n";

        if (file_put_contents($configPath, $configContent, LOCK_EX) === false) {
            $error = 'No se pudo escribir config.php. Revisá permisos del directorio raíz.';
        }
    }

    if (!$error) {
        require $configPath;
        require __DIR__ . '/../lib/db.php';
        require __DIR__ . '/../lib/migrate.php';

        try {
            runMigrations();

            $hash = password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = getDB()->prepare('INSERT INTO users (email, password_hash, is_active) VALUES (?, ?, 1)');
            $stmt->execute([$email, $hash]);

            $stmt = getDB()->prepare(
                'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
            );
            $stmt->execute(['notification_email', $email]);

            @file_put_contents($installLock, date('c'));
            @file_put_contents(__DIR__ . '/.htaccess', "Require all denied\n");

            $step = 'done';
        } catch (Throwable $e) {
            error_log('Install error: ' . $e->getMessage());
            @unlink($configPath);
            $error = 'Error durante la instalación. Revisá el log del servidor.';
        }
    }
}

$stepIdx = $step === 'done' ? 2 : 1;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Instalación</title>
<link rel="stylesheet" href="/assets/css/base.css">
<link rel="stylesheet" href="/assets/css/components.css">
<link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-body">

<div class="auth-card auth-card--wide">

    <div class="auth-brand">
        <span class="auth-brand__logo">★</span>
        <span>Instalación del sitio</span>
    </div>

    <div class="auth-steps">
        <div class="auth-step <?= $stepIdx === 1 ? 'auth-step--active' : '' ?>">
            <span class="auth-step__dot"><?= $stepIdx > 1 ? '✓' : '1' ?></span>
            <span>Configuración</span>
        </div>
        <div class="auth-step__sep"></div>
        <div class="auth-step <?= $stepIdx === 2 ? 'auth-step--active' : '' ?>">
            <span class="auth-step__dot">2</span>
            <span>Finalizar</span>
        </div>
    </div>

    <?php if ($step === 'done'): ?>

        <div class="auth-done__center">
            <div class="auth-done__icon">✓</div>
            <h1>Instalación completa</h1>
            <p class="auth-subtitle">El directorio <code>/install/</code> quedó bloqueado automáticamente.</p>
        </div>

        <div class="auth-alert auth-alert--info">
            <span><strong>Importante:</strong> por seguridad, borrá la carpeta <code>/install/</code> del servidor cuando puedas.</span>
        </div>

        <a class="btn auth-submit" href="/admin/">Ir al panel admin →</a>

    <?php else: ?>

        <h1>Configurá tu sitio</h1>
        <p class="auth-subtitle">Conectá la base de datos y creá tu cuenta de administrador. Toma menos de un minuto.</p>

        <?php if ($error): ?>
            <div class="auth-alert auth-alert--error">
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off" novalidate>
            <fieldset class="auth-fieldset">
                <legend>Base de datos</legend>

                <div class="auth-grid-2">
                    <div class="auth-field">
                        <label for="db_host">Host</label>
                        <input id="db_host" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                        <small class="auth-field__hint">En Hostinger suele ser <code>localhost</code>.</small>
                    </div>
                    <div class="auth-field">
                        <label for="db_name">Nombre</label>
                        <input id="db_name" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>" required placeholder="u123_misitio">
                    </div>
                </div>

                <div class="auth-grid-2">
                    <div class="auth-field">
                        <label for="db_user">Usuario</label>
                        <input id="db_user" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required placeholder="u123_admin">
                    </div>
                    <div class="auth-field auth-field--password">
                        <label for="db_pass">Contraseña</label>
                        <div class="auth-field__control">
                            <input id="db_pass" name="db_pass" type="password" placeholder="••••••••">
                            <button type="button" class="auth-field__toggle" data-toggle="db_pass">Ver</button>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="auth-fieldset">
                <legend>Cuenta de administrador</legend>

                <div class="auth-field">
                    <label for="admin_email">Email</label>
                    <input id="admin_email" type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" required placeholder="vos@tudominio.com">
                    <small class="auth-field__hint">Vas a recibir los leads en esta dirección.</small>
                </div>

                <div class="auth-field auth-field--password">
                    <label for="admin_pw">Contraseña</label>
                    <div class="auth-field__control">
                        <input id="admin_pw" name="admin_pw" type="password" required minlength="8" placeholder="Mínimo 8 caracteres">
                        <button type="button" class="auth-field__toggle" data-toggle="admin_pw">Ver</button>
                    </div>
                    <small class="auth-field__hint">Mínimo 8 caracteres. Guardala en un lugar seguro.</small>
                </div>
            </fieldset>

            <button type="submit" class="btn auth-submit">Instalar</button>
        </form>

    <?php endif; ?>

</div>

<script>
document.querySelectorAll('[data-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.toggle);
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.textContent = show ? 'Ocultar' : 'Ver';
    });
});
</script>

</body>
</html>

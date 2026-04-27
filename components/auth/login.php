<?php
/** Requiere: $siteName, $initial, $loginError */
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Iniciar sesión — <?= htmlspecialchars($siteName) ?></title>
<link rel="stylesheet" href="/assets/css/base.css">
<link rel="stylesheet" href="/assets/css/components.css">
<link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body class="auth-body">
    <div class="auth-card">
        <div class="auth-brand">
            <span class="auth-brand__logo"><?= htmlspecialchars($initial) ?></span>
            <span><?= htmlspecialchars($siteName) ?></span>
        </div>

        <h1>Bienvenido</h1>
        <p class="auth-subtitle">Iniciá sesión para acceder al panel.</p>

        <?php if (!empty($loginError)): ?>
            <div class="auth-alert auth-alert--error">
                <span><?= htmlspecialchars($loginError) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">

            <div class="auth-field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" required autofocus autocomplete="email" placeholder="vos@ejemplo.com">
            </div>

            <div class="auth-field auth-field--password">
                <label for="password">Contraseña</label>
                <div class="auth-field__control">
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <button type="button" class="auth-field__toggle" data-toggle="password" aria-label="Mostrar contraseña">Ver</button>
                </div>
            </div>

            <button type="submit" class="btn auth-submit">Entrar</button>
        </form>

        <div class="auth-footer">
            <a href="/">← Volver al sitio</a>
        </div>
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

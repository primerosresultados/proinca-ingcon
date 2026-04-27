<?php
/** Requiere: $settings (array key => value) */
?>
<header class="admin-header">
    <div>
        <h1>Configuración</h1>
        <div class="admin-header__sub">Ajustes generales del sitio, notificaciones y tracking.</div>
    </div>
</header>

<?php if ($msg = flashGet('settings_success')): ?>
    <div class="auth-alert auth-alert--success"><span><?= htmlspecialchars($msg) ?></span></div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">

    <div class="card">
        <h3 class="card__title">General</h3>
        <p class="form__field"><label>Nombre del sitio
            <input name="s[site_name]" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
        </label></p>
        <p class="form__field" style="margin:0;"><label>Timezone
            <input name="s[timezone]" value="<?= htmlspecialchars($settings['timezone'] ?? 'America/Argentina/Buenos_Aires') ?>">
        </label></p>
    </div>

    <div class="card">
        <h3 class="card__title">Notificaciones de leads</h3>
        <p class="form__field"><label>Email destino (recibe los leads)
            <input type="email" name="s[notification_email]" value="<?= htmlspecialchars($settings['notification_email'] ?? '') ?>">
        </label></p>
        <p class="form__field"><label>From: (remitente — debe ser de tu dominio)
            <input type="email" name="s[notification_from]" value="<?= htmlspecialchars($settings['notification_from'] ?? '') ?>" placeholder="no-reply@tudominio.com">
        </label></p>

        <p class="form__field">
            <label style="display:flex;align-items:center;gap:.5rem;">
                <input type="checkbox" name="s[autoreply_enabled]" value="1" style="width:auto;" <?= ($settings['autoreply_enabled'] ?? '0') === '1' ? 'checked' : '' ?>>
                <span>Enviar auto-respuesta al lead</span>
            </label>
        </p>
        <p class="form__field"><label>Asunto auto-respuesta
            <input name="s[autoreply_subject]" value="<?= htmlspecialchars($settings['autoreply_subject'] ?? '') ?>">
        </label></p>
        <p class="form__field" style="margin:0;"><label>Cuerpo auto-respuesta (variables: <code>{{name}}</code>, <code>{{email}}</code>)
            <textarea name="s[autoreply_body]" rows="5"><?= htmlspecialchars($settings['autoreply_body'] ?? '') ?></textarea>
        </label></p>
    </div>

    <div class="card">
        <h3 class="card__title">Tracking</h3>
        <p class="form__field"><label>Google Analytics ID (G-XXXXXXX)
            <input name="s[ga_id]" value="<?= htmlspecialchars($settings['ga_id'] ?? '') ?>">
        </label></p>
        <p class="form__field" style="margin:0;"><label>Facebook Pixel ID
            <input name="s[pixel_id]" value="<?= htmlspecialchars($settings['pixel_id'] ?? '') ?>">
        </label></p>
    </div>

    <p style="margin-top:1.5rem;"><button type="submit" class="btn">Guardar cambios</button></p>
</form>

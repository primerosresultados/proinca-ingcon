<?php
/*
 * Lead form (público)
 * ------------------------------------------------------------
 * Form de contacto con honeypot + timing check + CSRF.
 * El handler POST vive en /index.php (action = 'submit_lead').
 *
 * Uso:
 *   require __DIR__ . '/components/lead_form.php';
 *
 * Attribution:
 *   Setear $leadSource antes del require para distinguir origen
 *   (ej: 'facebook_ad', 'google_campaign_2026'). Default: 'website'.
 * ------------------------------------------------------------
 */
$leadSource = $leadSource ?? 'website';
?>
<form method="post" class="lead-form">
    <input type="hidden" name="action" value="submit_lead">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <input type="hidden" name="form_started" value="<?= time() ?>">
    <input type="hidden" name="source" value="<?= htmlspecialchars($leadSource) ?>">
    <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off">

    <p class="lead-form__field">
        <label>Nombre <input name="name" required></label>
    </p>
    <p class="lead-form__field">
        <label>Email <input name="email" type="email" required></label>
    </p>
    <p class="lead-form__field">
        <label>Teléfono <input name="phone"></label>
    </p>
    <p class="lead-form__field">
        <label>Mensaje <textarea name="message" rows="4"></textarea></label>
    </p>
    <p class="lead-form__submit">
        <button type="submit" class="btn">Enviar</button>
    </p>
</form>

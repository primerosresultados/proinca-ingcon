<?php
/** Requiere: $user, $pwError */
?>
<header class="admin-header">
    <div>
        <h1>Mi cuenta</h1>
        <div class="admin-header__sub"><?= htmlspecialchars($user['email']) ?></div>
    </div>
</header>

<div class="card" style="max-width:480px;">
    <h3 class="card__title">Cambiar contraseña</h3>

    <?php if ($msg = flashGet('pw_success')): ?>
        <div class="auth-alert auth-alert--success"><span><?= htmlspecialchars($msg) ?></span></div>
    <?php endif; ?>
    <?php if ($pwError): ?>
        <div class="auth-alert auth-alert--error"><span><?= htmlspecialchars($pwError) ?></span></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="change_password">
        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
        <p class="form__field"><label>Contraseña actual <input type="password" name="current_password" required></label></p>
        <p class="form__field"><label>Nueva contraseña <input type="password" name="new_password" required minlength="8"></label></p>
        <p class="form__field"><label>Repetir nueva contraseña <input type="password" name="confirm_password" required minlength="8"></label></p>
        <p class="form__submit" style="margin:0;"><button type="submit" class="btn">Actualizar contraseña</button></p>
    </form>
</div>

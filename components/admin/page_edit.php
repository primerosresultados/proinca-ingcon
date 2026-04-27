<?php
/** Requiere: $page, $pageError */
$isNew = empty($page['id']);
?>
<header class="admin-header">
    <div>
        <div style="margin-bottom:.3rem;"><a href="/admin/?view=pages" class="text-muted" style="font-size:.88rem;">← Volver a páginas</a></div>
        <h1><?= $isNew ? 'Nueva página' : 'Editar página' ?></h1>
        <?php if (!$isNew): ?>
            <div class="admin-header__sub">/<?= htmlspecialchars($page['slug'] ?? '') ?></div>
        <?php endif; ?>
    </div>
</header>

<?php if ($pageError): ?>
    <div class="auth-alert auth-alert--error"><span><?= htmlspecialchars($pageError) ?></span></div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="action" value="save_page">
    <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
    <input type="hidden" name="id" value="<?= (int) ($page['id'] ?? 0) ?>">

    <div class="card">
        <h3 class="card__title">Contenido</h3>

        <p class="form__field"><label>Slug <small class="text-muted">(URL: /slug)</small>
            <input name="slug" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" required pattern="[a-z0-9-]+" placeholder="sobre-nosotros">
        </label></p>

        <p class="form__field"><label>Título
            <input name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
        </label></p>

        <p class="form__field" style="margin:0;"><label>Contenido <small class="text-muted">(HTML permitido)</small>
            <textarea name="body" rows="18" style="font-family:var(--font-family-mono);font-size:.88rem;"><?= htmlspecialchars($page['body'] ?? '') ?></textarea>
        </label></p>
    </div>

    <div class="card">
        <h3 class="card__title">SEO & publicación</h3>

        <p class="form__field"><label>Meta description <small class="text-muted">(~160 chars)</small>
            <input name="meta_description" maxlength="300" value="<?= htmlspecialchars($page['meta_description'] ?? '') ?>">
        </label></p>

        <p class="form__field" style="margin:0;">
            <label style="display:flex;align-items:center;gap:.5rem;">
                <input type="checkbox" name="is_published" value="1" style="width:auto;" <?= !empty($page['is_published']) || $isNew ? 'checked' : '' ?>>
                <span>Publicada (visible en /<?= htmlspecialchars($page['slug'] ?? 'slug') ?>)</span>
            </label>
        </p>
    </div>

    <div style="margin-top:1.5rem;display:flex;gap:.5rem;justify-content:space-between;align-items:center;flex-wrap:wrap;">
        <button type="submit" class="btn">Guardar</button>
        <?php if (!$isNew): ?>
            <button type="submit" name="action" value="delete_page" class="btn btn--danger"
                onclick="return confirm('¿Eliminar esta página?')">Eliminar página</button>
        <?php endif; ?>
    </div>
</form>

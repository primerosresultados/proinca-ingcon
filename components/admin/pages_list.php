<?php
/** Requiere: $pages (array) */
?>
<header class="admin-header">
    <div>
        <h1>Páginas</h1>
        <div class="admin-header__sub">Contenido estático accesible desde /slug.</div>
    </div>
    <div class="admin-header__actions">
        <a class="btn" href="/admin/?view=page&amp;id=new">+ Nueva página</a>
    </div>
</header>

<?php if ($msg = flashGet('page_success')): ?>
    <div class="auth-alert auth-alert--success"><span><?= htmlspecialchars($msg) ?></span></div>
<?php endif; ?>

<?php if (empty($pages)): ?>
    <div class="empty">
        <h3>Sin páginas</h3>
        <p>Creá tu primera página para agregarle contenido al sitio (ej. "Sobre nosotros", "Términos").</p>
        <p style="margin-top:1rem;"><a class="btn" href="/admin/?view=page&amp;id=new">+ Crear primera página</a></p>
    </div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Slug</th>
                <th>Título</th>
                <th style="width:120px;">Estado</th>
                <th style="width:170px;">Actualizada</th>
                <th style="width:120px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $p): ?>
                <tr>
                    <td><code><?= htmlspecialchars($p['slug']) ?></code></td>
                    <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                    <td>
                        <?php if ((int) $p['is_published']): ?>
                            <span class="badge badge--qualified">publicada</span>
                        <?php else: ?>
                            <span class="badge badge--closed">borrador</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted" style="font-variant-numeric:tabular-nums;"><?= htmlspecialchars($p['updated_at']) ?></td>
                    <td>
                        <a href="/admin/?view=page&amp;id=<?= (int) $p['id'] ?>">Editar</a>
                        <?php if ((int) $p['is_published']): ?>
                             · <a href="/<?= htmlspecialchars($p['slug']) ?>" target="_blank">Ver →</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
/** Requiere: $lead, $notes, $LEAD_STATUSES */
?>
<header class="admin-header">
    <div>
        <div style="margin-bottom:.3rem;"><a href="/admin/" class="text-muted" style="font-size:.88rem;">← Volver a leads</a></div>
        <h1><?= htmlspecialchars($lead['name']) ?> <span class="badge badge--<?= htmlspecialchars($lead['status']) ?>" style="font-size:.7rem;vertical-align:middle;"><?= htmlspecialchars($lead['status']) ?></span></h1>
        <div class="admin-header__sub">Lead #<?= (int) $lead['id'] ?> · <?= htmlspecialchars($lead['created_at']) ?></div>
    </div>
    <div class="admin-header__actions">
        <a class="btn btn--secondary" href="mailto:<?= htmlspecialchars($lead['email']) ?>">Responder por email</a>
    </div>
</header>

<div class="lead-detail">
    <dl>
        <dt>Email</dt>    <dd><a href="mailto:<?= htmlspecialchars($lead['email']) ?>"><?= htmlspecialchars($lead['email']) ?></a></dd>
        <dt>Teléfono</dt> <dd><?= htmlspecialchars($lead['phone'] ?? '') ?: '—' ?></dd>
        <dt>Mensaje</dt>  <dd><?= nl2br(htmlspecialchars($lead['message'] ?? '')) ?: '—' ?></dd>
        <dt>Source</dt>   <dd><?= htmlspecialchars($lead['source'] ?? 'website') ?></dd>
        <dt>IP</dt>       <dd class="text-muted"><?= htmlspecialchars($lead['ip_address'] ?? '—') ?></dd>
    </dl>
</div>

<section class="admin-section">
    <h2>Cambiar estado</h2>
    <form method="post" class="inline-form" style="margin:0;">
        <input type="hidden" name="action" value="update_lead_status">
        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= (int) $lead['id'] ?>">
        <select name="status">
            <?php foreach ($LEAD_STATUSES as $s): ?>
                <option value="<?= $s ?>" <?= $lead['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Guardar</button>
    </form>
</section>

<section class="admin-section">
    <h2>Notas</h2>
    <?php if (!empty($lead['notes'])): ?>
        <div class="alert" style="margin-bottom:1rem;">
            <strong>Notas legacy:</strong>
            <pre style="white-space:pre-wrap;margin:.4rem 0 0;background:transparent;border:0;padding:0;"><?= htmlspecialchars($lead['notes']) ?></pre>
        </div>
    <?php endif; ?>

    <?php if (empty($notes)): ?>
        <?php if (empty($lead['notes'])): ?>
            <p class="text-muted" style="margin:0 0 1rem;">Todavía no hay notas.</p>
        <?php endif; ?>
    <?php else: ?>
        <ul class="notes-list">
            <?php foreach ($notes as $n): ?>
                <li>
                    <div class="note__meta">
                        <span><?= htmlspecialchars($n['created_at']) ?></span>
                        <?php if ($n['author_email']): ?>
                            <span class="dot"></span><span><?= htmlspecialchars($n['author_email']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div><?= nl2br(htmlspecialchars($n['body'])) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" style="margin-top:1rem;">
        <input type="hidden" name="action" value="add_note">
        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= (int) $lead['id'] ?>">
        <p class="form__field"><textarea name="note" rows="3" placeholder="Nueva nota..." required></textarea></p>
        <p class="form__submit" style="margin:0;"><button type="submit" class="btn">Agregar nota</button></p>
    </form>
</section>

<section class="admin-section" style="border-color:#fecaca;background:#fef9f9;">
    <h2 style="color:#991b1b;">Zona peligrosa</h2>
    <p class="text-muted" style="margin-bottom:.8rem;font-size:.88rem;">Eliminar un lead es permanente. También borra las notas asociadas.</p>
    <form method="post" onsubmit="return confirm('¿Eliminar este lead? Esta acción no se puede deshacer.')" style="margin:0;">
        <input type="hidden" name="action" value="delete_lead">
        <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="id" value="<?= (int) $lead['id'] ?>">
        <button type="submit" class="btn btn--danger">Eliminar lead</button>
    </form>
</section>

<?php
/** Requiere: $stats, $leads, $search, $statusFilter, $page, $totalPages, $totalLeads, $LEAD_STATUSES, $paginationUrl */
?>
<header class="admin-header">
    <div>
        <h1>Leads</h1>
        <div class="admin-header__sub">Contactos recibidos desde el sitio.</div>
    </div>
    <div class="admin-header__actions">
        <a class="btn btn--secondary" href="/admin/?action=export_csv&amp;<?= http_build_query(['search' => $search, 'status_filter' => $statusFilter]) ?>">
            Exportar CSV
        </a>
    </div>
</header>

<div class="stats">
    <div class="stat"><div class="stat__label">Total</div><div class="stat__value"><?= $stats['total'] ?></div></div>
    <div class="stat"><div class="stat__label">Hoy</div><div class="stat__value"><?= $stats['today'] ?></div></div>
    <div class="stat"><div class="stat__label">Últimos 7 días</div><div class="stat__value"><?= $stats['this_week'] ?></div></div>
    <div class="stat"><div class="stat__label">Sin atender</div><div class="stat__value"><?= $stats['new'] ?></div></div>
</div>

<form method="get" class="filters">
    <div class="filters__group">
        <label for="search">Buscar</label>
        <input type="search" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Nombre, email o teléfono">
    </div>
    <div class="filters__group">
        <label for="status_filter">Estado</label>
        <select id="status_filter" name="status_filter">
            <option value="">Todos</option>
            <?php foreach ($LEAD_STATUSES as $s): ?>
                <option value="<?= $s ?>" <?= $statusFilter === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filters__group filters__group--actions">
        <button type="submit" class="btn">Filtrar</button>
        <?php if ($search !== '' || $statusFilter !== ''): ?>
            <a href="/admin/" class="btn btn--ghost">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<?php if (empty($leads)): ?>
    <div class="empty">
        <h3>No hay leads todavía</h3>
        <p>Cuando alguien complete el formulario del sitio, va a aparecer acá.</p>
    </div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th style="width:60px;">ID</th>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Source</th>
                <th>Estado</th>
                <th style="width:60px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($leads as $l): ?>
                <tr>
                    <td class="text-muted">#<?= (int) $l['id'] ?></td>
                    <td class="text-muted" style="font-variant-numeric:tabular-nums;"><?= htmlspecialchars($l['created_at']) ?></td>
                    <td><strong><?= htmlspecialchars($l['name']) ?></strong></td>
                    <td><?= htmlspecialchars($l['email']) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($l['source'] ?? 'website') ?></td>
                    <td><span class="badge badge--<?= htmlspecialchars($l['status']) ?>"><?= htmlspecialchars($l['status']) ?></span></td>
                    <td><a href="/admin/?id=<?= (int) $l['id'] ?>">Ver →</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <nav class="pagination">
        <span class="pagination__info">Mostrando <?= count($leads) ?> de <?= $totalLeads ?></span>
        <?php if ($totalPages > 1): ?>
            <?php if ($page > 1): ?><a href="<?= $paginationUrl($page - 1) ?>" class="btn btn--secondary">← Anterior</a><?php endif; ?>
            <span class="text-muted" style="font-size:.88rem;">Página <?= $page ?> de <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?><a href="<?= $paginationUrl($page + 1) ?>" class="btn btn--secondary">Siguiente →</a><?php endif; ?>
        <?php endif; ?>
    </nav>
<?php endif; ?>

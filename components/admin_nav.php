<?php
/** Sidebar del admin. Requiere: $user, $view (opcional). */
$view = $view ?? '';
$is = fn(string $v) => $view === $v;
$siteName = getSetting('site_name', 'Mi Sitio');
$initial  = strtoupper(mb_substr($siteName, 0, 1)) ?: 'A';
$userInitial = strtoupper(mb_substr($user['email'] ?? '?', 0, 1));

// Simple helper para el link activo cuando es la vista "leads" (sin ?view=)
$activeLeads = $view === '' || $view === 'leads';
?>

<div class="admin-sidebar__backdrop" id="sidebar-backdrop" aria-hidden="true"></div>

<aside class="admin-sidebar" id="admin-sidebar">
    <a href="/admin/" class="admin-sidebar__brand">
        <span class="admin-sidebar__logo"><?= htmlspecialchars($initial) ?></span>
        <span><?= htmlspecialchars($siteName) ?></span>
    </a>

    <div class="admin-sidebar__section">General</div>

    <a class="admin-sidebar__link<?= $activeLeads ? ' admin-sidebar__link--active' : '' ?>" href="/admin/">
        <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Leads</span>
    </a>

    <a class="admin-sidebar__link<?= $is('pages') || $is('page') ? ' admin-sidebar__link--active' : '' ?>" href="/admin/?view=pages">
        <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/></svg>
        <span>Páginas</span>
    </a>

    <div class="admin-sidebar__section">Sistema</div>

    <a class="admin-sidebar__link<?= $is('settings') ? ' admin-sidebar__link--active' : '' ?>" href="/admin/?view=settings">
        <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Configuración</span>
    </a>

    <a class="admin-sidebar__link<?= $is('account') ? ' admin-sidebar__link--active' : '' ?>" href="/admin/?view=account">
        <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Mi cuenta</span>
    </a>

    <div class="admin-sidebar__footer">
        <span class="admin-sidebar__avatar"><?= htmlspecialchars($userInitial) ?></span>
        <span class="admin-sidebar__user" title="<?= htmlspecialchars($user['email']) ?>"><?= htmlspecialchars($user['email']) ?></span>
        <form method="post" style="margin:0;">
            <input type="hidden" name="action" value="logout">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <button type="submit" class="admin-sidebar__logout" title="Cerrar sesión">Salir</button>
        </form>
    </div>
</aside>

<div class="admin-mobile-bar">
    <button class="admin-mobile-toggle" type="button" id="sidebar-toggle" aria-label="Abrir menú">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        <span>Menú</span>
    </button>
    <strong style="font-size:.95rem;"><?= htmlspecialchars($siteName) ?></strong>
</div>

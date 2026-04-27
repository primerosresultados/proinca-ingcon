<?php

require __DIR__ . '/../lib/bootstrap.php';

$LEAD_STATUSES    = ['new', 'contacted', 'qualified', 'closed', 'discarded'];
$SETTING_KEYS     = [
    'site_name', 'timezone',
    'notification_email', 'notification_from',
    'autoreply_enabled', 'autoreply_subject', 'autoreply_body',
    'ga_id', 'pixel_id',
];

$action     = $_POST['action'] ?? $_GET['action'] ?? '';
$view       = $_GET['view'] ?? '';
$loginError = '';
$pwError    = '';
$pageError  = '';

// -------------------- acciones públicas (sin login) --------------------
if ($action === 'login') {
    csrfCheck();
    $result = login($_POST['email'] ?? '', $_POST['password'] ?? '');
    if ($result === 'ok') redirect('/admin/');
    $loginError = $result === 'rate_limited'
        ? 'Demasiados intentos fallidos. Esperá 15 minutos.'
        : 'Credenciales inválidas.';
}

if ($action === 'logout') {
    csrfCheck();
    logout();
    redirect('/admin/');
}

// -------------------- acciones autenticadas --------------------
$user = currentUser();

if ($user) {
    // Migraciones solo para usuarios autenticados (evita pingback anónimo).
    runMigrations();

    if ($action === 'change_password') {
        csrfCheck();
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 8)               $pwError = 'La nueva contraseña debe tener al menos 8 caracteres.';
        elseif ($new !== $confirm)          $pwError = 'Las contraseñas no coinciden.';
        elseif (!changePassword($_SESSION['user_id'], $current, $new))
                                             $pwError = 'Contraseña actual incorrecta.';
        else {
            flashSet('pw_success', 'Contraseña actualizada.');
            redirect('/admin/?view=account');
        }
        $view = 'account';
    }

    if ($action === 'update_lead_status') {
        csrfCheck();
        $id     = (int) ($_POST['id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', $LEAD_STATUSES, true) ? $_POST['status'] : 'new';
        $stmt = getDB()->prepare('UPDATE leads SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
        redirect('/admin/?id=' . $id);
    }

    if ($action === 'add_note') {
        csrfCheck();
        $id   = (int) ($_POST['id'] ?? 0);
        $body = trim($_POST['note'] ?? '');
        if ($body !== '' && $id > 0) {
            $stmt = getDB()->prepare(
                'INSERT INTO lead_notes (lead_id, user_id, body) VALUES (?, ?, ?)'
            );
            $stmt->execute([$id, $user['id'], $body]);
        }
        redirect('/admin/?id=' . $id);
    }

    if ($action === 'delete_lead') {
        csrfCheck();
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = getDB()->prepare('DELETE FROM leads WHERE id = ?');
        $stmt->execute([$id]);
        redirect('/admin/');
    }

    if ($action === 'save_settings') {
        csrfCheck();
        $submitted = $_POST['s'] ?? [];
        // Checkbox: si no viene, es 0.
        $submitted['autoreply_enabled'] = !empty($submitted['autoreply_enabled']) ? '1' : '0';
        foreach ($SETTING_KEYS as $k) {
            if (array_key_exists($k, $submitted)) {
                setSetting($k, (string) $submitted[$k]);
            }
        }
        flashSet('settings_success', 'Configuración actualizada.');
        redirect('/admin/?view=settings');
    }

    if ($action === 'export_csv') {
        $search       = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status_filter'] ?? '';
        $where  = [];
        $params = [];
        if ($search !== '') {
            $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like);
        }
        if (in_array($statusFilter, $LEAD_STATUSES, true)) {
            $where[] = 'status = ?';
            $params[] = $statusFilter;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = getDB()->prepare(
            "SELECT id, name, email, phone, message, source, status, ip_address, created_at
             FROM leads $whereSql ORDER BY created_at DESC"
        );
        $stmt->execute($params);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="leads-' . date('Ymd-His') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
        fputcsv($out, ['id', 'nombre', 'email', 'telefono', 'mensaje', 'source', 'estado', 'ip', 'fecha']);
        while ($row = $stmt->fetch()) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    if ($action === 'save_page' || $action === 'delete_page') {
        csrfCheck();
        $id = (int) ($_POST['id'] ?? 0);

        if ($action === 'delete_page' && $id > 0) {
            $stmt = getDB()->prepare('DELETE FROM pages WHERE id = ?');
            $stmt->execute([$id]);
            flashSet('page_success', 'Página eliminada.');
            redirect('/admin/?view=pages');
        }

        $slug  = slugify($_POST['slug'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $body  = $_POST['body'] ?? '';
        $meta  = trim($_POST['meta_description'] ?? '');
        $pub   = !empty($_POST['is_published']) ? 1 : 0;

        if (!$slug || !$title) {
            $pageError = 'Slug y título son requeridos.';
            $pageFormData = ['id' => $id, 'slug' => $slug, 'title' => $title, 'body' => $body,
                             'meta_description' => $meta, 'is_published' => $pub];
            $view = 'page';
        } else {
            try {
                if ($id > 0) {
                    $stmt = getDB()->prepare(
                        'UPDATE pages SET slug=?, title=?, body=?, meta_description=?, is_published=? WHERE id=?'
                    );
                    $stmt->execute([$slug, $title, $body, $meta, $pub, $id]);
                } else {
                    $stmt = getDB()->prepare(
                        'INSERT INTO pages (slug, title, body, meta_description, is_published) VALUES (?, ?, ?, ?, ?)'
                    );
                    $stmt->execute([$slug, $title, $body, $meta, $pub]);
                }
                flashSet('page_success', 'Página guardada.');
                redirect('/admin/?view=pages');
            } catch (PDOException $e) {
                $pageError = 'No se pudo guardar (¿slug duplicado?).';
                $pageFormData = ['id' => $id, 'slug' => $slug, 'title' => $title, 'body' => $body,
                                 'meta_description' => $meta, 'is_published' => $pub];
                $view = 'page';
            }
        }
    }
}

// -------------------- datos para views --------------------
$lead     = null;
$notes    = [];
$leads    = [];
$leadId   = isset($_GET['id']) && $_GET['id'] !== 'new' ? (int) $_GET['id'] : 0;
$stats    = ['total' => 0, 'today' => 0, 'this_week' => 0, 'new' => 0];
$search       = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status_filter'] ?? '';
$page         = max(1, (int) ($_GET['page'] ?? 1));
$perPage      = 25;
$totalLeads   = 0;
$totalPages   = 1;
$settings     = [];
$pages        = [];
$pageRec      = $pageFormData ?? null; // datos del registro/form (distinto de $page paginación)

if ($user) {
    $db = getDB();

    if ($view === 'settings') {
        foreach ($SETTING_KEYS as $k) {
            $settings[$k] = (string) getSetting($k, '');
        }
    } elseif ($view === 'pages') {
        $pages = $db->query('SELECT id, slug, title, is_published, updated_at FROM pages ORDER BY updated_at DESC')->fetchAll();
    } elseif ($view === 'page') {
        if ($pageRec === null) { // no vino de una re-render por error
            $pid = $_GET['id'] ?? '';
            if ($pid !== 'new' && $pid !== '') {
                $stmt = $db->prepare('SELECT * FROM pages WHERE id = ?');
                $stmt->execute([(int) $pid]);
                $pageRec = $stmt->fetch() ?: null;
            }
        }
    } elseif ($leadId > 0) {
        $stmt = $db->prepare('SELECT * FROM leads WHERE id = ?');
        $stmt->execute([$leadId]);
        $lead = $stmt->fetch() ?: null;
        if ($lead) {
            $stmt = $db->prepare(
                'SELECT n.body, n.created_at, u.email AS author_email
                 FROM lead_notes n LEFT JOIN users u ON u.id = n.user_id
                 WHERE n.lead_id = ? ORDER BY n.created_at DESC'
            );
            $stmt->execute([$leadId]);
            $notes = $stmt->fetchAll();
        }
    } elseif ($view !== 'account') {
        $stats['total']     = (int) $db->query('SELECT COUNT(*) FROM leads')->fetchColumn();
        $stats['today']     = (int) $db->query('SELECT COUNT(*) FROM leads WHERE DATE(created_at) = CURDATE()')->fetchColumn();
        $stats['this_week'] = (int) $db->query('SELECT COUNT(*) FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
        $stats['new']       = (int) $db->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn();

        $where  = [];
        $params = [];
        if ($search !== '') {
            $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like);
        }
        if (in_array($statusFilter, $LEAD_STATUSES, true)) {
            $where[] = 'status = ?';
            $params[] = $statusFilter;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $cnt = $db->prepare("SELECT COUNT(*) FROM leads $whereSql");
        $cnt->execute($params);
        $totalLeads = (int) $cnt->fetchColumn();
        $totalPages = max(1, (int) ceil($totalLeads / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        $sql = "SELECT id, name, email, source, status, created_at
                FROM leads $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $db->prepare($sql);
        $i = 1;
        foreach ($params as $p) $stmt->bindValue($i++, $p, PDO::PARAM_STR);
        $stmt->bindValue($i++, $perPage, PDO::PARAM_INT);
        $stmt->bindValue($i++, $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $leads = $stmt->fetchAll();
    }
}

$paginationUrl = function (int $p) use ($search, $statusFilter): string {
    $params = array_filter([
        'search' => $search, 'status_filter' => $statusFilter, 'page' => $p,
    ], fn($v) => $v !== '' && $v !== null);
    return '/admin/?' . http_build_query($params);
};

// -------------------- render: login --------------------
if (!$user) {
    $siteName = getSetting('site_name', 'Mi Sitio');
    $initial  = strtoupper(mb_substr($siteName, 0, 1)) ?: 'A';
    require __DIR__ . '/../components/auth/login.php';
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — <?= htmlspecialchars(getSetting('site_name', 'Admin')) ?></title>
<link rel="stylesheet" href="/assets/css/base.css">
<link rel="stylesheet" href="/assets/css/components.css">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-shell">

    <?php require __DIR__ . '/../components/admin_nav.php'; ?>

    <main class="admin-main">
        <?php
        if ($view === 'account') {
            require __DIR__ . '/../components/admin/account.php';
        } elseif ($view === 'settings') {
            require __DIR__ . '/../components/admin/settings.php';
        } elseif ($view === 'pages') {
            require __DIR__ . '/../components/admin/pages_list.php';
        } elseif ($view === 'page') {
            $page = $pageRec;
            require __DIR__ . '/../components/admin/page_edit.php';
        } elseif ($lead) {
            require __DIR__ . '/../components/admin/lead_detail.php';
        } else {
            require __DIR__ . '/../components/admin/dashboard.php';
        }
        ?>
    </main>

</div>

<script>
(function(){
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!toggle || !sidebar || !backdrop) return;
    const close = () => { sidebar.classList.remove('is-open'); backdrop.classList.remove('is-open'); };
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('is-open');
        backdrop.classList.toggle('is-open');
    });
    backdrop.addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
})();
</script>

</body>
</html>

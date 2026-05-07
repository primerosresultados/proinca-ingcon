<?php

// ----------------------------------------------------------------
// Modo preview local: sin MySQL. Activar con env DEV_PREVIEW=1.
// ----------------------------------------------------------------
if (getenv('DEV_PREVIEW') === '1') {
    require __DIR__ . '/lib/dev-stubs.php';
} else {
    require __DIR__ . '/lib/bootstrap.php';
}

/* ----------------------------------------------------------------
 * Router CMS: si el path coincide con un slug publicado, render.
 * ---------------------------------------------------------------- */
$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
if ($path !== '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $slug = slugify($path);
    if ($slug === $path) {
        try {
            $stmt = getDB()->prepare('SELECT title, body, meta_description FROM pages WHERE slug = ? AND is_published = 1');
            $stmt->execute([$slug]);
            $cms = $stmt->fetch();
        } catch (Throwable $e) {
            $cms = null;
        }
        if (!empty($cms)) {
            $siteName = getSetting('site_name', 'Mi Sitio');
            ?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($cms['title']) ?> — <?= htmlspecialchars($siteName) ?></title>
<?php if (!empty($cms['meta_description'])): ?>
<meta name="description" content="<?= htmlspecialchars($cms['meta_description']) ?>">
<?php endif; ?>
<link rel="stylesheet" href="/assets/css/base.css">
<link rel="stylesheet" href="/assets/css/layout.css">
<link rel="stylesheet" href="/assets/css/components.css">
</head>
<body>
<header class="site-header">
    <div class="container"><h1><a href="/" style="color:inherit;text-decoration:none;"><?= htmlspecialchars($siteName) ?></a></h1></div>
</header>
<main class="container">
    <article class="page">
        <h1><?= htmlspecialchars($cms['title']) ?></h1>
        <?= $cms['body'] ?>
    </article>
</main>
</body>
</html><?php
            exit;
        }
    }
}

/* ----------------------------------------------------------------
 * POST: submit_lead (CSRF + honeypot + timing + dedupe 5min)
 * ---------------------------------------------------------------- */
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_lead') {

    if (getenv('DEV_PREVIEW') === '1') {
        error_log('[preview] form submit: ' . json_encode($_POST));
        redirect('/?sent=1');
    }

    $honeypotTripped = !empty($_POST['website']);
    $tooFast         = !isset($_POST['form_started']) || (time() - (int) $_POST['form_started']) < 2;

    if ($honeypotTripped || $tooFast) {
        redirect('/?sent=1');
    }

    csrfCheck();
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $phone    = trim($_POST['phone']    ?? '');
    $servicio = trim($_POST['servicio'] ?? '');
    $ciudad   = trim($_POST['ciudad']   ?? '');
    $message  = trim($_POST['message']  ?? '');
    $source   = trim($_POST['source']   ?? 'website');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Nombre y email válido son requeridos.';
    } else {
        // Componer el mensaje con servicio + ciudad para no perder data.
        $composed = '';
        if ($servicio) $composed .= "Servicio: {$servicio}\n";
        if ($ciudad)   $composed .= "Ciudad: {$ciudad}\n";
        if ($composed && $message) $composed .= "\n";
        $composed .= $message;

        $dupe = getDB()->prepare(
            'SELECT COUNT(*) FROM leads
             WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)'
        );
        $dupe->execute([$email]);

        if ((int) $dupe->fetchColumn() > 0) {
            redirect('/?sent=1');
        }

        $db = getDB();
        $stmt = $db->prepare(
            'INSERT INTO leads (name, email, phone, message, source, status, ip_address, user_agent)
             VALUES (?, ?, ?, ?, ?, "new", ?, ?)'
        );
        $stmt->execute([
            $name, $email, $phone, $composed, $source,
            clientIp(),
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
        ]);

        $lead = [
            'id'      => (int) $db->lastInsertId(),
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'message' => $composed,
            'source'  => $source,
        ];

        try { notifyLeadCreated($lead); }   catch (Throwable $e) { error_log('notifyLead: ' . $e->getMessage()); }
        try { sendLeadAutoReply($lead); }   catch (Throwable $e) { error_log('autoReply: ' . $e->getMessage()); }

        redirect('/?sent=1');
    }
}

/* ----------------------------------------------------------------
 * Settings / variables de presentación
 * ---------------------------------------------------------------- */
$sent      = isset($_GET['sent']);
$siteName  = getSetting('site_name', 'Alexis Bello — PROINCA · INGEPUCON');
$gaId      = trim((string) getSetting('ga_id', ''));
$pixelId   = trim((string) getSetting('pixel_id', ''));
$phone     = trim((string) getSetting('contact_phone', '+56 9 1234 5678'));
$phoneRaw  = preg_replace('/[^0-9]/', '', $phone);
$email     = trim((string) getSetting('contact_email', 'contacto@alexisbello.cl'));
$waText    = rawurlencode('Hola Alexis, me interesa consultar sobre un proyecto de ingeniería/construcción en Pucón/Villarrica. ¿Podríamos conversar?');
$waUrl     = 'https://wa.me/' . $phoneRaw . '?text=' . $waText;
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Alexis Bello · Ingeniero Civil Calculista — PROINCA · INGEPUCON | Pucón · Villarrica</title>
<meta name="description" content="Ingeniería estructural, cálculo y construcción profesional en Pucón, Villarrica y La Araucanía. PROINCA (construcción) e INGEPUCON (ingeniería). Bajo norma chilena vigente.">
<meta name="author" content="Alexis Bello">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://<?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'alexisbello.cl') ?>/">

<meta property="og:type" content="website">
<meta property="og:locale" content="es_CL">
<meta property="og:title" content="Alexis Bello — Ingeniería y Construcción · Pucón · Villarrica">
<meta property="og:description" content="PROINCA · INGEPUCON. Cálculo estructural, ingeniería de proyectos y construcción profesional en La Araucanía.">

<link rel="icon" href="/assets/img/logo.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/site.css">

<?php if ($gaId): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($gaId) ?>');</script>
<?php endif; ?>
<?php if ($pixelId): ?>
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','<?= htmlspecialchars($pixelId) ?>');fbq('track','PageView');</script>
<?php endif; ?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ProfessionalService",
  "name": "Alexis Bello — PROINCA · INGEPUCON",
  "description": "Ingeniería estructural y construcción en Pucón, Villarrica y La Araucanía.",
  "areaServed": ["Pucón", "Villarrica", "La Araucanía", "Cunco", "Loncoche", "Freire"],
  "telephone": "<?= htmlspecialchars($phone) ?>",
  "email": "<?= htmlspecialchars($email) ?>",
  "address": { "@type": "PostalAddress", "addressLocality": "Pucón", "addressRegion": "La Araucanía", "addressCountry": "CL" }
}
</script>
</head>

<body>

<!-- Topbar -->
<div class="topbar">
  <div class="topbar__inner">
    <div class="topbar__left">
      <span><span class="topbar__dot"></span> Atención hoy · 09:00 – 19:00</span>
      <span><i class="fa-solid fa-location-dot"></i> Pucón · Villarrica · La Araucanía</span>
    </div>
    <div class="topbar__right">
      <a href="tel:<?= htmlspecialchars($phoneRaw) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($phone) ?></a>
      <a class="topbar__email" href="mailto:<?= htmlspecialchars($email) ?>"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($email) ?></a>
    </div>
  </div>
</div>

<!-- Nav -->
<nav class="nav" id="siteNav" aria-label="Navegación principal">
  <div class="nav__inner">
    <a href="#inicio" class="nav__brand" aria-label="Alexis Bello — Inicio">
      <img src="/assets/img/logo.png" alt="">
      <span>
        Alexis Bello
        <span class="nav__brand-sub">PROINCA · INGEPUCON</span>
      </span>
    </a>
    <ul class="nav__menu">
      <li><a href="/servicios.php">Servicios</a></li>
      <li><a href="/proyectos.php">Proyectos</a></li>
      <li><a href="/cobertura.php">Cobertura</a></li>
      <li><a href="/contacto.php">Contacto</a></li>
    </ul>
    <div class="nav__cta-wrap">
      <a href="/proinca.php" class="nav__brand-link nav__brand-link--proinca">PROINCA</a>
      <a href="/ingepucon.php"  class="nav__brand-link nav__brand-link--ingcon">INGEPUCON</a>
      <button class="nav__toggle" id="navToggle" aria-label="Abrir menú"><i class="fa-solid fa-bars"></i></button>
    </div>
  </div>
</nav>

<!-- ========================== HERO ========================== -->
<header class="hero" id="inicio">

  <div class="hero__vlabel" aria-hidden="true">
    Doc · A. Bello · La Araucanía · 2026
  </div>

  <div class="hero__plan" aria-hidden="true">
    <svg viewBox="0 0 1600 900" preserveAspectRatio="xMaxYMax slice">
      <!-- Dimension lines (top) -->
      <g>
        <path class="ln ln--dim" style="--d:.0s"  d="M180 80 L1100 80"/>
        <path class="ln ln--thin" style="--len:30; --d:.1s" d="M180 70 L180 90"/>
        <path class="ln ln--thin" style="--len:30; --d:.2s" d="M540 70 L540 90"/>
        <path class="ln ln--thin" style="--len:30; --d:.3s" d="M820 70 L820 90"/>
        <path class="ln ln--thin" style="--len:30; --d:.4s" d="M1100 70 L1100 90"/>
        <text class="lbl" x="350" y="68"  style="--d:.5s">7.20 m</text>
        <text class="lbl" x="670" y="68"  style="--d:.6s">5.60 m</text>
        <text class="lbl" x="945" y="68"  style="--d:.7s">5.60 m</text>
      </g>

      <!-- Outer walls -->
      <path class="ln" style="--len:1900; --d:.2s"  d="M180 130 L1100 130 L1100 720 L180 720 Z"/>
      <!-- Inner thin offset (double wall) -->
      <path class="ln ln--thin" style="--len:1880; --d:1.0s" d="M195 145 L1085 145 L1085 705 L195 705 Z"/>

      <!-- Internal walls dividing rooms -->
      <path class="ln" style="--len:600; --d:1.4s"  d="M540 130 L540 480"/>
      <path class="ln" style="--len:580; --d:1.6s"  d="M540 480 L1100 480"/>
      <path class="ln" style="--len:240; --d:1.7s"  d="M820 480 L820 720"/>
      <path class="ln" style="--len:360; --d:1.8s"  d="M180 360 L540 360"/>

      <!-- Door swings (arcs) -->
      <path class="ln ln--thin" style="--len:120; --d:2.0s" d="M540 200 A 60 60 0 0 1 480 260"/>
      <path class="ln ln--thin" style="--len:90;  --d:2.1s" d="M540 200 L480 200"/>
      <path class="ln ln--thin" style="--len:120; --d:2.2s" d="M820 540 A 60 60 0 0 1 760 600"/>
      <path class="ln ln--thin" style="--len:90;  --d:2.3s" d="M820 540 L760 540"/>
      <path class="ln ln--thin" style="--len:120; --d:2.4s" d="M260 360 A 60 60 0 0 0 320 420"/>

      <!-- Window openings (gaps drawn as small sills) -->
      <path class="ln ln--gold" style="--len:140; --d:1.2s" d="M260 130 L380 130"/>
      <path class="ln ln--gold" style="--len:140; --d:1.3s" d="M700 130 L820 130"/>
      <path class="ln ln--gold" style="--len:140; --d:1.4s" d="M880 720 L1000 720"/>

      <!-- Stairs -->
      <g style="--d:1.9s">
        <path class="ln ln--thin" style="--len:80;  --d:1.95s" d="M620 540 L760 540"/>
        <path class="ln ln--thin" style="--len:80;  --d:2.05s" d="M620 560 L760 560"/>
        <path class="ln ln--thin" style="--len:80;  --d:2.10s" d="M620 580 L760 580"/>
        <path class="ln ln--thin" style="--len:80;  --d:2.15s" d="M620 600 L760 600"/>
        <path class="ln ln--thin" style="--len:80;  --d:2.20s" d="M620 620 L760 620"/>
        <path class="ln ln--thin" style="--len:80;  --d:2.25s" d="M620 640 L760 640"/>
      </g>

      <!-- Room labels -->
      <text class="lbl" x="320" y="240"  style="--d:2.3s">Living</text>
      <text class="lbl" x="700" y="280"  style="--d:2.4s">Cocina</text>
      <text class="lbl" x="930" y="290"  style="--d:2.5s">Comedor</text>
      <text class="lbl" x="320" y="540"  style="--d:2.6s">Dorm. 01</text>
      <text class="lbl" x="900" y="600"  style="--d:2.7s">Dorm. 02</text>

      <!-- Side dimension (right) -->
      <g>
        <path class="ln ln--dim" style="--d:.6s" d="M1140 130 L1140 720"/>
        <path class="ln ln--thin" style="--len:30; --d:.8s" d="M1130 130 L1150 130"/>
        <path class="ln ln--thin" style="--len:30; --d:.9s" d="M1130 720 L1150 720"/>
        <text class="lbl" x="1156" y="430" transform="rotate(90 1156 430)" style="--d:1.0s">9.40 m</text>
      </g>

      <!-- North arrow -->
      <g style="--d:1.0s">
        <path class="ln ln--thin" style="--len:90; --d:1.0s" d="M1280 200 L1280 110"/>
        <path class="ln ln--thin" style="--len:30; --d:1.1s" d="M1265 130 L1280 110 L1295 130"/>
        <text class="lbl" x="1273" y="225" style="--d:1.2s">N</text>
      </g>

      <!-- Foundation grid (axis lines outside) -->
      <path class="ln ln--dim" style="--d:.1s" d="M180 50 L180 760"/>
      <path class="ln ln--dim" style="--d:.2s" d="M540 50 L540 760"/>
      <path class="ln ln--dim" style="--d:.3s" d="M820 50 L820 760"/>
      <path class="ln ln--dim" style="--d:.4s" d="M1100 50 L1100 760"/>

      <!-- Axis circles -->
      <circle class="ln ln--thin" style="--len:60; --d:.5s" cx="180" cy="40" r="9"/>
      <circle class="ln ln--thin" style="--len:60; --d:.6s" cx="540" cy="40" r="9"/>
      <circle class="ln ln--thin" style="--len:60; --d:.7s" cx="820" cy="40" r="9"/>
      <circle class="ln ln--thin" style="--len:60; --d:.8s" cx="1100" cy="40" r="9"/>
      <text class="lbl" x="178" y="42" text-anchor="middle" style="--d:.9s">A</text>
      <text class="lbl" x="538" y="42" text-anchor="middle" style="--d:1.0s">B</text>
      <text class="lbl" x="818" y="42" text-anchor="middle" style="--d:1.1s">C</text>
      <text class="lbl" x="1098" y="42" text-anchor="middle" style="--d:1.2s">D</text>
    </svg>
  </div>

  <div class="hero__inner">

    <div data-reveal>
      <div class="hero__brands">
        <span class="hero__brand-tag hero__brand-tag--proinca">PROINCA</span>
        <span>·</span>
        <span class="hero__brand-tag hero__brand-tag--ingcon">INGEPUCON</span>
        <span class="mono" style="margin-left:6px; opacity:.6;">EST. 2014</span>
      </div>

      <h1 class="hero__title">
        Ingeniería y construcción.
      </h1>

      <div class="hero__rule">
        <span><strong>Estudio Nº 02</strong> · 2014 — 2026</span>
        <span style="opacity:.6;">Pucón · La Araucanía · CL</span>
      </div>

      <p class="hero__lede">
        Cálculo estructural, ingeniería de proyectos y construcción profesional
        en Pucón, Villarrica y toda La Araucanía. Bajo norma chilena vigente
        — <span class="mono" style="font-size:13px;">NCh 433 · 430 · 427 · 2369</span>.
      </p>

      <div class="hero__signature">
        Atendido personalmente por <strong>Alexis Bello</strong>, Ingeniero Civil — Calculista.
      </div>

      <div class="hero__ctas">
        <a href="#contacto" class="btn btn--primary">
          Cotizar proyecto <i class="fa-solid fa-arrow-right arrow"></i>
        </a>
        <a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener" class="btn btn--whatsapp">
          <i class="fa-brands fa-whatsapp"></i> Escribir por WhatsApp
        </a>
      </div>

      <div class="hero__meta">
        <div class="hero__author">
          <div class="hero__author-mark">AB</div>
          <div>
            <strong>Alexis Bello</strong>
            <span>Ingeniero Civil · Calculista</span>
          </div>
        </div>
        <div class="hero__stats">
          <div class="hero__stat">
            <strong data-count="10" data-suffix="+">10+</strong>
            <span>Años</span>
          </div>
          <div class="hero__stat">
            <strong data-count="150" data-suffix="+">150+</strong>
            <span>Proyectos</span>
          </div>
          <div class="hero__stat">
            <strong data-count="100" data-suffix="%">100%</strong>
            <span>Normativa</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Form card -->
    <aside class="hero__card" data-reveal>

      <?php if ($sent): ?>
        <div class="form__success">
          <i class="fa-solid fa-circle-check"></i>
          <h3>¡Mensaje enviado!</h3>
          <p>Gracias por escribir. Te responderemos dentro de las 24 horas hábiles.</p>
          <p style="margin-top:14px;"><a href="/" style="border-bottom:1px solid;">← Volver al inicio</a></p>
        </div>
      <?php else: ?>
        <span class="eyebrow hero__card-eyebrow">Consulta directa</span>
        <h3>Cuéntanos tu proyecto.</h3>
        <p class="lead">Evaluación inicial sin costo · Respuesta en 24h hábiles.</p>

        <?php if ($error): ?>
          <p class="alert alert--error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" novalidate>
          <input type="hidden" name="action" value="submit_lead">
          <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
          <input type="hidden" name="form_started" value="<?= time() ?>">
          <input type="hidden" name="source" value="website">
          <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

          <div class="form__row">
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="hf-name">Nombre <span class="req">*</span></label>
              <input id="hf-name" name="name" class="form__input" required autocomplete="name" placeholder="Tu nombre">
            </div>
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="hf-phone">Teléfono <span class="req">*</span></label>
              <input id="hf-phone" name="phone" type="tel" class="form__input" required autocomplete="tel" placeholder="+56 9 …">
            </div>
          </div>

          <div class="form__group">
            <label class="form__label" for="hf-email">Correo electrónico <span class="req">*</span></label>
            <input id="hf-email" name="email" type="email" class="form__input" required autocomplete="email" placeholder="tu@correo.cl">
          </div>

          <div class="form__row">
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="hf-svc">Tipo de proyecto</label>
              <select id="hf-svc" name="servicio" class="form__select">
                <option value="">Selecciona…</option>
                <optgroup label="PROINCA · Construcción">
                  <option>Construcción habitacional</option>
                  <option>Construcción comercial</option>
                  <option>Supervisión técnica</option>
                  <option>Remodelación / ampliación</option>
                </optgroup>
                <optgroup label="INGEPUCON · Ingeniería">
                  <option>Cálculo estructural</option>
                  <option>Ingeniería de proyecto</option>
                  <option>Asesoría técnica</option>
                  <option>Regularización</option>
                </optgroup>
                <option>Otro / no estoy seguro</option>
              </select>
            </div>
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="hf-city">Ciudad</label>
              <input id="hf-city" name="ciudad" class="form__input" autocomplete="address-level2" placeholder="Pucón, Villarrica…">
            </div>
          </div>

          <div class="form__group">
            <label class="form__label" for="hf-msg">Mensaje</label>
            <textarea id="hf-msg" name="message" rows="3" class="form__textarea" placeholder="Cuéntanos brevemente tu proyecto…"></textarea>
          </div>

          <button type="submit" class="btn btn--gold btn--full">
            Enviar consulta <i class="fa-solid fa-arrow-right arrow"></i>
          </button>
          <p class="form__note">
            <i class="fa-solid fa-lock"></i> Tu información es privada y no será compartida.
          </p>
        </form>
      <?php endif; ?>
    </aside>

  </div>
</header>

<!-- ========================== STRIP ========================== -->
<div class="strip">
  <div class="strip__track">
    <span class="strip__item"><i class="fa-solid fa-shield-halved"></i> Norma chilena NCh 433</span>
    <span class="strip__item"><i class="fa-solid fa-compass-drafting"></i> Memoria + planos firmados</span>
    <span class="strip__item"><i class="fa-solid fa-helmet-safety"></i> Supervisión en terreno</span>
    <span class="strip__item"><i class="fa-solid fa-handshake"></i> Trato directo con el calculista</span>
  </div>
</div>

<!-- ========================== SERVICIOS ========================== -->
<section class="section" id="servicios">
  <div class="container">
    <div class="svc__head" data-reveal>
      <div>
        <span class="eyebrow">01 · Servicios</span>
        <h2 style="margin-top:14px;">Soluciones integrales <em>de ingeniería y obra.</em></h2>
      </div>
      <p>Dos líneas profesionales coordinadas. Un mismo criterio técnico desde el cálculo hasta la entrega.</p>
    </div>

    <div class="svc__grid" data-reveal>

      <article class="svc__card svc__card--xl svc__card--feat">
        <span class="svc__card-num">01</span>
        <h3>Cálculo estructural</h3>
        <p>Diseño y cálculo de estructuras de hormigón armado, acero y madera bajo normativa chilena vigente. Memoria de cálculo y planos firmados.</p>
        <span class="svc__card-tag svc__card-tag--ingcon"><span class="dot"></span> INGEPUCON</span>
      </article>

      <article class="svc__card svc__card--m">
        <span class="svc__card-num">02</span>
        <h3>Construcción</h3>
        <p>Ejecución de obras civiles completas con control de calidad, plazos y costos.</p>
        <span class="svc__card-tag svc__card-tag--proinca"><span class="dot"></span> PROINCA</span>
      </article>

      <article class="svc__card svc__card--m">
        <span class="svc__card-num">03</span>
        <h3>Ingeniería de proyectos</h3>
        <p>Coordinación multidisciplinaria desde anteproyecto hasta entrega.</p>
        <span class="svc__card-tag svc__card-tag--both"><span class="dot"></span> PROINCA · INGEPUCON</span>
      </article>

      <article class="svc__card svc__card--m">
        <span class="svc__card-num">04</span>
        <h3>Supervisión de obras</h3>
        <p>Inspección técnica en terreno: plazos, costos, calidad y normativa.</p>
        <span class="svc__card-tag svc__card-tag--proinca"><span class="dot"></span> PROINCA</span>
      </article>

      <article class="svc__card svc__card--m">
        <span class="svc__card-num">05</span>
        <h3>Regularizaciones</h3>
        <p>Gestión completa ante la DOM, subsanación de observaciones y permisos.</p>
        <span class="svc__card-tag svc__card-tag--both"><span class="dot"></span> PROINCA · INGEPUCON</span>
      </article>

      <article class="svc__card svc__card--m">
        <span class="svc__card-num">06</span>
        <h3>Asesoría técnica</h3>
        <p>Revisión de proyectos, peritajes y segundas opiniones profesionales.</p>
        <span class="svc__card-tag svc__card-tag--ingcon"><span class="dot"></span> INGEPUCON</span>
      </article>

      <article class="svc__card svc__card--m">
        <span class="svc__card-num">07</span>
        <h3>Patologías estructurales</h3>
        <p>Diagnóstico, peritaje e informe técnico sobre daños y patologías en estructuras existentes.</p>
        <span class="svc__card-tag svc__card-tag--ingcon"><span class="dot"></span> INGEPUCON</span>
      </article>

      <article class="svc__card svc__card--m">
        <span class="svc__card-num">08</span>
        <h3>Dirección de proyectos</h3>
        <p>Coordinación entre arquitectura, especialidades y mandante. Una sola interlocución técnica.</p>
        <span class="svc__card-tag svc__card-tag--proinca"><span class="dot"></span> PROINCA</span>
      </article>

    </div>

    <div style="margin-top:48px; text-align:center;" data-reveal>
      <a href="/servicios.php" class="btn btn--primary">
        Ver todos los servicios <i class="fa-solid fa-arrow-right arrow"></i>
      </a>
    </div>
  </div>
</section>

<!-- ========================== MARCAS ========================== -->
<section class="brands" id="marcas">
  <div class="brands__head" data-reveal>
    <span class="eyebrow">02 · Marcas</span>
    <h2 style="margin-top:14px;">Dos líneas. <em>Un mismo criterio profesional.</em></h2>
  </div>

  <div class="brands__split">

    <article class="brand-card brand-card--proinca" data-reveal>
      <div class="brand-card__media">
        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=1200&q=85&auto=format&fit=crop" alt="Construcción PROINCA" loading="lazy">
        <span class="brand-card__badge">Construcción</span>
        <span class="brand-card__num">01</span>
      </div>
      <div class="brand-card__body">
        <h3 class="brand-card__name">PROINCA</h3>
        <p class="brand-card__tagline">Proyectos · Ejecución · Resultados</p>
        <p class="brand-card__text">
          Línea de construcción. Ejecutamos proyectos habitacionales y comerciales
          con supervisión técnica profesional, gestión en terreno y control de calidad.
        </p>
        <ul class="brand-card__list">
          <li>Habitacional</li>
          <li>Comercial</li>
          <li>Supervisión</li>
          <li>Dirección de proyectos</li>
          <li>Remodelaciones</li>
        </ul>
        <a href="/proinca.php" class="brand-card__cta">
          Ver PROINCA <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </article>

    <article class="brand-card brand-card--ingcon" data-reveal>
      <div class="brand-card__media">
        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=85&auto=format&fit=crop" alt="Ingeniería INGEPUCON" loading="lazy">
        <span class="brand-card__badge">Ingeniería</span>
        <span class="brand-card__num">02</span>
      </div>
      <div class="brand-card__body">
        <h3 class="brand-card__name">INGEPUCON</h3>
        <p class="brand-card__tagline">Cálculo · Diseño · Certeza Técnica</p>
        <p class="brand-card__text">
          Línea de ingeniería y cálculo estructural. Proyectos técnicos, cálculos y
          asesorías especializadas para arquitectos, constructoras y propietarios.
        </p>
        <ul class="brand-card__list">
          <li>Cálculo estructural</li>
          <li>Diseño</li>
          <li>Ing. de proyectos</li>
          <li>Asesoría técnica</li>
          <li>Regularizaciones</li>
        </ul>
        <a href="/ingepucon.php" class="brand-card__cta">
          Ver INGEPUCON <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </article>

  </div>
</section>

<!-- ========================== PROCESO ========================== -->
<section class="section process" id="proceso">
  <div class="container">
    <div class="process__head" data-reveal>
      <span class="eyebrow">03 · Proceso</span>
      <h2 style="margin-top:14px; max-width:18ch; margin-inline:auto;">
        Cómo trabajamos <em>cada proyecto.</em>
      </h2>
    </div>

    <div class="process__steps" data-reveal>
      <div class="process__step">
        <div class="process__step-num">01</div>
        <h3>Consulta inicial</h3>
        <p>Conversamos sobre el proyecto y enviamos una evaluación inicial sin costo en 24h.</p>
      </div>
      <div class="process__step">
        <div class="process__step-num">02</div>
        <h3>Anteproyecto</h3>
        <p>Definimos alcance, propuesta técnica y honorarios. Trato directo con el calculista.</p>
      </div>
      <div class="process__step">
        <div class="process__step-num">03</div>
        <h3>Desarrollo</h3>
        <p>Cálculo, planos firmados y memoria. Coordinación con arquitectura y mandante.</p>
      </div>
      <div class="process__step">
        <div class="process__step-num">04</div>
        <h3>Entrega &amp; obra</h3>
        <p>Entrega documental y, si corresponde, ejecución y supervisión técnica en terreno.</p>
      </div>
    </div>
  </div>
</section>

<!-- ========================== COBERTURA ========================== -->
<section class="section coverage" id="cobertura">
  <div class="container">
    <div class="coverage__grid">
      <div data-reveal>
        <span class="eyebrow">04 · Cobertura</span>
        <h2>Presencia local. <em>Conocimiento del territorio.</em></h2>
        <p class="coverage__lede">
          Trabajamos en Pucón, Villarrica y toda la zona lacustre y precordillerana
          de La Araucanía. Conocemos las condiciones del suelo, la normativa municipal
          local y los requerimientos sísmicos específicos de la región.
        </p>
        <a href="#contacto" class="btn btn--primary" style="margin-top:28px;">
          Consultar disponibilidad <i class="fa-solid fa-arrow-right arrow"></i>
        </a>
      </div>

      <div class="coverage__zones" data-reveal>
        <div class="coverage__zone coverage__zone--primary">
          <i class="fa-solid fa-location-dot"></i>
          <div><strong>Pucón</strong><span>Cobertura principal</span></div>
        </div>
        <div class="coverage__zone coverage__zone--primary">
          <i class="fa-solid fa-location-dot"></i>
          <div><strong>Villarrica</strong><span>Cobertura principal</span></div>
        </div>
        <div class="coverage__zone">
          <i class="fa-solid fa-location-dot"></i>
          <div><strong>Cunco</strong><span>Cobertura regular</span></div>
        </div>
        <div class="coverage__zone">
          <i class="fa-solid fa-location-dot"></i>
          <div><strong>Loncoche</strong><span>Cobertura regular</span></div>
        </div>
        <div class="coverage__zone">
          <i class="fa-solid fa-location-dot"></i>
          <div><strong>Freire</strong><span>Cobertura regular</span></div>
        </div>
        <div class="coverage__zone">
          <i class="fa-solid fa-location-dot"></i>
          <div><strong>Toda La Araucanía</strong><span>Consultar disponibilidad</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ========================== FAQ ========================== -->
<section class="section faq" id="faq">
  <div class="container">
    <div class="faq__grid">
      <div data-reveal>
        <span class="eyebrow">05 · Preguntas</span>
        <h2>Lo que más <em>nos preguntan.</em></h2>
        <p style="color:var(--ink-soft); margin-top:18px;">
          ¿Tu duda no está aquí? Escríbenos y respondemos personalmente.
        </p>
      </div>
      <div class="faq__list" data-reveal>
        <details class="faq__item">
          <summary>¿La consulta inicial tiene costo?</summary>
          <div class="faq__item-body">No. La primera evaluación y propuesta de honorarios es gratuita y sin compromiso. Respondemos dentro de 24 horas hábiles.</div>
        </details>
        <details class="faq__item">
          <summary>¿Trabajan fuera de Pucón y Villarrica?</summary>
          <div class="faq__item-body">Sí. Cubrimos toda La Araucanía. Para proyectos fuera de la zona principal evaluamos disponibilidad y traslado caso a caso.</div>
        </details>
        <details class="faq__item">
          <summary>¿Entregan memoria de cálculo y planos firmados?</summary>
          <div class="faq__item-body">Sí. Todos los proyectos de INGEPUCON se entregan con memoria de cálculo conforme a NCh 433/430/427/2369 y planos firmados por el calculista.</div>
        </details>
        <details class="faq__item">
          <summary>¿Coordinan con mi arquitecto/a?</summary>
          <div class="faq__item-body">Por supuesto. Habitualmente trabajamos coordinados con oficinas de arquitectura, constructoras y mandantes.</div>
        </details>
        <details class="faq__item">
          <summary>¿Pueden regularizar una construcción existente?</summary>
          <div class="faq__item-body">Sí. Hacemos regularizaciones ante la DOM, levantamiento, cálculo retroactivo y subsanación de observaciones.</div>
        </details>
      </div>
    </div>
  </div>
</section>

<!-- ========================== CONTACTO ========================== -->
<section class="section contact" id="contacto">
  <div class="container">
    <div class="contact__grid">

      <div data-reveal>
        <span class="eyebrow">06 · Contacto</span>
        <h2>¿Tienes un <em>proyecto en mente?</em></h2>
        <p class="contact__lede">
          Cuéntanos en qué consiste tu proyecto. Respondemos dentro de las 24 horas hábiles
          con una evaluación inicial sin costo.
        </p>

        <div class="contact__details">
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-brands fa-whatsapp"></i></div>
            <div>
              <span class="contact__row-label">WhatsApp directo</span>
              <span class="contact__row-val"><a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($phone) ?></a></span>
            </div>
          </div>
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-solid fa-envelope"></i></div>
            <div>
              <span class="contact__row-label">Correo electrónico</span>
              <span class="contact__row-val"><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></span>
            </div>
          </div>
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div>
              <span class="contact__row-label">Ubicación</span>
              <span class="contact__row-val">Pucón · La Araucanía · Chile</span>
            </div>
          </div>
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-solid fa-clock"></i></div>
            <div>
              <span class="contact__row-label">Horario</span>
              <span class="contact__row-val">Lunes a viernes · 09:00 – 19:00</span>
            </div>
          </div>
        </div>
      </div>

      <div data-reveal>
        <?php if ($sent): ?>
          <div class="form__success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Mensaje enviado!</h3>
            <p>Gracias por escribir. Te responderemos dentro de las próximas 24 horas hábiles.</p>
          </div>
        <?php else: ?>
          <form method="post" novalidate style="background:var(--paper); border:1px solid var(--line); border-radius:var(--r-lg); padding:32px;">
            <input type="hidden" name="action" value="submit_lead">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="form_started" value="<?= time() ?>">
            <input type="hidden" name="source" value="website-contact">
            <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

            <?php if ($error): ?>
              <p class="alert alert--error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cf-name">Nombre <span class="req">*</span></label>
                <input id="cf-name" name="name" class="form__input" required autocomplete="name">
              </div>
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cf-phone">Teléfono <span class="req">*</span></label>
                <input id="cf-phone" name="phone" type="tel" class="form__input" required autocomplete="tel">
              </div>
            </div>
            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cf-email">Correo <span class="req">*</span></label>
                <input id="cf-email" name="email" type="email" class="form__input" required autocomplete="email">
              </div>
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cf-city">Ciudad</label>
                <input id="cf-city" name="ciudad" class="form__input">
              </div>
            </div>
            <div class="form__group">
              <label class="form__label" for="cf-svc">Tipo de proyecto</label>
              <select id="cf-svc" name="servicio" class="form__select">
                <option value="">Selecciona…</option>
                <optgroup label="PROINCA · Construcción">
                  <option>Construcción habitacional</option>
                  <option>Construcción comercial</option>
                  <option>Supervisión técnica</option>
                  <option>Remodelación / ampliación</option>
                </optgroup>
                <optgroup label="INGEPUCON · Ingeniería">
                  <option>Cálculo estructural</option>
                  <option>Ingeniería de proyecto</option>
                  <option>Asesoría técnica</option>
                  <option>Regularización</option>
                </optgroup>
                <option>Otro / no estoy seguro</option>
              </select>
            </div>
            <div class="form__group">
              <label class="form__label" for="cf-msg">Mensaje</label>
              <textarea id="cf-msg" name="message" rows="4" class="form__textarea" placeholder="Cuéntanos brevemente tu proyecto…"></textarea>
            </div>
            <button type="submit" class="btn btn--primary btn--full">
              Enviar consulta <i class="fa-solid fa-paper-plane"></i>
            </button>
            <p class="form__note">
              <i class="fa-solid fa-lock"></i> Tu información es privada y no será compartida con terceros.
            </p>
          </form>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<!-- ========================== FOOTER ========================== -->
<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div>
        <div class="footer__brand-name">Alexis Bello</div>
        <div class="footer__brand-role">Ingeniero Civil · Calculista</div>
        <p class="footer__brand-desc">
          Ingeniería estructural y construcción profesional en Pucón, Villarrica y
          La Araucanía. Dos líneas especializadas, un mismo compromiso técnico.
        </p>
        <div class="footer__brand-tags">
          <span class="footer__brand-tag footer__brand-tag--proinca">PROINCA</span>
          <span class="footer__brand-tag footer__brand-tag--ingcon">INGEPUCON</span>
        </div>
      </div>
      <div>
        <h4 class="footer__col-title">Navegación</h4>
        <ul class="footer__list">
          <li><a href="#servicios">Servicios</a></li>
          <li><a href="#marcas">Marcas</a></li>
          <li><a href="#proceso">Proceso</a></li>
          <li><a href="#cobertura">Cobertura</a></li>
          <li><a href="#faq">Preguntas</a></li>
        </ul>
      </div>
      <div>
        <h4 class="footer__col-title">Servicios</h4>
        <ul class="footer__list">
          <li><a href="#servicios">Cálculo estructural</a></li>
          <li><a href="#servicios">Ingeniería de proyectos</a></li>
          <li><a href="#servicios">Construcción</a></li>
          <li><a href="#servicios">Asesoría técnica</a></li>
          <li><a href="#servicios">Regularizaciones</a></li>
        </ul>
      </div>
      <div>
        <h4 class="footer__col-title">Contacto</h4>
        <ul class="footer__list">
          <li><a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> <?= htmlspecialchars($phone) ?></a></li>
          <li><a href="mailto:<?= htmlspecialchars($email) ?>"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($email) ?></a></li>
          <li><i class="fa-solid fa-location-dot"></i> Pucón, La Araucanía</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="footer__bottom">
    <div class="container footer__bottom-inner">
      <span>© <span id="year">2026</span> Alexis Bello — PROINCA · INGEPUCON. Todos los derechos reservados.</span>
      <span>Pucón · Villarrica · Región de La Araucanía · Chile</span>
    </div>
  </div>
</footer>

<!-- WhatsApp float -->
<a class="wa-float" href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener" aria-label="Contactar por WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobileMenu" aria-hidden="true">
  <div class="mobile-menu__top">
    <span style="font-size:22px;">Alexis Bello</span>
    <button class="mobile-menu__close" id="mobileMenuClose" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <nav class="mobile-menu__list">
    <a href="#servicios">Servicios</a>
    <a href="#marcas">Marcas</a>
    <a href="#proceso">Proceso</a>
    <a href="#cobertura">Cobertura</a>
    <a href="#faq">Preguntas</a>
    <a href="#contacto">Contacto</a>
  </nav>
</div>

<script src="/assets/js/site.js"></script>
</body>
</html>

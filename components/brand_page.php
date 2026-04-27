<?php
/**
 * Template de página de marca (PROINCA / INGCON).
 * Espera $brand con: key, name, kind, tagline, accent, eyebrow, hero_bg,
 *   chips[], description, services[{title, text}], projects[{title, type, img}].
 *
 * Reutiliza el handler POST de index.php (action=submit_lead).
 * Se incluye desde proinca.php / ingcon.php que también delegan al handler.
 */

$siteName  = getSetting('site_name', 'Alexis Bello — PROINCA · INGCON');
$gaId      = trim((string) getSetting('ga_id', ''));
$pixelId   = trim((string) getSetting('pixel_id', ''));
$phone     = trim((string) getSetting('contact_phone', '+56 9 1234 5678'));
$phoneRaw  = preg_replace('/[^0-9]/', '', $phone);
$email     = trim((string) getSetting('contact_email', 'contacto@alexisbello.cl'));
$waText    = rawurlencode("Hola Alexis, me interesa consultar a {$brand['name']} sobre un proyecto. ¿Podríamos conversar?");
$waUrl     = 'https://wa.me/' . $phoneRaw . '?text=' . $waText;
$sent      = isset($_GET['sent']);
$error     = $error ?? '';
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= htmlspecialchars($brand['name']) ?> · <?= htmlspecialchars($brand['kind']) ?> — Alexis Bello | Pucón · Villarrica</title>
<meta name="description" content="<?= htmlspecialchars($brand['description']) ?>">
<meta name="robots" content="index, follow">

<meta property="og:type" content="website">
<meta property="og:locale" content="es_CL">
<meta property="og:title" content="<?= htmlspecialchars($brand['name']) ?> — <?= htmlspecialchars($brand['kind']) ?>">
<meta property="og:description" content="<?= htmlspecialchars($brand['description']) ?>">

<link rel="icon" href="/assets/img/logo.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/site.css">

<style>
  :root { --brand: <?= $brand['accent'] ?>; --brand-dk: <?= $brand['accent_dk'] ?>; }
</style>

<?php if ($gaId): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($gaId) ?>');</script>
<?php endif; ?>
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
<nav class="nav is-scrolled" id="siteNav">
  <div class="nav__inner">
    <a href="/" class="nav__brand">
      <img src="/assets/img/logo.png" alt="">
      <span>
        Alexis Bello
        <span class="nav__brand-sub">PROINCA · INGCON</span>
      </span>
    </a>
    <ul class="nav__menu">
      <li><a href="/#servicios">Servicios</a></li>
      <li><a href="/proinca.php" <?= $brand['key'] === 'proinca' ? 'style="color:var(--ink)"' : '' ?>>PROINCA</a></li>
      <li><a href="/ingcon.php" <?= $brand['key'] === 'ingcon' ? 'style="color:var(--ink)"' : '' ?>>INGCON</a></li>
      <li><a href="/#proceso">Proceso</a></li>
      <li><a href="/#cobertura">Cobertura</a></li>
    </ul>
    <div class="nav__cta-wrap">
      <a href="#contacto" class="btn btn--ghost">Cotizar</a>
      <button class="nav__toggle" id="navToggle" aria-label="Abrir menú"><i class="fa-solid fa-bars"></i></button>
    </div>
  </div>
</nav>

<!-- ========================== BRAND HERO ========================== -->
<header class="bhero bhero--<?= htmlspecialchars($brand['key']) ?>">
  <div class="bhero__bg" aria-hidden="true">
    <img src="<?= htmlspecialchars($brand['hero_bg']) ?>" alt="" loading="eager">
    <div class="bhero__bg-overlay"></div>
  </div>

  <div class="bhero__inner">
    <div class="bhero__info" data-reveal>
      <nav class="bhero__crumbs" aria-label="Migas">
        <a href="/">Inicio</a>
        <span>/</span>
        <span>Marcas</span>
        <span>/</span>
        <strong><?= htmlspecialchars($brand['name']) ?></strong>
      </nav>
      <span class="bhero__kind"><?= htmlspecialchars($brand['eyebrow']) ?></span>
      <h1 class="bhero__name"><?= htmlspecialchars($brand['name']) ?></h1>
      <p class="bhero__tagline"><?= htmlspecialchars($brand['tagline']) ?></p>
      <ul class="bhero__chips">
        <?php foreach ($brand['chips'] as $chip): ?>
          <li><?= htmlspecialchars($chip) ?></li>
        <?php endforeach; ?>
      </ul>
      <div style="margin-top:32px; display:flex; gap:12px; flex-wrap:wrap;">
        <a href="#contacto" class="btn btn--gold">
          Cotizar con <?= htmlspecialchars($brand['name']) ?> <i class="fa-solid fa-arrow-right arrow"></i>
        </a>
        <a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener" class="btn btn--whatsapp">
          <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
      </div>
    </div>

    <aside class="bhero__card" data-reveal>
      <?php if ($sent): ?>
        <div class="form__success">
          <i class="fa-solid fa-circle-check"></i>
          <h3>¡Mensaje enviado!</h3>
          <p>Gracias por escribir. Te responderemos en menos de 24 horas hábiles.</p>
        </div>
      <?php else: ?>
        <span class="eyebrow" style="color:var(--ink);">Contacto directo</span>
        <h3 style="font-size:24px; margin:6px 0 4px;">Cotizar con <?= htmlspecialchars($brand['name']) ?>.</h3>
        <p style="color:var(--muted); font-size:14px; margin-bottom:20px;">
          Respuesta en menos de 24 h hábiles · Evaluación inicial sin costo.
        </p>

        <?php if ($error): ?><p class="alert alert--error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <form method="post" novalidate>
          <input type="hidden" name="action" value="submit_lead">
          <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
          <input type="hidden" name="form_started" value="<?= time() ?>">
          <input type="hidden" name="source" value="<?= htmlspecialchars($brand['key']) ?>">
          <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

          <div class="form__row">
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="bf-name">Nombre <span class="req">*</span></label>
              <input id="bf-name" name="name" class="form__input" required autocomplete="name">
            </div>
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="bf-phone">Teléfono <span class="req">*</span></label>
              <input id="bf-phone" name="phone" type="tel" class="form__input" required autocomplete="tel">
            </div>
          </div>
          <div class="form__group">
            <label class="form__label" for="bf-email">Correo <span class="req">*</span></label>
            <input id="bf-email" name="email" type="email" class="form__input" required autocomplete="email">
          </div>
          <div class="form__row">
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="bf-svc">Tipo de proyecto</label>
              <select id="bf-svc" name="servicio" class="form__select">
                <option value="">Selecciona…</option>
                <?php foreach ($brand['form_options'] as $opt): ?>
                  <option><?= htmlspecialchars($opt) ?></option>
                <?php endforeach; ?>
                <option>Otro / no estoy seguro</option>
              </select>
            </div>
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="bf-city">Ciudad</label>
              <input id="bf-city" name="ciudad" class="form__input">
            </div>
          </div>
          <div class="form__group">
            <label class="form__label" for="bf-msg">Mensaje</label>
            <textarea id="bf-msg" name="message" rows="3" class="form__textarea" placeholder="Cuéntanos brevemente…"></textarea>
          </div>
          <button type="submit" class="btn btn--gold btn--full">
            Enviar consulta <i class="fa-solid fa-arrow-right arrow"></i>
          </button>
          <p class="form__note"><i class="fa-solid fa-lock"></i> Información privada · no compartimos datos.</p>
        </form>
      <?php endif; ?>
    </aside>
  </div>
</header>

<!-- ========================== SERVICIOS DE LA MARCA ========================== -->
<section class="section" id="servicios-brand">
  <div class="container">
    <div class="svc__head" data-reveal>
      <div>
        <span class="eyebrow">01 · Servicios</span>
        <h2 style="margin-top:14px;"><?= htmlspecialchars($brand['services_title']) ?></h2>
      </div>
      <p><?= htmlspecialchars($brand['services_lede']) ?></p>
    </div>

    <div class="bsvc__grid" data-reveal>
      <?php foreach ($brand['services'] as $i => $svc): ?>
        <article class="bsvc__card">
          <span class="bsvc__num"><?= sprintf('%02d', $i + 1) ?></span>
          <h3><?= htmlspecialchars($svc['title']) ?></h3>
          <p><?= htmlspecialchars($svc['text']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================== PROYECTOS / GALERIA ========================== -->
<section class="section bproj" style="background:var(--paper-2);">
  <div class="container">
    <div class="svc__head" data-reveal>
      <div>
        <span class="eyebrow">02 · Trabajos</span>
        <h2 style="margin-top:14px;">Algunos proyectos.</h2>
      </div>
      <p>Una selección representativa del trabajo de <?= htmlspecialchars($brand['name']) ?>.</p>
    </div>

    <div class="bproj__grid" data-reveal>
      <?php foreach ($brand['projects'] as $p): ?>
        <article class="bproj__card">
          <div class="bproj__media"><img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy"></div>
          <div class="bproj__body">
            <span class="bproj__type"><?= htmlspecialchars($p['type']) ?></span>
            <h3><?= htmlspecialchars($p['title']) ?></h3>
            <p><?= htmlspecialchars($p['text']) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================== POR QUÉ ========================== -->
<section class="section section--dark process">
  <div class="container">
    <div class="process__head" data-reveal>
      <span class="eyebrow">03 · Por qué <?= htmlspecialchars($brand['name']) ?></span>
      <h2 style="margin-top:14px; max-width:18ch; margin-inline:auto;">
        <?= $brand['why_title'] ?>
      </h2>
    </div>
    <div class="process__steps" data-reveal>
      <?php foreach ($brand['why'] as $i => $w): ?>
        <div class="process__step">
          <div class="process__step-num"><?= sprintf('%02d', $i + 1) ?></div>
          <h3><?= htmlspecialchars($w['title']) ?></h3>
          <p><?= htmlspecialchars($w['text']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ========================== CONTACTO ========================== -->
<section class="section contact" id="contacto">
  <div class="container">
    <div class="contact__grid">
      <div data-reveal>
        <span class="eyebrow">04 · Contacto</span>
        <h2>Conversemos sobre <em>tu proyecto.</em></h2>
        <p class="contact__lede">
          Cuéntanos en qué consiste. Respondemos personalmente con una evaluación inicial sin costo,
          dentro de las 24 horas hábiles.
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
        </div>
      </div>

      <div data-reveal>
        <?php if ($sent): ?>
          <div class="form__success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Mensaje enviado!</h3>
            <p>Te responderemos pronto.</p>
          </div>
        <?php else: ?>
          <form method="post" novalidate style="background:var(--paper); border:1px solid var(--line); border-radius:var(--r-lg); padding:32px;">
            <input type="hidden" name="action" value="submit_lead">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="form_started" value="<?= time() ?>">
            <input type="hidden" name="source" value="<?= htmlspecialchars($brand['key']) ?>-contact">
            <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cb-name">Nombre <span class="req">*</span></label>
                <input id="cb-name" name="name" class="form__input" required autocomplete="name">
              </div>
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cb-phone">Teléfono <span class="req">*</span></label>
                <input id="cb-phone" name="phone" type="tel" class="form__input" required autocomplete="tel">
              </div>
            </div>
            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cb-email">Correo <span class="req">*</span></label>
                <input id="cb-email" name="email" type="email" class="form__input" required autocomplete="email">
              </div>
              <div class="form__group" style="margin-bottom:0;">
                <label class="form__label" for="cb-city">Ciudad</label>
                <input id="cb-city" name="ciudad" class="form__input">
              </div>
            </div>
            <div class="form__group">
              <label class="form__label" for="cb-svc">Tipo de proyecto</label>
              <select id="cb-svc" name="servicio" class="form__select">
                <option value="">Selecciona…</option>
                <?php foreach ($brand['form_options'] as $opt): ?>
                  <option><?= htmlspecialchars($opt) ?></option>
                <?php endforeach; ?>
                <option>Otro / no estoy seguro</option>
              </select>
            </div>
            <div class="form__group">
              <label class="form__label" for="cb-msg">Mensaje</label>
              <textarea id="cb-msg" name="message" rows="4" class="form__textarea"></textarea>
            </div>
            <button type="submit" class="btn btn--primary btn--full">
              Enviar consulta <i class="fa-solid fa-paper-plane"></i>
            </button>
            <p class="form__note"><i class="fa-solid fa-lock"></i> Información privada · no compartimos datos.</p>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div>
        <div class="footer__brand-name">Alexis Bello</div>
        <div class="footer__brand-role">Ingeniero Civil · Calculista</div>
        <p class="footer__brand-desc">Ingeniería estructural y construcción profesional en Pucón, Villarrica y La Araucanía.</p>
        <div class="footer__brand-tags">
          <span class="footer__brand-tag footer__brand-tag--proinca">PROINCA</span>
          <span class="footer__brand-tag footer__brand-tag--ingcon">INGCON</span>
        </div>
      </div>
      <div>
        <h4 class="footer__col-title">Marcas</h4>
        <ul class="footer__list">
          <li><a href="/proinca.php">PROINCA · Construcción</a></li>
          <li><a href="/ingcon.php">INGCON · Ingeniería</a></li>
        </ul>
      </div>
      <div>
        <h4 class="footer__col-title">Sitio</h4>
        <ul class="footer__list">
          <li><a href="/#servicios">Servicios</a></li>
          <li><a href="/#proceso">Proceso</a></li>
          <li><a href="/#cobertura">Cobertura</a></li>
          <li><a href="/#faq">Preguntas</a></li>
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
      <span>© <span id="year">2026</span> Alexis Bello — PROINCA · INGCON.</span>
      <span>Pucón · Villarrica · Región de La Araucanía · Chile</span>
    </div>
  </div>
</footer>

<a class="wa-float" href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
  <i class="fa-brands fa-whatsapp"></i>
</a>

<script src="/assets/js/site.js"></script>
</body>
</html>

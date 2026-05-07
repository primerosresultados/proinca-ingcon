<?php
/**
 * PROINCA — template de página de marca.
 * Estilo: journal de obra — fotográfico, hands-on, terracota/dorado.
 */

$siteName  = getSetting('site_name', 'Alexis Bello — PROINCA · INGEPUCON');
$gaId      = trim((string) getSetting('ga_id', ''));
$phone     = trim((string) getSetting('contact_phone', '+56 9 1234 5678'));
$phoneRaw  = preg_replace('/[^0-9]/', '', $phone);
$email     = trim((string) getSetting('contact_email', 'contacto@alexisbello.cl'));
$waText    = rawurlencode("Hola, me interesa cotizar una obra con PROINCA. ¿Podríamos conversar?");
$waUrl     = 'https://wa.me/' . $phoneRaw . '?text=' . $waText;
$sent      = isset($_GET['sent']);
$error     = $error ?? '';
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>PROINCA · Construcción — Alexis Bello | Pucón · Villarrica</title>
<meta name="description" content="<?= htmlspecialchars($brand['description']) ?>">
<meta name="robots" content="index, follow">
<meta property="og:type" content="website">
<meta property="og:locale" content="es_CL">
<meta property="og:title" content="PROINCA — Construcción · Pucón · Villarrica">
<meta property="og:description" content="<?= htmlspecialchars($brand['description']) ?>">

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
</head>

<body class="bp-proinca">

<!-- Topbar -->
<div class="topbar">
  <div class="topbar__inner">
    <div class="topbar__left">
      <span><span class="topbar__dot"></span> Atención · 09:00 – 19:00</span>
      <span><i class="fa-solid fa-helmet-safety"></i> En obra · Pucón · Villarrica</span>
    </div>
    <div class="topbar__right">
      <a href="tel:<?= htmlspecialchars($phoneRaw) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($phone) ?></a>
      <a class="topbar__email" href="mailto:<?= htmlspecialchars($email) ?>"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($email) ?></a>
    </div>
  </div>
</div>

<!-- Nav -->
<nav class="nav is-scrolled" id="siteNav" style="background:rgba(255,255,255,.92);">
  <div class="nav__inner">
    <a href="/" class="nav__brand">
      <img src="/assets/img/logo.png" alt="">
      <span>Alexis Bello<span class="nav__brand-sub">PROINCA · INGEPUCON</span></span>
    </a>
    <ul class="nav__menu">
      <li><a href="/servicios.php">Servicios</a></li>
      <li><a href="/proyectos.php">Proyectos</a></li>
      <li><a href="/cobertura.php">Cobertura</a></li>
      <li><a href="/contacto.php">Contacto</a></li>
    </ul>
    <div class="nav__cta-wrap">
      <a href="/proinca.php" class="nav__brand-link nav__brand-link--proinca is-active">PROINCA</a>
      <a href="/ingepucon.php"  class="nav__brand-link nav__brand-link--ingcon">INGEPUCON</a>
      <button class="nav__toggle" id="navToggle"><i class="fa-solid fa-bars"></i></button>
    </div>
  </div>
</nav>

<!-- ============== HERO ============== -->
<header class="pcover">
  <div class="pcover__bg" aria-hidden="true">
    <img src="<?= htmlspecialchars($brand['hero_bg']) ?>" alt="" loading="eager">
  </div>
  <div class="pcover__inner">
    <div data-reveal>
      <nav class="pcover__crumbs">
        <a href="/">Inicio</a><span>/</span><span>Marcas</span><span>/</span><strong>PROINCA</strong>
      </nav>
      <span class="pcover__kind">01 · Construcción</span>
      <h1 class="pcover__name">PROINCA</h1>
      <p class="pcover__tagline"><?= htmlspecialchars($brand['tagline']) ?></p>
      <ul class="pcover__chips">
        <?php foreach ($brand['chips'] as $chip): ?>
          <li><?= htmlspecialchars($chip) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <aside class="pcover__card" data-reveal>
      <?php if ($sent): ?>
        <div class="form__success">
          <i class="fa-solid fa-circle-check"></i>
          <h3>¡Cotización en camino!</h3>
          <p>Gracias. Te respondemos dentro de 24h hábiles.</p>
        </div>
      <?php else: ?>
        <span class="eyebrow" style="color:var(--ink);">Cotización directa</span>
        <h3 style="margin:6px 0 18px; font-size:24px;">Cotizar tu obra.</h3>

        <?php if ($error): ?><p class="alert alert--error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <form method="post" novalidate>
          <input type="hidden" name="action" value="submit_lead">
          <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
          <input type="hidden" name="form_started" value="<?= time() ?>">
          <input type="hidden" name="source" value="proinca">
          <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

          <div class="form__group">
            <label class="form__label" for="pf-name">Nombre <span class="req">*</span></label>
            <input id="pf-name" name="name" class="form__input" required autocomplete="name">
          </div>
          <div class="form__row">
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="pf-phone">Teléfono <span class="req">*</span></label>
              <input id="pf-phone" name="phone" type="tel" class="form__input" required autocomplete="tel">
            </div>
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label" for="pf-email">Correo <span class="req">*</span></label>
              <input id="pf-email" name="email" type="email" class="form__input" required autocomplete="email">
            </div>
          </div>
          <div class="form__group">
            <label class="form__label" for="pf-svc">Tipo de obra</label>
            <select id="pf-svc" name="servicio" class="form__select">
              <option value="">Selecciona…</option>
              <?php foreach ($brand['form_options'] as $opt): ?>
                <option><?= htmlspecialchars($opt) ?></option>
              <?php endforeach; ?>
              <option>Otro</option>
            </select>
          </div>
          <div class="form__group">
            <label class="form__label" for="pf-msg">Detalle</label>
            <textarea id="pf-msg" name="message" rows="3" class="form__textarea" placeholder="m², ubicación, fecha estimada…"></textarea>
          </div>
          <button type="submit" class="btn btn--gold btn--full">
            Cotizar obra <i class="fa-solid fa-arrow-right arrow"></i>
          </button>
        </form>
      <?php endif; ?>
    </aside>
  </div>
</header>

<!-- ============== STATS ============== -->
<section class="pstats">
  <div class="container">
    <div class="pstats__grid" data-reveal>
      <div class="pstats__item">
        <div class="pstats__num">10+</div>
        <span class="pstats__label">Años en obra</span>
      </div>
      <div class="pstats__item">
        <div class="pstats__num">150+</div>
        <span class="pstats__label">Proyectos entregados</span>
      </div>
      <div class="pstats__item">
        <div class="pstats__num">100%</div>
        <span class="pstats__label">Bajo norma</span>
      </div>
      <div class="pstats__item">
        <div class="pstats__num">24h</div>
        <span class="pstats__label">Tiempo de respuesta</span>
      </div>
    </div>
  </div>
</section>

<!-- ============== SERVICIOS — staggered list ============== -->
<section class="pservices">
  <div class="container">
    <div class="pservices__head" data-reveal>
      <h2><?= $brand['services_title'] ?></h2>
      <p><?= htmlspecialchars($brand['services_lede']) ?></p>
    </div>
    <div class="pservices__list" data-reveal>
      <?php foreach ($brand['services'] as $i => $svc): ?>
        <div class="pservices__row">
          <div class="pservices__row-num"><?= sprintf('%02d', $i + 1) ?> /</div>
          <h3><?= htmlspecialchars($svc['title']) ?></h3>
          <p><?= htmlspecialchars($svc['text']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============== PROYECTOS ============== -->
<section class="pprojects">
  <div class="container">
    <div class="pprojects__head" data-reveal>
      <div>
        <span class="eyebrow">02 · Trabajos</span>
        <h2 style="margin-top:14px;">Algunas obras.</h2>
      </div>
      <p style="color:var(--ink-soft); max-width:38ch;">Una selección representativa del trabajo de PROINCA en la zona.</p>
    </div>
    <div class="pprojects__grid" data-reveal>
      <?php foreach ($brand['projects'] as $p): ?>
        <article class="pproj">
          <div class="pproj__media"><img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy"></div>
          <div class="pproj__body">
            <span class="pproj__type"><?= htmlspecialchars($p['type']) ?></span>
            <h3><?= htmlspecialchars($p['title']) ?></h3>
            <p><?= htmlspecialchars($p['text']) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============== POR QUÉ ============== -->
<section class="pwhy">
  <div class="container">
    <div class="pwhy__head" data-reveal>
      <span class="eyebrow">03 · Por qué PROINCA</span>
      <h2 style="margin-top:14px;"><?= $brand['why_title'] ?></h2>
    </div>
    <div class="pwhy__grid" data-reveal>
      <?php foreach ($brand['why'] as $i => $w): ?>
        <div class="pwhy__item">
          <h3 data-num="0<?= $i + 1 ?> /"><?= htmlspecialchars($w['title']) ?></h3>
          <p><?= htmlspecialchars($w['text']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============== CONTACTO ============== -->
<section class="section contact" id="contacto" style="background:var(--paper);">
  <div class="container">
    <div class="contact__grid">
      <div data-reveal>
        <span class="eyebrow">04 · Contacto</span>
        <h2 style="margin-top:18px;">Hablemos de tu obra.</h2>
        <p class="contact__lede">Cuéntanos en qué consiste el proyecto. Visita en terreno y cotización detallada en menos de 24h hábiles.</p>
        <div class="contact__details">
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-brands fa-whatsapp"></i></div>
            <div><span class="contact__row-label">WhatsApp</span>
              <span class="contact__row-val"><a href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($phone) ?></a></span></div>
          </div>
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-solid fa-envelope"></i></div>
            <div><span class="contact__row-label">Correo</span>
              <span class="contact__row-val"><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></span></div>
          </div>
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div><span class="contact__row-label">Visita en terreno</span>
              <span class="contact__row-val">Pucón, Villarrica y zona</span></div>
          </div>
        </div>
      </div>
      <div data-reveal>
        <?php if ($sent): ?>
          <div class="form__success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Mensaje recibido!</h3>
            <p>Te respondemos pronto.</p>
          </div>
        <?php else: ?>
          <form method="post" novalidate style="background:var(--paper); border:1px solid var(--line); border-radius:4px; padding:32px; border-top:4px solid var(--proinca);">
            <input type="hidden" name="action" value="submit_lead">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="form_started" value="<?= time() ?>">
            <input type="hidden" name="source" value="proinca-contact">
            <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Nombre <span class="req">*</span></label><input name="name" class="form__input" required></div>
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Teléfono <span class="req">*</span></label><input name="phone" type="tel" class="form__input" required></div>
            </div>
            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Correo <span class="req">*</span></label><input name="email" type="email" class="form__input" required></div>
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Ciudad</label><input name="ciudad" class="form__input"></div>
            </div>
            <div class="form__group">
              <label class="form__label">Tipo de obra</label>
              <select name="servicio" class="form__select">
                <option value="">Selecciona…</option>
                <?php foreach ($brand['form_options'] as $opt): ?><option><?= htmlspecialchars($opt) ?></option><?php endforeach; ?>
                <option>Otro</option>
              </select>
            </div>
            <div class="form__group">
              <label class="form__label">Mensaje</label>
              <textarea name="message" rows="3" class="form__textarea"></textarea>
            </div>
            <button type="submit" class="btn btn--gold btn--full">Cotizar obra <i class="fa-solid fa-arrow-right arrow"></i></button>
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
        <p class="footer__brand-desc">Construcción y obra civil profesional en Pucón, Villarrica y La Araucanía.</p>
        <div class="footer__brand-tags">
          <span class="footer__brand-tag footer__brand-tag--proinca">PROINCA</span>
          <span class="footer__brand-tag footer__brand-tag--ingcon">INGEPUCON</span>
        </div>
      </div>
      <div>
        <h4 class="footer__col-title">Marcas</h4>
        <ul class="footer__list">
          <li><a href="/proinca.php">PROINCA · Construcción</a></li>
          <li><a href="/ingepucon.php">INGEPUCON · Ingeniería</a></li>
        </ul>
      </div>
      <div>
        <h4 class="footer__col-title">Sitio</h4>
        <ul class="footer__list">
          <li><a href="/#servicios">Servicios</a></li>
          <li><a href="/#proceso">Proceso</a></li>
          <li><a href="/#cobertura">Cobertura</a></li>
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
      <span>© <span id="year">2026</span> Alexis Bello — PROINCA · INGEPUCON.</span>
      <span>Pucón · Villarrica · Región de La Araucanía · Chile</span>
    </div>
  </div>
</footer>

<a class="wa-float" href="<?= htmlspecialchars($waUrl) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i></a>

<script src="/assets/js/site.js"></script>
</body>
</html>

<?php
/**
 * INGEPUCON — template de página de marca.
 * Estilo: spec sheet técnico — blueprint, datos, secciones numeradas ISO.
 */

$siteName  = getSetting('site_name', 'Alexis Bello — PROINCA · INGEPUCON');
$gaId      = trim((string) getSetting('ga_id', ''));
$phone     = trim((string) getSetting('contact_phone', '+56 9 1234 5678'));
$phoneRaw  = preg_replace('/[^0-9]/', '', $phone);
$email     = trim((string) getSetting('contact_email', 'contacto@alexisbello.cl'));
$waText    = rawurlencode("Hola, me interesa consultar a INGEPUCON sobre cálculo / ingeniería estructural. ¿Podríamos conversar?");
$waUrl     = 'https://wa.me/' . $phoneRaw . '?text=' . $waText;
$sent      = isset($_GET['sent']);
$error     = $error ?? '';
?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>INGEPUCON · Ingeniería Estructural — Alexis Bello | Pucón · Villarrica</title>
<meta name="description" content="<?= htmlspecialchars($brand['description']) ?>">
<meta name="robots" content="index, follow">
<meta property="og:type" content="website">
<meta property="og:locale" content="es_CL">
<meta property="og:title" content="INGEPUCON — Ingeniería Estructural · Pucón · Villarrica">
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

<body class="bp-ingcon">

<!-- Topbar -->
<div class="topbar">
  <div class="topbar__inner">
    <div class="topbar__left">
      <span><span class="topbar__dot"></span> Atención · 09:00 – 19:00</span>
      <span><i class="fa-solid fa-compass-drafting"></i> Estudio de cálculo · Pucón</span>
    </div>
    <div class="topbar__right">
      <a href="tel:<?= htmlspecialchars($phoneRaw) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($phone) ?></a>
      <a class="topbar__email" href="mailto:<?= htmlspecialchars($email) ?>"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($email) ?></a>
    </div>
  </div>
</div>

<!-- Nav -->
<nav class="nav is-scrolled" id="siteNav" style="background:rgba(244,246,250,.92);">
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
      <a href="/proinca.php" class="nav__brand-link nav__brand-link--proinca">PROINCA</a>
      <a href="/ingepucon.php"  class="nav__brand-link nav__brand-link--ingcon is-active">INGEPUCON</a>
      <button class="nav__toggle" id="navToggle"><i class="fa-solid fa-bars"></i></button>
    </div>
  </div>
</nav>

<!-- ============== HERO ============== -->
<header class="icover">
  <div class="container icover__inner">
    <div data-reveal>
      <div class="icover__spec">
        <span><strong>DOC</strong> 02-INGEPUCON</span>
        <span><strong>REV</strong> 2026·A</span>
        <span><strong>ISSUE</strong> Pucón · La Araucanía · CL</span>
      </div>
      <span class="icover__sub">02 · Ingeniería estructural</span>
      <h1 class="icover__name">INGEPUCON</h1>
      <p class="icover__tagline"><?= htmlspecialchars($brand['tagline']) ?></p>

      <div class="icover__data">
        <div class="icover__data-cell">
          <div class="icover__data-key">Norma vigente</div>
          <div class="icover__data-val">NCh 433 · 430 · 427 · 2369</div>
        </div>
        <div class="icover__data-cell">
          <div class="icover__data-key">Materialidad</div>
          <div class="icover__data-val">Hormigón · Acero · Madera</div>
        </div>
        <div class="icover__data-cell">
          <div class="icover__data-key">Entregables</div>
          <div class="icover__data-val">Memoria + planos firmados</div>
        </div>
        <div class="icover__data-cell">
          <div class="icover__data-key">Firma</div>
          <div class="icover__data-val">Alexis Bello — IC Calculista</div>
        </div>
      </div>
    </div>

    <aside class="icover__card" data-reveal>
      <?php if ($sent): ?>
        <div class="form__success">
          <i class="fa-solid fa-circle-check"></i>
          <h3>¡Solicitud recibida!</h3>
          <p>Te respondemos en menos de 24h hábiles.</p>
        </div>
      <?php else: ?>
        <span class="eyebrow" style="color:var(--ink);">Solicitud técnica</span>
        <h3 style="margin:6px 0 4px; font-size:22px;">Consultar a INGEPUCON.</h3>
        <p style="color:var(--muted); font-size:13.5px; margin-bottom:20px;">
          Respuesta en menos de 24 h hábiles · Evaluación inicial sin costo.
        </p>

        <?php if ($error): ?><p class="alert alert--error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

        <form method="post" novalidate>
          <input type="hidden" name="action" value="submit_lead">
          <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
          <input type="hidden" name="form_started" value="<?= time() ?>">
          <input type="hidden" name="source" value="ingcon">
          <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

          <div class="form__group">
            <label class="form__label" for="if-name">Nombre <span class="req">*</span></label>
            <input id="if-name" name="name" class="form__input" required>
          </div>
          <div class="form__row">
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label">Teléfono <span class="req">*</span></label>
              <input name="phone" type="tel" class="form__input" required>
            </div>
            <div class="form__group" style="margin-bottom:0;">
              <label class="form__label">Correo <span class="req">*</span></label>
              <input name="email" type="email" class="form__input" required>
            </div>
          </div>
          <div class="form__group">
            <label class="form__label">Servicio</label>
            <select name="servicio" class="form__select">
              <option value="">Selecciona…</option>
              <?php foreach ($brand['form_options'] as $opt): ?>
                <option><?= htmlspecialchars($opt) ?></option>
              <?php endforeach; ?>
              <option>Otro</option>
            </select>
          </div>
          <div class="form__group">
            <label class="form__label">Detalle</label>
            <textarea name="message" rows="3" class="form__textarea" placeholder="Tipología, m², ubicación…"></textarea>
          </div>
          <button type="submit" class="btn btn--primary btn--full">
            Enviar solicitud <i class="fa-solid fa-arrow-right arrow"></i>
          </button>
        </form>
      <?php endif; ?>
    </aside>
  </div>
</header>

<!-- ============== NORMAS ============== -->
<section class="inorm">
  <div class="container inorm__inner">
    <div class="inorm__head" data-reveal>
      <h2>Normativa vigente.</h2>
      <p>Cada proyecto se entrega bajo norma chilena. Sin atajos, sin interpretaciones libres.</p>
    </div>
    <div class="inorm__grid" data-reveal>
      <div class="inorm__card">
        <div class="inorm__code">NCh 433</div>
        <div class="inorm__name">Diseño sísmico de edificios</div>
      </div>
      <div class="inorm__card">
        <div class="inorm__code">NCh 430</div>
        <div class="inorm__name">Hormigón armado</div>
      </div>
      <div class="inorm__card">
        <div class="inorm__code">NCh 427</div>
        <div class="inorm__name">Diseño en acero</div>
      </div>
      <div class="inorm__card">
        <div class="inorm__code">NCh 2369</div>
        <div class="inorm__name">Diseño sísmico industrial</div>
      </div>
    </div>
  </div>
</section>

<!-- ============== SERVICIOS — datasheet ============== -->
<section class="isection">
  <div class="container">
    <div class="ihead" data-reveal>
      <div class="ihead__num">§ 01 · Servicios</div>
      <h2 class="ihead__title"><?= $brand['services_title'] ?></h2>
    </div>
    <p style="color:var(--ink-soft); max-width:60ch; margin-bottom:36px; margin-top:-32px;" data-reveal>
      <?= htmlspecialchars($brand['services_lede']) ?>
    </p>

    <div class="isheet" data-reveal>
      <div class="isheet__row isheet__head">
        <div class="isheet__cell">ID</div>
        <div class="isheet__cell">Servicio</div>
        <div class="isheet__cell">Descripción</div>
      </div>
      <?php foreach ($brand['services'] as $i => $svc): ?>
        <div class="isheet__row">
          <div class="isheet__cell isheet__id">ING.<?= sprintf('%02d', $i + 1) ?></div>
          <div class="isheet__cell isheet__title"><?= htmlspecialchars($svc['title']) ?></div>
          <div class="isheet__cell isheet__desc"><?= htmlspecialchars($svc['text']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============== PROYECTOS ============== -->
<section class="isection iprojects">
  <div class="container">
    <div class="ihead" data-reveal>
      <div class="ihead__num">§ 02 · Trabajos</div>
      <h2 class="ihead__title">Proyectos calculados.</h2>
    </div>
    <div class="iprojects__grid" data-reveal>
      <?php foreach ($brand['projects'] as $i => $p): ?>
        <article class="iproj">
          <div class="iproj__media"><img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy"></div>
          <div class="iproj__body">
            <div class="iproj__id">
              <span>PRJ.<?= sprintf('%03d', $i + 1) ?></span>
              <span><?= htmlspecialchars($p['type']) ?></span>
            </div>
            <h3><?= htmlspecialchars($p['title']) ?></h3>
            <p><?= htmlspecialchars($p['text']) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============== POR QUÉ ============== -->
<section class="isection iwhy">
  <div class="container">
    <div class="ihead" data-reveal>
      <div class="ihead__num">§ 03 · Por qué INGEPUCON</div>
      <h2 class="ihead__title"><?= $brand['why_title'] ?></h2>
    </div>
    <div class="iwhy__grid" data-reveal>
      <?php foreach ($brand['why'] as $w): ?>
        <div class="iwhy__item">
          <div class="iwhy__check"><i class="fa-solid fa-check"></i></div>
          <div>
            <h3><?= htmlspecialchars($w['title']) ?></h3>
            <p><?= htmlspecialchars($w['text']) ?></p>
          </div>
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
        <div class="ihead__num" style="color:var(--steel);">§ 04 · Contacto</div>
        <h2 style="margin-top:14px;">Solicitud técnica directa.</h2>
        <p class="contact__lede">Cuéntanos sobre el proyecto. Evaluación inicial sin costo y propuesta de honorarios en menos de 24h hábiles.</p>
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
            <div class="contact__row-icon"><i class="fa-solid fa-compass-drafting"></i></div>
            <div><span class="contact__row-label">Estudio</span>
              <span class="contact__row-val">Pucón · Región de La Araucanía</span></div>
          </div>
        </div>
      </div>
      <div data-reveal>
        <?php if ($sent): ?>
          <div class="form__success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Solicitud recibida!</h3>
            <p>Te respondemos pronto.</p>
          </div>
        <?php else: ?>
          <form method="post" novalidate style="background:var(--paper); border:1px solid var(--line); padding:32px; border-top:4px solid var(--ingcon); border-radius:4px;">
            <input type="hidden" name="action" value="submit_lead">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="form_started" value="<?= time() ?>">
            <input type="hidden" name="source" value="ingcon-contact">
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
              <label class="form__label">Servicio</label>
              <select name="servicio" class="form__select">
                <option value="">Selecciona…</option>
                <?php foreach ($brand['form_options'] as $opt): ?><option><?= htmlspecialchars($opt) ?></option><?php endforeach; ?>
                <option>Otro</option>
              </select>
            </div>
            <div class="form__group">
              <label class="form__label">Detalle del proyecto</label>
              <textarea name="message" rows="4" class="form__textarea"></textarea>
            </div>
            <button type="submit" class="btn btn--primary btn--full">Enviar solicitud <i class="fa-solid fa-arrow-right arrow"></i></button>
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
        <p class="footer__brand-desc">Cálculo estructural e ingeniería de proyectos en Pucón, Villarrica y La Araucanía. Bajo norma chilena vigente.</p>
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

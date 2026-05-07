<?php
/**
 * Helpers de cromo del sitio: render_head_open, render_topbar_nav, render_footer_close.
 * Centraliza topbar + nav + footer + scripts para todas las páginas internas.
 *
 * Uso:
 *   $page = ['title' => '…', 'description' => '…', 'active' => 'servicios'];
 *   require __DIR__ . '/components/site_chrome.php';
 *   render_head_open($page);
 *   render_topbar_nav($page);
 *   // … contenido específico de la página …
 *   render_footer_close();
 */

function _chrome_settings(): array {
    static $cached = null;
    if ($cached !== null) return $cached;
    $phone     = trim((string) getSetting('contact_phone', '+56 9 1234 5678'));
    $phoneRaw  = preg_replace('/[^0-9]/', '', $phone);
    $email     = trim((string) getSetting('contact_email', 'contacto@alexisbello.cl'));
    $waText    = rawurlencode("Hola Alexis, me interesa consultar sobre un proyecto de ingeniería/construcción. ¿Podríamos conversar?");
    return $cached = [
        'phone'    => $phone,
        'phoneRaw' => $phoneRaw,
        'email'    => $email,
        'waUrl'    => 'https://wa.me/' . $phoneRaw . '?text=' . $waText,
        'gaId'     => trim((string) getSetting('ga_id', '')),
    ];
}

function render_head_open(array $page): void {
    $s = _chrome_settings();
    $title = $page['title'] ?? 'Alexis Bello — PROINCA · INGEPUCON';
    $desc  = $page['description'] ?? 'Ingeniería estructural y construcción profesional en Pucón, Villarrica y La Araucanía.';
    ?><!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($desc) ?>">
<meta name="robots" content="index, follow">
<meta property="og:type" content="website">
<meta property="og:locale" content="es_CL">
<meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($desc) ?>">
<link rel="icon" href="/assets/img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;1,500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/site.css">
<?php if ($s['gaId']): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($s['gaId']) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($s['gaId']) ?>');</script>
<?php endif; ?>
</head>
<body>
<?php
}

function render_topbar_nav(array $page): void {
    $s = _chrome_settings();
    $active = $page['active'] ?? '';
    $items = [
        'servicios'  => ['Servicios',  '/servicios.php'],
        'proyectos'  => ['Proyectos',  '/proyectos.php'],
        'cobertura'  => ['Cobertura',  '/cobertura.php'],
        'contacto'   => ['Contacto',   '/contacto.php'],
    ];
    ?>
    <div class="topbar">
      <div class="topbar__inner">
        <div class="topbar__left">
          <span><span class="topbar__dot"></span> Atención hoy · 09:00 – 19:00</span>
          <span><i class="fa-solid fa-location-dot"></i> Pucón · Villarrica · La Araucanía</span>
        </div>
        <div class="topbar__right">
          <a href="tel:<?= htmlspecialchars($s['phoneRaw']) ?>"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($s['phone']) ?></a>
          <a class="topbar__email" href="mailto:<?= htmlspecialchars($s['email']) ?>"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($s['email']) ?></a>
        </div>
      </div>
    </div>

    <nav class="nav is-scrolled" id="siteNav">
      <div class="nav__inner">
        <a href="/" class="nav__brand">
          <img src="/assets/img/logo.png" alt="">
          <span>Alexis Bello<span class="nav__brand-sub">PROINCA · INGEPUCON</span></span>
        </a>
        <ul class="nav__menu">
          <?php foreach ($items as $k => [$label, $href]): ?>
            <li><a href="<?= htmlspecialchars($href) ?>" <?= $k === $active ? 'style="color:var(--ink); font-weight:600;"' : '' ?>><?= htmlspecialchars($label) ?></a></li>
          <?php endforeach; ?>
        </ul>
        <div class="nav__cta-wrap">
          <a href="/proinca.php" class="nav__brand-link nav__brand-link--proinca <?= $active === 'proinca' ? 'is-active' : '' ?>">PROINCA</a>
          <a href="/ingepucon.php"  class="nav__brand-link nav__brand-link--ingcon  <?= $active === 'ingcon'  ? 'is-active' : '' ?>">INGEPUCON</a>
          <button class="nav__toggle" id="navToggle"><i class="fa-solid fa-bars"></i></button>
        </div>
      </div>
    </nav>
<?php
}

function render_footer_close(): void {
    $s = _chrome_settings();
    ?>
    <footer class="footer">
      <div class="container">
        <div class="footer__grid">
          <div>
            <div class="footer__brand-name">Alexis Bello</div>
            <div class="footer__brand-role">Ingeniero Civil · Calculista</div>
            <p class="footer__brand-desc">Ingeniería estructural y construcción profesional en Pucón, Villarrica y La Araucanía.</p>
            <div class="footer__brand-tags">
              <span class="footer__brand-tag footer__brand-tag--proinca">PROINCA</span>
              <span class="footer__brand-tag footer__brand-tag--ingcon">INGEPUCON</span>
            </div>
          </div>
          <div>
            <h4 class="footer__col-title">Sitio</h4>
            <ul class="footer__list">
              <li><a href="/servicios.php">Servicios</a></li>
              <li><a href="/proyectos.php">Proyectos</a></li>
              <li><a href="/cobertura.php">Cobertura</a></li>
              <li><a href="/contacto.php">Contacto</a></li>
            </ul>
          </div>
          <div>
            <h4 class="footer__col-title">Marcas</h4>
            <ul class="footer__list">
              <li><a href="/proinca.php">PROINCA · Construcción</a></li>
              <li><a href="/ingepucon.php">INGEPUCON · Ingeniería</a></li>
            </ul>
          </div>
          <div>
            <h4 class="footer__col-title">Contacto</h4>
            <ul class="footer__list">
              <li><a href="<?= htmlspecialchars($s['waUrl']) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> <?= htmlspecialchars($s['phone']) ?></a></li>
              <li><a href="mailto:<?= htmlspecialchars($s['email']) ?>"><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($s['email']) ?></a></li>
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

    <a class="wa-float" href="<?= htmlspecialchars($s['waUrl']) ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i></a>

    <script src="/assets/js/site.js"></script>
    </body>
    </html>
<?php
}

/**
 * Catálogo de demo proyectos compartido entre /proyectos.php y la sección home.
 */
function demo_projects(): array {
    return [
        ['id' => 'PRJ.001', 'brand' => 'PROINCA', 'title' => 'Casa Lago Villarrica', 'type' => 'Habitacional', 'area' => '220 m²', 'year' => 2024, 'city' => 'Villarrica', 'img' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1400&q=85&auto=format&fit=crop', 'text' => 'Vivienda en hormigón armado y madera laminada con vista al volcán Villarrica. Ejecución completa con coordinación full con arquitectura.'],
        ['id' => 'PRJ.002', 'brand' => 'INGEPUCON',  'title' => 'Galpón Industrial Freire', 'type' => 'Industrial · Acero', 'area' => '600 m²', 'year' => 2024, 'city' => 'Freire', 'img' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=1400&q=85&auto=format&fit=crop', 'text' => 'Estructura metálica para galpón industrial. Diseño sismorresistente bajo NCh 2369. Memoria de cálculo y planos firmados.'],
        ['id' => 'PRJ.003', 'brand' => 'PROINCA', 'title' => 'Local Comercial Pucón Centro', 'type' => 'Comercial', 'area' => '140 m²', 'year' => 2024, 'city' => 'Pucón', 'img' => 'https://images.unsplash.com/photo-1486325212027-8081e485255e?w=1400&q=85&auto=format&fit=crop', 'text' => 'Habilitación comercial completa: estructura, instalaciones, terminaciones y permisos finales.'],
        ['id' => 'PRJ.004', 'brand' => 'INGEPUCON',  'title' => 'Cálculo Casa Lican Ray', 'type' => 'Habitacional · Cálculo', 'area' => '180 m²', 'year' => 2023, 'city' => 'Lican Ray', 'img' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1400&q=85&auto=format&fit=crop', 'text' => 'Cálculo estructural en hormigón armado y madera laminada. Memoria + planos firmados bajo NCh 433/430/427.'],
        ['id' => 'PRJ.005', 'brand' => 'PROINCA', 'title' => 'Ampliación Familiar Villarrica', 'type' => 'Remodelación', 'area' => '80 m²', 'year' => 2023, 'city' => 'Villarrica', 'img' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?w=1400&q=85&auto=format&fit=crop', 'text' => 'Ampliación de vivienda existente con refuerzo estructural calculado por INGEPUCON y ejecución por PROINCA.'],
        ['id' => 'PRJ.006', 'brand' => 'INGEPUCON',  'title' => 'Regularización Vivienda Pucón', 'type' => 'Regularización', 'area' => '160 m²', 'year' => 2023, 'city' => 'Pucón', 'img' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1400&q=85&auto=format&fit=crop', 'text' => 'Cálculo retroactivo y memoria para regularización ante DOM Pucón. Aprobado a primera vuelta.'],
        ['id' => 'PRJ.007', 'brand' => 'PROINCA', 'title' => 'Casa Habitación Caburgua', 'type' => 'Habitacional', 'area' => '195 m²', 'year' => 2022, 'city' => 'Caburgua', 'img' => 'https://images.unsplash.com/photo-1416331108676-a22ccb276e35?w=1400&q=85&auto=format&fit=crop', 'text' => 'Vivienda de segunda residencia en madera y hormigón. Supervisión técnica permanente en obra.'],
        ['id' => 'PRJ.008', 'brand' => 'INGEPUCON',  'title' => 'Diseño Estructural Edificio', 'type' => 'Edificación', 'area' => '1.200 m²', 'year' => 2022, 'city' => 'Villarrica', 'img' => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=1400&q=85&auto=format&fit=crop', 'text' => 'Cálculo estructural completo de edificación de 4 pisos en hormigón armado. Sistema de muros sismorresistentes.'],
    ];
}

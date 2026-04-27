<?php

if (getenv('DEV_PREVIEW') === '1') {
    require __DIR__ . '/lib/dev-stubs.php';
} else {
    require __DIR__ . '/lib/bootstrap.php';
}

require __DIR__ . '/components/site_chrome.php';

$zones = [
    ['name' => 'Pucón',         'tier' => 'principal', 'note' => 'Cobertura total · visita en terreno'],
    ['name' => 'Villarrica',    'tier' => 'principal', 'note' => 'Cobertura total · visita en terreno'],
    ['name' => 'Caburgua',      'tier' => 'regular',   'note' => 'Cobertura habitual'],
    ['name' => 'Lican Ray',     'tier' => 'regular',   'note' => 'Cobertura habitual'],
    ['name' => 'Cunco',         'tier' => 'regular',   'note' => 'Cobertura habitual'],
    ['name' => 'Loncoche',      'tier' => 'regular',   'note' => 'Cobertura habitual'],
    ['name' => 'Freire',        'tier' => 'regular',   'note' => 'Cobertura habitual'],
    ['name' => 'Temuco',        'tier' => 'consulta',  'note' => 'Consultar disponibilidad'],
    ['name' => 'Toda La Araucanía', 'tier' => 'consulta', 'note' => 'Proyectos puntuales'],
];

render_head_open([
    'title'       => 'Cobertura — Alexis Bello · PROINCA · INGCON',
    'description' => 'Zonas de cobertura: Pucón, Villarrica y toda La Araucanía. Conocimiento del territorio, suelos y normativa local.',
    'active'      => 'cobertura',
]);
render_topbar_nav(['active' => 'cobertura']);
?>

<header class="page-hero">
  <div class="page-hero__inner" data-reveal>
    <nav class="page-hero__crumbs"><a href="/">Inicio</a> <span>/</span> <strong>Cobertura</strong></nav>
    <h1>Presencia local. <em>Conocimiento del territorio.</em></h1>
    <p>Trabajamos donde vivimos. Conocemos los suelos, el clima, la normativa municipal y los requerimientos sísmicos específicos de la zona.</p>
  </div>
</header>

<section class="section">
  <div class="container">
    <div class="coverage__grid" data-reveal>
      <div>
        <span class="eyebrow">Zona principal</span>
        <h2 style="margin-top:14px; font-size:clamp(28px,4vw,44px); letter-spacing:-.025em;">Pucón · Villarrica <em>y zona lacustre.</em></h2>
        <p style="color:var(--ink-soft); margin-top:22px; max-width:50ch;">
          La zona lacustre y precordillerana de La Araucanía tiene características sísmicas, geológicas y climáticas particulares. Trabajar acá no es lo mismo que trabajar en otra región — y eso es parte de nuestro diferencial técnico.
        </p>
        <p style="color:var(--ink-soft); margin-top:14px; max-width:50ch;">
          Conocemos las DOM municipales, los profesionales de la zona, los proveedores y las condiciones reales de obra. Tu proyecto avanza más rápido cuando el equipo conoce el terreno.
        </p>
      </div>

      <div class="coverage__zones">
        <?php foreach ($zones as $z):
          $isPrimary = $z['tier'] === 'principal';
        ?>
          <div class="coverage__zone <?= $isPrimary ? 'coverage__zone--primary' : '' ?>">
            <i class="fa-solid fa-location-dot"></i>
            <div>
              <strong><?= htmlspecialchars($z['name']) ?></strong>
              <span><?= htmlspecialchars($z['note']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section--dark" style="padding-block: clamp(60px, 8vw, 100px);">
  <div class="container" style="text-align:center;" data-reveal>
    <h2 style="font-size:clamp(28px, 4vw, 44px); max-width:24ch; margin-inline:auto;">¿Tu proyecto está en la zona?</h2>
    <p style="color:rgba(255,255,255,.7); margin-top:18px; max-width:48ch; margin-inline:auto;">Consulta disponibilidad. Si está fuera del área principal, evaluamos caso a caso.</p>
    <a href="/contacto.php" class="btn btn--gold" style="margin-top:32px;">Consultar disponibilidad <i class="fa-solid fa-arrow-right arrow"></i></a>
  </div>
</section>

<?php render_footer_close(); ?>

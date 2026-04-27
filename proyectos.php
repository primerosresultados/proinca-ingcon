<?php

if (getenv('DEV_PREVIEW') === '1') {
    require __DIR__ . '/lib/dev-stubs.php';
} else {
    require __DIR__ . '/lib/bootstrap.php';
}

require __DIR__ . '/components/site_chrome.php';

$projects = demo_projects();

render_head_open([
    'title'       => 'Proyectos — Alexis Bello · PROINCA · INGCON',
    'description' => 'Selección de obras y proyectos calculados por Alexis Bello en Pucón, Villarrica y La Araucanía.',
    'active'      => 'proyectos',
]);
render_topbar_nav(['active' => 'proyectos']);
?>

<header class="page-hero">
  <div class="page-hero__inner" data-reveal>
    <nav class="page-hero__crumbs"><a href="/">Inicio</a> <span>/</span> <strong>Proyectos</strong></nav>
    <h1>Una selección de obras <em>y proyectos calculados.</em></h1>
    <p>Una muestra del trabajo de PROINCA e INGCON en la zona lacustre y precordillerana de La Araucanía.</p>
  </div>
</header>

<section class="section">
  <div class="container">

    <div class="pfilter" data-reveal>
      <button class="is-active" data-filter="all">Todos · <?= count($projects) ?></button>
      <button data-filter="PROINCA">PROINCA · Construcción</button>
      <button data-filter="INGCON">INGCON · Ingeniería</button>
    </div>

    <div class="pgrid" data-reveal id="projectsGrid">
      <?php foreach ($projects as $p): ?>
        <article class="pcard" data-brand="<?= htmlspecialchars($p['brand']) ?>">
          <div class="pcard__media">
            <span class="pcard__brand pcard__brand--<?= strtolower($p['brand']) ?>"><?= htmlspecialchars($p['brand']) ?></span>
            <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" loading="lazy">
          </div>
          <div class="pcard__body">
            <div class="pcard__id">
              <span><?= htmlspecialchars($p['id']) ?></span>
              <span><?= htmlspecialchars((string) $p['year']) ?></span>
            </div>
            <h3><?= htmlspecialchars($p['title']) ?></h3>
            <p class="pcard__text"><?= htmlspecialchars($p['text']) ?></p>
            <div class="pcard__meta">
              <span><?= htmlspecialchars($p['type']) ?></span>
              <span><?= htmlspecialchars($p['area']) ?></span>
              <span><?= htmlspecialchars($p['city']) ?></span>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<section class="section section--dark" style="padding-block: clamp(60px, 8vw, 100px);">
  <div class="container" style="text-align:center;" data-reveal>
    <h2 style="font-size:clamp(28px, 4vw, 44px); max-width:24ch; margin-inline:auto;">¿Tienes un proyecto en mente?</h2>
    <p style="color:rgba(255,255,255,.7); margin-top:18px; max-width:48ch; margin-inline:auto;">Cuéntanos en qué consiste. Evaluación inicial sin costo, respuesta en 24h hábiles.</p>
    <a href="/contacto.php" class="btn btn--gold" style="margin-top:32px;">Cotizar proyecto <i class="fa-solid fa-arrow-right arrow"></i></a>
  </div>
</section>

<script>
(function(){
  var btns = document.querySelectorAll('.pfilter button');
  var cards = document.querySelectorAll('#projectsGrid .pcard');
  btns.forEach(function(b){
    b.addEventListener('click', function(){
      btns.forEach(function(x){ x.classList.remove('is-active'); });
      b.classList.add('is-active');
      var f = b.getAttribute('data-filter');
      cards.forEach(function(c){
        c.style.display = (f === 'all' || c.getAttribute('data-brand') === f) ? '' : 'none';
      });
    });
  });
})();
</script>

<?php render_footer_close(); ?>

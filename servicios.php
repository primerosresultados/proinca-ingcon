<?php

if (getenv('DEV_PREVIEW') === '1') {
    require __DIR__ . '/lib/dev-stubs.php';
} else {
    require __DIR__ . '/lib/bootstrap.php';
}

require __DIR__ . '/components/site_chrome.php';

$services = [
    ['num' => '01', 'brand' => 'PROINCA', 'title' => 'Construcción habitacional',  'text' => 'Casas, segundas residencias y edificaciones residenciales. Fundaciones, estructura y terminaciones bajo control profesional.'],
    ['num' => '02', 'brand' => 'PROINCA', 'title' => 'Construcción comercial',     'text' => 'Locales, oficinas y obras menores comerciales. Foco en plazos, costos y normativa local.'],
    ['num' => '03', 'brand' => 'PROINCA', 'title' => 'Supervisión técnica de obra','text' => 'Inspección técnica de obra (ITO) en proyectos propios y de terceros. Control de avance, calidad y registro documental.'],
    ['num' => '04', 'brand' => 'PROINCA', 'title' => 'Dirección de proyectos',     'text' => 'Coordinación integral entre arquitectura, especialidades, mandante y municipio. Una sola interlocución.'],
    ['num' => '05', 'brand' => 'PROINCA', 'title' => 'Remodelaciones y ampliaciones', 'text' => 'Reformas estructurales y ampliaciones con cálculo asociado. Trabajo coordinado con INGEPUCON.'],
    ['num' => '06', 'brand' => 'INGEPUCON',  'title' => 'Cálculo estructural',        'text' => 'Diseño y cálculo de estructuras de hormigón armado, acero y madera. Memoria de cálculo y planos firmados conforme a NCh 433/430/427/2369.'],
    ['num' => '07', 'brand' => 'INGEPUCON',  'title' => 'Diseño estructural',         'text' => 'Predimensionamiento y modelación desde anteproyecto. Coordinación con arquitectura.'],
    ['num' => '08', 'brand' => 'INGEPUCON',  'title' => 'Ingeniería de proyectos',    'text' => 'Coordinación multidisciplinaria de especialidades hasta entrega completa de proyecto.'],
    ['num' => '09', 'brand' => 'INGEPUCON',  'title' => 'Asesoría técnica',           'text' => 'Revisión de proyectos de terceros, segunda opinión profesional y peritajes estructurales.'],
    ['num' => '10', 'brand' => 'INGEPUCON',  'title' => 'Regularizaciones',           'text' => 'Cálculo retroactivo y memoria para regularización de construcciones existentes ante la DOM.'],
    ['num' => '11', 'brand' => 'INGEPUCON',  'title' => 'Patologías estructurales',   'text' => 'Inspección, diagnóstico e informe técnico sobre patologías y daños en estructuras existentes.'],
];

render_head_open([
    'title'       => 'Servicios — Alexis Bello · PROINCA · INGEPUCON',
    'description' => 'Catálogo completo de servicios de construcción e ingeniería estructural en La Araucanía.',
    'active'      => 'servicios',
]);
render_topbar_nav(['active' => 'servicios']);
?>

<header class="page-hero">
  <div class="page-hero__inner" data-reveal>
    <nav class="page-hero__crumbs"><a href="/">Inicio</a> <span>/</span> <strong>Servicios</strong></nav>
    <h1>Servicios completos <em>de construcción e ingeniería.</em></h1>
    <p>Once líneas de trabajo coordinadas, divididas entre PROINCA (construcción) e INGEPUCON (ingeniería). Bajo el mismo criterio técnico.</p>
  </div>
</header>

<section class="section">
  <div class="container">
    <div class="bp-proinca">
      <div class="pservices__list" data-reveal>
        <?php foreach ($services as $svc): ?>
          <div class="pservices__row">
            <div class="pservices__row-num"><?= htmlspecialchars($svc['num']) ?> /</div>
            <div>
              <div style="display:flex; align-items:center; gap:14px; margin-bottom:8px;">
                <h3 style="margin:0;"><?= htmlspecialchars($svc['title']) ?></h3>
                <span style="font-family:var(--f-mono); font-size:11px; letter-spacing:.14em; text-transform:uppercase; padding:4px 10px; border-radius:4px; <?= $svc['brand'] === 'PROINCA' ? 'background:rgba(200,162,74,.12); color:var(--proinca-dk);' : 'background:rgba(31,53,86,.08); color:var(--ingcon);' ?>"><?= htmlspecialchars($svc['brand']) ?></span>
              </div>
            </div>
            <p style="margin:0;"><?= htmlspecialchars($svc['text']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section--dark" style="padding-block: clamp(60px, 8vw, 100px);">
  <div class="container" style="text-align:center;" data-reveal>
    <h2 style="font-size:clamp(28px, 4vw, 44px); max-width:24ch; margin-inline:auto;">¿Necesitas alguno de estos servicios?</h2>
    <p style="color:rgba(255,255,255,.7); margin-top:18px; max-width:48ch; margin-inline:auto;">Evaluación inicial sin costo. Respuesta en menos de 24h hábiles.</p>
    <div style="margin-top:32px; display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
      <a href="/contacto.php" class="btn btn--gold">Solicitar consulta <i class="fa-solid fa-arrow-right arrow"></i></a>
      <a href="/proyectos.php" class="btn btn--ghost" style="color:var(--paper); border-color:rgba(255,255,255,.25);">Ver proyectos</a>
    </div>
  </div>
</section>

<?php render_footer_close(); ?>

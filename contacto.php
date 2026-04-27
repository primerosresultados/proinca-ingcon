<?php

if (getenv('DEV_PREVIEW') === '1') {
    require __DIR__ . '/lib/dev-stubs.php';
} else {
    require __DIR__ . '/lib/bootstrap.php';
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_lead') {
    require __DIR__ . '/index.php';
    return;
}

require __DIR__ . '/components/site_chrome.php';

$s        = _chrome_settings();
$phone    = $s['phone'];
$phoneRaw = $s['phoneRaw'];
$email    = $s['email'];
$waUrl    = $s['waUrl'];
$sent     = isset($_GET['sent']);

render_head_open([
    'title'       => 'Contacto — Alexis Bello · PROINCA · INGCON',
    'description' => 'Cuéntanos sobre tu proyecto. Evaluación inicial sin costo, respuesta en menos de 24h hábiles.',
    'active'      => 'contacto',
]);
render_topbar_nav(['active' => 'contacto']);
?>

<header class="page-hero">
  <div class="page-hero__inner" data-reveal>
    <nav class="page-hero__crumbs"><a href="/">Inicio</a> <span>/</span> <strong>Contacto</strong></nav>
    <h1>Hablemos sobre <em>tu proyecto.</em></h1>
    <p>Cuéntanos en qué consiste. Respondemos personalmente con una evaluación inicial sin costo, dentro de las 24 horas hábiles.</p>
  </div>
</header>

<section class="section contact">
  <div class="container">
    <div class="contact__grid">

      <div data-reveal>
        <span class="eyebrow">Datos directos</span>
        <h2 style="margin-top:18px;">Tres formas <em>de empezar.</em></h2>
        <p class="contact__lede">Elige la que prefieras — todas las consultas las atiende personalmente Alexis Bello.</p>

        <div class="contact__details">
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-brands fa-whatsapp"></i></div>
            <div>
              <span class="contact__row-label">WhatsApp directo · respuesta más rápida</span>
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
            <div class="contact__row-icon"><i class="fa-solid fa-phone"></i></div>
            <div>
              <span class="contact__row-label">Teléfono</span>
              <span class="contact__row-val"><a href="tel:<?= htmlspecialchars($phoneRaw) ?>"><?= htmlspecialchars($phone) ?></a></span>
            </div>
          </div>
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-solid fa-clock"></i></div>
            <div>
              <span class="contact__row-label">Horario de atención</span>
              <span class="contact__row-val">Lunes a viernes · 09:00 – 19:00 (hora Chile)</span>
            </div>
          </div>
          <div class="contact__row">
            <div class="contact__row-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div>
              <span class="contact__row-label">Ubicación</span>
              <span class="contact__row-val">Pucón · Región de La Araucanía · Chile</span>
            </div>
          </div>
        </div>
      </div>

      <div data-reveal>
        <?php if ($sent): ?>
          <div class="form__success">
            <i class="fa-solid fa-circle-check"></i>
            <h3>¡Mensaje enviado!</h3>
            <p>Gracias por escribir. Te respondemos dentro de las próximas 24 horas hábiles.</p>
          </div>
        <?php else: ?>
          <form method="post" novalidate style="background:var(--paper); border:1px solid var(--line); border-radius:var(--r); padding:32px;">
            <span class="eyebrow" style="color:var(--ink); margin-bottom:18px;">Formulario de contacto</span>
            <h3 style="font-size:24px; margin:8px 0 22px; letter-spacing:-.02em;">Cuéntanos tu proyecto.</h3>

            <input type="hidden" name="action" value="submit_lead">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="form_started" value="<?= time() ?>">
            <input type="hidden" name="source" value="contacto-page">
            <input type="text" name="website" value="" style="position:absolute;left:-9999px;" tabindex="-1" autocomplete="off" aria-hidden="true">

            <?php if ($error): ?><p class="alert alert--error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Nombre <span class="req">*</span></label><input name="name" class="form__input" required autocomplete="name"></div>
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Teléfono <span class="req">*</span></label><input name="phone" type="tel" class="form__input" required autocomplete="tel"></div>
            </div>
            <div class="form__row">
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Correo <span class="req">*</span></label><input name="email" type="email" class="form__input" required autocomplete="email"></div>
              <div class="form__group" style="margin-bottom:0;"><label class="form__label">Ciudad</label><input name="ciudad" class="form__input"></div>
            </div>
            <div class="form__group">
              <label class="form__label">Tipo de proyecto</label>
              <select name="servicio" class="form__select">
                <option value="">Selecciona…</option>
                <optgroup label="PROINCA · Construcción">
                  <option>Construcción habitacional</option>
                  <option>Construcción comercial</option>
                  <option>Supervisión técnica</option>
                  <option>Remodelación / ampliación</option>
                </optgroup>
                <optgroup label="INGCON · Ingeniería">
                  <option>Cálculo estructural</option>
                  <option>Ingeniería de proyecto</option>
                  <option>Asesoría técnica</option>
                  <option>Regularización</option>
                </optgroup>
                <option>Otro / no estoy seguro</option>
              </select>
            </div>
            <div class="form__group">
              <label class="form__label">Mensaje</label>
              <textarea name="message" rows="5" class="form__textarea" placeholder="Cuéntanos brevemente tu proyecto: tipo de obra, ubicación, m² estimados, fecha aproximada…"></textarea>
            </div>
            <button type="submit" class="btn btn--primary btn--full">Enviar consulta <i class="fa-solid fa-paper-plane"></i></button>
            <p class="form__note"><i class="fa-solid fa-lock"></i> Información privada · no compartimos datos con terceros.</p>
          </form>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php render_footer_close(); ?>

(() => {
  const DATA = {
    pucon: {
      name: 'Pucón',
      tier: 'Cobertura principal',
      desc: 'Base operativa de PROINCA · INGCON. Disponibilidad inmediata para visitas técnicas, inspecciones y gestión de permisos municipales.',
      facts: [
        'Atención presencial de lunes a viernes',
        'Visitas técnicas sin costo en sector urbano',
        'Conocimiento del PRC y normativa municipal',
        'Red de proveedores y subcontratos locales',
      ],
      response: 'Respuesta en 24 h',
      distance: 'Zona de residencia — sin traslado',
    },
    villarrica: {
      name: 'Villarrica',
      tier: 'Cobertura principal',
      desc: 'Cobertura completa para proyectos de cálculo estructural, regularizaciones y construcción. Visitas periódicas durante la semana.',
      facts: [
        'Visitas técnicas programadas',
        'Trámites municipales presenciales',
        'Especialización en zonas lacustres y laderas',
        'Experiencia con suelos de la cuenca',
      ],
      response: 'Respuesta en 24 h',
      distance: '25 km desde Pucón',
    },
    cunco: {
      name: 'Cunco',
      tier: 'Cobertura regular',
      desc: 'Atendemos proyectos bajo agenda. Ideal para obras habitacionales rurales y cálculo estructural de galpones y bodegas.',
      facts: [
        'Visita técnica bajo coordinación previa',
        'Presupuesto incluye traslado',
        'Experiencia en construcción rural',
      ],
      response: 'Respuesta en 48 h',
      distance: '58 km desde Pucón',
    },
    loncoche: {
      name: 'Loncoche',
      tier: 'Cobertura regular',
      desc: 'Proyectos habitacionales y comerciales con coordinación previa. Cálculo estructural y regularizaciones a distancia.',
      facts: [
        'Visita programada por hitos',
        'Planimetría y cálculo remoto disponible',
        'Coordinación con DOM local',
      ],
      response: 'Respuesta en 48 h',
      distance: '65 km desde Pucón',
    },
    freire: {
      name: 'Freire',
      tier: 'Cobertura regular',
      desc: 'Servicios de ingeniería y construcción con agenda previa. Enfoque en vivienda unifamiliar y ampliaciones.',
      facts: [
        'Visita técnica coordinada',
        'Regularizaciones y permisos DOM',
        'Cálculo estructural con criterios sísmicos locales',
      ],
      response: 'Respuesta en 48 h',
      distance: '85 km desde Pucón',
    },
    araucania: {
      name: 'Toda La Araucanía',
      tier: 'Consultar disponibilidad',
      desc: 'Evaluamos proyectos puntuales en el resto de la región. Dependiendo de la escala, visitamos la obra o trabajamos en coordinación con un profesional local.',
      facts: [
        'Cotización según ubicación y escala',
        'Cálculo y planimetría a distancia',
        'Coordinación con profesionales locales',
      ],
      response: 'Evaluación en 72 h',
      distance: 'Variable — según comuna',
    },
  };

  const style = document.createElement('style');
  style.textContent = `
    .coverage__zone { border: 0; font: inherit; text-align: left; cursor: pointer;
      width: 100%; transition: transform .25s var(--ease), background .25s; }
    .coverage__zone:hover { transform: translateY(-2px); }
    .coverage__zone:focus-visible { outline: 2px solid var(--magenta); outline-offset: 3px; }
    .locmodal[hidden] { display: none; }
    .locmodal { position: fixed; inset: 0; z-index: 9999; display: flex;
      align-items: center; justify-content: center; padding: 20px; }
    .locmodal__backdrop { position: absolute; inset: 0;
      background: rgba(20,18,50,.72); backdrop-filter: blur(6px);
      animation: locmodal-fade .25s ease; }
    .locmodal__dialog { position: relative; width: min(920px, 100%);
      max-height: calc(100vh - 40px); overflow: auto;
      background: #fff; color: var(--ink); border-radius: 16px;
      box-shadow: 0 30px 80px -20px rgba(0,0,0,.5);
      animation: locmodal-pop .35s cubic-bezier(.2,.9,.3,1.1); }
    .locmodal__close { position: absolute; top: 14px; right: 14px; z-index: 2;
      width: 36px; height: 36px; border-radius: 50%; border: 1px solid var(--line);
      background: #fff; color: var(--ink); cursor: pointer; font-size: 16px;
      display: grid; place-items: center; transition: all .2s; }
    .locmodal__close:hover { background: var(--ink); color: #fff; transform: rotate(90deg); }
    .locmodal__grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 0; min-height: 460px; }
    .locmodal__info { padding: clamp(28px, 3vw, 44px); background: var(--fog); }
    .locmodal__eyebrow { display: inline-block; font-size: 11px; font-weight: 700;
      letter-spacing: .18em; text-transform: uppercase; color: var(--magenta);
      padding: 6px 12px; background: rgba(232,178,45,.12); border-radius: 999px;
      margin-bottom: 16px; }
    .locmodal__title { font-family: var(--f-display); font-size: clamp(28px, 3vw, 40px);
      font-weight: 700; letter-spacing: -.02em; margin: 0 0 12px; color: var(--ink); }
    .locmodal__desc { font-size: 14px; line-height: 1.6; color: var(--steel);
      margin: 0 0 20px; }
    .locmodal__facts { list-style: none; padding: 0; margin: 0 0 24px;
      display: flex; flex-direction: column; gap: 10px; }
    .locmodal__facts li { position: relative; padding-left: 22px; font-size: 13.5px;
      line-height: 1.5; color: var(--graphite); }
    .locmodal__facts li::before { content: ''; position: absolute; left: 0; top: 7px;
      width: 12px; height: 2px; background: var(--magenta); }
    .locmodal__meta { display: flex; flex-direction: column; gap: 10px;
      padding-top: 18px; border-top: 1px solid var(--line); font-size: 12.5px; color: var(--steel); }
    .locmodal__meta i { color: var(--magenta); margin-right: 8px; width: 14px; }
    .locmodal__form { padding: clamp(28px, 3vw, 44px); display: flex; flex-direction: column; gap: 14px; }
    .locmodal__form-title { font-family: var(--f-display); font-size: 18px; font-weight: 700;
      margin: 0 0 6px; color: var(--ink); }
    .locmodal__submit { margin-top: 4px; justify-content: center; }
    .locmodal__success { margin: 8px 0 0; padding: 12px; background: rgba(34,197,94,.1);
      color: #15803d; font-size: 13px; border-radius: 8px; text-align: center; font-weight: 600; }
    @keyframes locmodal-fade { from { opacity: 0; } to { opacity: 1; } }
    @keyframes locmodal-pop { from { opacity: 0; transform: translateY(12px) scale(.98); }
      to { opacity: 1; transform: translateY(0) scale(1); } }
    @media (max-width: 720px) {
      .locmodal__grid { grid-template-columns: 1fr; }
      .locmodal__dialog { max-height: calc(100vh - 20px); }
    }
    body.locmodal-open { overflow: hidden; }
  `;
  document.head.appendChild(style);

  const modal = document.getElementById('locationModal');
  if (!modal) return;
  const form = modal.querySelector('#locmodalForm');
  const successEl = modal.querySelector('[data-role="success"]');

  const fill = (key) => {
    const d = DATA[key];
    if (!d) return;
    modal.querySelector('[data-role="name"]').textContent = d.name;
    modal.querySelector('[data-role="tier"]').textContent = d.tier;
    modal.querySelector('[data-role="desc"]').textContent = d.desc;
    const facts = modal.querySelector('[data-role="facts"]');
    facts.innerHTML = d.facts.map(f => `<li>${f}</li>`).join('');
    modal.querySelector('[data-role="response"]').textContent = d.response;
    modal.querySelector('[data-role="distance"]').textContent = d.distance;
    modal.querySelector('[data-role="hidden-location"]').value = d.name;
  };

  const open = (key) => {
    fill(key);
    modal.hidden = false;
    document.body.classList.add('locmodal-open');
    setTimeout(() => modal.querySelector('#locm-nombre')?.focus(), 200);
  };
  const close = () => {
    modal.hidden = true;
    document.body.classList.remove('locmodal-open');
    form.reset();
    form.hidden = false;
    successEl.hidden = true;
  };

  document.querySelectorAll('.coverage__zone[data-location]').forEach(btn => {
    btn.addEventListener('click', () => open(btn.dataset.location));
  });

  modal.querySelectorAll('[data-role="close"]').forEach(el => el.addEventListener('click', close));
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && !modal.hidden) close();
  });

  form.addEventListener('submit', e => {
    e.preventDefault();
    form.hidden = true;
    successEl.hidden = false;
    setTimeout(close, 2000);
  });
})();

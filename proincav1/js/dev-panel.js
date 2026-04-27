/* ============================================================
   DEV COLOR PANEL — live CSS custom property tuner
   Gated by window.__devPanel === true or ?dev=1 in the URL.
============================================================ */
(() => {
  const params = new URLSearchParams(location.search);
  if (params.get('dev') === '0' || window.__devPanel === false) return;

  const STORAGE_KEY = 'proinca_dev_colors_v1';

  const VARS = [
    { name: '--ink',          label: 'Ink (fondo oscuro)' },
    { name: '--ink-2',        label: 'Ink 2' },
    { name: '--ink-deep',     label: 'Ink deep' },
    { name: '--brand-purple', label: 'Brand purple' },
    { name: '--graphite',     label: 'Graphite' },
    { name: '--steel',        label: 'Steel' },
    { name: '--violet',       label: 'Violet' },
    { name: '--violet-dk',    label: 'Violet dark' },
    { name: '--violet-lt',    label: 'Violet light' },
    { name: '--violet-hi',    label: 'Violet highlight' },
    { name: '--magenta',      label: 'Acento dorado' },
    { name: '--cyan',         label: 'Cyan' },
    { name: '--proinca',      label: 'PROINCA' },
    { name: '--proinca-dk',   label: 'PROINCA dark' },
    { name: '--ingcon',       label: 'INGCON' },
    { name: '--ingcon-dk',    label: 'INGCON dark' },
    { name: '--sand',         label: 'Sand' },
    { name: '--sand-dk',      label: 'Sand dark' },
    { name: '--fog',          label: 'Fog' },
    { name: '--paper',        label: 'Paper' },
    { name: '--line',         label: 'Line' },
    { name: '--line-soft',    label: 'Line soft' },
    { name: '--muted',        label: 'Muted text' },
  ];

  const rgbToHex = (str) => {
    if (!str) return '#000000';
    const s = str.trim();
    if (s.startsWith('#')) {
      if (s.length === 4) return '#' + [...s.slice(1)].map(c => c + c).join('').toLowerCase();
      return s.slice(0, 7).toLowerCase();
    }
    const m = s.match(/rgba?\(([^)]+)\)/i);
    if (!m) return '#000000';
    const parts = m[1].split(',').map(n => parseFloat(n.trim()));
    const toHex = n => Math.max(0, Math.min(255, Math.round(n))).toString(16).padStart(2, '0');
    return '#' + toHex(parts[0]) + toHex(parts[1]) + toHex(parts[2]);
  };

  const isValidHex = (v) => /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(v.trim());

  const loadStored = () => {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); }
    catch { return {}; }
  };
  const saveStored = (obj) => localStorage.setItem(STORAGE_KEY, JSON.stringify(obj));

  const applyVar = (name, value) => document.documentElement.style.setProperty(name, value);

  const restoreStored = () => {
    const stored = loadStored();
    Object.entries(stored).forEach(([k, v]) => applyVar(k, v));
    return stored;
  };

  const getComputed = (name) =>
    getComputedStyle(document.documentElement).getPropertyValue(name).trim();

  // CSS
  const style = document.createElement('style');
  style.textContent = `
    .devp { position: fixed; right: 18px; bottom: 18px; z-index: 999999;
      width: 300px; max-height: calc(100vh - 40px);
      background: rgba(14,32,56,.96); backdrop-filter: blur(10px);
      color: #fff; border-radius: 12px;
      box-shadow: 0 24px 60px -12px rgba(0,0,0,.55), 0 6px 18px -6px rgba(0,0,0,.4);
      font: 12px/1.4 system-ui, -apple-system, sans-serif;
      display: flex; flex-direction: column; overflow: hidden;
      transition: transform .3s cubic-bezier(.2,.8,.2,1); }
    .devp.is-collapsed { transform: translateY(calc(100% - 40px)); }
    .devp__head { display: flex; align-items: center; justify-content: space-between;
      padding: 10px 14px; height: 40px; background: rgba(255,255,255,.05);
      cursor: pointer; user-select: none; flex: none;
      border-bottom: 1px solid rgba(255,255,255,.08); }
    .devp__title { font-weight: 600; letter-spacing: .05em; text-transform: uppercase; font-size: 11px; }
    .devp__min { background: none; border: 0; color: #fff; cursor: pointer; font-size: 16px;
      line-height: 1; width: 22px; height: 22px; border-radius: 4px; }
    .devp__min:hover { background: rgba(255,255,255,.1); }
    .devp__body { padding: 10px 12px 12px; overflow-y: auto; flex: 1; }
    .devp__hint { font-size: 10.5px; color: rgba(255,255,255,.55); margin: 0 0 10px;
      line-height: 1.35; }
    .devp__row { display: grid; grid-template-columns: 1fr 26px 72px; gap: 6px;
      align-items: center; margin-bottom: 6px; }
    .devp__label { font-size: 10.5px; color: rgba(255,255,255,.8);
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .devp__color { width: 26px; height: 22px; border: 1px solid rgba(255,255,255,.15);
      border-radius: 4px; padding: 0; background: transparent; cursor: pointer; }
    .devp__color::-webkit-color-swatch { border: 0; border-radius: 3px; }
    .devp__color::-moz-color-swatch { border: 0; border-radius: 3px; }
    .devp__hex { width: 100%; font: 11px/1 ui-monospace, Menlo, monospace;
      padding: 4px 6px; border-radius: 4px; border: 1px solid rgba(255,255,255,.15);
      background: rgba(255,255,255,.06); color: #fff; text-transform: lowercase; }
    .devp__hex:focus { outline: 1px solid rgba(255,255,255,.4); }
    .devp__hex.is-invalid { border-color: #ef4444; }
    .devp__actions { display: flex; gap: 6px; margin-top: 10px;
      padding-top: 10px; border-top: 1px solid rgba(255,255,255,.08); }
    .devp__btn { flex: 1; padding: 7px 8px; font: 600 10.5px/1 system-ui, sans-serif;
      text-transform: uppercase; letter-spacing: .08em;
      background: rgba(255,255,255,.1); color: #fff;
      border: 1px solid rgba(255,255,255,.15); border-radius: 5px; cursor: pointer; }
    .devp__btn:hover { background: rgba(255,255,255,.18); }
    .devp__btn--primary { background: #E8B22D; color: #14223A; border-color: transparent; }
    .devp__btn--primary:hover { background: #d9a322; }
    .devp__feedback { position: absolute; top: 8px; left: 50%; transform: translateX(-50%);
      background: #22c55e; color: #fff; font-size: 10.5px; font-weight: 600;
      padding: 4px 10px; border-radius: 999px; opacity: 0; pointer-events: none;
      transition: opacity .2s; }
    .devp__feedback.is-show { opacity: 1; }
    .devp__body::-webkit-scrollbar { width: 6px; }
    .devp__body::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 3px; }
  `;
  document.head.appendChild(style);

  // DOM
  const panel = document.createElement('aside');
  panel.className = 'devp';
  panel.innerHTML = `
    <div class="devp__head" data-role="head">
      <span class="devp__title">Dev · Colores</span>
      <button class="devp__min" data-role="min" aria-label="Minimizar">—</button>
    </div>
    <div class="devp__body">
      <p class="devp__hint">Cambios en vivo. Se guardan en <code>localStorage</code>.</p>
      <div class="devp__rows"></div>
      <div class="devp__actions">
        <button class="devp__btn devp__btn--primary" data-role="copy">Copiar CSS</button>
        <button class="devp__btn" data-role="reset">Reset</button>
      </div>
    </div>
    <div class="devp__feedback" data-role="feedback">¡Copiado!</div>
  `;
  document.body.appendChild(panel);

  const stored = restoreStored();
  const rowsEl = panel.querySelector('.devp__rows');
  const inputs = {};

  VARS.forEach(({ name, label }) => {
    const current = stored[name] || getComputed(name) || '#000000';
    const hex = isValidHex(current) ? current.toLowerCase() : rgbToHex(current);

    const row = document.createElement('div');
    row.className = 'devp__row';
    row.innerHTML = `
      <span class="devp__label" title="${name}">${label}</span>
      <input type="color" class="devp__color" value="${hex}">
      <input type="text" class="devp__hex" value="${hex}" spellcheck="false">
    `;
    rowsEl.appendChild(row);

    const colorInput = row.querySelector('.devp__color');
    const hexInput = row.querySelector('.devp__hex');
    inputs[name] = { colorInput, hexInput };

    const update = (val, syncColor = true, syncHex = true) => {
      const v = val.toLowerCase();
      if (!isValidHex(v)) { hexInput.classList.add('is-invalid'); return; }
      hexInput.classList.remove('is-invalid');
      const full = v.length === 4 ? '#' + [...v.slice(1)].map(c => c + c).join('') : v;
      if (syncColor) colorInput.value = full;
      if (syncHex) hexInput.value = full;
      applyVar(name, full);
      const all = loadStored();
      all[name] = full;
      saveStored(all);
    };

    colorInput.addEventListener('input', e => update(e.target.value, false, true));
    hexInput.addEventListener('input', e => {
      const v = e.target.value.trim();
      if (isValidHex(v)) update(v, true, false);
      else hexInput.classList.add('is-invalid');
    });
  });

  // Collapse
  const head = panel.querySelector('[data-role="head"]');
  const minBtn = panel.querySelector('[data-role="min"]');
  const toggleCollapsed = (e) => {
    if (e) e.stopPropagation();
    panel.classList.toggle('is-collapsed');
  };
  head.addEventListener('click', (e) => {
    if (e.target === minBtn) return;
    panel.classList.toggle('is-collapsed');
  });
  minBtn.addEventListener('click', toggleCollapsed);

  // Copy
  const feedback = panel.querySelector('[data-role="feedback"]');
  const showFeedback = (msg = '¡Copiado!') => {
    feedback.textContent = msg;
    feedback.classList.add('is-show');
    setTimeout(() => feedback.classList.remove('is-show'), 1400);
  };
  panel.querySelector('[data-role="copy"]').addEventListener('click', async () => {
    const lines = VARS.map(({ name }) => `  ${name}: ${inputs[name].hexInput.value};`);
    const css = `:root {\n${lines.join('\n')}\n}`;
    try { await navigator.clipboard.writeText(css); showFeedback(); }
    catch { showFeedback('Error al copiar'); }
  });

  // Reset
  panel.querySelector('[data-role="reset"]').addEventListener('click', () => {
    localStorage.removeItem(STORAGE_KEY);
    VARS.forEach(({ name }) => document.documentElement.style.removeProperty(name));
    location.reload();
  });
})();

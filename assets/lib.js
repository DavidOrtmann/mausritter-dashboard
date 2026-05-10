const currentLang = localStorage.getItem('lang') || 'en';
window.LOCALE = (LOCALES[currentLang] || LOCALES['en']);

function t(key, vars = {}) {
  const parts = key.split('.');
  let val = window.LOCALE;
  for (const p of parts) val = val?.[p];
  if (typeof val !== 'string') return key;
  return val.replace(/\{(\w+)\}/g, (_, k) => String(vars[k] ?? k));
}

function applyI18n() {
  document.title = t('meta.title');
  document.documentElement.lang = currentLang;
  document.querySelectorAll('[data-i18n]').forEach(el => {
    el.textContent = t(el.dataset.i18n);
  });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    el.placeholder = t(el.dataset.i18nPlaceholder);
  });
}

function statLabel(key) {
  return t('stats.' + key.toLowerCase());
}

function uuid() {
  return crypto.randomUUID ? crypto.randomUUID()
    : Math.random().toString(36).slice(2) + Date.now().toString(36);
}

function clamp(v, mn, mx) { return Math.max(mn, Math.min(mx, v)); }
function stat(v) { return { max: v, current: v }; }

function escHtml(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function setupDoubleTap(el, callback) {
  let lastTap = 0;
  el.addEventListener('click', e => {
    const now = Date.now();
    if (now - lastTap < 350) {
      e.stopPropagation();
      callback();
      lastTap = 0;
    } else {
      lastTap = now;
    }
  });
}

function setupLongPress(el, callback) {
  let timer = null;
  el.addEventListener('pointerdown', () => {
    timer = setTimeout(() => { callback(); timer = null; }, 600);
  });
  el.addEventListener('pointerup', () => { clearTimeout(timer); });
  el.addEventListener('pointerleave', () => { clearTimeout(timer); });
}

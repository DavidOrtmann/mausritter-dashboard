let boatMode = localStorage.getItem('boatMode') === 'true';

function setBoatMode(enabled) {
  boatMode = enabled;
  localStorage.setItem('boatMode', String(enabled));
  document.body.classList.toggle('boat-mode', enabled);
  document.querySelectorAll('.boat-mode-btn').forEach(btn => {
    btn.classList.toggle('active', (btn.dataset.mode === 'on') === enabled);
  });
  if (enabled) {
    if (state.turns.boxes.length !== 11) {
      state.turns.boxes = new Array(11).fill('');
    }
    if (state.boats.length === 0) {
      initBoats();
      renderBoats();
      scheduleSave();
    }
  }
  if (!enabled) {
    const activeBtn = document.querySelector('#tab-bar button[data-tab].active');
    if (activeBtn && activeBtn.dataset.tab === 'boat') {
      showTab('turns');
    }
    state.boats = [];
    state.turns.boxes = new Array(6).fill('');
    state.turns.count = 1;
    renderBoats();
    scheduleSave();
  }
  renderTurns();
}

document.querySelectorAll('.lang-btn').forEach(btn => {
  btn.classList.toggle('active', btn.dataset.lang === currentLang);
  btn.addEventListener('click', () => {
    localStorage.setItem('lang', btn.dataset.lang);
    location.reload();
  });
});

document.getElementById('btn-force-reload').addEventListener('click', () => {
  clearTimeout(saveTimer);
  saveTimer = null;
  stateLoaded = false;
  window.location.href = window.location.pathname + '?_=' + Date.now();
});

document.querySelectorAll('.boat-mode-btn').forEach(btn => {
  btn.addEventListener('click', () => setBoatMode(btn.dataset.mode === 'on'));
});

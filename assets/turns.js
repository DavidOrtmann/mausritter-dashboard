const TURN_CYCLE = ['', '✓', 'T', 'L'];

const SVG_TORCH = `<svg viewBox="0 0 10 14" width="14" height="18" xmlns="http://www.w3.org/2000/svg"><path d="M5 13C2.5 13 1 11 1 9c0-2 1.2-3.8 2.5-5C3.5 6 4.2 7 5 7c0-1.5 1-4 3.5-6C8.5 4 9.5 6 9.5 9c0 2.5-2 4-4.5 4z" fill="currentColor"/></svg>`;
const SVG_LAMP  = `<svg viewBox="0 0 10 15" width="14" height="19" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"><path d="M5 1a4 4 0 0 1 4 4c0 1.8-1.2 3.3-2.5 4.1V11h-3V9.1C2.2 8.3 1 6.8 1 5a4 4 0 0 1 4-4z"/><line x1="3.5" y1="12.5" x2="6.5" y2="12.5"/><line x1="4" y1="14" x2="6" y2="14"/></svg>`;

function turnBoxHTML(s) {
  if (s === 'T') return SVG_TORCH;
  if (s === 'L') return SVG_LAMP;
  return escHtml(s);
}
const RACE_LABELS = ['S', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];

function renderTurns() {
  const container = document.getElementById('turn-boxes');
  container.innerHTML = '';
  container.classList.toggle('race-mode', boatMode);

  if (boatMode) {
    RACE_LABELS.forEach((label, i) => {
      const box = document.createElement('div');
      box.className = 'turn-box';
      box.dataset.index = i;
      const s = state.turns.boxes[i] || '';
      box.dataset.state = s;
      box.innerHTML = s === '✓' ? '✓' : `<span class="race-label">${label}</span>`;
      container.appendChild(box);
    });
  } else {
    for (let i = 0; i < 6; i++) {
      const box = document.createElement('div');
      box.className = 'turn-box' + (i === 2 || i === 5 ? ' encounter-box' : '');
      box.dataset.index = i;
      const s = state.turns.boxes[i] || '';
      box.dataset.state = s;
      box.innerHTML = turnBoxHTML(s);
      container.appendChild(box);
    }
    const allT = state.turns.boxes.every(b => b === 'T');
    const allL = state.turns.boxes.every(b => b === 'L');
    document.getElementById('turn-alert').classList.toggle('visible', allT || allL);
    document.getElementById('turn-count').textContent = state.turns.count;
  }

  document.querySelectorAll('.tod-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.tod === state.turns.timeOfDay);
  });
  renderTurnDrawer();
}

function renderTurnDrawer() {
  const mini = document.getElementById('turn-boxes-mini');
  mini.innerHTML = '';
  mini.classList.toggle('race-mode', boatMode);

  if (boatMode) {
    RACE_LABELS.forEach((label, i) => {
      const box = document.createElement('div');
      box.className = 'turn-box';
      box.dataset.index = i;
      const s = state.turns.boxes[i] || '';
      box.dataset.state = s;
      box.innerHTML = s === '✓' ? '✓' : `<span class="race-label">${label}</span>`;
      mini.appendChild(box);
    });
    document.getElementById('turn-panel-default-ctrls').style.display = 'none';
    document.getElementById('turn-panel-race-ctrls').style.display = '';
    const racePos = state.turns.boxes.filter(b => b === '✓').length || 'S';
    document.getElementById('turn-tab-count').textContent = racePos;
    document.getElementById('turn-count-mini').textContent = racePos;
  } else {
    for (let i = 0; i < 6; i++) {
      const box = document.createElement('div');
      box.className = 'turn-box' + (i === 2 || i === 5 ? ' encounter-box' : '');
      box.dataset.index = i;
      const s = state.turns.boxes[i] || '';
      box.dataset.state = s;
      box.innerHTML = turnBoxHTML(s);
      mini.appendChild(box);
    }
    document.getElementById('turn-panel-default-ctrls').style.display = '';
    document.getElementById('turn-panel-race-ctrls').style.display = 'none';
    document.getElementById('turn-tab-count').textContent = state.turns.count;
    document.getElementById('turn-count-mini').textContent = state.turns.count;
  }
}

document.getElementById('turn-boxes').addEventListener('click', e => {
  const box = e.target.closest('.turn-box');
  if (!box) return;
  const i = parseInt(box.dataset.index);
  if (boatMode) {
    state.turns.boxes[i] = state.turns.boxes[i] === '✓' ? '' : '✓';
  } else {
    const prev = state.turns.boxes[i];
    const next = TURN_CYCLE[(TURN_CYCLE.indexOf(prev) + 1) % TURN_CYCLE.length];
    state.turns.boxes[i] = next;
    if (prev === '' && next !== '') state.turns.count++;
    if (prev !== '' && next === '') state.turns.count = Math.max(1, state.turns.count - 1);
  }
  renderTurns();
  scheduleSave();
});

document.getElementById('btn-next-turn').addEventListener('click', () => {
  state.turns.boxes = new Array(6).fill('');
  renderTurns();
  scheduleSave();
});

document.getElementById('btn-reset-count').addEventListener('click', () => {
  state.turns.count = 1;
  state.turns.boxes = new Array(6).fill('');
  renderTurns();
  scheduleSave();
});

document.getElementById('btn-reset-race').addEventListener('click', () => {
  state.turns.boxes = new Array(11).fill('');
  renderTurns();
  scheduleSave();
});

document.getElementById('tod-grid').addEventListener('click', e => {
  const btn = e.target.closest('.tod-btn');
  if (!btn) return;
  state.turns.timeOfDay = btn.dataset.tod;
  renderTurns();
  scheduleSave();
});

document.getElementById('btn-turn-tab').addEventListener('click', () => {
  document.getElementById('turn-panel').classList.toggle('open');
});

document.getElementById('btn-goto-turns').addEventListener('click', () => {
  showTab('turns');
});

document.getElementById('turn-boxes-mini').addEventListener('click', e => {
  const box = e.target.closest('.turn-box');
  if (!box) return;
  const i = parseInt(box.dataset.index);
  if (boatMode) {
    state.turns.boxes[i] = state.turns.boxes[i] === '✓' ? '' : '✓';
  } else {
    const prev = state.turns.boxes[i];
    const next = TURN_CYCLE[(TURN_CYCLE.indexOf(prev) + 1) % TURN_CYCLE.length];
    state.turns.boxes[i] = next;
    if (prev === '' && next !== '') state.turns.count++;
    if (prev !== '' && next === '') state.turns.count = Math.max(1, state.turns.count - 1);
  }
  renderTurns();
  scheduleSave();
});

document.getElementById('btn-next-turn-mini').addEventListener('click', () => {
  state.turns.boxes = new Array(6).fill('');
  renderTurns();
  scheduleSave();
});

document.getElementById('btn-reset-count-mini').addEventListener('click', () => {
  state.turns.count = 1;
  state.turns.boxes = new Array(6).fill('');
  renderTurns();
  scheduleSave();
});

document.getElementById('btn-reset-race-mini').addEventListener('click', () => {
  state.turns.boxes = new Array(11).fill('');
  renderTurns();
  scheduleSave();
});

<?php
$supportedLangs = ['en', 'de'];
$locales = [];
foreach ($supportedLangs as $lang) {
  $file = __DIR__ . "/locales/$lang.json";
  if (file_exists($file)) $locales[$lang] = json_decode(file_get_contents($file), true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mausritter GM Tracker</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Special+Elite&family=IBM+Plex+Mono:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css?v=<?= filemtime('assets/style.css') ?>">
  <script>const LOCALES = <?= json_encode($locales) ?>;</script>
</head>
<body>

<div id="app">
  <div id="tab-content">

    <!-- ── TAB 1: TURNS ─────────────────────────────────────────────── -->
    <div id="tab-turns" class="tab-panel active">
      <h2 data-i18n="turns.heading">Turn Tracker</h2>

      <div class="turn-boxes" id="turn-boxes"></div>

      <div class="turn-alert default-mode-only" id="turn-alert" data-i18n="turns.alert">⚠ Mark light source usage!</div>

      <div class="turn-counter-row default-mode-only">
        <span class="turn-counter-label" data-i18n="turns.counter_label">Turn</span>
        <span class="turn-counter-num" id="turn-count">1</span>
        <button id="btn-next-turn" class="btn-inverted" data-i18n="turns.new_cycle">New Cycle</button>
        <button id="btn-reset-count" class="btn-sm" data-i18n="turns.reset_counter">Reset Counter</button>
      </div>

      <div class="turn-counter-row default-mode-only">
        <button id="btn-short-rest-all" class="btn-sm" data-i18n="turns.short_rest_all">Short Rest</button>
        <button id="btn-long-rest-all" class="btn-sm" data-i18n="players.long_rest_all">Long Rest All</button>
        <button id="btn-full-rest-all" class="btn-sm" data-i18n="players.full_rest_all">Full Rest All</button>
      </div>

      <div class="turn-counter-row boat-mode-only">
        <button id="btn-reset-race" class="btn-sm" data-i18n="boat.reset_race">Reset Race</button>
      </div>

      <div class="default-mode-only">
        <h3 data-i18n="turns.time_of_day">Time of Day</h3>
        <div class="tod-grid" id="tod-grid">
          <button class="tod-btn" data-tod="sunrise" data-i18n="turns.tod_sunrise">Sunrise</button>
          <button class="tod-btn" data-tod="noon" data-i18n="turns.tod_noon">Noon</button>
          <button class="tod-btn" data-tod="sunset" data-i18n="turns.tod_sunset">Sunset</button>
          <button class="tod-btn" data-tod="night-watch" data-i18n="turns.tod_night_watch">Night Watch</button>
          <button class="tod-btn" data-tod="morning-watch" data-i18n="turns.tod_morning_watch">Morning Watch</button>
          <button class="tod-btn" data-tod="afternoon-watch" data-i18n="turns.tod_afternoon_watch">Afternoon Watch</button>
          <button class="tod-btn" data-tod="evening-watch" style="grid-column: span 2;" data-i18n="turns.tod_evening_watch">Evening Watch</button>
        </div>
      </div>

      <div class="default-mode-only">
        <h3 data-i18n="turns.enc_dice_heading">Encounter Dice</h3>
        <div class="enc-ref">
          <p><strong data-i18n="turns.enc_dice_dungeon_label">Dungeon:</strong> <span data-i18n="turns.enc_dice_dungeon_text">Roll d6 every 3 turns. On a 1: encounter. On a 2: omen.</span></p>
          <p><strong data-i18n="turns.enc_dice_wilderness_label">Wilderness:</strong> <span data-i18n="turns.enc_dice_wilderness_text">Roll d6 at start of Morning Watch and Evening Watch. On a 1: encounter. On a 2: omen.</span></p>
          <p><strong data-i18n="turns.enc_dice_reaction_label">Reaction (2d6):</strong> <span data-i18n="turns.enc_dice_reaction_text">2 hostile · 3–5 unfriendly · 6–8 unsure · 9–11 talkative · 12 helpful</span></p>
          <p><strong data-i18n="turns.enc_dice_morale_label">Morale:</strong> <span data-i18n="turns.enc_dice_morale_text">WIL save when: outmatched at start · takes critical damage · sees ally flee or fall</span></p>
        </div>
      </div>
      <div class="boat-mode-only">
        <h3 data-i18n="boat.ref_heading">Race Reference</h3>
        <div class="enc-ref">
          <p><strong data-i18n="boat.mechanics_label">Mechanics</strong></p>
          <p><strong data-i18n="boat.fork_label">Fork:</strong> <span data-i18n="boat.fork_text">All choose L/R simultaneously; unanimous → Speed d12</span></p>
          <p><strong data-i18n="boat.narrows_label">Narrows:</strong> <span data-i18n="boat.narrows_text">Encounter with nearest rival — attack, board, or flee</span></p>
          <p><strong data-i18n="boat.encounter_label">Encounter:</strong> <span data-i18n="boat.encounter_text">Roll d6 on table below</span></p>
          <p><strong data-i18n="boat.enc_table_heading">d6 Encounter</strong></p>
          <p>1 · <span data-i18n="boat.enc_1">d6 Goldfish Offspring — glimmering scales, 100p each</span></p>
          <p>2 · <span data-i18n="boat.enc_2">Sunken boat — chest (1000p); retrieval → last place</span></p>
          <p>3 · <span data-i18n="boat.enc_3">d6 Spectral Frogs — d4 to pick boat attacked</span></p>
          <p>4 · <span data-i18n="boat.enc_4">Vortex — all DEX save or drop one place</span></p>
          <p>5 · <span data-i18n="boat.enc_5">Strange Tides — all STR save or drop one place</span></p>
          <p>6 · <span data-i18n="boat.enc_6">Goldfish — attacks last-place boat</span></p>
          <p><strong data-i18n="boat.track_heading">Race Track</strong></p>
          <p data-i18n="boat.space_1">1. Grandstands — Fork · L: WIL adv · R: DEX adv</p>
          <p data-i18n="boat.space_2">2. Reedy Passage — Narrows · all Speed d4</p>
          <p data-i18n="boat.space_3">3. The Rapids — Encounter</p>
          <p data-i18n="boat.space_4">4. Leap of Faith — Encounter</p>
          <p data-i18n="boat.space_5">5. Racing Pennants — Fork · L: Speed d4 · R: d4 damage</p>
          <p data-i18n="boat.space_6">6. Coral Cave — 1st boat: d4 Spectral Frogs</p>
          <p data-i18n="boat.space_7">7. Forbidden Waters — Encounter</p>
          <p data-i18n="boat.space_8">8. Rocky Ramp — Up/Down · Down: d8 per crew member</p>
          <p data-i18n="boat.space_9">9. Lilypad Field — Encounter</p>
          <p data-i18n="boat.space_10">10. Stormleaf Cliff — 🏁 Finish</p>
        </div>
      </div>

    </div>

    <!-- ── TAB 2: PLAYERS ───────────────────────────────────────────── -->
    <div id="tab-players" class="tab-panel">
      <h2 data-i18n="players.heading">Players</h2>
      <div class="bulk-row">
        <button id="btn-add-player" class="btn-inverted" data-i18n="players.add">+ Add Player</button>
        <button id="btn-collapse-all-players" class="btn-sm">Collapse All</button>
      </div>
      <div id="player-list"></div>
    </div>

    <!-- ── TAB 3: ROSTER ──────────────────────────────────────────── -->
    <div id="tab-roster" class="tab-panel">
      <h2 data-i18n="roster.heading">Roster</h2>
      <div class="row-gap">
        <button id="btn-add-roster" class="btn-inverted" data-i18n="roster.add">+ Add Enemy</button>
        <button id="btn-collapse-all-roster" class="btn-sm">Collapse All</button>
      </div>
      <div class="roster-grid" id="roster-grid"></div>
    </div>

    <!-- ── TAB 4: ENCOUNTER ─────────────────────────────────────────── -->
    <div id="tab-encounter" class="tab-panel">
      <h2 data-i18n="encounter.heading">Encounter</h2>
      <div class="row-gap">
        <button id="btn-add-combatant" class="btn-inverted" data-i18n="encounter.add_combatant">+ Add Combatant</button>
        <button id="btn-collapse-all" class="btn-sm" data-i18n="encounter.collapse_all">Collapse All</button>
        <button id="btn-clear-defeated" class="btn-sm" data-i18n="encounter.clear_defeated">Clear Defeated</button>
        <button id="btn-clear-initiative" class="btn-sm" data-i18n="encounter.clear_initiative">Clear Initiative</button>
        <button id="btn-new-encounter" class="btn-sm" data-i18n="encounter.new_encounter">New Encounter</button>
      </div>
      <div id="encounter-list"></div>
    </div>

    <!-- ── TAB 5: BOAT ────────────────────────────────────────────── -->
    <div id="tab-boat" class="tab-panel boat-mode-only">
      <h2 data-i18n="boat.heading">Boats</h2>
      <div id="boat-list"></div>
    </div>

    <!-- ── TAB 6: SETTINGS ─────────────────────────────────────────── -->
    <div id="tab-settings" class="tab-panel">
      <h2 data-i18n="settings.heading">Settings</h2>
      <div class="settings-section">
        <div class="settings-row">
          <span class="settings-label" data-i18n="settings.language_label">Language</span>
          <div class="settings-lang-btns">
            <button class="lang-btn btn-sm" data-lang="en">EN</button>
            <button class="lang-btn btn-sm" data-lang="de">DE</button>
          </div>
        </div>
        <div class="settings-row">
          <span class="settings-label" data-i18n="boat.mode_label">Boat Mode</span>
          <div class="settings-lang-btns">
            <button class="boat-mode-btn btn-sm" data-mode="off" data-i18n="boat.mode_off">Off</button>
            <button class="boat-mode-btn btn-sm" data-mode="on" data-i18n="boat.mode_on">On</button>
          </div>
        </div>

      </div>
    </div>

  </div><!-- #tab-content -->

  <!-- ── TURN PANEL ────────────────────────────────────────────────── -->
  <div id="turn-panel">
    <div class="turn-boxes" id="turn-boxes-mini"></div>
    <div id="turn-panel-default-ctrls" class="turn-counter-row">
      <span class="turn-counter-num" id="turn-count-mini">1</span>
      <button id="btn-next-turn-mini" class="btn-sm" data-i18n="turns.new_cycle">New Cycle</button>
      <button id="btn-reset-count-mini" class="btn-sm" data-i18n="turns.reset_counter">Reset Counter</button>
    </div>
    <div id="turn-panel-race-ctrls" class="turn-counter-row" style="display:none">
      <button id="btn-reset-race-mini" class="btn-sm" data-i18n="boat.reset_race">Reset Race</button>
    </div>
    <button id="btn-goto-turns" data-i18n="turns.goto_turns">→ Turns</button>
  </div>

  <!-- ── TAB BAR ──────────────────────────────────────────────────── -->
  <nav id="tab-bar">
    <button id="btn-turn-tab" class="active"><span id="turn-tab-label" data-i18n="turns.counter_label">Turn</span>&thinsp;<span id="turn-tab-count">1</span></button>
    <button data-tab="players" data-i18n="nav.players">Players</button>
    <button data-tab="roster" data-i18n="nav.roster">Roster</button>
    <button data-tab="encounter" data-i18n="nav.fights">Encounter</button>
    <button class="boat-mode-only" data-tab="boat" data-i18n="boat.tab_label">Boat</button>
    <button data-tab="settings" data-i18n="nav.settings">Settings</button>
  </nav>
</div><!-- #app -->

<!-- ── NUMPAD MODAL ─────────────────────────────────────────────────── -->
<div id="numpad-overlay">
  <div id="numpad">
    <div id="numpad-title" data-i18n="numpad.edit_max">Edit Max</div>
    <div id="numpad-display">—</div>
    <div id="numpad-keys">
      <button data-k="7">7</button>
      <button data-k="8">8</button>
      <button data-k="9">9</button>
      <button data-k="4">4</button>
      <button data-k="5">5</button>
      <button data-k="6">6</button>
      <button data-k="1">1</button>
      <button data-k="2">2</button>
      <button data-k="3">3</button>
      <button data-k="bs">⌫</button>
      <button data-k="0">0</button>
      <button data-k="ok" class="btn-inverted">✓</button>
    </div>
    <button id="numpad-cancel" data-i18n="numpad.cancel">Cancel</button>
  </div>
</div>

<!-- ── DAMAGE / HEAL POPOVER ────────────────────────────────────────── -->
<div id="popover-overlay">
  <div id="popover">
    <div class="popover-mode-row">
      <button id="popover-mode-dmg" class="popover-mode-btn active" data-i18n="popover.damage">Damage</button>
      <button id="popover-mode-heal" class="popover-mode-btn" data-i18n="popover.heal">Heal</button>
    </div>
    <h4 id="popover-title"></h4>
    <div class="popover-num-row">
      <button id="popover-dec">−</button>
      <input id="popover-val" type="number" min="0" value="1">
      <button id="popover-inc">+</button>
    </div>
    <div id="popover-armor-hint"></div>
    <div class="popover-actions">
      <button id="popover-apply" class="btn-inverted" data-i18n="popover.apply_damage">Apply Damage</button>
      <button id="popover-cancel" data-i18n="popover.cancel">Cancel</button>
    </div>
  </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════════════
   I18N
═══════════════════════════════════════════════════════════════════ */
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
}

function statLabel(key) {
  return t('stats.' + key.toLowerCase());
}

/* ═══════════════════════════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════════════════════════ */
let state = {
  turns: { count: 1, boxes: ['','','','','',''], timeOfDay: 'sunrise' },
  players: [],
  encounter: [],
  roster: [],
};

let saveTimer = null;
function scheduleSave() {
  clearTimeout(saveTimer);
  saveTimer = setTimeout(persistState, 500);
}
async function persistState() {
  try {
    await fetch('api.php?action=save', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(state),
    });
  } catch (e) { console.error('Save failed', e); }
}
window.addEventListener('beforeunload', () => {
  if (saveTimer) {
    clearTimeout(saveTimer);
    navigator.sendBeacon('api.php?action=save', JSON.stringify(state));
  }
});
async function loadState() {
  try {
    const r = await fetch('api.php?action=load');
    const data = await r.json();
    state = data;
    if (!state.roster) state.roster = [];
    if (!state.boats) state.boats = [];
    state.players.forEach(p => { if (!('paused' in p)) p.paused = false; });
    state.encounter.forEach(e => { if (!('initiative' in e)) e.initiative = null; });
  } catch (e) { console.error('Load failed', e); }
}

/* ═══════════════════════════════════════════════════════════════════
   UTILS
═══════════════════════════════════════════════════════════════════ */
function uuid() {
  return crypto.randomUUID ? crypto.randomUUID()
    : Math.random().toString(36).slice(2) + Date.now().toString(36);
}
function makePlayer(name = '') {
  return { id: uuid(), name, hp:{max:6,current:6}, str:{max:10,current:10},
           dex:{max:10,current:10}, wil:{max:10,current:10},
           injured:false, drained:false, encumbered:false, paused:false,
           level:1, xp:0, grit:0, pips:0, treasury:0, armor:0 };
}

function addPlayerToEncounter(p) {
  if (!state.encounter.find(e => e.id === p.id)) {
    state.encounter.unshift({
      id: p.id, name: p.name, type: 'pc',
      hp: {...p.hp}, str: {...p.str}, dex: {...p.dex}, wil: {...p.wil},
      injured: p.injured, drained: p.drained, encumbered: p.encumbered,
      notes: '', defeated: false, initiative: null, armor: p.armor ?? 0,
    });
  }
}
function makeCombatant(overrides = {}) {
  return { id: uuid(), name:'', type:'enemy', hp:{max:6,current:6},
           str:{max:10,current:10}, dex:{max:8,current:8}, wil:{max:6,current:6},
           injured:false, drained:false, encumbered:false, notes:'', defeated:false,
           initiative:null, armor:0, weapon:'', attackDice:'d6',
           ...overrides };
}
function clamp(v, mn, mx) { return Math.max(mn, Math.min(mx, v)); }
function stat(v) { return { max: v, current: v }; }
function allEncounterCollapsed() {
  return state.encounter.length > 0 && state.encounter.every(e => encCollapsed[e.id]);
}

/* ═══════════════════════════════════════════════════════════════════
   TABS
═══════════════════════════════════════════════════════════════════ */
function showTab(name) {
  document.querySelectorAll('#tab-bar button[data-tab]').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  const btn = document.querySelector(`#tab-bar button[data-tab="${name}"]`);
  if (btn) btn.classList.add('active');
  document.getElementById('turn-panel').classList.remove('open');
  document.getElementById('btn-turn-tab').classList.toggle('active', name === 'turns');
}

document.querySelectorAll('#tab-bar button[data-tab]').forEach(btn => {
  btn.addEventListener('click', () => showTab(btn.dataset.tab));
});

/* ═══════════════════════════════════════════════════════════════════
   TURN TRACKER
═══════════════════════════════════════════════════════════════════ */
const TURN_CYCLE = ['', '✓', 'T', 'L'];
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
      box.textContent = s;
      container.appendChild(box);
    }
    // Alert only when all 6 boxes are the same non-empty light type
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

/* ── Turn Panel ──────────────────────────────────────────────────── */
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
    document.getElementById('turn-tab-count').textContent = '';
    document.getElementById('turn-count-mini').textContent = '';
  } else {
    for (let i = 0; i < 6; i++) {
      const box = document.createElement('div');
      box.className = 'turn-box' + (i === 2 || i === 5 ? ' encounter-box' : '');
      box.dataset.index = i;
      const s = state.turns.boxes[i] || '';
      box.dataset.state = s;
      box.textContent = s;
      mini.appendChild(box);
    }
    document.getElementById('turn-panel-default-ctrls').style.display = '';
    document.getElementById('turn-panel-race-ctrls').style.display = 'none';
    document.getElementById('turn-tab-count').textContent = state.turns.count;
    document.getElementById('turn-count-mini').textContent = state.turns.count;
  }
}

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


/* ═══════════════════════════════════════════════════════════════════
   PLAYERS
═══════════════════════════════════════════════════════════════════ */
const playerCollapsed = {};  // id → bool; persists across re-renders
const encCollapsed = {};     // id → bool; persists across re-renders
const rosterCollapsed = {};  // id → bool; persists across re-renders

function buildPlayerCard(p) {
  const card = document.createElement('div');
  card.className = 'card' + (p.paused ? ' paused' : '');
  card.dataset.id = p.id;

  // Helper: flag class for stat labels
  function statFlags(stat) {
    if (stat === 'str' && p.injured)    return ' injured-flag';
    if (stat === 'dex' && p.drained)    return ' drained-flag';
    if (stat === 'wil' && p.encumbered) return ' encumb-flag';
    return '';
  }

  const statusSummary = [
    p.injured    && t('players.status_injured'),
    p.drained    && t('players.status_drained'),
    p.encumbered && t('players.status_encumbered'),
  ].filter(Boolean).map(s => `<span class="ccs-status">${s}</span>`).join('');

  card.innerHTML = `
    <div class="card-header">
      <input class="card-name-input" type="text" placeholder="${escHtml(t('players.name_placeholder'))}" value="${escHtml(p.name)}">
      <button class="card-collapse-btn btn-sm" title="${escHtml(t('players.tip_collapse'))}">▲</button>
    </div>
    <div class="card-collapsed-summary" style="display:none">
      <div class="ccs-stat-grid">
        <div class="ccs-stat"><span class="ccs-label">${statLabel('hp')}</span><span class="ccs-val ccs-tappable" data-popstat="hp">${p.hp.current}/${p.hp.max}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('str')}</span><span class="ccs-val ccs-tappable" data-popstat="str">${p.str.current}/${p.str.max}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('dex')}</span><span class="ccs-val ccs-tappable" data-popstat="dex">${p.dex.current}/${p.dex.max}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('wil')}</span><span class="ccs-val ccs-tappable" data-popstat="wil">${p.wil.current}/${p.wil.max}</span></div>
      </div>
      ${statusSummary}
    </div>
    <div class="card-body">
      <div class="stat-grid">
        <div class="stat-block stat-hp">
          <div class="stat-label">${statLabel('hp')}</div>
          <div class="stat-vals">
            <span class="stat-current" data-stat="hp" title="${escHtml(t('players.tip_tap_damage'))}">${p.hp.current}</span>
            <span class="stat-sep">/</span>
            <span class="stat-max" data-stat="hp" title="${escHtml(t('players.tip_dbl_tap_max'))}">${p.hp.max}</span>
          </div>
        </div>
        <div class="stat-block">
          <div class="stat-label${statFlags('str')}">${statLabel('str')}</div>
          <div class="stat-vals">
            <span class="stat-current" data-stat="str">${p.str.current}</span>
            <span class="stat-sep">/</span>
            <span class="stat-max" data-stat="str" title="${escHtml(t('players.tip_dbl_tap_max'))}">${p.str.max}</span>
          </div>
        </div>
        <div class="stat-block">
          <div class="stat-label${statFlags('dex')}">${statLabel('dex')}</div>
          <div class="stat-vals">
            <span class="stat-current" data-stat="dex">${p.dex.current}</span>
            <span class="stat-sep">/</span>
            <span class="stat-max" data-stat="dex" title="${escHtml(t('players.tip_dbl_tap_max'))}">${p.dex.max}</span>
          </div>
        </div>
        <div class="stat-block">
          <div class="stat-label${statFlags('wil')}">${statLabel('wil')}</div>
          <div class="stat-vals">
            <span class="stat-current" data-stat="wil">${p.wil.current}</span>
            <span class="stat-sep">/</span>
            <span class="stat-max" data-stat="wil" title="${escHtml(t('players.tip_dbl_tap_max'))}">${p.wil.max}</span>
          </div>
        </div>
      </div>
      <div class="status-row">
        <button class="status-btn${p.injured?    ' active':''}" data-status="injured">${t('players.status_injured')}</button>
        <button class="status-btn${p.drained?    ' active':''}" data-status="drained">${t('players.status_drained')}</button>
        <button class="status-btn${p.encumbered? ' active':''}" data-status="encumbered">${t('players.status_encumbered')}</button>
      </div>
      <div class="player-meta-row">
        <span class="meta-stat" data-meta="armor"><span class="meta-lbl">${t('players.armor')}</span><span class="meta-val">${p.armor ?? 0}</span></span>
        <span class="meta-stat" data-meta="level"><span class="meta-lbl">${t('players.level')}</span><span class="meta-val">${p.level ?? 1}</span></span>
        <span class="meta-stat" data-meta="xp"><span class="meta-lbl">${t('players.xp')}</span><span class="meta-val">${p.xp ?? 0}</span></span>
        <span class="meta-stat" data-meta="grit"><span class="meta-lbl">${t('players.grit')}</span><span class="meta-val">${p.grit ?? 0}</span></span>
        <span class="meta-stat" data-meta="pips"><span class="meta-lbl">${t('players.pips')}</span><span class="meta-val">${p.pips ?? 0}</span></span>
        <span class="meta-stat" data-meta="treasury"><span class="meta-lbl">${t('players.treasury')}</span><span class="meta-val">${p.treasury ?? 0}</span></span>
      </div>
      <div class="card-actions">
        <button class="btn-sm rest-long">${t('players.long_rest')}</button>
        <button class="btn-sm rest-full">${t('players.full_rest')}</button>
        <button class="btn-sm player-pause-btn">${p.paused ? t('players.resume') : t('players.pause')}</button>
        <button class="btn-sm card-delete-btn">${t('players.delete')}</button>
      </div>
      <button class="undo-btn" style="display:none">↩ Undo</button>
    </div>`;

  // Name
  card.querySelector('.card-name-input').addEventListener('input', e => {
    p.name = e.target.value;
    syncEncounterPC(p);
    scheduleSave();
  });

  // Collapse — default true; state persists across re-renders
  if (!(p.id in playerCollapsed)) playerCollapsed[p.id] = true;
  let collapsed = playerCollapsed[p.id];
  const colBtn = card.querySelector('.card-collapse-btn');
  const body = card.querySelector('.card-body');
  const summary = card.querySelector('.card-collapsed-summary');
  const applyCollapse = () => {
    body.classList.toggle('collapsed', collapsed);
    summary.style.display = collapsed ? 'block' : 'none';
    card.classList.toggle('collapsed-card', collapsed);
    colBtn.textContent = collapsed ? '▼' : '▲';
  };
  applyCollapse();
  colBtn.addEventListener('click', () => {
    collapsed = !collapsed;
    playerCollapsed[p.id] = collapsed;
    applyCollapse();
  });

  // Tappable stats in collapsed summary → damage/heal popover
  card.querySelectorAll('.card-collapsed-summary .ccs-tappable').forEach(el => {
    el.addEventListener('click', () => {
      openPopover({ mode: 'damage', statKey: el.dataset.popstat, sourceId: p.id, source: 'player' });
    });
  });

  // Stat current: open damage popover
  card.querySelectorAll('.stat-current').forEach(el => {
    el.addEventListener('click', () => {
      openPopover({ mode: 'damage', statKey: el.dataset.stat, sourceId: p.id, source: 'player' });
    });
    // Long press = decrement by 1
    setupLongPress(el, () => {
      applyDamageToPlayer(p, el.dataset.stat, 1);
      renderPlayers();
      renderEncounter();
      scheduleSave();
    });
  });

  // Stat max: double-tap opens numpad to edit max
  card.querySelectorAll('.stat-max').forEach(el => {
    setupDoubleTap(el, () => {
      const sk = el.dataset.stat;
      openNumpad(t('numpad.max_stat', { stat: statLabel(sk) }), p[sk].max, v => {
        p[sk].max = v;
        p[sk].current = v;
        syncEncounterPC(p);
        renderPlayers();
        scheduleSave();
      });
    });
  });

  // Status toggles
  card.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const s = btn.dataset.status;
      p[s] = !p[s];
      renderPlayers();
      scheduleSave();
    });
  });

  // Meta stats (level, xp, grit, pips, treasury)
  card.querySelectorAll('.meta-stat').forEach(el => {
    el.addEventListener('click', () => {
      const key = el.dataset.meta;
      const label = t('players.' + key);
      const defaults = { level: 1 };
      const wideKeys = ['xp', 'pips', 'treasury'];
      openNumpad(label, p[key] ?? (defaults[key] || 0), v => {
        p[key] = v;
        if (key === 'armor') syncEncounterPC(p);
        renderPlayers();
        scheduleSave();
      }, 0, wideKeys.includes(key) ? 5 : 2);
    });
  });

  // Rest buttons
  card.querySelector('.rest-long').addEventListener('click', () => {
    longRest(p);
    syncEncounterPC(p);
    renderPlayers();
    renderEncounter();
    scheduleSave();
  });
  card.querySelector('.rest-full').addEventListener('click', () => {
    fullRest(p);
    syncEncounterPC(p);
    renderPlayers();
    renderEncounter();
    scheduleSave();
  });

  // Pause / Resume
  card.querySelector('.player-pause-btn').addEventListener('click', () => {
    p.paused = !p.paused;
    if (p.paused) {
      state.encounter = state.encounter.filter(e => e.id !== p.id);
    } else {
      addPlayerToEncounter(p);
    }
    renderPlayers();
    renderEncounter();
    scheduleSave();
  });

  // Delete
  card.querySelector('.card-delete-btn').addEventListener('click', () => {
    if (!confirm(t('players.confirm_delete', { name: p.name || t('players.this_player') }))) return;
    state.players = state.players.filter(x => x.id !== p.id);
    state.encounter = state.encounter.filter(x => x.id !== p.id);
    renderPlayers();
    renderEncounter();
    scheduleSave();
  });

  return card;
}

// Long rest (1 Watch): restore all HP
function longRest(p) {
  p.hp.current = p.hp.max;
}

// Full rest (1 week): restore all stats + clear conditions
function fullRest(p) {
  p.hp.current  = p.hp.max;
  p.str.current = p.str.max;
  p.dex.current = p.dex.max;
  p.wil.current = p.wil.max;
  p.injured = false;
  p.drained = false;
  p.encumbered = false;
}

function applyDamageToPlayer(p, stat, amount) {
  if (stat === 'hp') {
    const effective = Math.max(0, amount - (p.armor ?? 0));
    const overflow = effective - p.hp.current;
    p.hp.current = clamp(p.hp.current - effective, 0, p.hp.max);
    if (overflow > 0) {
      p.str.current = clamp(p.str.current - overflow, 0, p.str.max);
    }
  } else {
    p[stat].current = clamp(p[stat].current - amount, 0, p[stat].max);
  }
  syncEncounterPC(p);
}

function applyHealToPlayer(p, stat, amount) {
  p[stat].current = clamp(p[stat].current + amount, 0, p[stat].max);
  syncEncounterPC(p);
}

function applyHealToEncounter(ec, stat, amount) {
  ec[stat].current = clamp(ec[stat].current + amount, 0, ec[stat].max);
  // Un-defeat if STR is restored above 0
  if (stat === 'str' && ec[stat].current > 0) ec.defeated = false;
  if (ec.type === 'pc') writeBackToPlayer(ec);
}

function syncEncounterPC(p) {
  const ec = state.encounter.find(e => e.id === p.id);
  if (!ec) return;
  ec.name = p.name;
  ec.hp = { ...p.hp };
  ec.str = { ...p.str };
  ec.dex = { ...p.dex };
  ec.wil = { ...p.wil };
  ec.injured = p.injured;
  ec.drained = p.drained;
  ec.encumbered = p.encumbered;
  ec.armor = p.armor ?? 0;
}

function renderPlayers() {
  const list = document.getElementById('player-list');
  list.innerHTML = '';
  state.players.forEach(p => list.appendChild(buildPlayerCard(p)));
  updatePlayerCollapseAllBtn();
}

document.getElementById('btn-add-player').addEventListener('click', () => {
  const p = makePlayer();
  state.players.push(p);
  addPlayerToEncounter(p);
  renderPlayers();
  renderEncounter();
  scheduleSave();
});

document.getElementById('btn-short-rest-all').addEventListener('click', () => {
  openNumpad(t('turns.short_rest_all'), 1, v => {
    state.players.forEach(p => {
      p.hp.current = Math.min(p.hp.current + v, p.hp.max);
      syncEncounterPC(p);
    });
    renderPlayers();
    renderEncounter();
    scheduleSave();
  }, 0);
});

document.getElementById('btn-long-rest-all').addEventListener('click', () => {
  state.players.forEach(p => { longRest(p); syncEncounterPC(p); });
  renderPlayers();
  renderEncounter();
  scheduleSave();
});

document.getElementById('btn-full-rest-all').addEventListener('click', () => {
  state.players.forEach(p => { fullRest(p); syncEncounterPC(p); });
  renderPlayers();
  renderEncounter();
  scheduleSave();
});

function allPlayersCollapsed() {
  const active = state.players.filter(p => !p.paused);
  return active.length > 0 && active.every(p => playerCollapsed[p.id]);
}
function updatePlayerCollapseAllBtn() {
  const btn = document.getElementById('btn-collapse-all-players');
  if (btn) btn.textContent = allPlayersCollapsed() ? t('encounter.expand_all') : t('encounter.collapse_all');
}
document.getElementById('btn-collapse-all-players').addEventListener('click', () => {
  const newCollapsed = !allPlayersCollapsed();
  state.players.filter(p => !p.paused).forEach(p => {
    playerCollapsed[p.id] = newCollapsed;
    const card = document.querySelector(`#player-list [data-id="${p.id}"]`);
    if (!card) return;
    card.querySelector('.card-body').classList.toggle('collapsed', newCollapsed);
    card.querySelector('.card-collapsed-summary').style.display = newCollapsed ? 'block' : 'none';
    card.classList.toggle('collapsed-card', newCollapsed);
    card.querySelector('.card-collapse-btn').textContent = newCollapsed ? '▼' : '▲';
  });
  updatePlayerCollapseAllBtn();
});

/* ═══════════════════════════════════════════════════════════════════
   ENCOUNTER
═══════════════════════════════════════════════════════════════════ */

function buildPipBar(stat, current, max, onTap, onMaxChange) {
  const wrap = document.createElement('div');
  wrap.className = 'pip-row';

  const label = document.createElement('span');
  label.className = 'pip-label';
  label.textContent = statLabel(stat);
  wrap.appendChild(label);

  if (max > 12) {
    // Progress bar
    const pbw = document.createElement('div');
    pbw.className = 'prog-bar-wrap';
    const pbf = document.createElement('div');
    pbf.className = 'prog-bar-fill';
    pbf.style.width = max > 0 ? (current / max * 100) + '%' : '0%';
    const pbl = document.createElement('div');
    pbl.className = 'prog-bar-label';
    pbl.textContent = `${current}/${max}`;
    pbw.appendChild(pbf);
    pbw.appendChild(pbl);
    pbw.addEventListener('click', onTap);
    wrap.appendChild(pbw);
  } else {
    const bar = document.createElement('div');
    bar.className = 'pip-bar';
    for (let i = 0; i < max; i++) {
      const pip = document.createElement('div');
      pip.className = 'pip' + (i < current ? ' filled' : '');
      bar.appendChild(pip);
    }
    bar.addEventListener('click', onTap);
    wrap.appendChild(bar);
  }

  // current (tappable) / max (double-tap to edit)
  const numsWrap = document.createElement('span');
  numsWrap.className = 'pip-nums';

  const curSpan = document.createElement('span');
  curSpan.textContent = `${current} / `;
  curSpan.addEventListener('click', onTap);
  numsWrap.appendChild(curSpan);

  const maxSpan = document.createElement('span');
  maxSpan.className = 'pip-max';
  maxSpan.textContent = max;
  maxSpan.title = t('encounter.tip_dbl_tap_max', { stat: statLabel(stat) });
  if (onMaxChange) {
    setupDoubleTap(maxSpan, () => {
      openNumpad(t('numpad.max_stat', { stat: statLabel(stat) }), max, onMaxChange);
    });
  }
  numsWrap.appendChild(maxSpan);
  wrap.appendChild(numsWrap);

  return wrap;
}

function buildEncCard(ec) {
  const card = document.createElement('div');
  card.className = 'enc-card' + (ec.defeated ? ' defeated collapsed-card' : '');
  card.dataset.type = ec.type;
  card.dataset.id = ec.id;

  const isPC = ec.type === 'pc';

  const statusSummary = [
    ec.injured    && t('encounter.status_injured'),
    ec.drained    && t('encounter.status_drained'),
    ec.encumbered && t('encounter.status_encumbered'),
  ].filter(Boolean).map(s => `<span class="ccs-status">${s}</span>`).join('');

  const initSet = ec.initiative !== null && ec.initiative !== undefined;
  card.innerHTML = `
    <div class="enc-card-header">
      <span class="type-badge${initSet ? ' has-initiative' : ''}" title="${escHtml(t('encounter.tip_initiative'))}">${initSet ? ec.initiative : t('encounter.type_' + ec.type)}</span>
      ${ec.defeated
        ? `<span class="defeated-label">${escHtml(ec.name)}</span>`
        : `<input class="card-name-input" type="text" placeholder="${escHtml(t('encounter.name_placeholder'))}" value="${escHtml(ec.name)}">`}
      ${!ec.defeated ? `<button class="card-collapse-btn btn-sm" title="${escHtml(t('players.tip_collapse'))}">▲</button>` : ''}
      <button class="enc-defeat-btn btn-sm">${ec.defeated ? t('encounter.revive') : t('encounter.defeat')}</button>
    </div>
    <div class="enc-collapsed-summary" style="display:none">
      <div class="ccs-stat-grid">
        <div class="ccs-stat"><span class="ccs-label">${statLabel('hp')}</span><span class="ccs-val ccs-tappable" data-popstat="hp">${ec.hp.current}/${ec.hp.max}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('str')}</span><span class="ccs-val ccs-tappable" data-popstat="str">${ec.str.current}/${ec.str.max}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('dex')}</span><span class="ccs-val ccs-tappable" data-popstat="dex">${ec.dex.current}/${ec.dex.max}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('wil')}</span><span class="ccs-val ccs-tappable" data-popstat="wil">${ec.wil.current}/${ec.wil.max}</span></div>
      </div>
      ${!isPC && ec.weapon ? `<div class="enc-weapon-line"><span class="enc-weapon-name">${escHtml(ec.weapon)}</span> <span class="enc-weapon-dice">${t('roster.dice_prefix')}${(ec.attackDice||'d6').slice(1)}</span></div>` : ''}
      ${statusSummary}
    </div>
    <div class="enc-card-body${ec.defeated ? ' collapsed' : ''}">
      <div class="pip-rows-container"></div>
      <div class="compact-stat-row">
        <span class="compact-stat">
          <span class="csl">${statLabel('dex')}</span>
          <span class="csv" data-cstat="dex">${ec.dex.current}</span>
          <span>/ </span><span class="csm" data-cstat="dex" title="${escHtml(t('encounter.tip_dbl_tap_max', { stat: statLabel('dex') }))}">${ec.dex.max}</span>
        </span>
        <span class="compact-stat">
          <span class="csl">${statLabel('wil')}</span>
          <span class="csv" data-cstat="wil">${ec.wil.current}</span>
          <span>/ </span><span class="csm" data-cstat="wil" title="${escHtml(t('encounter.tip_dbl_tap_max', { stat: statLabel('wil') }))}">${ec.wil.max}</span>
        </span>
        <span class="compact-stat enc-armor-stat">
          <span class="csl">${t('stats.armor')}</span>
          <span class="csv-armor">${ec.armor ?? 0}</span>
        </span>
      </div>
      ${!isPC && ec.weapon ? `<div class="enc-weapon-line"><span class="enc-weapon-name">${escHtml(ec.weapon)}</span> <span class="enc-weapon-dice">${t('roster.dice_prefix')}${(ec.attackDice||'d6').slice(1)}</span></div>` : ''}
      <div class="status-row">
        <button class="status-btn${ec.injured?    ' active':''}" data-status="injured">${t('encounter.status_injured')}</button>
        <button class="status-btn${ec.drained?    ' active':''}" data-status="drained">${t('encounter.status_drained')}</button>
        <button class="status-btn${ec.encumbered? ' active':''}" data-status="encumbered">${t('encounter.status_encumbered')}</button>
      </div>
      <input class="enc-notes-input" type="text" placeholder="${escHtml(t('encounter.notes_placeholder'))}" value="${escHtml(ec.notes)}">
      <button class="undo-btn">↩ Undo</button>
    </div>`;

  // Pip bars
  const pipContainer = card.querySelector('.pip-rows-container');
  pipContainer.appendChild(buildPipBar('HP', ec.hp.current, ec.hp.max,
    () => { openPopover({ mode: 'damage', statKey: 'hp', sourceId: ec.id, source: 'encounter' }); },
    (v) => { ec.hp.max = v; ec.hp.current = v; if (isPC) writeBackToPlayer(ec); renderEncounter(); scheduleSave(); }
  ));
  pipContainer.appendChild(buildPipBar('STR', ec.str.current, ec.str.max,
    () => { openPopover({ mode: 'damage', statKey: 'str', sourceId: ec.id, source: 'encounter' }); },
    (v) => { ec.str.max = v; ec.str.current = v; if (isPC) writeBackToPlayer(ec); renderEncounter(); scheduleSave(); }
  ));

  // Compact stat clicks (DEX, WIL)
  card.querySelectorAll('.csv').forEach(el => {
    el.addEventListener('click', () => {
      openPopover({ mode: 'damage', statKey: el.dataset.cstat, sourceId: ec.id, source: 'encounter' });
    });
    setupLongPress(el, () => {
      applyDamageToEncounter(ec, el.dataset.cstat, 1);
      renderEncounter();
      scheduleSave();
    });
  });
  card.querySelectorAll('.csm').forEach(el => {
    setupDoubleTap(el, () => {
      const sk = el.dataset.cstat;
      openNumpad(t('numpad.max_stat', { stat: statLabel(sk) }), ec[sk].max, v => {
        ec[sk].max = v;
        ec[sk].current = v;
        if (isPC) writeBackToPlayer(ec);
        renderEncounter();
        scheduleSave();
      });
    });
  });
  // Armor — tap to edit
  card.querySelector('.csv-armor').addEventListener('click', () => {
    openNumpad(t('players.armor'), ec.armor ?? 0, v => {
      ec.armor = v;
      if (isPC) writeBackToPlayer(ec);
      renderEncounter();
      scheduleSave();
    }, 0);
  });

  // Initiative — tap type badge to enter roll
  card.querySelector('.type-badge').addEventListener('click', () => {
    openNumpad(t('numpad.initiative', { name: ec.name || ec.type.toUpperCase() }), 0, v => {
      ec.initiative = v;
      renderEncounter();
      scheduleSave();
    }, 0);
  });

  // Name
  const nameInput = card.querySelector('.card-name-input');
  if (nameInput) {
    nameInput.addEventListener('input', e => {
      ec.name = e.target.value;
      if (isPC) writeBackToPlayer(ec);
      scheduleSave();
    });
  }

  // Collapse (persistent across re-renders)
  const colBtn = card.querySelector('.card-collapse-btn');
  const summary = card.querySelector('.enc-collapsed-summary');
  if (colBtn) {
    if (!(ec.id in encCollapsed)) encCollapsed[ec.id] = false;
    let collapsed = encCollapsed[ec.id];
    const body = card.querySelector('.enc-card-body');
    const applyCollapse = () => {
      body.classList.toggle('collapsed', collapsed);
      summary.style.display = collapsed ? 'block' : 'none';
      card.classList.toggle('collapsed-card', collapsed);
      colBtn.textContent = collapsed ? '▼' : '▲';
    };
    applyCollapse();
    colBtn.addEventListener('click', () => {
      collapsed = !collapsed;
      encCollapsed[ec.id] = collapsed;
      applyCollapse();
      updateCollapseAllBtn();
    });
  }

  // Tappable stats in collapsed summary → damage/heal popover
  card.querySelectorAll('.ccs-tappable').forEach(el => {
    el.addEventListener('click', () => {
      openPopover({ mode: 'damage', statKey: el.dataset.popstat, sourceId: ec.id, source: 'encounter' });
    });
  });

  // Defeat toggle
  card.querySelector('.enc-defeat-btn').addEventListener('click', () => {
    ec.defeated = !ec.defeated;
    renderEncounter();
    scheduleSave();
  });

  // Status toggles
  card.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const s = btn.dataset.status;
      ec[s] = !ec[s];
      if (isPC) writeBackToPlayer(ec);
      renderEncounter();
      scheduleSave();
    });
  });

  // Notes
  card.querySelector('.enc-notes-input').addEventListener('input', e => {
    ec.notes = e.target.value;
    scheduleSave();
  });

  return card;
}

function applyDamageToEncounter(ec, stat, amount) {
  const isPC = ec.type === 'pc';
  if (stat === 'hp') {
    const effective = Math.max(0, amount - (ec.armor ?? 0));
    const overflow = effective - ec.hp.current;
    ec.hp.current = clamp(ec.hp.current - effective, 0, ec.hp.max);
    if (overflow > 0) {
      ec.str.current = clamp(ec.str.current - overflow, 0, ec.str.max);
    }
  } else {
    ec[stat].current = clamp(ec[stat].current - amount, 0, ec[stat].max);
  }
  if (ec.str.current === 0) ec.defeated = true;
  if (isPC) writeBackToPlayer(ec);
}

function writeBackToPlayer(ec) {
  const p = state.players.find(p => p.id === ec.id);
  if (!p) return;
  p.hp = { ...ec.hp };
  p.str = { ...ec.str };
  p.dex = { ...ec.dex };
  p.wil = { ...ec.wil };
  p.injured = ec.injured;
  p.drained = ec.drained;
  p.encumbered = ec.encumbered;
  p.armor = ec.armor ?? 0;
}

function initiativeScore(ec) {
  const init = ec.initiative;
  if (init === null || init === undefined) return 1000;  // unset → bottom
  if (init === 0) return -1;                              // surprise → top
  if (init <= ec.dex.current) return init;               // success: lower roll = earlier
  return 100 + init;                                     // failure: after all successes
}

function updateCollapseAllBtn() {
  const btn = document.getElementById('btn-collapse-all');
  if (!btn) return;
  btn.textContent = allEncounterCollapsed() ? t('encounter.expand_all') : t('encounter.collapse_all');
}

function renderEncounter() {
  const list = document.getElementById('encounter-list');
  list.innerHTML = '';
  const anyInit = state.encounter.some(e => e.initiative !== null && e.initiative !== undefined);
  const sorted = anyInit
    ? [...state.encounter].sort((a, b) => initiativeScore(a) - initiativeScore(b))
    : state.encounter;
  sorted.forEach(ec => list.appendChild(buildEncCard(ec)));
  updateCollapseAllBtn();
}


document.getElementById('btn-collapse-all').addEventListener('click', () => {
  const newCollapsed = !allEncounterCollapsed();
  state.encounter.forEach(e => {
    encCollapsed[e.id] = newCollapsed;
    const card = document.querySelector(`#encounter-list [data-id="${e.id}"]`);
    if (!card) return;
    const body = card.querySelector('.enc-card-body');
    const summary = card.querySelector('.enc-collapsed-summary');
    const colBtn = card.querySelector('.card-collapse-btn');
    if (body) body.classList.toggle('collapsed', newCollapsed);
    if (summary) summary.style.display = newCollapsed ? 'block' : 'none';
    card.classList.toggle('collapsed-card', newCollapsed);
    if (colBtn) colBtn.textContent = newCollapsed ? '▼' : '▲';
  });
  updateCollapseAllBtn();
});

document.getElementById('btn-add-combatant').addEventListener('click', () => {
  state.encounter.push(makeCombatant());
  renderEncounter();
  scheduleSave();
});

document.getElementById('btn-clear-defeated').addEventListener('click', () => {
  state.encounter = state.encounter.filter(e => !e.defeated || e.type === 'pc');
  renderEncounter();
  scheduleSave();
});

document.getElementById('btn-clear-initiative').addEventListener('click', () => {
  state.encounter.forEach(e => { e.initiative = null; });
  renderEncounter();
  scheduleSave();
});

document.getElementById('btn-new-encounter').addEventListener('click', () => {
  if (!confirm(t('encounter.confirm_new'))) return;
  state.encounter = state.encounter.filter(e => e.type === 'pc');
  renderEncounter();
  scheduleSave();
});

/* ═══════════════════════════════════════════════════════════════════
   DAMAGE POPOVER
═══════════════════════════════════════════════════════════════════ */
let popoverCtx = null;
let popoverMode = 'damage'; // 'damage' | 'heal'
let undoStack = {};  // id -> snapshot

function updatePopoverTitle() {
  const sk = popoverCtx ? statLabel(popoverCtx.statKey) : '';
  document.getElementById('popover-title').textContent =
    popoverMode === 'damage' ? t('popover.damage_title', { stat: sk }) : t('popover.heal_title', { stat: sk });
  document.getElementById('popover-apply').textContent =
    popoverMode === 'damage' ? t('popover.apply_damage') : t('popover.apply_heal');
  document.getElementById('popover-mode-dmg').classList.toggle('active', popoverMode === 'damage');
  document.getElementById('popover-mode-heal').classList.toggle('active', popoverMode === 'heal');
  updateArmorHint();
}

function getPopoverArmor() {
  if (!popoverCtx || popoverCtx.statKey !== 'hp' || popoverMode !== 'damage') return 0;
  const { source, sourceId } = popoverCtx;
  if (source === 'player') {
    const p = state.players.find(x => x.id === sourceId);
    return p ? (p.armor ?? 0) : 0;
  }
  if (source === 'encounter') {
    const ec = state.encounter.find(x => x.id === sourceId);
    return ec ? (ec.armor ?? 0) : 0;
  }
  return 0;
}

function updateArmorHint() {
  const hint = document.getElementById('popover-armor-hint');
  if (!hint) return;
  const armor = getPopoverArmor();
  if (armor <= 0) { hint.style.display = 'none'; return; }
  const raw = parseInt(document.getElementById('popover-val').value) || 0;
  const effective = Math.max(0, raw - armor);
  hint.textContent = t('popover.armor_hint', { taken: effective, armor });
  hint.style.display = 'block';
}

function openPopover(ctx) {
  popoverCtx = ctx;
  popoverMode = 'damage';
  document.getElementById('popover-val').value = 1;
  updatePopoverTitle();
  document.getElementById('popover-overlay').classList.add('open');
}

document.getElementById('popover-mode-dmg').addEventListener('click', () => {
  popoverMode = 'damage'; updatePopoverTitle();
});
document.getElementById('popover-mode-heal').addEventListener('click', () => {
  popoverMode = 'heal'; updatePopoverTitle();
});

document.getElementById('popover-overlay').addEventListener('click', e => {
  if (e.target === document.getElementById('popover-overlay')) closePopover();
});
document.getElementById('popover-cancel').addEventListener('click', closePopover);

function closePopover() {
  document.getElementById('popover-overlay').classList.remove('open');
  popoverCtx = null;
}

document.getElementById('popover-dec').addEventListener('click', () => {
  const v = parseInt(document.getElementById('popover-val').value) || 1;
  document.getElementById('popover-val').value = Math.max(0, v - 1);
  updateArmorHint();
});
document.getElementById('popover-inc').addEventListener('click', () => {
  const v = parseInt(document.getElementById('popover-val').value) || 0;
  document.getElementById('popover-val').value = v + 1;
  updateArmorHint();
});
document.getElementById('popover-val').addEventListener('input', updateArmorHint);

document.getElementById('popover-apply').addEventListener('click', () => {
  if (!popoverCtx) return;
  const amount = parseInt(document.getElementById('popover-val').value) || 0;
  const { source, sourceId, statKey } = popoverCtx;

  if (source === 'boat') {
    const b = state.boats.find(x => x.id === sourceId);
    if (b) {
      if (popoverMode === 'damage') applyDamageToBoat(b, statKey, amount);
      else applyHealToBoat(b, statKey, amount);
      renderBoats();
    }
  } else if (source === 'player') {
    const p = state.players.find(x => x.id === sourceId);
    if (p) {
      saveUndo(sourceId, 'player', p);
      if (popoverMode === 'damage') applyDamageToPlayer(p, statKey, amount);
      else applyHealToPlayer(p, statKey, amount);
      renderPlayers();
      renderEncounter();
      showUndo(sourceId, 'player');
    }
  } else {
    const ec = state.encounter.find(x => x.id === sourceId);
    if (ec) {
      saveUndo(sourceId, 'encounter', ec);
      if (popoverMode === 'damage') applyDamageToEncounter(ec, statKey, amount);
      else applyHealToEncounter(ec, statKey, amount);
      renderEncounter();
      if (ec.type === 'pc') renderPlayers();
      showUndo(sourceId, 'encounter');
    }
  }
  scheduleSave();
  closePopover();
});

function saveUndo(id, source, entity) {
  undoStack[id] = { source, snapshot: JSON.parse(JSON.stringify(entity)) };
}

function showUndo(id, source) {
  // Find the card and surface undo button
  const list = source === 'player' ? document.getElementById('player-list') : document.getElementById('encounter-list');
  const card = list.querySelector(`[data-id="${id}"]`);
  if (!card) return;
  const btn = card.querySelector('.undo-btn');
  if (!btn) return;
  btn.style.display = 'inline-flex';
  btn.classList.add('visible');
  const timer = setTimeout(() => { btn.style.display = 'none'; }, 5000);
  btn.onclick = () => {
    clearTimeout(timer);
    doUndo(id, source);
  };
}

function doUndo(id, source) {
  if (!undoStack[id]) return;
  const { snapshot } = undoStack[id];
  if (source === 'player') {
    const idx = state.players.findIndex(x => x.id === id);
    if (idx !== -1) {
      state.players[idx] = snapshot;
      syncEncounterPC(snapshot);
    }
  } else {
    const idx = state.encounter.findIndex(x => x.id === id);
    if (idx !== -1) {
      state.encounter[idx] = snapshot;
      if (snapshot.type === 'pc') writeBackToPlayer(snapshot);
    }
  }
  delete undoStack[id];
  renderPlayers();
  renderEncounter();
  scheduleSave();
}

/* ═══════════════════════════════════════════════════════════════════
   NUMPAD MODAL
═══════════════════════════════════════════════════════════════════ */
let numpadCallback = null;
let numpadValue = '';
let numpadMin = 1;
let numpadMaxDigits = 2;

function openNumpad(label, current, callback, min = 1, maxDigits = 2) {
  numpadCallback = callback;
  numpadMin = min;
  numpadMaxDigits = maxDigits;
  numpadValue = '';
  document.getElementById('numpad-title').textContent = label;
  document.getElementById('numpad-display').textContent = '—';
  document.getElementById('numpad-overlay').classList.add('open');
}

function closeNumpad() {
  document.getElementById('numpad-overlay').classList.remove('open');
  numpadCallback = null;
  numpadValue = '';
}

document.getElementById('numpad-keys').addEventListener('click', e => {
  const btn = e.target.closest('button[data-k]');
  if (!btn) return;
  const k = btn.dataset.k;
  if (k === 'bs') {
    numpadValue = numpadValue.slice(0, -1) || '';
  } else if (k === 'ok') {
    const raw = parseInt(numpadValue);
    const v = Math.max(numpadMin, isNaN(raw) ? numpadMin : raw);
    if (numpadCallback) numpadCallback(v);
    closeNumpad();
    return;
  } else {
    if (numpadValue.length < numpadMaxDigits) numpadValue += k;
  }
  document.getElementById('numpad-display').textContent = numpadValue || '—';
});

document.getElementById('numpad-cancel').addEventListener('click', closeNumpad);
document.getElementById('numpad-overlay').addEventListener('click', e => {
  if (e.target === document.getElementById('numpad-overlay')) closeNumpad();
});

/* ═══════════════════════════════════════════════════════════════════
   ROSTER
═══════════════════════════════════════════════════════════════════ */
function makeRosterEnemy() {
  return { id: uuid(), name: '', hp: 6, str: 10, dex: 8, wil: 6, armor: 0, weapon: '', attackDice: 'd6', notes: '' };
}

// Returns a unique name for the new combatant, renumbering any existing
// exact-name match in the encounter (e.g. Spinne → Spinne 1 / Spinne 2).
function resolveEncounterName(baseName) {
  if (!baseName || !state.encounter.some(e => e.name === baseName)) return baseName;
  const taken = new Set(state.encounter.map(e => e.name));
  let n = 1;
  while (taken.has(baseName + ' ' + n)) n++;
  state.encounter.filter(e => e.name === baseName)
    .forEach(e => { e.name = baseName + ' ' + n; taken.add(e.name); n++; });
  while (taken.has(baseName + ' ' + n)) n++;
  return baseName + ' ' + n;
}

function buildRosterCard(re) {
  const card = document.createElement('div');
  card.className = 'roster-card';
  card.dataset.id = re.id;

  const dice = re.attackDice || 'd6';
  const dp = t('roster.dice_prefix');
  const diceSelectHtml = [4,6,8,10,12].map(n =>
    `<option value="d${n}"${dice==='d'+n?' selected':''}>${dp}${n}</option>`
  ).join('');

  card.innerHTML = `
    <div class="roster-card-header">
      <input class="roster-name-input" type="text" placeholder="${escHtml(t('roster.name_placeholder'))}" value="${escHtml(re.name)}">
      <button class="card-collapse-btn btn-sm" title="${escHtml(t('players.tip_collapse'))}">▲</button>
    </div>
    <div class="roster-collapsed-summary" style="display:none">
      <div class="ccs-stat-grid">
        <div class="ccs-stat"><span class="ccs-label">${statLabel('hp')}</span><span class="ccs-val">${re.hp}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('str')}</span><span class="ccs-val">${re.str}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('dex')}</span><span class="ccs-val">${re.dex}</span></div>
        <div class="ccs-stat"><span class="ccs-label">${statLabel('wil')}</span><span class="ccs-val">${re.wil}</span></div>
      </div>
      <button class="roster-add-btn btn-inverted" style="width:100%;margin-top:6px">${t('roster.fight')}</button>
    </div>
    <div class="roster-card-body">
      <div class="roster-stat-grid">
        <div class="roster-stat stat-hp"><span class="roster-stat-label">${statLabel('hp')}</span><span class="roster-stat-val" data-rstat="hp">${re.hp}</span></div>
        <div class="roster-stat"><span class="roster-stat-label">${statLabel('str')}</span><span class="roster-stat-val" data-rstat="str">${re.str}</span></div>
        <div class="roster-stat"><span class="roster-stat-label">${statLabel('dex')}</span><span class="roster-stat-val" data-rstat="dex">${re.dex}</span></div>
        <div class="roster-stat"><span class="roster-stat-label">${statLabel('wil')}</span><span class="roster-stat-val" data-rstat="wil">${re.wil}</span></div>
        <div class="roster-stat"><span class="roster-stat-label">${t('stats.armor')}</span><span class="roster-stat-val" data-rstat="armor">${re.armor ?? 0}</span></div>
      </div>
      <div class="roster-weapon-row">
        <input class="roster-weapon-input" type="text" placeholder="${escHtml(t('roster.weapon_placeholder'))}" value="${escHtml(re.weapon || '')}">
        <select class="roster-dice-select">${diceSelectHtml}</select>
      </div>
      <input class="roster-notes-input" type="text" placeholder="${escHtml(t('roster.notes_placeholder'))}" value="${escHtml(re.notes)}">
      <div class="roster-actions">
        <button class="roster-add-btn btn-inverted">${t('roster.fight')}</button>
        <button class="roster-del-btn btn-sm">${t('roster.delete')}</button>
      </div>
    </div>`;

  card.querySelector('.roster-name-input').addEventListener('input', e => {
    re.name = e.target.value; scheduleSave();
  });

  // Collapse — mirrors player card behaviour
  if (!(re.id in rosterCollapsed)) rosterCollapsed[re.id] = false;
  let collapsed = rosterCollapsed[re.id];
  const colBtn = card.querySelector('.card-collapse-btn');
  const body = card.querySelector('.roster-card-body');
  const summary = card.querySelector('.roster-collapsed-summary');
  const applyCollapse = () => {
    body.classList.toggle('collapsed', collapsed);
    summary.style.display = collapsed ? 'block' : 'none';
    card.classList.toggle('collapsed-card', collapsed);
    colBtn.textContent = collapsed ? '▼' : '▲';
  };
  applyCollapse();
  colBtn.addEventListener('click', () => {
    collapsed = !collapsed;
    rosterCollapsed[re.id] = collapsed;
    applyCollapse();
  });

  card.querySelectorAll('.roster-stat-val').forEach(el => {
    el.addEventListener('click', () => {
      const sk = el.dataset.rstat;
      openNumpad(t('numpad.max_stat', { stat: statLabel(sk) }), re[sk], v => {
        re[sk] = v;
        renderRoster();
        scheduleSave();
      });
    });
  });

  card.querySelector('.roster-weapon-input').addEventListener('input', e => {
    re.weapon = e.target.value; scheduleSave();
  });

  card.querySelector('.roster-dice-select').addEventListener('change', e => {
    re.attackDice = e.target.value; scheduleSave();
  });

  card.querySelector('.roster-notes-input').addEventListener('input', e => {
    re.notes = e.target.value; scheduleSave();
  });

  card.querySelectorAll('.roster-add-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      this.classList.add('flash');
      setTimeout(() => this.classList.remove('flash'), 400);
      const name = resolveEncounterName(re.name);
      state.encounter.push(makeCombatant({
        name, type: 'enemy',
        hp: stat(re.hp), str: stat(re.str), dex: stat(re.dex), wil: stat(re.wil),
        armor: re.armor ?? 0,
        weapon: re.weapon || '',
        attackDice: re.attackDice || 'd6',
        notes: re.notes || '',
      }));
      renderEncounter();
      scheduleSave();
    });
  });

  card.querySelector('.roster-del-btn').addEventListener('click', () => {
    state.roster = state.roster.filter(x => x.id !== re.id);
    renderRoster();
    scheduleSave();
  });

  return card;
}

function allRosterCollapsed() {
  return state.roster.length > 0 && state.roster.every(re => rosterCollapsed[re.id]);
}
function updateRosterCollapseAllBtn() {
  const btn = document.getElementById('btn-collapse-all-roster');
  if (btn) btn.textContent = allRosterCollapsed() ? t('encounter.expand_all') : t('encounter.collapse_all');
}
function renderRoster() {
  const grid = document.getElementById('roster-grid');
  grid.innerHTML = '';
  state.roster.forEach(re => grid.appendChild(buildRosterCard(re)));
  updateRosterCollapseAllBtn();
}

document.getElementById('btn-collapse-all-roster').addEventListener('click', () => {
  const newCollapsed = !allRosterCollapsed();
  state.roster.forEach(re => {
    rosterCollapsed[re.id] = newCollapsed;
    const card = document.querySelector(`#roster-grid [data-id="${re.id}"]`);
    if (!card) return;
    card.querySelector('.roster-card-body').classList.toggle('collapsed', newCollapsed);
    card.querySelector('.roster-collapsed-summary').style.display = newCollapsed ? 'block' : 'none';
    card.classList.toggle('collapsed-card', newCollapsed);
    card.querySelector('.card-collapse-btn').textContent = newCollapsed ? '▼' : '▲';
  });
  updateRosterCollapseAllBtn();
});

document.getElementById('btn-add-roster').addEventListener('click', () => {
  state.roster.push(makeRosterEnemy());
  renderRoster();
  scheduleSave();
});

/* ═══════════════════════════════════════════════════════════════════
   SETTINGS
═══════════════════════════════════════════════════════════════════ */
document.querySelectorAll('.lang-btn').forEach(btn => {
  btn.classList.toggle('active', btn.dataset.lang === currentLang);
  btn.addEventListener('click', () => {
    localStorage.setItem('lang', btn.dataset.lang);
    location.reload();
  });
});

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

document.querySelectorAll('.boat-mode-btn').forEach(btn => {
  btn.addEventListener('click', () => setBoatMode(btn.dataset.mode === 'on'));
});

/* ═══════════════════════════════════════════════════════════════════
   BOATS
═══════════════════════════════════════════════════════════════════ */
function initBoats() {
  state.boats = [
    { id: uuid(), name: t('boat.paperboat_name'), hp: {max:8,  current:8},  str: {max:8,  current:8},  dex: 8,  wil: 8,  speed: 'd4', position: 0, defeated: false },
    { id: uuid(), name: t('boat.lyra_name'),       hp: {max:10, current:10}, str: {max:8,  current:8},  dex: 10, wil: 10, speed: 'd8', position: 0, defeated: false },
    { id: uuid(), name: t('boat.oslo_name'),       hp: {max:8,  current:8},  str: {max:8,  current:8},  dex: 8,  wil: 12, speed: 'd4', position: 0, defeated: false },
    { id: uuid(), name: t('boat.grak_name'),       hp: {max:6,  current:6},  str: {max:6,  current:6},  dex: 12, wil: 6,  speed: 'd6', position: 0, defeated: false },
  ];
}

function applyDamageToBoat(b, stat, amount) {
  if (stat === 'hp') {
    const overflow = amount - b.hp.current;
    b.hp.current = clamp(b.hp.current - amount, 0, b.hp.max);
    if (overflow > 0) b.str.current = clamp(b.str.current - overflow, 0, b.str.max);
  } else {
    b[stat].current = clamp(b[stat].current - amount, 0, b[stat].max);
  }
  if (b.str.current === 0) b.defeated = true;
}

function applyHealToBoat(b, stat, amount) {
  b[stat].current = clamp(b[stat].current + amount, 0, b[stat].max);
  if (stat === 'str' && b.str.current > 0) b.defeated = false;
}

function buildBoatCard(b) {
  const card = document.createElement('div');
  card.className = 'boat-card' + (b.defeated ? ' defeated' : '');
  card.dataset.id = b.id;

  const posLabel = b.position > 0 ? b.position : '—';
  card.innerHTML = `
    <div class="boat-card-header">
      <span class="boat-pos">${posLabel}</span>
      <span class="boat-name">${escHtml(b.name)}</span>
      <span class="boat-speed" title="${escHtml(t('boat.speed_label', { name: b.name }))}">${b.speed}</span>
      <button class="enc-defeat-btn btn-sm">${b.defeated ? t('encounter.revive') : t('encounter.defeat')}</button>
    </div>
    <div class="boat-card-body${b.defeated ? ' collapsed' : ''}">
      <div class="pip-rows-container"></div>
      <div class="compact-stat-row">
        <span class="compact-stat"><span class="csl">${statLabel('dex')}</span><span class="csv-ref" data-stat="dex" title="${escHtml(t('boat.stat_label', { stat: statLabel('dex'), name: b.name }))}">${b.dex}</span></span>
        <span class="compact-stat"><span class="csl">${statLabel('wil')}</span><span class="csv-ref" data-stat="wil" title="${escHtml(t('boat.stat_label', { stat: statLabel('wil'), name: b.name }))}">${b.wil}</span></span>
      </div>
    </div>`;

  const pipContainer = card.querySelector('.pip-rows-container');
  pipContainer.appendChild(buildPipBar('HP', b.hp.current, b.hp.max,
    () => openPopover({ mode: 'damage', statKey: 'hp', sourceId: b.id, source: 'boat' }),
    v => { b.hp.max = v; b.hp.current = v; renderBoats(); scheduleSave(); }));
  pipContainer.appendChild(buildPipBar('STR', b.str.current, b.str.max,
    () => openPopover({ mode: 'damage', statKey: 'str', sourceId: b.id, source: 'boat' }),
    v => { b.str.max = v; b.str.current = v; renderBoats(); scheduleSave(); }));

  card.querySelector('.boat-pos').addEventListener('click', () => {
    openNumpad(t('boat.position_label', { name: b.name }), b.position, v => {
      b.position = v;
      renderBoats();
      scheduleSave();
    }, 0);
  });

  card.querySelectorAll('.csv-ref[data-stat]').forEach(el => {
    setupDoubleTap(el, () => {
      const sk = el.dataset.stat;
      openNumpad(t('boat.stat_label', { stat: statLabel(sk), name: b.name }), b[sk], v => {
        b[sk] = v;
        renderBoats();
        scheduleSave();
      });
    });
  });

  setupDoubleTap(card.querySelector('.boat-speed'), () => {
    const current = parseInt(b.speed.replace('d', ''), 10) || 6;
    openNumpad(t('boat.speed_label', { name: b.name }), current, v => {
      b.speed = 'd' + v;
      renderBoats();
      scheduleSave();
    });
  });

  card.querySelector('.enc-defeat-btn').addEventListener('click', () => {
    b.defeated = !b.defeated;
    renderBoats();
    scheduleSave();
  });

  return card;
}

function renderBoats() {
  const list = document.getElementById('boat-list');
  if (!list) return;
  list.innerHTML = '';
  const sorted = [...state.boats].sort((a, b) => {
    if (a.position === 0 && b.position === 0) return 0;
    if (a.position === 0) return 1;
    if (b.position === 0) return -1;
    return a.position - b.position;
  });
  sorted.forEach(b => list.appendChild(buildBoatCard(b)));
}

/* ═══════════════════════════════════════════════════════════════════
   DOUBLE-TAP
═══════════════════════════════════════════════════════════════════ */
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

/* ═══════════════════════════════════════════════════════════════════
   LONG PRESS
═══════════════════════════════════════════════════════════════════ */
function setupLongPress(el, callback) {
  let timer = null;
  el.addEventListener('pointerdown', () => {
    timer = setTimeout(() => { callback(); timer = null; }, 600);
  });
  el.addEventListener('pointerup', () => { clearTimeout(timer); });
  el.addEventListener('pointerleave', () => { clearTimeout(timer); });
}

/* ═══════════════════════════════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════════════════════════════ */
function escHtml(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ═══════════════════════════════════════════════════════════════════
   BOOT
═══════════════════════════════════════════════════════════════════ */
(async () => {
  applyI18n();
  await loadState();
  setBoatMode(boatMode);
  state.players.forEach(p => { if (!p.paused) addPlayerToEncounter(p); });
  renderTurns();
  renderPlayers();
  renderRoster();
  renderEncounter();
  renderBoats();
})();
</script>
</body>
</html>

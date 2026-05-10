let state = {
  turns: { count: 1, boxes: ['','','','','',''], timeOfDay: 'sunrise' },
  players: [],
  encounter: [],
  roster: [],
  notes: '',
};

let saveTimer = null;
let stateLoaded = false;
let lastSaveTime = 0;

function scheduleSave() {
  if (!stateLoaded) return;
  clearTimeout(saveTimer);
  saveTimer = setTimeout(persistState, 500);
}

async function persistState() {
  lastSaveTime = Date.now();
  saveTimer = null;
  try {
    await fetch('api.php?action=save', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(state),
    });
  } catch (e) { console.error('Save failed', e); }
}

window.addEventListener('beforeunload', () => {
  if (stateLoaded && saveTimer) {
    clearTimeout(saveTimer);
    navigator.sendBeacon('api.php?action=save', JSON.stringify(state));
  }
});

function showLoadError(msg) {
  const el = document.getElementById('load-error');
  el.textContent = msg;
  el.style.display = 'block';
}

async function loadState() {
  try {
    const r = await fetch('api.php?action=load&_=' + Date.now());
    if (!r.ok) { showLoadError(`Load failed: HTTP ${r.status} ${r.statusText} — ${await r.text()}`); return; }
    const raw = await r.text();
    let data;
    try { data = JSON.parse(raw); } catch (e) { showLoadError(`Load failed: invalid JSON — ${raw.slice(0, 200)}`); return; }
    state = data;
    if (!state.roster) state.roster = [];
    if (!state.boats) state.boats = [];
    if (!('notes' in state)) state.notes = '';
    state.turns.boxes = state.turns.boxes.map(b => b === '🕯' ? 'T' : b === '💡' ? 'L' : b);
    state.players.forEach(p => { if (!('paused' in p)) p.paused = false; });
    state.encounter.forEach(e => { if (!('initiative' in e)) e.initiative = null; });
    stateLoaded = true;
  } catch (e) { showLoadError(`Load failed: ${e}`); }
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

function makeRosterEnemy() {
  return { id: uuid(), name: '', hp: 6, str: 10, dex: 8, wil: 6, armor: 0, weapon: '', attackDice: 'd6', notes: '' };
}

function deduplicateEncounterNames(name) {
  const dupes = state.encounter.filter(e => e.name === name);
  if (dupes.length < 2) return false;
  const otherNames = new Set(state.encounter.filter(e => e.name !== name).map(e => e.name));
  let n = 1;
  dupes.forEach(e => {
    while (otherNames.has(n + '. ' + name)) n++;
    e.name = n + '. ' + name;
    otherNames.add(e.name);
    n++;
  });
  return true;
}

function resolveEncounterName(baseName) {
  if (!baseName) return baseName;
  const taken = new Set(state.encounter.map(e => e.name));
  let n = 1;
  while (taken.has(n + '. ' + baseName)) n++;
  if (!taken.has(baseName) && n === 1) return baseName;
  state.encounter.filter(e => e.name === baseName)
    .forEach(e => { e.name = n + '. ' + baseName; taken.add(e.name); n++; });
  while (taken.has(n + '. ' + baseName)) n++;
  return n + '. ' + baseName;
}

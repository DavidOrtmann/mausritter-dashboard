/* ── Numpad ──────────────────────────────────────────────────────── */
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

/* ── Damage / Heal Popover ───────────────────────────────────────── */
let popoverCtx = null;
let popoverMode = 'damage';
let undoStack = {};

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

function closePopover() {
  document.getElementById('popover-overlay').classList.remove('open');
  popoverCtx = null;
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

/* ── Undo ────────────────────────────────────────────────────────── */
function saveUndo(id, source, entity) {
  undoStack[id] = { source, snapshot: JSON.parse(JSON.stringify(entity)) };
}

function showUndo(id, source) {
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

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
  const notesArea = document.getElementById('notes-area');
  notesArea.value = state.notes || '';
  notesArea.addEventListener('input', () => { state.notes = notesArea.value; scheduleSave(); });

  setInterval(async () => {
    if (!stateLoaded || saveTimer || Date.now() - lastSaveTime < 5000) return;
    try {
      const r = await fetch('api.php?action=load&_=' + Date.now());
      if (!r.ok) return;
      const serverState = await r.json();
      if (JSON.stringify(serverState) === JSON.stringify(state)) return;
      state = serverState;
      if (!state.roster) state.roster = [];
      if (!state.boats) state.boats = [];
      if (!('notes' in state)) state.notes = '';
      state.turns.boxes = state.turns.boxes.map(b => b === '🕯' ? 'T' : b === '💡' ? 'L' : b);
      state.players.forEach(p => { if (!('paused' in p)) p.paused = false; });
      state.encounter.forEach(e => { if (!('initiative' in e)) e.initiative = null; });
      renderTurns();
      renderPlayers();
      renderRoster();
      renderEncounter();
      renderBoats();
      document.getElementById('notes-area').value = state.notes || '';
    } catch (e) {}
  }, 30000);
})();

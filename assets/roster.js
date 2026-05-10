const rosterCollapsed = {};

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

  if (!(re.id in rosterCollapsed)) rosterCollapsed[re.id] = true;
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

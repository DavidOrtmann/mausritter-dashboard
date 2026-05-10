const encCollapsed = {};
let currentTurnId = null;

function allEncounterCollapsed() {
  return state.encounter.length > 0 && state.encounter.every(e => encCollapsed[e.id]);
}

function applyDamageToEncounter(ec, statKey, amount) {
  const isPC = ec.type === 'pc';
  if (statKey === 'hp') {
    const effective = Math.max(0, amount - (ec.armor ?? 0));
    const overflow = effective - ec.hp.current;
    ec.hp.current = clamp(ec.hp.current - effective, 0, ec.hp.max);
    if (overflow > 0) {
      ec.str.current = clamp(ec.str.current - overflow, 0, ec.str.max);
    }
  } else {
    ec[statKey].current = clamp(ec[statKey].current - amount, 0, ec[statKey].max);
  }
  if (ec.str.current === 0) ec.defeated = true;
  if (isPC) writeBackToPlayer(ec);
}

function applyHealToEncounter(ec, statKey, amount) {
  ec[statKey].current = clamp(ec[statKey].current + amount, 0, ec[statKey].max);
  if (statKey === 'str' && ec[statKey].current > 0) ec.defeated = false;
  if (ec.type === 'pc') writeBackToPlayer(ec);
}

function buildPipBar(statKey, current, max, onTap, onMaxChange) {
  const wrap = document.createElement('div');
  wrap.className = 'pip-row';

  const label = document.createElement('span');
  label.className = 'pip-label';
  label.textContent = statLabel(statKey);
  wrap.appendChild(label);

  if (max > 12) {
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

  const numsWrap = document.createElement('span');
  numsWrap.className = 'pip-nums';

  const curSpan = document.createElement('span');
  curSpan.textContent = `${current} / `;
  curSpan.addEventListener('click', onTap);
  numsWrap.appendChild(curSpan);

  const maxSpan = document.createElement('span');
  maxSpan.className = 'pip-max';
  maxSpan.textContent = max;
  maxSpan.title = t('encounter.tip_dbl_tap_max', { stat: statLabel(statKey) });
  if (onMaxChange) {
    setupDoubleTap(maxSpan, () => {
      openNumpad(t('numpad.max_stat', { stat: statLabel(statKey) }), max, onMaxChange);
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

  const pipContainer = card.querySelector('.pip-rows-container');
  pipContainer.appendChild(buildPipBar('HP', ec.hp.current, ec.hp.max,
    () => { openPopover({ mode: 'damage', statKey: 'hp', sourceId: ec.id, source: 'encounter' }); },
    (v) => { ec.hp.max = v; ec.hp.current = v; if (isPC) writeBackToPlayer(ec); renderEncounter(); scheduleSave(); }
  ));
  pipContainer.appendChild(buildPipBar('STR', ec.str.current, ec.str.max,
    () => { openPopover({ mode: 'damage', statKey: 'str', sourceId: ec.id, source: 'encounter' }); },
    (v) => { ec.str.max = v; ec.str.current = v; if (isPC) writeBackToPlayer(ec); renderEncounter(); scheduleSave(); }
  ));

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

  card.querySelector('.csv-armor').addEventListener('click', () => {
    openNumpad(t('players.armor'), ec.armor ?? 0, v => {
      ec.armor = v;
      if (isPC) writeBackToPlayer(ec);
      renderEncounter();
      scheduleSave();
    }, 0);
  });

  card.querySelector('.type-badge').addEventListener('click', () => {
    openNumpad(t('numpad.initiative', { name: ec.name || ec.type.toUpperCase() }), 0, v => {
      ec.initiative = v;
      renderEncounter();
      scheduleSave();
    }, 0);
  });

  const nameInput = card.querySelector('.card-name-input');
  if (nameInput) {
    nameInput.addEventListener('input', e => {
      ec.name = e.target.value;
      if (isPC) writeBackToPlayer(ec);
      scheduleSave();
    });
    if (!isPC) {
      nameInput.addEventListener('blur', () => {
        if (deduplicateEncounterNames(ec.name)) {
          renderEncounter();
          scheduleSave();
        }
      });
    }
  }

  const colBtn = card.querySelector('.card-collapse-btn');
  const summary = card.querySelector('.enc-collapsed-summary');
  if (colBtn) {
    if (!(ec.id in encCollapsed)) encCollapsed[ec.id] = true;
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

  card.querySelectorAll('.ccs-tappable').forEach(el => {
    el.addEventListener('click', () => {
      openPopover({ mode: 'damage', statKey: el.dataset.popstat, sourceId: ec.id, source: 'encounter' });
    });
  });

  card.querySelector('.enc-defeat-btn').addEventListener('click', () => {
    ec.defeated = !ec.defeated;
    renderEncounter();
    scheduleSave();
  });

  card.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const s = btn.dataset.status;
      ec[s] = !ec[s];
      if (isPC) writeBackToPlayer(ec);
      renderEncounter();
      scheduleSave();
    });
  });

  card.querySelector('.enc-notes-input').addEventListener('input', e => {
    ec.notes = e.target.value;
    scheduleSave();
  });

  return card;
}

function initiativeScore(ec) {
  const init = ec.initiative;
  if (init === null || init === undefined) return 1000;
  if (init === 0) return -1;
  if (init <= ec.dex.current) return init;
  return 100 + init;
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
  if (currentTurnId) {
    if (!state.encounter.find(e => e.id === currentTurnId && !e.defeated)) {
      currentTurnId = null;
    } else {
      const cur = list.querySelector(`[data-id="${currentTurnId}"]`);
      if (cur) cur.classList.add('current-turn');
    }
  }
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
  currentTurnId = null;
  renderEncounter();
  scheduleSave();
});

document.getElementById('btn-next-combatant').addEventListener('click', () => {
  const cards = [...document.querySelectorAll('#encounter-list .enc-card:not(.defeated)')];
  if (cards.length === 0) return;
  const currentIdx = cards.findIndex(c => c.dataset.id === currentTurnId);
  const nextIdx = (currentIdx + 1) % cards.length;
  cards.forEach(c => c.classList.remove('current-turn'));
  currentTurnId = cards[nextIdx].dataset.id;
  cards[nextIdx].classList.add('current-turn');
  cards[nextIdx].scrollIntoView({ block: 'nearest' });
});

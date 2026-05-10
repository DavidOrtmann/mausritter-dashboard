const playerCollapsed = {};

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

function longRest(p) {
  p.hp.current = p.hp.max;
}

function fullRest(p) {
  p.hp.current  = p.hp.max;
  p.str.current = p.str.max;
  p.dex.current = p.dex.max;
  p.wil.current = p.wil.max;
  p.injured = false;
  p.drained = false;
  p.encumbered = false;
}

function applyDamageToPlayer(p, statKey, amount) {
  if (statKey === 'hp') {
    const effective = Math.max(0, amount - (p.armor ?? 0));
    const overflow = effective - p.hp.current;
    p.hp.current = clamp(p.hp.current - effective, 0, p.hp.max);
    if (overflow > 0) {
      p.str.current = clamp(p.str.current - overflow, 0, p.str.max);
    }
  } else {
    p[statKey].current = clamp(p[statKey].current - amount, 0, p[statKey].max);
  }
  syncEncounterPC(p);
}

function applyHealToPlayer(p, statKey, amount) {
  p[statKey].current = clamp(p[statKey].current + amount, 0, p[statKey].max);
  syncEncounterPC(p);
}

function buildPlayerCard(p) {
  const card = document.createElement('div');
  card.className = 'card' + (p.paused ? ' paused' : '');
  card.dataset.id = p.id;

  function statFlags(s) {
    if (s === 'str' && p.injured)    return ' injured-flag';
    if (s === 'dex' && p.drained)    return ' drained-flag';
    if (s === 'wil' && p.encumbered) return ' encumb-flag';
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

  card.querySelector('.card-name-input').addEventListener('input', e => {
    p.name = e.target.value;
    syncEncounterPC(p);
    scheduleSave();
  });

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

  card.querySelectorAll('.card-collapsed-summary .ccs-tappable').forEach(el => {
    el.addEventListener('click', () => {
      openPopover({ mode: 'damage', statKey: el.dataset.popstat, sourceId: p.id, source: 'player' });
    });
  });

  card.querySelectorAll('.stat-current').forEach(el => {
    el.addEventListener('click', () => {
      openPopover({ mode: 'damage', statKey: el.dataset.stat, sourceId: p.id, source: 'player' });
    });
    setupLongPress(el, () => {
      applyDamageToPlayer(p, el.dataset.stat, 1);
      renderPlayers();
      renderEncounter();
      scheduleSave();
    });
  });

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

  card.querySelectorAll('.status-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const s = btn.dataset.status;
      p[s] = !p[s];
      renderPlayers();
      scheduleSave();
    });
  });

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

function renderPlayers() {
  const list = document.getElementById('player-list');
  list.innerHTML = '';
  state.players.forEach(p => list.appendChild(buildPlayerCard(p)));
  updatePlayerCollapseAllBtn();
}

function allPlayersCollapsed() {
  const active = state.players.filter(p => !p.paused);
  return active.length > 0 && active.every(p => playerCollapsed[p.id]);
}

function updatePlayerCollapseAllBtn() {
  const btn = document.getElementById('btn-collapse-all-players');
  if (btn) btn.textContent = allPlayersCollapsed() ? t('encounter.expand_all') : t('encounter.collapse_all');
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

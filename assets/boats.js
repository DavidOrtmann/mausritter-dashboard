function initBoats() {
  state.boats = [
    { id: uuid(), name: t('boat.paperboat_name'), hp: {max:8,  current:8},  str: {max:8,  current:8},  dex: 8,  wil: 8,  speed: 'd4', position: 0, defeated: false },
    { id: uuid(), name: t('boat.lyra_name'),       hp: {max:10, current:10}, str: {max:8,  current:8},  dex: 10, wil: 10, speed: 'd8', position: 0, defeated: false },
    { id: uuid(), name: t('boat.oslo_name'),       hp: {max:8,  current:8},  str: {max:8,  current:8},  dex: 8,  wil: 12, speed: 'd4', position: 0, defeated: false },
    { id: uuid(), name: t('boat.grak_name'),       hp: {max:6,  current:6},  str: {max:6,  current:6},  dex: 12, wil: 6,  speed: 'd6', position: 0, defeated: false },
  ];
}

function applyDamageToBoat(b, statKey, amount) {
  if (statKey === 'hp') {
    const overflow = amount - b.hp.current;
    b.hp.current = clamp(b.hp.current - amount, 0, b.hp.max);
    if (overflow > 0) b.str.current = clamp(b.str.current - overflow, 0, b.str.max);
  } else {
    b[statKey].current = clamp(b[statKey].current - amount, 0, b[statKey].max);
  }
  if (b.str.current === 0) b.defeated = true;
}

function applyHealToBoat(b, statKey, amount) {
  b[statKey].current = clamp(b[statKey].current + amount, 0, b[statKey].max);
  if (statKey === 'str' && b.str.current > 0) b.defeated = false;
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

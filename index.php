<?php
header('Cache-Control: no-store');
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

<div id="load-error" style="display:none;padding:12px;background:#fff;border-bottom:2px solid #000;font-family:monospace;font-size:13px;word-break:break-all"></div>
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
      <button id="btn-next-combatant" class="btn-inverted" data-i18n="encounter.next_turn">Next</button>
    </div>

    <!-- ── TAB 5: BOAT ────────────────────────────────────────────── -->
    <div id="tab-boat" class="tab-panel boat-mode-only">
      <h2 data-i18n="boat.heading">Boats</h2>
      <div id="boat-list"></div>
    </div>

    <!-- ── TAB 6: NOTES ──────────────────────────────────────────────── -->
    <div id="tab-notes" class="tab-panel">
      <textarea id="notes-area" data-i18n-placeholder="notes.placeholder" placeholder="Notes…"></textarea>
    </div>

    <!-- ── TAB 7: SETTINGS ─────────────────────────────────────────── -->
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
        <div class="settings-row">
          <span class="settings-label" data-i18n="settings.reload_label">Reload</span>
          <button id="btn-force-reload" class="btn-sm" data-i18n="settings.reload_btn">Force Reload</button>
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
    <button data-tab="notes" data-i18n="nav.notes">Notes</button>
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

<script src="assets/lib.js?v=<?= filemtime('assets/lib.js') ?>"></script>
<script src="assets/state.js?v=<?= filemtime('assets/state.js') ?>"></script>
<script src="assets/ui.js?v=<?= filemtime('assets/ui.js') ?>"></script>
<script src="assets/turns.js?v=<?= filemtime('assets/turns.js') ?>"></script>
<script src="assets/players.js?v=<?= filemtime('assets/players.js') ?>"></script>
<script src="assets/encounter.js?v=<?= filemtime('assets/encounter.js') ?>"></script>
<script src="assets/roster.js?v=<?= filemtime('assets/roster.js') ?>"></script>
<script src="assets/boats.js?v=<?= filemtime('assets/boats.js') ?>"></script>
<script src="assets/settings.js?v=<?= filemtime('assets/settings.js') ?>"></script>
<script src="assets/boot.js?v=<?= filemtime('assets/boot.js') ?>"></script>
</body>
</html>

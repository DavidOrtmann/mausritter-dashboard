# Mausritter GM Session Tracker — SPEC.md

> Living specification. Update this file after each working session to reflect what has been built vs. what remains. Add new requests to `## Pending Changes` at the bottom.

---

## Status

### Setup & Infrastructure
- [x] Create file structure (`index.php`, `api.php`, `data/session.json`, `assets/style.css`)
- [x] Implement `api.php` with `load`, `save`, `reset` actions
- [x] Implement write safety in `api.php` via `flock()`
- [x] Auto-save on state change, debounced 500ms
- [x] State hydration on page load via `fetch()` GET
- [x] Bottom tab bar navigation (Turns / Players / Roster / Fights / Settings)
- [x] Localisation infrastructure: `locales/en.json` + `locales/de.json`, `t(key, vars)` lookup with `{placeholder}` substitution, `applyI18n()` walks `data-i18n` attributes on page load; language preference stored in `localStorage`, change triggers page reload
- [x] Settings tab: language selector (EN / DE)

### Tab 1: Turns
- [x] Persistent turn drawer: 50% grey 48px strip fixed at top of every screen; shows "TURNS" label + current turn count; tap to expand/collapse mini turn tracker (6 boxes + New Cycle / Reset Counter); in boat mode shows race boxes + Reset Race; fully synced with main Turns tab
- [x] Rest action row (default mode only): Short Rest All (opens numpad — GM enters d6+1 roll result, heals all mice by that amount) · Long Rest All · Full Rest All
- [x] 6-box turn tracker; each box cycles `empty → ✓ (turn passed) → T (torch) → L (electric light) → empty`; first click checks the turn, further clicks track light source type
- [x] Boxes 3 and 6 have double-border and a faint `!` indicator — visual cue to roll for encounter (per SRD: every 3 turns)
- [x] Marking a box (empty→T) increments the turn counter; clearing it (L→empty) decrements it
- [x] Banner alert *"Mark light source usage!"* fires only when all 6 boxes show the same light type (all T or all L) — torches and electric lights last 6 turns per SRD
- [x] "New Cycle" button: resets the 6 boxes only; turn counter keeps the running total
- [x] "Reset Counter" button
- [x] Time of day selector: 7 large toggle buttons (Sunrise / Noon / Sunset / Night Watch / Morning Watch / Afternoon Watch / Evening Watch)
- [x] Static encounter dice reference text (dungeon + wilderness rules) — corrected to SRD 2.3.1: dungeon every 3 turns; wilderness Morning+Evening Watch only; reaction table uses SRD wording; morale is WIL save not a score
- ~~Optional: "Roll d6" button with inline result and interpretation~~ — removed

### Tab 2: Players
- [x] Player cards with editable Name field
- [x] HP, STR, DEX, WIL — each with `current / max` dual values
- [x] Per-card meta stats: Level, XP, Grit, Pips, Treasury — each a plain integer, tap to edit via on-screen numpad; displayed as a compact row below the status toggles; XP/Pips/Treasury allow up to 5 digits, Level/Grit capped at 2
- [x] HP displayed largest/most prominently per card
- [x] Status toggles per card: Injured / Drained / Encumbered
- [x] Visual flag on affected stat labels when a status is active
- [x] Cards collapsible: collapsed card becomes half-width, showing all four stats (HP/STR/DEX/WIL current/max in a compact 2×2 grid) and any active status badges; two collapsed cards sit side by side, expanded cards span full width; tapping any stat in the collapsed summary opens the damage/heal popover
- [x] Delete button per card (with confirmation)
- [x] "Add Player" button (no hard limit; 6 typical); new players are automatically added to the Encounter tab
- [x] Per-card reset button (⟳) with Long Rest / Full Rest options
- [x] Bulk rest actions moved to Turns tab: Short Rest All / Long Rest All / Full Rest All
- [x] "Collapse/Expand All" bulk toggle in tab header
- [x] Player data persists across session resets (not cleared by "New Encounter")
- [x] "Pause" / "Resume" button per card: paused players are hidden from the Encounter tab and their card body is greyed out; useful for players sitting out a session
- [x] Double-tap any max value to edit it via on-screen numpad (native keyboard suppressed); setting a new max also sets current to that value
- [x] Numpad opens blank (not pre-filled) so the GM can type immediately

### Tab 3: Fights (formerly Encounter)
- [x] Unified combatant list: PCs, NPCs, enemies in one view
- [x] Players are automatically present in the encounter; no import needed — adding a player or resuming a paused player syncs them in automatically
- [x] "Add Combatant" button: creates blank card
- [x] Type badge per card: `PC` / `NPC` / `Enemy` — label only, not interactive
- [x] Type badge visual language: PC = thin border, NPC = dashed border, Enemy = inverted black/white
- [x] HP and STR pip bar display with `current / max` label
- [x] DEX and WIL compact current/max display (no pip bar)
- [x] Pip bars: filled pip = black square, empty pip = white square with black border, 2px gap between pips; stat at 0 shows all empty pips (no special depleted state)
- [x] For stats > 12: compact progress bar + number instead of individual pips
- [x] Status toggles: Injured / Drained / Encumbered
- [x] Notes field: single free-text line per card
- [x] Cards collapsible: collapsed card becomes half-width (2-column grid); collapsed summary shows all four stats tappable for damage/heal; initiative always visible in header badge
- [x] Collapse state persists across re-renders (e.g. after damage applied)
- [x] Defeated toggle: strikes through and collapses card, keeps it visible
- [x] "Clear Defeated" button
- [x] "New Encounter" button: clears non-PC combatants, confirmation required
- [x] Double-tap any max value (HP, STR pip bars; DEX, WIL compact) to edit via on-screen numpad; setting a new max also sets current to that value

### Combat Damage
- [x] Tap HP or STR pip bar / value to open combined damage/heal modal
- [x] Damage/Heal modal: mode toggle (Damage / Heal) at top, number field (default 1), +/− buttons, Apply, Cancel
- [x] Damage overflow logic: damage hits HP first; remainder carries into STR
- [x] Heal logic: adds to current stat, capped at max; healing STR above 0 un-defeats the combatant
- [x] Auto-mark Defeated when STR reaches 0
- [x] Undo button appears on card for ~5 seconds after damage/heal is applied
- [x] Long-press on HP or STR value decrements by 1 directly (no popover)
- [ ] Manual stat restore: tap individual stat's max label to restore that stat to max — *not yet implemented (max label reserved for double-tap edit numpad)*

### Tab 4: Roster
- [x] New "Roster" tab for preparing enemy templates before a fight
- [x] Cards collapsible — mirrors Mice tab: expanded cards are full-width, two collapsed cards sit side by side; HP displayed prominently (32px, full width) in expanded view
- [x] Each card: inverted black header with name + collapse button; body has HP / STR / DEX / WIL (max values, tap to edit via numpad), armor, weapon name + attack dice (d4/d6/d8/d10/d12 select), notes field
- [x] Collapsed summary: HP/STR/DEX/WIL in 2×2 grid + "▶ Add to Fight" button — fully functional without expanding
- [x] "▶ Add to Fight" button (both expanded and collapsed): copies template into Fights tab as a fresh enemy (all stats at max, new UUID); weapon formatted as `weapon (dice)` prepended to notes; stays on Roster for rapid multi-add; flash feedback on tap
- [x] Duplicate-name auto-numbering: if "Spinne" already exists in the encounter, the existing entry is renamed "Spinne 1" and the new one arrives as "Spinne 2"; further additions increment the suffix
- [x] "✕" delete button per card
- [x] "Add Enemy" + "Collapse/Expand All" buttons at top of tab
- [x] Roster persists in saved state across sessions
- [x] Backward-compatible: sessions without `roster` key treat it as empty array; entries without `weapon`/`attackDice` default to empty/`'d6'`

### Boat Mode (Froschakel adventure)

- [x] Settings toggle: Boat Mode On/Off; stored in `localStorage`; `body.boat-mode` CSS class drives show/hide of `boat-mode-only` / `default-mode-only` elements
- [x] Turning off Boat Mode deletes `state.boats` and resets turn boxes to 6
- [x] Dedicated Boat tab (only visible in boat mode): 4 pre-defined boat cards (Paperboat, Lyra's Steamboat, Oslo's Runeboat, Grak's Rat-rocket)
- [x] Each boat card: position badge (tap → numpad, min 0; `0` displays as `—`), name, speed die, HP/STR pip bars, DEX/WIL reference values, defeated toggle
- [x] `renderBoats()` sorts by position; position 0 floats to bottom
- [x] Turns tab in boat mode: 11 boxes (S, 1–10) with binary ✓ toggle; no light states, no counter; "Reset Race" button replaces counter row
- [x] Time of Day, turn alert, and turn counter hidden in boat mode
- [x] Race reference (mechanics, d6 encounter table, 10 track spaces) replaces encounter dice reference in boat mode
- [x] Stat labels localised: EN = HP/STR/DEX/WIL, DE = TP/STÄ/GES/WIL

### Reset Logic
- [x] Long rest (1 Watch per SRD): restore HP to max — button on each player card; "Long Rest All" bulk action on Turns tab
- [x] Full rest (1 week per SRD): restore HP + STR + DEX + WIL to max, clear all conditions — button on each player card; "Full Rest All" bulk action on Turns tab
- [x] Short rest (1 Turn, d6+1 HP per SRD): "Short Rest All" on Turns tab — GM enters the d6+1 roll result into a numpad; heals all mice by that amount (capped at max HP)

---

## Full State Model

```json
{
  "roster": [
    {
      "id": "uuid",
      "name": "Cave Spider",
      "hp": 6,
      "str": 12,
      "dex": 8,
      "wil": 5,
      "armor": 0,
      "weapon": "Bite",
      "attackDice": "d6",
      "notes": "morale 6, sticky webs"
    }
  ],
  "turns": {
    "count": 1,
    "boxes": ["", "T", "L", "", "", ""],
    "timeOfDay": "sunrise"
  },
  "players": [
    {
      "id": "uuid",
      "name": "Pip",
      "hp":  { "max": 5, "current": 3 },
      "str": { "max": 9, "current": 7 },
      "dex": { "max": 12, "current": 12 },
      "wil": { "max": 7, "current": 5 },
      "injured": false,
      "drained": true,
      "encumbered": false,
      "armor": 0,
      "level": 1,
      "xp": 0,
      "grit": 0,
      "pips": 0,
      "treasury": 0
    }
  ],
  "encounter": [
    {
      "id": "uuid",
      "name": "Cave Spider",
      "type": "enemy",
      "hp":  { "max": 6, "current": 4 },
      "str": { "max": 12, "current": 12 },
      "dex": { "max": 8, "current": 8 },
      "wil": { "max": 5, "current": 5 },
      "injured": false,
      "drained": false,
      "encumbered": false,
      "armor": 0,
      "notes": "morale 6, sticky webs",
      "defeated": false
    }
  ]
}
```

**Important:** Players are synced into the encounter automatically (by `id`). Damage dealt in the Encounter tab writes through to the canonical player record. Paused players are excluded. On load, any non-paused player missing from the encounter is re-added.

---

## File Structure

```
mausritter/
├── index.php                       # App shell and HTML entry point
├── api.php                         # JSON API (load / save / reset)
├── SPEC.md                         # This file
├── mausritter-srd-2.3.1.md         # Mausritter SRD (reference only)
├── Mausritter-Schnellstarter.txt   # German quick-start (translation reference)
├── data/
│   └── session.json                # Persisted state — must be writable: chmod 775 data/
├── locales/
│   ├── en.json                     # English strings
│   └── de.json                     # German strings
└── assets/
    └── style.css
```

---

## Technical Constraints

- **PHP 8+** — `file_get_contents` / `file_put_contents` with `flock()` for write safety
- **Frontend:** vanilla JS + CSS, no framework, no build step
- **Viewport:** `<meta name="viewport" content="width=device-width, initial-scale=1">`
- **No hover interactions** (e-ink display)
- **Minimum tap target:** 48px everywhere
- **Primary layout target:** 600px portrait width (Boox Go 10)
- **No animations, gradients, or shadows** (e-ink)
- **Fonts via Google Fonts:** "Special Elite" for names/headers; "IBM Plex Mono" for stats

---

## Design Direction

**Aesthetic:** Typed field manual. Thick solid borders, no rounded corners, no gradients, no shadows. Black on white throughout.

**Typography:**
- Names and headers: Special Elite or Courier Prime
- Stats and numbers: Source Sans 3 or IBM Plex Mono

**Type badge visual language:**
- `PC` — plain text in thin border box
- `NPC` — dashed border box
- `Enemy` — solid black fill, white text

**Pip bars:** thick square pips, 2px gap between them. Filled = black square, empty = white square with black border. For stats > 12, use compact progress bar + number instead.

---

## Pending Changes

> Add new feature requests and change requests here. Claude Code should move items into the Status checklist above when implementing them, and check them off when done.

<!-- all pending changes completed as of 2026-05-03 (session 2) -->


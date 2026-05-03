# Mausritter GM Tracker — CLAUDE.md

Quick-reference for Claude Code. See SPEC.md for the full living spec.

## File layout

```
index.php          — entire app: HTML shell + all JS (no build step)
api.php            — JSON API: ?action=load|save|reset
assets/style.css   — all CSS
locales/en.json    — English strings
locales/de.json    — German strings
data/session.json  — persisted state (must be chmod 775)
SPEC.md            — living spec + state model
```

## Stack

- PHP 8+ backend (only serves the page and persists JSON)
- Vanilla JS + CSS, no framework, no build step
- Target: 600px portrait, e-ink display (Boox Go 10)
- No hover, no animations, no gradients, no shadows; minimum 48px tap targets

## State shape (abridged)

```js
state = {
  turns:   { count, boxes[], timeOfDay },
  players: [{ id, name, hp, str, dex, wil, injured, drained, encumbered,
              level, xp, grit, pips, treasury, paused }],
  roster:  [{ id, name, hp, str, dex, wil, weapon, attackDice, notes }],
  encounter: [{ id, name, type('pc'|'enemy'), hp, str, dex, wil,
                injured, drained, encumbered, notes, defeated, initiative }],
  boats:   [{ id, name, hp, str, dex, wil, speed, position, defeated }],
}
```

State is auto-saved via `scheduleSave()` (debounced 500 ms → `api.php`). A `beforeunload` handler flushes any pending save immediately via `navigator.sendBeacon` so a quick page refresh never loses the last change.

> **`data/session.json` is excluded from SFTP deployment** (`.vscode/sftp.json` ignore list) — the live server's session file is never overwritten by deploys. All state shape changes must therefore be backward-compatible: new fields must default gracefully when missing (e.g. `p.foo ?? defaultValue`), and no field may be renamed or removed without a migration path.

## I18n pattern

- Strings live in `locales/en.json` and `locales/de.json` under the same keys.
- `t('section.key', { var: val })` — resolves `{var}` placeholders.
- `data-i18n="section.key"` on static HTML elements — applied once at boot by `applyI18n()`.
- Dynamic text (e.g. button labels that change at runtime) must be set via `el.textContent = t(...)` directly; `data-i18n` is not re-applied after boot.
- **Always add both EN and DE strings** when adding a new key.

## Rendering pattern

Each tab has a `render*()` function that rebuilds the DOM from state:
`renderTurns()` / `renderPlayers()` / `renderRoster()` / `renderEncounter()` / `renderBoats()`

`renderTurns()` always calls `renderTurnDrawer()` at the end to keep the persistent drawer in sync. Do not call `renderTurnDrawer()` directly from event handlers — go through `renderTurns()`.

Collapse state for player cards (`playerCollapsed`), encounter cards (`encCollapsed`), and roster cards (`rosterCollapsed`) is kept in plain objects keyed by `id` — checked before re-setting defaults on each render so state survives re-renders.

Each tab with collapsible cards has a corresponding `updateXxxCollapseAllBtn()` called at the end of its `render*()` function to keep the Collapse/Expand All label in sync.

## Key helpers

| Helper | Purpose |
|---|---|
| `uuid()` | New unique ID |
| `makePlayer(name)` | Fresh player object |
| `makeCombatant(overrides)` | Fresh enemy/PC for encounter |
| `makeRosterEnemy()` | Fresh roster template |
| `scheduleSave()` | Debounced persist |
| `openNumpad(label, cur, cb, min)` | On-screen number entry |
| `openPopover(ctx)` | Damage/heal modal |
| `setupDoubleTap(el, cb)` | Double-tap gesture |
| `setupLongPress(el, cb)` | Long-press gesture |
| `t(key, vars)` | i18n lookup |
| `escHtml(str)` | XSS-safe HTML escaping |
| `updateCollapseAllBtn()` | Sync Fights "Collapse/Expand All" label |
| `updatePlayerCollapseAllBtn()` | Sync Mice "Collapse/Expand All" label |
| `updateRosterCollapseAllBtn()` | Sync Roster "Collapse/Expand All" label |
| `renderTurnDrawer()` | Sync persistent top drawer with turn state (called by `renderTurns()`) |

## Common patterns when adding a feature

1. **New roster/player field**: add to `make*()` factory, add to card `innerHTML`, wire `addEventListener`, update both locale files, update SPEC.md state model.
2. **New button**: add HTML with `data-i18n`, add locale key to both files, add `addEventListener` near sibling buttons in JS.
3. **New encounter card element**: update `buildEncCard()` innerHTML + wire listeners; if it affects collapse summary, update that section too.
4. **Backward compat**: new state fields should default gracefully (e.g. `re.weapon || ''`, `re.attackDice || 'd6'`).
5. **Update SPEC.md**: after every feature or change, update SPEC.md — check off completed items, add new ones to the status checklist, and update the state model if the shape changed.

## Useful skills

- `/simplify` — run after adding features to keep `index.php` clean; catches redundancy and over-complexity in the growing single-file codebase.
- `/fewer-permission-prompts` — scans past transcripts and adds a Bash/tool allowlist to `.claude/settings.json` to reduce approval prompts in future sessions.

## Fonts

- Names / headers: `Special Elite` (`var(--font-name)`)
- Stats / numbers: `IBM Plex Mono` (`var(--font-stat)`)

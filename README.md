# Mausritter GM Dashboard

A session tracker for [Mausritter](https://mausritter.com) game masters. Built to run on a Boox Go 10 e-ink tablet, so it stays out of the way at the table while keeping everything the GM needs within a tap or two.

## What it does

**Turns** — Six-box turn tracker that cycles through empty, checked, torch, and electric light states. Tracks the running turn count, fires a reminder when all lights are the same type, and shows a time-of-day selector for the seven watch periods.

**Players** — Cards for each mouse with HP, STR, DEX, and WIL (current and max), status conditions, and meta stats like XP, Grit, and Treasury. Cards collapse to half-width to fit two side by side. Long Rest and Full Rest buttons per card, plus bulk actions for the whole party.

**Roster** — A library of enemy templates. Each entry has stats, a weapon with attack dice, and notes. Tap "Add to Encounter" to push a fresh copy into the fight — duplicates get auto-numbered so you can add three Schaben without any fuss.

**Encounter** — Unified combatant list for PCs and enemies. Pip bars for HP and STR, tap to open a damage/heal modal, collapse cards to keep the list manageable. Set initiative by tapping the type badge on any card. Undo button appears briefly after damage is applied.

**Boat Mode** — A special mode for the Froschakel boat race adventure, with a 11-space race track, four pre-defined boat cards, and a race reference sheet.

## Stack

Plain PHP and vanilla JavaScript — no framework, no build step. State is saved to a JSON file on the server via a small PHP API, debounced as you make changes.

## Setup

1. Clone the repo and drop it into a PHP-enabled web server (shared hosting works fine).
2. Make the `data/` directory writable:
   ```bash
   chmod 775 data/
   ```
   The directory is created automatically on first save if it doesn't exist.
3. Open `index.php` in a browser. That's it.

The app stores everything in `data/session.json`. That file is gitignored — it's live game state, not something you'd want to commit.

## Localisation

The interface is available in English and German. Strings live in `locales/en.json` and `locales/de.json`. Switch languages in the Settings tab; the preference is remembered in `localStorage`.

## Design notes

The whole UI is designed for an e-ink display: thick borders, no rounded corners, no shadows, no animations, minimum 48px tap targets, black on white throughout. Fonts are Special Elite for names and headers, IBM Plex Mono for stats and numbers.

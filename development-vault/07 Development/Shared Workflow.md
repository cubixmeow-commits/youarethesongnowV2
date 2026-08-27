---
type: workflow
status: active
updated: 2026-08-27
area: collaboration
---

# Shared Vault Workflow

This vault is designed for two collaborators using Obsidian on different Macs/PCs while GitHub remains the shared source of truth.

## Basic sync habit

Before working:

```bash
git pull
```

After a meaningful session:

```bash
git add .
git commit -m "Describe the development-note changes"
git pull --rebase
git push
```

## Conflict prevention

- Prefer smaller focused notes over one giant shared brainstorming file.
- Avoid editing the same long note simultaneously when possible.
- Machine-specific Obsidian workspace files are ignored by Git.
- Do not commit secrets, `.env` files, runtime SQLite databases, generated media, or personal credentials.

## What belongs here

- current project truth;
- research;
- decisions;
- prompt experiments;
- product/UX exploration;
- development logs;
- meeting/partner notes;
- useful failures and rejected experiments.

## What belongs in `/docs`

Polished summaries intended to be easy to browse on GitHub Pages.

## Information labels

Use these consistently when useful:

- **Observed** — verified evidence/current behavior.
- **Inferred** — strongly suggested but not proven.
- **Proposed** — recommendation or design idea.
- **Open Question** — unresolved owner choice.
- **Decision** — explicitly accepted direction.

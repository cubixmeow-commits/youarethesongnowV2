# Components

| Doc | Purpose |
| --- | --- |
| [inventory.md](./inventory.md) | Current classes/patterns and canonical candidates |
| [core-components.md](./core-components.md) | Production visual, behavioral, state, accessibility, responsive, and Flutter contracts |

**Implementation reality:** Almost no PHP partials. Shared chrome is `templates/layouts/main.php`. Interactive widgets are mostly built in `public/assets/js/app.js` / `showcase.js` + CSS classes in `app.css`.

**Phase 1 runtime classes** (canonical CSS + fixture/Explore usage; Flutter maps 1:1 later):

| Product component | Web class | Current use |
| --- | --- | --- |
| Button | `.yatsn-btn` + `--primary/--secondary/--quiet/--destructive` | Component lab; Explore Generate / Explore options / Create this direction / retry |
| IconButton | `.yatsn-icon-btn` | Component lab |
| StatusBanner | `.yatsn-status` + `--info/--success/--warning/--error` | Component lab; Explore error/retry |
| SongDnaCard | `.yatsn-dna-card` | Component lab fixtures only |
| CreativeDirectionCard | `.yatsn-direction-card` (Explore also keeps `.ai-direction-card`) | Component lab; current Explore |
| Sheet / Dialog | `.yatsn-sheet` / `.yatsn-dialog` | Component lab |
| Artwork | `.yatsn-artwork` | Component lab |

Remaining product screens still use Round 008 `.btn` / `.status` / `.style-option` until later slices migrate them. Do not treat the lab as a public surface.

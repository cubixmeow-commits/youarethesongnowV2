# First Ruck brand and image system

## Brand idea

**Quiet capability.** First Ruck makes a loaded walk feel understandable, attainable, and worth repeating. It is an outdoor field guide with the finish of a premium fitness product, without military posturing or transformation hype.

> Start where you are. Carry forward.

Secondary product line: **Your first month of rucking, built around you.**

## Logo

The mark combines a trail loop, a rising route, and two waypoints. The lower point means “where you are”; the orange route means deliberate progress; the upper point means “the next reachable step.” It deliberately avoids shields, rank insignia, mountains, kettlebells, and military symbols.

Files:

- `assets/logo/firstruck-mark.svg` — primary dark product mark
- `assets/logo/firstruck-mark-reversed.svg` — paper-background variant
- `assets/logo/firstruck-lockup.svg` — presentation lockup
- `assets/logo/firstruck-app-icon-1024.png` — raster iOS source

Rules:

- Keep clear space of at least one endpoint-dot diameter around the mark.
- Minimum digital size: 28 px for the mark, 120 px for the lockup.
- Never recolor the route away from safety orange.
- Do not add shadows, outlines, or slogans inside the mark, or place it over a busy face.
- In the web lab, prefer the SVG mark plus live HTML text for the product name.

## Color

| Role | Name | Hex | Use |
| --- | --- | --- | --- |
| Foundation | Trailhead | `#051609` | immersive dark fields, app icon |
| Brand | Deep forest | `#14331D` | headers, cards, route context |
| Action | Safety orange | `#F45707` | one primary action, route stroke, progress |
| Action pressed | Burnt orange | `#C73700` | pressed/active state |
| Canvas | Field paper | `#F9F3E6` | primary light surface |
| Elevated canvas | Warm white | `#FFFDFA` | sheets and reading surfaces |
| Primary ink | Pine ink | `#131F16` | body and headings on light |
| Secondary ink | Moss ink | `#3C493E` | descriptions and metadata |
| Quiet | Lichen | `#637063` | nonessential labels only |
| Positive | Trail green | `#347D48` | confirmed/safe completion states |

Orange means progress and action, not general decoration. If a screen has an orange primary button, other controls remain forest, paper, or outline.

## Typography

- **Editorial display:** Georgia for the no-download prototype; only short human headlines and result reveals.
- **Interface:** native system sans for choices, guidance, and safety copy.
- Use no more than these two families in the lab.
- Display: `clamp(2.75rem, 10vw, 5.5rem)`, line-height `0.96–1.05`, slight negative tracking.
- Screen heading: `clamp(2rem, 7vw, 3.5rem)`, line-height `1.05–1.12`.
- Body: at least `1rem`, line-height `1.5–1.6`, measure around `34–42ch` inside the phone.
- Inputs are at least 16 px on mobile. Changing plan values use tabular numbers.

## Photography direction

The photography feels premium, real, and inclusive. The supplied reference establishes finish, contrast, and confidence, but First Ruck’s people are at the beginning of an achievable practice.

Always show civilian clothing, ordinary or modest outdoor packs, believable fit and effort, natural skin and body types, accessible parks/greenways before remote landscapes, useful negative space, and a forest/warm-daylight grade.

Avoid oversized tactical packs, plates, camouflage, insignia, weapons, punishment imagery, elite-only physiques, fake instructors, unsafe load positions, running under load, technical terrain, or essential text baked into images.

Generated people are visual models, not employees, experts, coaches, or customers. Never attach a name, credential, quote, rating, or result claim to them.

## Asset catalog and intended use

| File | Best screens | Crop/focal guidance | Alt-text intent |
| --- | --- | --- | --- |
| `photography/hero-beginner-greenway.png` | 1, optionally 22 | Walker in right 45%; dark left field carries copy | `A woman beginning a walk on a tree-lined greenway with a compact backpack.` |
| `photography/community-park-walk.png` | 4 or 9 | Wide/full bleed; keep all three walkers when possible | `Three adults walking together through a park with small backpacks.` |
| `photography/equipment-flatlay.png` | 3, 13 or 14 | Preserve open bag, bottles, shoes, upper-left paper | `An ordinary backpack packed with cushioned water bottles beside walking shoes and socks.` |
| `photography/pack-fit-adjustment.png` | 12 or 15 | Keep torso, straps, and pack visible | `A man adjusting the sternum strap of a compact backpack before walking.` |
| `photography/route-choice-greenway.png` | 16 or 24 | Favor the fork and gentle path; person may stay small | `A walker approaching a choice between two gentle public park paths.` |
| `photography/completion-portrait.png` | 23 or 25 | Keep subject left and bright right field for results | `A man standing calmly after a neighborhood walk with a small backpack.` |

## Provenance

These six photographs were generated specifically for First Ruck with OpenAI’s built-in image generator on 2026-09-03. The user-supplied image was used only as a production-quality and editorial-mood reference. No STOPPR image was used as an input. Do not present generated imagery as documentary evidence of a real First Ruck user.

Final prompts are recorded in `IMAGE-PROMPTS.md` for future consistency.

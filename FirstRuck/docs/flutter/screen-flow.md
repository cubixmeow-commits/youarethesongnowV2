# Screen and state flow

## Current journey

```text
Welcome
  -> Onboarding questions 1–12
  -> Analysis
  -> Starting profile
  -> Today / recommended routes
  -> Route detail
  -> Future tracked session
```

Back navigation preserves answers. A failed recommendation request returns to onboarding with a useful error. Location remains optional: a general place name is valid input.

## Screens

### 1. Welcome

- Brand mark and `First Ruck`
- Eyebrow: `A beginner plan built around you`
- Headline: `Start where you are. Carry forward.`
- Summary: `Tell us what feels comfortable. First Ruck will shape your first weeks and find nearby routes that fit.`
- Primary action: `Build my plan`
- Expectation: `About 4 minutes · You control location access`

### 2. Onboarding

One question per screen. Show Back, brand, `n of 12`, progress, title, help, radio-card answers, validation, and Continue.

| Key | Prompt | Values |
| --- | --- | --- |
| `goal` | What would make rucking worthwhile? | `general-fitness`, `outdoor-time`, `stress`, `event` |
| `weekly_movement` | How often are you active now? | `rarely`, `1-2`, `3-4`, `5-plus` |
| `comfortable_minutes` | How long can you walk comfortably? | `20`, `30`, `45`, `60` |
| `equipment` | What will you carry? | `backpack`, `ruck`, `vest`, `none` |
| `available_load` | What weight do you have available? | `unweighted`, `5-lb`, `10-lb`, `15-lb` |
| `surface` | Which surface feels best? | `paved`, `compacted`, `trail`, `either` |
| `hill_comfort` | How do hills feel right now? | `gentle`, `rolling`, `steep` |
| `sessions_per_week` | How often can you realistically ruck? | `1`, `2`, `3`, `4` |
| `route_type` | How do you like to explore? | `out-and-back`, `loop`, `either` |
| `setting` | What matters most nearby? | `quiet`, `shade`, `facilities`, `scenic` |
| `body_consideration` | Anything you want the plan to protect? | `none`, `knees`, `back`, `feet` |
| `location_label` | Where should we look for routes? | Free text or user-approved approximate location |

Canonical visible labels and help text remain in `FirstRuck/public/assets/app.js`. Store stable values, not visible labels.

### 3. Analysis

Show honest progress language while a recommendation is produced. Reduce Motion replaces route-drawing animation with a quiet static or opacity transition. Never imply that AI has inspected facts it has not received.

### 4. Starting profile

Show level, first-session duration, starting-load guidance, terrain, weekly rhythm, coaching note, and general-fitness disclaimer. Primary action opens Today.

### 5. Today

Show one featured route, its match score, source/data status, distance, climbing, time estimate, summary, and up to two transparent match reasons. Show alternatives below it.

### 6. Route detail

Use a full-screen route on compact iPhone layouts. Include route source, freshness, known facts, unknowns, and pre-departure checks before offering `Start this ruck`.

## Initial Flutter slice

Milestone 1 implements only:

```text
Welcome -> first onboarding question -> Back -> Welcome
```

No answer persistence or network request is required in that slice.

---
type: product-design-contract
status: owner-approved-for-private-development-build-1
updated: 2026-08-30
area: visual-design
owners:
  - CuBiX Meow
  - Brut
foundation:
  - V1 dark cinematic atmosphere
  - V1 generated artwork
  - V1 direct creation workflow
approved: 2026-08-28
---

# V2 Visual Design Direction

CuBiX Meow approved this direction for Private Development Build 1 on 2026-08-28. Example imagery, final font files and measured color values may be refined during implementation without changing the core art direction.

## Visual thesis

**A platinum threshold in a silent black gallery, lit by deep sapphire: dark, tactile, editorial and emotionally charged, with generated artwork providing the spectacle and the interface behaving like quiet album packaging.**

The product should feel created by people who love music and visual art. It should not look like an AI dashboard, science-fiction control panel or generic gradient-based SaaS product.

## What V1 gives V2

Preserve:

- the dark cinematic atmosphere;
- strong generated imagery as the central proof;
- the direct relationship between song, portraits and final artwork;
- bold scale and a sense of reveal;
- simple creation controls;
- the feeling that the user is entering a visual world.

Replace:

- futuristic Orbitron-style typography;
- cyan-purple gradient buttons and glowing borders;
- glass panels and cards around every group;
- neon effects used as a substitute for hierarchy;
- emojis inside primary controls;
- technical AI-console language;
- dense form layouts that expose every option at once;
- repeated boxes, shadows and ornamental interface chrome;
- model/provider names in the customer experience.

## Design concept: The Listening Room

The interface behaves like a private listening room, contemporary gallery, record sleeve and photographer's contact sheet rather than a machine console. The selected intertwined YS monogram is the primary identity. Production usage is governed by `design/BRAND_SYSTEM_YS.md` and `design/PRODUCTION_ASSET_MANIFEST.md`.

Music influence appears through:

- album-sleeve proportions and full-bleed art;
- track-style numbering and restrained liner-note labels;
- editorial credits and metadata placement;
- playhead and progress-line behavior;
- strong rhythm in spacing and repeated alignment;
- occasional concentric-groove or paper-grain texture used very subtly;
- transitions resembling a cover reveal, crossfade or track advance;
- gallery images presented as a collection of finished works, not AI result cards.

Avoid literal musical decoration everywhere. Do not cover the interface with notes, equalizers, vinyl records, headphones, waveforms or concert icons. Music should shape the composition and rhythm, not become clip art.

## Visual hierarchy

### Marketing and onboarding

Treat the first viewport as a poster:

- edge-to-edge genuine V1 or V2 artwork;
- unmistakable `You Are The Song Now` brand;
- approved two-sentence premise;
- one primary action;
- short navigation over the artwork or in a quiet matte header;
- text positioned over a deliberately calm tonal area with a protective contrast treatment.

No hero card, feature grid, stat strip, floating dashboard mockup or decorative AI orb.

### Creation experience

The primary workspace is one continuous creation surface with three clearly numbered movements:

1. `The song`: artist and title search.
2. `The people`: one or two portrait uploads and saved portrait selection.
3. `The direction`: style, quality, orientation, text setting and optional Special instructions.

This respects the approved requirement that song search and portrait upload live together in one coherent experience. Progressive disclosure keeps later decisions quiet until the necessary earlier information exists.

Desktop may use a restrained two-column composition:

- main creation sequence on the leading side;
- sticky visual summary, selected portraits and generation action on the trailing side.

Mobile becomes one natural reading column with an inset, safe-area-aware action region. Controls never touch the viewport edges accidentally.

### Generation

Generation feels like a track being interpreted, not a technical job log:

- one large artwork stage or intentional empty stage;
- a thin playhead-like progress line;
- short honest status copy;
- gentle changes in image grain, light or crop where performance permits;
- no fake percentages or claims about steps that have not actually occurred;
- no technical provider, prompt or model details.

### Reveal

The completed image receives the screen:

- large, nearly full-bleed presentation;
- quiet metadata below or beside it;
- Download as the primary action;
- Create another, Share and Delete as clearly ordered secondary actions;
- portrait thumbnails and safe song identification shown discreetly when permitted;
- no confetti, excessive glow or competing panels.

### Gallery

The gallery resembles a record collection or photographer's contact sheet:

- artwork-first grid with consistent visual rhythm;
- minimal metadata outside the image;
- mixed square, portrait and landscape work handled without aggressive cropping;
- image actions revealed through a clear focus/hover state and always reachable on touch;
- no heavy card background around every artwork;
- full image detail view for sharing, download, regeneration and deletion.

### Account and admin

Account pages use the same typography and surfaces but prioritize clarity over atmosphere. The owner admin is a calm operational workspace with tables, filters and status, not a branded marketing page.

## Color system

Use a restrained dark-first system whose neutral surfaces allow varied artwork to remain dominant.

### Core roles

- `Ink`: near-black with a slight warm or violet character, used for the primary background.
- `Charcoal`: a modestly lifted working surface, used sparingly.
- `Paper`: warm off-white for primary text and occasional editorial sections.
- `Muted ink`: quiet gray-beige for supporting copy and metadata.
- `Platinum`: cool-white hierarchy, hairlines and the YS mark.
- `Deep sapphire`: spatial depth, focus and calm selected states.
- `Cobalt`: one restrained high-value action or active moment.
- Semantic success, warning and error colors remain separate and are never used decoratively.

Directionally, use the following sRGB anchors and convert them into tested semantic tokens where appropriate:

```css
--color-bg: #0A0A0A;
--color-surface: #1B1D21;
--color-text: #E6E7EA;
--color-accent-sapphire: #0D1B3D;
--color-accent-cobalt: #1E4CFF;
```

These are initial art-direction values, not acceptance-tested final values. Build 1 must verify gamut and WCAG 2.2 Level AA contrast for every rendered text/control pair and provide increased-contrast behavior.

Rules:

- one filled accent action per decision view;
- no blue-purple gradient as the main brand treatment;
- no amber, brass or gold as brand chrome;
- sapphire behaves like architectural depth, not a large painted panel;
- cobalt remains rare enough to preserve action hierarchy;
- no accent-colored decorative body text that resembles a link;
- use semantic tokens rather than raw colors in components;
- image color may bleed edge to edge while controls remain inside safe margins;
- translucent overlays must remain readable over the lightest and darkest possible artwork.

## Typography

Replace V1's futuristic display typography with an editorial contrast:

- expressive serif or humanist display face for the brand, promises and major reveal moments;
- clean sans serif for interface controls, body copy, metadata and account/admin surfaces;
- no more than two families;
- self-host WOFF2 files when a selected license permits it;
- no synthetic weights or styles in the final build.

Preferred starting comparison:

- `Instrument Serif` for display and `Inter` or `Geist` for interface text;
- compare against one alternative editorial display face before final selection.

The word `Instrument` is not the reason to choose the font. The rendered letterforms must earn the decision.

Type behavior:

- major display type may be large, elegant and slightly tight;
- body text remains at approximately 16 px with 1.5 to 1.6 line height;
- mobile input text is at least 16 px to prevent iOS zoom;
- headings balance across lines;
- descriptions use comfortable wrapping and remain within a readable measure;
- credits, prices and changing values use tabular numerals where alignment matters;
- labels use natural sentence case; small uppercase liner-note labels are rare and slightly tracked;
- product copy does not use em dashes.

## Surfaces and controls

- Default to cardless layout using spacing, alignment and image scale.
- Use a filled surface only when a group is genuinely one interaction, such as portrait upload or a selected style.
- Use quiet borders for form structure and focus, not glowing decoration.
- Use layered shadows only for real elevation such as menus, dialogs and the artwork reveal.
- Images receive a subtle neutral one-pixel inner outline for consistent edges.
- Rounded corners are moderate and mathematically concentric when surfaces nest.
- Buttons are clear and tactile, with one primary filled action and neutral secondary actions.
- Primary button press may scale to 0.96 with an interruptible 150 ms transition.
- Use one coherent outline icon set with consistent stroke weight and `currentColor`.
- Do not use emojis as interface icons.

## Imagery

Build 1 may use a curated mix of strong V1 sample outputs and later V2 benchmark results.

Rules:

- use images as narrative content, not decorative thumbnails;
- select examples that demonstrate individuals, couples, weddings and varied styles;
- avoid embedded old branding where a clean version is available;
- do not use a generated collage as the hero;
- crop intentionally around text-safe tonal areas;
- never place interface text over a visually busy focal region;
- replace provisional V1 examples with genuine V2 benchmark results before external beta.

## Interaction thesis

Use three restrained motion ideas:

1. **Cover reveal:** the hero or finished artwork enters through a short opacity, blur and 12 px vertical reveal, with semantic chunks staggered by approximately 100 ms.
2. **Track advance:** moving between the three creation movements uses an interruptible horizontal or vertical transition with a persistent numbered progress cue.
3. **Playhead progress:** generation uses an honest thin progress/state line with calm text changes and no ornamental pulsing.

Rules:

- interactive transitions remain interruptible;
- exits are quieter and shorter than entrances;
- high-frequency interactions use only immediate feedback or transitions of 150 ms or less;
- no `transition: all`;
- motion is never the only expression of state;
- honor reduced motion everywhere;
- do not add a motion dependency solely for minor icon effects.

## Responsive behavior

- Start at 320 CSS pixels and an actual iPhone, then test the largest desktop width before intermediate widths.
- Break layouts where their content stops fitting, not at arbitrary device labels.
- Keep text and controls inside logical margins while artwork and background media may bleed.
- Use logical properties so direction-aware layouts remain possible.
- Do not use fixed heights for text or creation sections.
- Keep critical actions reachable when the mobile keyboard opens.
- Account for device safe areas in sticky and bottom actions.
- Preserve usability at 200 percent zoom.

## Accessibility

The visual design inherits the approved WCAG 2.2 Level AA contract:

- strong visible focus treatment that fits the music-led palette;
- keyboard-complete creation and gallery flows;
- useful visible labels rather than placeholder-only forms;
- state never communicated by color or motion alone;
- informative images have useful alternative text;
- reduced-motion and increased-contrast support;
- touch targets aim for at least 44 by 44 CSS pixels;
- artwork overlays are tested against real light and dark images.

## Build 1 screen set

Cursor should establish this visual system across:

1. invitation/sign-in;
2. welcome and value promise;
3. example-artwork introduction;
4. one-page song, portrait and creation workspace;
5. personalized creation summary;
6. Stripe sandbox paywall;
7. generation progress;
8. reveal;
9. gallery and image detail;
10. profile, credits and subscription;
11. simple owner admin;
12. empty, loading, validation, failure and deletion states.

## Design acceptance

Build 1 design is accepted only when CuBiX Meow and Brut agree that it:

- feels music-led rather than AI-generated;
- retains V1's cinematic emotional energy;
- makes the artwork more important than the interface;
- is recognizable as You Are The Song Now without relying on neon gradients;
- works naturally for individuals, couples and weddings;
- feels polished on desktop and an actual iPhone;
- passes the approved accessibility and responsive checks;
- provides a strong enough visual foundation to refine rather than redesign completely.

## Approval status

CuBiX Meow and Brut authorized Private Development Build 1 and directed V2 to take inspiration from V1 while becoming more music-inspired and less visibly AI-generated. CuBiX Meow selected the YS black/platinum/sapphire refinement on 2026-08-30. Production identity and system assets are delivered. Exact overlay strength, cropping and responsive placement remain subject to web/mobile screenshot review. Flutter implementation remains deferred.

# Research Report 01 — Current State, Product Strengths, and Design Debt

**Purpose:** Establish the real starting point before researching tools or visual trends. Later reports must build from this repo state rather than proposing a greenfield product.

## Executive conclusion

YouAreTheSongNow does not need a wholesale technical restart. It already has a workable mobile-first web shell, a coherent dark visual foundation, functioning creation infrastructure, Song DNA analysis, a working Gemini Explore proof-of-concept, gallery/reveal/account flows, and an existing design-documentation base. The premium redesign should therefore be treated as a **product-system migration**: preserve good contracts and working functionality, replace weak interaction architecture and one-off presentation, and continuously formalize the result for Flutter.

The most important opportunity is not another palette pass. It is to reorganize the product around its strongest unique idea: **Song DNA as the creative control surface, with AI removing decisions by default.**

## What is already strong

### Product architecture

The new canonical model is substantially stronger than the current UI:

`Song → Song DNA → Quick Generate`

with progressive disclosure into:

`Song → Song DNA → Explore Options → optional Fine Tune → Generate`

The live Explore test proved that Gemini can convert Song DNA into genuinely differentiated, customer-readable creative directions such as a gothic monumental interpretation, a grounded cinematic interpretation, and a painterly elegiac interpretation. This validates the strategic direction: AI recommendations can replace a fixed generic style catalog as the primary choice surface.

### Mobile foundation

The existing app is already mostly mobile-first rather than desktop-first. The audit found:

- bottom navigation on phone/tablet
- desktop rail at ≥900px
- safe-area handling
- `viewport-fit=cover`
- `100svh`
- 16px mobile inputs to avoid iOS zoom
- progressive disclosure in Create
- phone-specific atmosphere assets

This means the premium work should refine and recompose the system, not throw away responsive foundations.

### Existing visual system

There is already a useful base:

- black / graphite / lacquer surfaces
- platinum + sapphire/cobalt accent language
- Instrument Serif + DM Sans
- OKLCH tokens
- spacing/radius/shadow scales
- CSS-mask icons
- reduced-motion hooks

The system is not yet disciplined enough, but it has a recognizable identity to evolve.

### Existing engineering contracts worth preserving

Preserve unless a later design decision requires a deliberate migration:

- song lookup + derived Song DNA pipeline
- creation draft / snapshot / generation-job architecture
- credit reservation and billing gates
- portrait library
- gallery and image detail contracts
- `/api/v1` boundary
- current worker/generation infrastructure
- Gemini Song DNA analysis and Explore provider path now that it is proven

## Primary design debt

### 1. The product still visually exposes its implementation history

The current Create flow still contains legacy stages and controls created before Song DNA became the primary product model. Examples include the People stage gating Direction, the old `Choose your world` catalog, and temporary compatibility text exposing internal StyleMap names.

Premium redesign work must remove the feeling that two products are stacked together.

### 2. Component architecture is incomplete

The audit found one main layout but no true reusable template/component library. JavaScript imperatively constructs portrait chips, style tiles, gallery tiles, and owner tables. Similar statuses, buttons, labels, confirmation patterns, and content widths are duplicated.

This creates drift and makes a future Flutter port harder because behavior is encoded in DOM-specific implementation instead of component contracts.

### 3. State design is inconsistent

Loading, disabled, retry, offline, success, and empty states are uneven. Premium quality depends heavily on these invisible states. The redesign must specify them at screen and component level, not add them at the end.

### 4. Accessibility debt is known and actionable

High-priority items from the audit include missing Create `<h1>`, gallery empty-state `aria-hidden`, broken showcase focus ring, incomplete keyboard behavior, weak focus management, inconsistent confirmation flows, and some undersized touch targets.

These should be folded into canonical components rather than patched independently forever.

### 5. Desktop expansion is currently more CSS-driven than product-driven

The current tab-to-rail transformation and sticky Create summary work, but the long-term desktop experience needs a deliberately designed adaptive shell: rail + main creative workspace + optional context, rather than simply repositioning phone content.

### 6. Flutter-hostile details are concentrated and removable

Examples include masonry dependencies, hover-only polish, imperative carousel math, backdrop-filter assumptions, and DOM-coupled mega-JS behavior. These do not invalidate the web implementation; they identify where the future design system should become platform-agnostic at the contract level.

## Strategic design constraints to carry forward

1. **Mobile remains canonical.** Desktop is an adaptive expansion.
2. **AI removes decisions by default.** Advanced control is progressive disclosure.
3. **Song DNA replaces generic generator controls whenever possible.**
4. **Artwork is always the hero.** Chrome recedes.
5. **Portrait management moves to Gallery.** Create ultimately uses an active/default portrait without requiring a repeated portrait-management step.
6. **Explore is not a fixed preset catalog.** Recommendations are generated for the selected Song DNA.
7. **No fake progress.** Creative-stage language must correspond to truthful system states or clearly branded non-progress animation.
8. **The web build is a proving ground for Flutter.** Every canonical screen/component needs a portable behavioral specification.

## Recommended redesign posture

Use the existing site as a live prototyping environment, but enforce a strict replacement rule:

- keep working backend/domain contracts
- redesign interaction architecture intentionally
- build canonical reusable UI primitives
- migrate one slice at a time
- remove obsolete legacy UI once its replacement is proven
- capture mobile + desktop screenshots for every major slice
- maintain Flutter notes while decisions are fresh

## Independent reviewer pass

**Potential mistake:** assuming the existing dark brand system is already the right final identity because it is coherent.

**Correction:** the current visual system should be treated as a strong candidate, not a sacred constraint. The later visual-direction research should test whether platinum/blue/black can be elevated through typography, imagery, material, spacing, and motion—or whether a more distinctive interpretation is needed—without discarding brand recognition unnecessarily.

**Potential mistake:** letting the working PHP/JS architecture dictate UX.

**Correction:** backend contracts should constrain feasibility, but the canonical product flow should be designed independently first, then mapped back to implementation.

## Decisions passed to Report 02

Report 02 should research the best AI-assisted design-engineering operating model for this exact situation: a functioning private development product, a persistent repo design OS, GPT acting as design director/critic/system architect, and Cursor/other coding agents implementing and testing focused slices.

## Repo evidence

- `docs/design/process/PREMIUM-SITE-DESIGN-BUILD-PLAN.md`
- `docs/design/audits/ui-audit-2026-08-31.md`
- `docs/design/screens/create-flow.md`
- `docs/design/CURSOR-HANDOFF.md`

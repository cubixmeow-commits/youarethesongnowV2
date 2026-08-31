# Research Report 09 — Tools, Prototyping Surfaces, and Agent Stack

**Builds on:** Reports 01–08 established the product architecture, adaptive model, token/component system, art-direction method, motion philosophy, and quality gates. This report assigns each tool a specific job.

## Executive conclusion

Use the **repo as the source of truth**, ChatGPT as design director/research synthesizer, Cursor as primary implementation agent, and screenshots/live builds as the decisive review surface. Add Figma only where it gives leverage—visual-system comparison, component variables, and rapid cross-screen composition—not as a parallel source of truth.

Optional Codex is most useful as an independent implementation/review agent for bounded tasks, not as another product-decision authority.

## Recommended stack

### 1. Repository design OS — canonical memory

Owns:

- product principles
- screen specs
- component contracts
- semantic tokens
- motion rules
- responsive rules
- Flutter mapping
- research reports
- decisions/ADRs
- current Cursor handoff

This is the durable layer all agents read.

### 2. ChatGPT — design director / research / critic

Best use:

- research synthesis
- product architecture
- interaction alternatives
- visual-direction briefs
- screenshot critique
- component-system architecture
- accessibility/adaptive critique
- writing exact Cursor handoffs
- deciding which result becomes canonical

Avoid using ChatGPT only to produce huge implementation prompts. The highest value is judgment and synthesis.

### 3. Cursor Cloud Agents — primary repo execution

Cursor's current Cloud Agent documentation describes long-running isolated environments that can implement, run tests, and return screenshots/logs/proof. That matches this project well.

Use for:

- repo archaeology
- implementation slices
- responsive CSS/JS work
- API integration
- automated tests
- browser screenshot evidence
- debugging provider/deployment issues
- updating the handoff

Require explicit stop points before moving to the next major screen.

### 4. Figma — targeted visual exploration

Best use:

- compare Candidate A/B/C visual systems on identical screens
- explore typography, spacing, surfaces, image treatment
- define component properties/variants
- test compact/expanded compositions side by side
- prototype key motion intent if code prototyping is slower

Do not duplicate all final documentation there. Locked decisions return to repo JSON/docs.

### 5. Live web build — interaction prototype and proving ground

Because the current site is private with no users, working code is often more valuable than high-fidelity static mockups after structural decisions are clear.

Use live code to validate:

- actual mobile ergonomics
- network/loading states
- Song DNA/Explore behavior
- generation timing
- responsive adaptation
- keyboard/focus behavior

### 6. Screenshot review packs

For every canonical slice, capture:

- 320–375px
- current iPhone-class width
- medium/tablet or narrow desktop
- desktop 1280–1440
- important loading/error/selected states

Store review packs in a predictable path and link them from the handoff.

### 7. Lightweight component lab

Instead of immediately adding Storybook to a PHP/vanilla-JS project, build a small internal `/dev/components` or static dev-only component-lab page once the first canonical primitives exist.

It should show:

- buttons/states
- selection cards/states
- Song DNA chips/cards
- fields
- error/status patterns
- navigation
- artwork/portrait tiles
- sheets/dialogs

This gives Cursor and GPT a single visual regression surface.

### 8. Optional Codex — second opinion and parallel engineering

OpenAI currently positions Codex for long-running, parallel agentic software work. In this project, use it selectively for:

- independent code review of a major Cursor refactor
- accessibility audit against a completed slice
- test-generation pass
- Flutter mapping/prototype when the port begins
- parallel low-overlap tasks

Do not run multiple agents on the same files without an explicit merge plan.

## Tool-selection rule

Choose the cheapest/fastest surface that answers the design question:

- **Does the flow make sense?** wireframe/spec first
- **Does the identity look right?** Figma or controlled HTML prototype
- **Does interaction feel right?** live web code
- **Does it survive states/responsiveness?** live build + screenshots/tests
- **Can it port to Flutter?** component/state contract + Flutter prototype when needed

## Research/reference workflow

When looking at premium apps/sites:

1. collect references for a specific interaction, not vague inspiration
2. identify the principle being borrowed
3. do not copy brand-specific visual assets
4. test the principle inside YouAreTheSongNow's own reference screen
5. document the accepted rule, then discard the reference dependency

## Independent reviewer pass

**Risk:** adding Figma creates a split-brain system.

**Countermeasure:** repo is canonical. Figma is a visual workbench. Locked tokens/components are synchronized back immediately.

**Risk:** working directly in code biases toward what is easy to implement.

**Countermeasure:** major interaction architecture and visual direction must be specified/compared before code lock.

**Risk:** too many agents increase inconsistency.

**Countermeasure:** one design authority, bounded agent roles, persistent rules, and a single handoff baton.

## Decisions passed to Report 10

Report 10 should synthesize all nine reports into the final recommended structure/process for the premium redesign and Flutter transition, including phases, deliverables, quality gates, and the exact next desktop-session work.

## Sources

- Cursor Cloud Agents: https://prod.cursor.com/help/ai-features/background-agents
- OpenAI Codex: https://openai.com/codex/
- OpenAI Codex multi-agent app: https://openai.com/index/introducing-the-codex-app/
- Figma design systems/variables/component properties: https://www.figma.com/design-systems/ and help.figma.com
- Storybook documentation/testing model: https://storybook.js.org/docs/

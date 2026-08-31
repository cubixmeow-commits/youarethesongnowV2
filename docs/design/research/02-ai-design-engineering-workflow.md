# Research Report 02 — AI-Assisted Design Engineering Workflow

**Builds on:** Report 01 established that this is a product-system migration, not a greenfield rebuild.

## Executive conclusion

The strongest workflow is a **persistent, repo-native design operating system with short, inspectable agent loops**. GPT should own intent, hierarchy, critique, and canonical decisions; Cursor should own repo inspection, implementation, automated verification, and evidence; long-running coding agents should be given bounded slices with explicit stop conditions. The repository—not chat history—must hold the durable state.

This mirrors current agent-tool guidance: Cursor project rules are version-controlled and intended to persist context across completions, while Cloud Agents are designed for long-running coding tasks with isolated environments, tests, screenshots/logs, and proof of work. OpenAI's current agent guidance similarly emphasizes agent-friendly repositories, automated tests, guardrails, and orchestration rather than relying on one huge prompt.

## The recommended operating model

### 1. Separate design authority from implementation authority

**GPT / design director responsibilities**

- define user goal and product intent
- decide what information deserves prominence
- remove unnecessary choices
- compare visual directions
- critique screenshots
- define component semantics and state behavior
- maintain the design OS
- decide when a slice becomes canonical

**Cursor / implementation engineer responsibilities**

- inspect actual repo/contracts before coding
- map spec to existing routes/services/state
- identify reuse vs migration
- implement only the approved slice
- add tests
- capture responsive evidence
- record deviations and unresolved questions
- update the handoff

This prevents a common AI failure mode: the coding agent silently becoming product designer because the prompt is underspecified.

### 2. Keep durable context in version control

Use three layers:

1. **Canonical design OS** under `/docs/design/`
2. **Cursor rules / AGENTS guidance** for persistent engineering behavior
3. **`CURSOR-HANDOFF.md`** as the short-lived baton between current design review and next implementation task

The handoff should stay concise. Detailed knowledge belongs in permanent docs, not an endlessly growing handoff.

### 3. Slice tasks by reviewable outcome

Bad task:

> Redesign the whole site to feel premium.

Good task:

> Replace the Explore result interaction with canonical selection cards, hide internal compatibility metadata, add selected/recommended states, preserve provider behavior, produce phone + desktop screenshots, and stop for review.

Each task should have:

- intent
- files/docs to read first
- non-goals
- functional constraints
- visual acceptance criteria
- state list
- test requirements
- evidence requirements
- stop condition

### 4. Use an explicit design loop

Canonical loop:

`Research → Product intent → Structural wireframe → Critique → Visual direction → Component spec → Implementation → Automated verification → Screenshot review → Refine → Lock`

Do not collapse wireframing, visual styling, implementation, and QA into one agent run.

### 5. Use independent critique before locking

For important screens, run a separate critic pass after the first design/implementation result. The critic should not be asked to redesign immediately. It should identify:

- hierarchy problems
- unnecessary controls
- visual imbalance
- confusing copy
- interaction ambiguity
- mobile ergonomics
- desktop wasted space
- accessibility issues
- platform-portability risks

Only then generate the revision brief.

### 6. Keep visual evidence mandatory

For every major slice, require at minimum:

- narrow phone (~320–375 CSS px)
- current iPhone-class width
- desktop (~1280–1440)
- loading/error/selected state where relevant

Text-only completion reports are insufficient for design work.

### 7. Prefer controlled experiments over global changes

When choosing a major visual direction, render the **same six reference screens** under 2–3 systems. This makes comparison meaningful and prevents selection based on a moodboard that fails in real UI.

Reference set:

- Create Home
- Song DNA selector
- Explore Options
- Generation
- Reveal
- Gallery

### 8. Formalize agent guardrails

Cursor's own documentation notes that rules are injected as persistent context because models do not retain memory between completions. Add project rules that state:

- mobile is canonical
- Song DNA is the primary creative control surface
- AI removes decisions by default
- no generic AI-dashboard styling
- no broad redesign without a screen spec
- preserve API contracts unless task says otherwise
- use design tokens, not one-off values
- every UI component must specify focus/loading/error/disabled states
- update handoff and screenshot evidence before stopping

## Recommended task hierarchy

### Level A — Research tasks
No production code. Produce evidence and decisions.

### Level B — Design-spec tasks
Produce screen/component specs and prototypes. No backend changes.

### Level C — Implementation slices
One coherent feature/screen/component family.

### Level D — Integration passes
Cross-screen consistency, navigation, responsive behavior.

### Level E — Quality gates
Accessibility, performance, screenshot regression, Flutter handoff.

This hierarchy prevents architectural work from being buried inside CSS edits.

## Tool allocation

### ChatGPT / Work
Best for deep research synthesis, design-system documents, comparison matrices, product architecture, and long-form review artifacts. Current OpenAI product guidance positions Work for longer multi-step research/deliverable work and Codex for software-development execution.

### Cursor Cloud Agents
Best for long-running repo-grounded implementation, bug fixing, test execution, screenshots/logs, and proof-of-work. Cursor documents Cloud Agents as isolated VMs that can run for long periods and report back artifacts.

### Codex (optional secondary implementation/critique tool)
Useful as a second implementation or code-review perspective, particularly when we want independent verification of Cursor's work or parallel bounded tasks. OpenAI's current Codex materials emphasize multi-agent supervision and repositories engineered with tests and guardrails.

### Figma / prototyping layer
Useful once visual direction exploration begins, especially for component variables/modes and side-by-side visual-system comparison. It should not become the only source of truth; code-facing semantic tokens must remain repo-owned.

## Independent reviewer pass

**Risk:** too much documentation can become bureaucracy.

**Countermeasure:** every permanent document must answer a recurring implementation question. If a doc cannot change a future decision, merge or delete it. `CURSOR-HANDOFF.md` should point to canonical docs rather than copy them.

**Risk:** direct-to-main agent work can blur review boundaries.

**Countermeasure:** because the site is private with no users, direct `main` is acceptable for speed, but commits must remain small and reversible. High-risk architecture experiments should still use branches.

**Risk:** agents may optimize for satisfying the prompt instead of product quality.

**Countermeasure:** screenshot review and explicit five-axis acceptance rubrics are mandatory. Passing tests is necessary but not equivalent to design success.

## Decisions passed to Report 03

Report 03 should define what "premium mobile" actually means for YouAreTheSongNow: ergonomics, hierarchy, interaction density, progressive disclosure, platform familiarity, image dominance, and accessibility. It must avoid reducing premium to glass effects, gradients, or animation.

## Sources

- Cursor Rules: https://prod.cursor.com/docs/rules
- Cursor Cloud Agents: https://prod.cursor.com/help/ai-features/background-agents
- OpenAI Codex orchestration / agent-friendly repos: https://openai.com/index/open-source-codex-orchestration-symphony/
- OpenAI Codex app / multi-agent supervision: https://openai.com/index/introducing-the-codex-app/
- ChatGPT Work and Codex: https://help.openai.com/en/articles/20001275

# NEXT DIRECTIVE — Round 013 complete, awaiting GPT review

**Date:** 2026-09-01  
**Working branch:** `main`  
**Published to:** `main` at pending  
**Status:** Mobile Generate action repair complete — stop for GPT/owner review

## Root cause

The final CTA was inside `[data-summary-actions hidden]` and only appeared after a successful manual Review POST. On mobile the Overview card sits below the direction column, so the button was off-screen; Quick Generate used timer bridges that raced the async review path.

## Repair

- Always-visible `data-generate-bar` with **Generate image** label
- Mobile fixed bar above bottom navigation + safe-area inset
- Disabled button + `data-generate-hint` when requirements missing (never silently hidden)
- Auto server review via `scheduleGenerationReview()`; Quick Generate awaits `YatsnCreate.prepareAndReview()`
- Duplicate-submit lock + recoverable failure restores actionable state

## Verification

- `php tests/run.php`: **1207 passed, 0 failed**
- Evidence: `design/review/round-013/` (320 + 390 widths, disabled/pending/error states)

Do not deploy. Do not resume broader design work.

---

# NEXT DIRECTIVE — Round 013 Blocking Mobile Generate Action Repair

**Date:** 2026-09-01  
**Working branch:** `main`  
**Priority:** Blocking functional defect  
**Scope:** Repair the existing Create completion action; no broader redesign or deployment

## Observed failure

On the live mobile Create flow, after the owner selects **Generate for me** and completes the visible choices, the review/Overview state contains no accessible final Generate action. The user cannot submit the image generation.

Observed on an iPhone-width viewport:

- song: Seven Pillars of Wisdom — Sabaton;
- one person selected;
- style: Cinematic Realism;
- square selected;
- optional special instructions visible;
- Overview card visible;
- fixed bottom navigation visible;
- no reachable **Generate image** button.

Treat this as a real product-blocking defect, not merely a screenshot adjustment. Determine whether the action is conditionally omitted, below an incomplete scroll region, obscured by the fixed bottom navigation/safe area, or disabled without an explanation.

## Required behavior

1. Quick Generate / **Generate for me** remains the default low-decision path.
2. Once all genuinely required inputs are present, expose one unmistakable primary action labeled **Generate image**.
3. On mobile, the action must remain reachable above the fixed bottom navigation and iOS safe area. Prefer the smallest robust solution consistent with the existing Luminous Night Studio system; a sticky mobile action region is appropriate if it does not duplicate actions or cover content.
4. If required data is missing, keep the action visible but disabled and provide concise nearby text identifying exactly what is missing. Never silently remove the action.
5. If an enabled submit is tapped:
   - prevent duplicate submission;
   - show immediate pending feedback;
   - use the existing generation endpoint, credit, queue, error, and refund contracts;
   - preserve entered selections and instructions on recoverable failure;
   - do not add new generation or credit behavior.
6. The manual-control path must continue to work, but do not add new Explore Options/Fine Tune design in this repair.
7. Desktop must retain the same premium app-like language and a reachable action without unnecessary sticky duplication.

## Audit before editing

Trace the actual state machine and markup from direction-mode selection through Overview and submission. Identify the exact root cause in the Cursor report. Check:

- conditional rendering for Generate for me versus manual control;
- form/button placement and form ownership;
- `hidden`, disabled, and validation conditions;
- scroll-container height, bottom padding, fixed navigation, and `env(safe-area-inset-bottom)`;
- keyboard and textarea interaction;
- return/resume state;
- pending, error, insufficient-credit, and success states.

## Verification

Add regression coverage that fails against the current defect and verifies:

- Generate for me + valid required data renders one enabled **Generate image** control;
- missing requirements render the same visible disabled control plus reason;
- the action remains within the scrollable/reachable area at 320×640, 390×844, and an iPhone safe-area viewport;
- the bottom navigation does not cover the action or final Overview content;
- textarea keyboard open/close does not permanently hide or displace it;
- one activation produces one request;
- pending prevents duplicate requests;
- recoverable API failure restores an actionable state without losing inputs;
- manual-control path still has a valid submit;
- keyboard focus, 200% zoom, reduced motion, and increased contrast remain usable.

Capture sanitized review evidence at `design/review/round-013/` for the valid Quick Generate state at 320 and 390 widths, plus disabled/missing-requirement, pending, and recoverable-error states. The evidence must show the bottom navigation and Generate action together.

Run the full suite and relevant syntax/lint checks. Commit implementation, tests, evidence, and updated handoffs to `main`, then stop for GPT/owner review. Do not deploy and do not resume broader design work.

---

# NEXT DIRECTIVE — Round 012.2 blocked; awaiting credentials + GPT review

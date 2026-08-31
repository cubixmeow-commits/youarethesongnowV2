---
type: future-ui-insertion-map
status: documentation-only
updated: 2026-08-30
area: current-web-mobile
phase: post-build-1
---

# Future Upscale, Print and T-Shirt UI Insertion Map

This file reserves sensible locations in the current web/mobile experience. It does not authorize commerce controls, provider integrations, pricing, checkout or fulfillment.

## Reveal

- Primary existing action remains Download.
- Future `Upscale` belongs directly below Download when the delivered image is not already print-ready.
- Future `Order print` and `Order T-shirt` belong in one `Make it physical` secondary group after upscale readiness is known.
- Do not mix permanent Delete into that group.

## Gallery

- Do not put three new commerce icons on every artwork tile.
- Future entry point is one `More` action per artwork opening the same action surface used by Reveal.
- Show upscale state on the item only when a real background job exists.

## Phone

- Use a safe-area-aware bottom action sheet launched from Reveal or Gallery item actions.
- Rows require at least 48px height and a clear text label; icons are supplementary.
- Poster and T-shirt previews open as their own focused step after the user chooses a product.

## Desktop

- Place the commerce group in the Reveal metadata/action column below Download/Share.
- Product previews may use a modal or dedicated route once the product contract exists. Do not squeeze mockups beside the artwork stage.

## Upscale progress

- Use the existing asynchronous job language: queued, processing, completed, failed.
- Progress belongs in Gallery/Reveal and may continue after the browser closes.
- Do not show a fabricated percentage or remove the original image while the upscale is running.

## Future backend contracts

The later functional phase will need owner-approved contracts for:

- print-readiness and source-resolution rules;
- upscale quote, idempotent submission, job status and retry/refund behavior;
- print/T-shirt catalog, variants, pricing, tax and shipping;
- product mockup generation and approval;
- order creation, payment, fulfillment status, cancellation and support;
- private media transfer to replaceable providers;
- audit, cost, deletion and retention behavior.

## Accessibility

- Controls require labels, keyboard operation, visible focus and non-color status.
- Product previews need useful alternative text.
- Action sheets/dialogs trap and return focus correctly.
- Errors preserve the selected artwork/product configuration.

## Current boundary

Round 008 must not render any of these future controls. Preserve clean space in Reveal and the mobile action architecture without implying that commerce exists today.

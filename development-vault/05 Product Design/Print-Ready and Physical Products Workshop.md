---
type: product-workshop
status: owner-approved-deferred
updated: 2026-08-28
area: print-and-merchandise
owners:
  - CuBiX Meow
  - Brut
source: V1 print and upscaling recovery
---

# Print-Ready and Physical Products Workshop

## Why this workshop reopened

The first-build workshop accidentally omitted an important long-term product goal: turning a completed gallery image into a high-resolution print master suitable for posters, art prints and T-shirts, with physical-product ordering as the intended destination.

This is a functionality requirement, not a commitment to any V1 provider. Upscaling, print preparation and fulfillment must remain behind replaceable provider adapters and be selected from current quality, cost, privacy, reliability and product-range evidence.

Private Development Build 1 is authorized, but this functionality remains deferred. CuBiX Meow and Brut decided on 2026-08-28 that it follows the first complete V2 web build and does not block the initial Cursor implementation.

## Recovered V1 evidence

V1 contains real but prototype-grade evidence of the intended workflow:

- A gallery action submitted an owned image to Replicate's `philz1337x/crystal-upscaler` model with a `scale_factor` of 3.
- The returned PNG was downloaded, uploaded to Backblaze B2 and inserted as a separate gallery item.
- Plan configuration declared a separate monthly upscale-credit allowance.
- The gallery interface inconsistently described the action as both 3x and 6x.
- The gallery handler did not visibly enforce or deduct the declared upscale credits.
- Debug output exposed an API-token prefix, source URL, request and provider response to the interface. This behavior must not be carried into V2.
- A T-shirt mode flag was accepted and stored with generation jobs.
- A standalone PHP/GD tool removed green-screen backgrounds and exported transparent PNG files.
- A separate Imagick tool feathered edges for DTG/DTF-ready transparent PNG exports.
- The `poster` folder contained demonstration clients for Printful and Gelato, including example order and webhook flows. It was a proof of concept, not a production store.

V2 should preserve the product intent while rebuilding the implementation, security, accounting and user experience.

## Provider-neutral functional target

A completed gallery image can enter a separate print-preparation workflow:

```text
Gallery image
  -> choose poster/print or T-shirt preparation
  -> choose supported product shape and size
  -> create immutable preparation job
  -> upscale or regenerate to the required pixel dimensions
  -> crop, bleed, safe-area and transparency preparation
  -> automated resolution, unauthorized-text, logo and artifact preflight
  -> user preview and approval
  -> print-ready master
  -> download or physical-product order, according to the approved release scope
```

The original gallery image remains unchanged. Every print master is a derived asset with its own dimensions, provider/model provenance, cost, status and deletion relationship.

## Minimum print-master rules

- Use exact output pixel dimensions from the selected product template, not an arbitrary DPI label applied to a small file.
- Preserve the original composition where possible and show the actual crop before the user approves it.
- Enforce product-specific bleed and safe areas.
- Keep poster and art-print masters in a high-quality opaque format suitable for the selected fulfillment workflow.
- Keep T-shirt masters as true transparent PNG files when transparency is required.
- Do not assume a green-screen remover is sufficient for production garments; benchmark transparent generation, background removal and edge cleanup.
- Check faces, hands, unauthorized or illegible text, logos, borders, seams, transparency edges, pixel dimensions and severe generation/upscale artifacts before approval.
- Do not treat upscaling as proof that the underlying image is print-quality.
- Never show provider tokens, requests, internal URLs or debug payloads to users.

## Copyright and trademark risk boundary

The design can use uncopyrightable ideas, themes, moods, general concepts, colors and familiar symbols as ingredients for a new visual work. That principle is useful but is not a complete commercial-clearance rule.

For V2:

- Do not quote lyrics or retain close paraphrases of distinctive lyric expression in prompts or creative artifacts.
- Do not reproduce a song's detailed sequence of protected narrative expression merely by changing it from words to an image.
- Do not copy album artwork, music-video frames, stage designs, trademarked symbols, band logos, recognizable merchandise designs or a public figure's likeness.
- Text is allowed when it fits the composition and is deliberate, readable and reviewed. It must be original, user-owned, licensed, public-domain or otherwise lawful.
- The creation page includes an optional `No text in image` checkbox. When selected, the prompt compiler and output preflight must enforce a completed image with no readable words or lettering.
- When the checkbox is not selected, text may appear only under the same originality, legibility and clearance rules; disabling the constraint does not permit copyrighted or misleading text.
- You Are The Song Now branding and owner-authored original titles or phrases may be added deliberately when the owners approve their placement.
- Do not print copyrighted lyrics, unauthorized artist/band branding, third-party logos or other protected expression on the product.
- Song titles and short phrases are not automatically protected by copyright, but their use can still create trademark, endorsement, attribution or unfair-competition risk. Treat them as a separate clearance decision rather than assuming they are safe.
- Use an original descriptive title for the customer-facing print asset.
- Do not imply that a band, songwriter, label or publisher approved, sponsored or collaborated on the product.
- `Inspired by` wording may reduce confusion when accurately used, but it is not an automatic copyright or trademark defense.
- Run an output preflight for accidental, illegible, infringing or misleading text and marks. The supplied V1 physical example includes generated sign and clothing text; text of that kind may remain only when it is intentional, aesthetically useful and cleared, otherwise it must be repaired or removed before printing.
- Provider safety acceptance is not legal clearance.
- Public commercial merchandise remains subject to qualified intellectual-property review and the approved lyrics/licensing gate.

Authoritative U.S. references:

- U.S. Copyright Office Circular 33, ideas and other uncopyrightable material: https://www.copyright.gov/circs/circ33.pdf
- U.S. Copyright Office fair-use overview and four-factor analysis: https://www.copyright.gov/fair-use/more-info.html
- Copyright Act definitions and derivative-work rights: https://www.copyright.gov/title17/92chap1.html
- U.S. Supreme Court, *Andy Warhol Foundation v. Goldsmith*, on commercial use and why new meaning or a different visual treatment is not automatically fair use: https://www.supremecourt.gov/opinions/22pdf/21-869_87ad.pdf
- USPTO overview of source confusion: https://www.uspto.gov/trademarks/search/likelihood-confusion
- U.S. Copyright Office AI copyrightability report: https://www.copyright.gov/ai/Copyright-and-Artificial-Intelligence-Part-2-Copyrightability-Report.pdf

## AI-output ownership boundary

Provider terms must permit commercial use, but provider permission alone does not guarantee copyright protection in the output. Current U.S. Copyright Office guidance requires sufficient human-authored expressive contribution; prompting alone may not establish copyright in every image. Preserve owner/user selection, creative inputs, revisions and human editing provenance where commercially useful, and obtain qualified advice before making exclusivity claims.

## Physical-commerce consequences

Adding physical goods reopens decisions that were intentionally excluded from the digital-subscription tax analysis:

- sales tax and registrations for tangible products;
- separate one-time product pricing and margin;
- shipping addresses and additional personal data;
- fulfillment, tracking and delivery webhooks;
- damaged, lost, misprinted and returned-order policies;
- customer approval of crop, size and product mockup;
- product deletion versus legally required order records;
- content review before sending a file to a printer;
- provider privacy, retention and training terms;
- support responsibilities and chargebacks.

Do not activate physical ordering merely by connecting a provider. It requires an approved commerce, tax, privacy, support and acceptance contract.

## Decisions to make one at a time

1. First-release boundary: print-ready preparation/download only, or complete physical ordering in the first web build.
2. Initial products: poster/art print, T-shirt, or both.
3. Upscale accounting: included in generation tier, separate credits, or a one-time print-preparation charge.
4. Product sizes, aspect ratios and crop behavior.
5. T-shirt preparation: transparent isolated design, rectangular full-front artwork, or both.
6. User approval and no-refund boundary for an approved print file.
7. Fulfillment-provider benchmark criteria and candidate shortlist.
8. One-time checkout, tax, shipping, refunds and support rules.
9. Gallery, deletion and order-history behavior.
10. Print-specific quality and end-to-end acceptance tests.

## Current decision status

CuBiX Meow and Brut approved the release boundary on 2026-08-28:

- First complete and approve the core V2 web application defined by the First Build Feature Contract.
- Do not include gallery upscaling, print-master preparation, poster ordering, T-shirt preparation or physical fulfillment in that first complete build.
- Begin the provider-neutral print and merchandise phase after the core V2 web build works and receives owner approval.
- Preserve this workshop as the starting contract for that follow-on phase.

The first-release boundary is resolved. Product, provider, pricing, tax, shipping, return and acceptance decisions remain for the later print phase.

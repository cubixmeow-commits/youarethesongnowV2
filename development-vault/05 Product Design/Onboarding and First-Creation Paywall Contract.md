---
type: product-design-contract
status: owner-approved
updated: 2026-08-28
area: onboarding-paywall
owners:
  - CuBiX Meow
  - Brut
approved: 2026-08-28
source: Onboarding and First-Creation Paywall Workshop
---

# Onboarding and First-Creation Paywall Contract

## Status

CuBiX Meow and Brut approved the onboarding philosophy, complete screen flow, recovery behavior and first-build copy foundation on 2026-08-28. It is authorized for Private Development Build 1. Final V2 example files, the exact monthly credit allowance and qualified legal language remain external-launch dependencies governed by their respective checklist gates, not open onboarding-design decisions.

## Approved opening promise

> You are the song now.
>
> A meaningful song becomes a cinematic world, with you and the people you love at the heart of its story.

Product copy must not use em dashes.

## Approved philosophy

Onboarding is the beginning of the value and subscription experience, not a feature tutorial. It should understand what the user wants, build anticipation for a personally meaningful result and make the first-creation paywall feel like the natural next step.

- Sell the emotional outcome, not the technology.
- Make the user's first creation the onboarding instead of adding a separate artificial questionnaire.
- Ask only for information that changes the artwork, recommendation, experience or later message.
- Begin with easy, emotionally relevant choices and progressively build commitment.
- Reflect the user's choices back before asking for payment.
- Use real examples and genuine proof only. Never invent ratings, reviews, user counts or testimonials.
- Treat onboarding and the first-creation paywall as one measurable funnel.
- Preserve the complete prepared creation if payment is cancelled or fails.
- Test the structure before spending effort on minor visual or wording variations.
- Use web-beta evidence to improve the later Flutter/iOS flow rather than copying the web screens mechanically.

## Approved high-level flow

1. Opening promise.
2. A small selection of exceptional artwork examples.
3. Song and artist entry.
4. Upload or select one or two portraits.
5. Choose style, orientation and quality.
6. Optionally require no text in the generated image.
7. Add optional Special instructions.
8. Review a personalized creation summary.
9. See the $20 monthly membership paywall before image generation begins.
10. After confirmed payment, generate the image and deliver the completed reveal.

This is a high-level sequence. The workshop may combine compatible steps on one responsive page as long as the emotional progression and approved paywall placement are preserved.

## Approved example-artwork mix

Show three exceptional examples, one at a time:

1. A romantic couple.
2. An individual cinematic portrait.
3. A wedding or celebration image.

The V1 archive's `sample_images` folder is an approved candidate source during design. Its collection contains strong cinematic references, but it leans dark and surreal and includes images with old branding or generated text. Final launch examples should be genuine V2 outputs created with the approved production pipeline. They must represent the quality, portrait fidelity, style range and compositions users can realistically receive. Do not use an example with unwanted text, old branding, a visible artist or band name, or misleading quality.

## Approved personalization boundary

Do not add a separate personality, preference or marketing quiz to the first build. Personalization comes from the inputs required to create the artwork: song and artist, one or two portraits, style, orientation, quality, the optional no-text constraint and optional Special instructions. Do not add onboarding questions unless an answer will materially change the artwork or immediate experience.

## Approved screen grouping

Use four simple experience moments:

1. A welcome screen with the opening promise and three artwork examples.
2. One creation page containing song and artist, portraits, style, orientation, quality, the optional no-text constraint and optional Special instructions in that order.
3. A personalized creation summary followed by the first-creation paywall.
4. Generation progress followed by the completed image reveal.

On mobile-width web, the creation sections stack vertically but remain one page. The portrait section becomes available after the song lookup succeeds or returns an approved fallback. Responsive grouping may change presentation without changing the order, required inputs or paywall placement.

Welcome-screen primary action: `Create your image`

## Approved generation-progress treatment

After payment and submission, show a subtle cinematic animation with short messages connected to real generation stages. Do not show a fabricated percentage or countdown.

Initial stage language:

1. `Finding the heart of your song`
2. `Building your cinematic world`
3. `Bringing you into the scene`
4. `Adding the final details`

The implementation may map or repeat messages as needed, but must not claim that a stage has occurred when it has not. If generation takes longer than expected, say: `Your image is still being created. You can leave this page and find it in your gallery when it is ready.`

## Approved personalized creation summary

Before the paywall or generation, show a compact review card.

Headline: `Your cinematic world is ready to create`

Show:

- song and artist;
- small round thumbnails of the selected portrait or portraits;
- selected style;
- orientation;
- quality and its credit cost;
- Special instructions only when provided.

Primary action: `Create my image`

The summary must not show a fabricated preview or imply that an image has already been generated. For an unsubscribed user, the primary action opens the membership paywall. For a subscribed user with enough credits, it submits the locked generation job after any required final confirmation.

## Approved paywall foundation

Headline: `Your song is ready. Step into its world.`

Supporting promise: `Create original cinematic art with you and the people you love at the heart of the story.`

Plan: `You Are The Song Now Membership`

Price: `$20 per month`

Benefit order:

1. Monthly credits for creating personalized artwork.
2. Low, medium and high-quality options.
3. Save, download and share creations.
4. Cancel anytime.

Renewal disclosure: `Your subscription renews monthly until cancelled.`

Primary action: `Continue to secure checkout`

The paywall must show the exact monthly credit allowance before any live offer. That number remains pending provider and cost benchmarks. Do not expose placeholder copy to a paying user. Stripe Checkout provides the final secure payment and recurring-charge confirmation.

## Approved trust and disclosure placement

Place each notice at the moment it becomes relevant instead of presenting a large legal interruption inside the creative flow.

- Account activation requires acceptance of the Terms of Service and Privacy Policy.
- Beneath portrait upload, show: `Only upload photos you have permission to use. Your photos remain private and are processed only to create your artwork.` Link to the relevant privacy and portrait terms.
- The paywall clearly shows the recurring monthly price, exact included credits, automatic renewal, cancellation terms and links to membership terms and the Privacy Policy.
- Account settings keep privacy controls, image deletion, permanent account deletion and subscription management easy to find.
- Complimentary reviewers must accept the same Terms of Service, Privacy Policy and portrait rules even though they do not complete Stripe Checkout.

Final legal wording remains subject to qualified review and must accurately disclose third-party AI processing, retention and deletion behavior.

## Approved recovery behavior and messages

Every recoverable problem preserves all valid creation inputs. Do not make the user repeat completed work.

### Song not found

Message: `We could not find enough reliable information about that song. Check the artist and title, or choose another song. No generation credits were used.`

Allow the user to edit the artist and title immediately.

### Portrait upload problem

Message: `We could not upload this photo. Choose another photo or try again.`

Preserve the song and all other completed creation choices.

### Checkout cancelled

Message: `Your creation is saved. You can continue whenever you are ready.`

Return the user to the preserved summary and paywall entry point.

### Payment failed

Message: `Your membership was not started. Your creation is still saved.`

Primary action: `Try payment again`

### Generation failed

After configured automatic technical retries fail, show: `We could not deliver a usable image. Your credits were returned. You can try again.`

Primary action: `Try again`

Only state that credits were returned after the credit ledger confirms the release or refund.

## Approved screen-by-screen copy foundation

### Welcome

Headline: `You are the song now.`

Supporting promise: `A meaningful song becomes a cinematic world, with you and the people you love at the heart of its story.`

Show the approved three-example artwork sequence.

Primary action: `Create your image`

### Creation page introduction

Headline: `Create your cinematic world`

Supporting copy: `Choose a song, add your portraits, and shape the world you want to enter.`

### Song section

Heading: `Choose your song`

Fields:

- `Artist or band`, with placeholder `Enter the artist or band`
- `Song title`, with placeholder `Enter the song title`

Primary action: `Find my song`

Searching state: `Finding your song...`

Found state: `We found your song and will use its themes and feeling to inspire your image.`

Reliable-context fallback: `We found reliable information about your song and will use its themes and feeling to inspire your image.`

Do not display lyrics or add a lyrics-confirmation step.

### Portrait section

Heading: `Who belongs in this story?`

Supporting copy: `Add one or two clear portraits. We will use their faces to place them naturally inside your cinematic world.`

Actions:

- `Upload a portrait`
- `Choose a saved portrait`
- `Add another person`

Permission and privacy note: `Only upload photos you have permission to use. Your photos remain private and are processed only to create your artwork.`

### Style section

Heading: `Choose your world`

Supporting copy: `Select the visual style that will lead your image.`

Show the approved active style choices.

### Quality section

Heading: `Choose image quality`

- `Low`, with supporting copy `Uses fewer credits`
- `Medium`, with supporting copy `Recommended`
- `High`, with supporting copy `Our most advanced option`

Show the exact credit cost beside each choice once the approved economics are available.

### Format section

Heading: `Choose a format`

Choices:

- `Square`
- `Portrait`
- `Landscape`

### Text preference

Optional checkbox: `No text in image`

Supporting copy: `Choose this if you want the finished artwork to contain no words or lettering.`

The checkbox starts unchecked. When it is selected, the prompt compiler treats it as a required constraint and the output check rejects or repairs any result containing readable text before delivery.

When it is not selected, fitting text may appear only under the approved originality and legal safeguards. Lyrics, unauthorized artist or band branding, third-party logos and misleading endorsement signals remain prohibited.

### Special instructions

Optional control: `I have something specific in mind`

Field guidance: `Describe a setting, mood, colors, clothing, or another detail you would like us to consider.`

Creation-page primary action: `Review my creation`

### Creation summary

Headline: `Your cinematic world is ready to create.`

Show the song and artist, portrait thumbnails, style, orientation, quality and credit cost, plus `No text in image` and Special instructions when selected or provided.

Primary action: `Create my image`

### Membership paywall

Use the approved paywall foundation in this contract. The primary action remains `Continue to secure checkout`.

### Generation progress

Use the approved honest stage messages and long-running state in this contract.

### Completed reveal

Headline: `You are the song now.`

Confirmation: `Your image has been saved to your gallery.`

Actions:

- `Download`
- `Share`
- `Create another image`

## Experience rules

- The flow should feel like creating something meaningful, not completing paperwork.
- Do not explain providers, models, prompts, APIs or hidden creative-engine stages.
- Do not display lyrics.
- Use honest progress language and clear actions.
- Keep button labels direct and action-oriented.
- Keep optional inputs visibly optional.
- Do not generate a paid image or imply that a finished image already exists before payment confirmation.
- Do not use manipulative countdowns, fabricated scarcity or misleading payment language.
- The paywall must clearly state the recurring price, billing frequency, included credits once defined, renewal behavior and cancellation path.

## Measurement requirements

Measure at least:

- onboarding start and completion;
- completion and abandonment by step;
- song-entry success and failure;
- portrait upload completion;
- configuration completion;
- paywall views;
- Checkout starts, successful payments, cancellations and failures;
- preserved-creation recovery after Checkout;
- first successful generation;
- time from onboarding start to first successful result.

The initial target is a clear, low-friction flow with roughly 90 to 95 percent completion among invited users who begin onboarding. Beta evidence, not assumptions, determines later revisions.

## Launch dependencies outside the completed copy workshop

- Final selection and order of the three example files after V2 provider and style benchmarks.
- Exact monthly credit allowance and tier credit costs after provider and cost benchmarks.
- Qualified legal approval of Terms, Privacy Policy, portrait consent and subscription disclosures.
- Responsive visual design and accessibility verification during implementation planning and acceptance testing.

---
type: product-workshop
status: completed
updated: 2026-08-27
area: first-build
owners:
  - CuBiX Meow
  - Brut
---

# First Build Feature Workshop

> Workshop completed on 2026-08-27. Decisions were consolidated into [[First Build Feature Contract]], approved by CuBiX Meow and Brut on 2026-08-27.

This focused workshop turns the owners' V2 goal into a precise **First Build Feature Contract**. It replaces the original 515-question exploratory workshop.

The goal is to resolve only the decisions that can materially change the first build. Do not reopen settled scope unless CuBiX Meow or Brut explicitly asks to reconsider it.

The build freeze remains active until the completed contract is approved and the owners explicitly lift the freeze.

## Starting point: already established by the owners

The V2 program will deliver the web application first, then a Flutter/Dart iOS client using the same PHP HTTP/JSON backend.

The essential experience is:

1. A user creates an account and signs in.
2. The user saves basic profile information and reusable portraits.
3. The user selects or enters a song and its artist/band.
4. The system finds the appropriate lyrics or song context.
5. The creative engine applies the successful prompting ideas and selectable visual styles developed in V1.
6. The user selects an image-quality level with a corresponding credit cost.
7. The system routes inexpensive AI work to inexpensive models and reserves stronger models for work that needs them.
8. The system generates the personalized image asynchronously.
9. The image is saved in the user's gallery and can be downloaded or shared.
10. The backend exposes clean APIs so the later Flutter iOS client uses the same accounts, credits, creative engine and gallery.

For the first web build, images may live on the application host. Moving them to Backblaze B2 is later.

Accounts, deliberate onboarding, an approximately $20 monthly subscription and a durable credit ledger belong in the invite-only first build. Additional credit-pack purchases and Backblaze B2 storage are deferred until soon after the core build functions.

## How to run the workshop

Work through the questions in order, one at a time. Skip a question when an earlier answer has made it irrelevant.

For each answer, capture:

- the decision in plain language;
- the smallest acceptable first-build behavior;
- what the user sees and can do;
- what must be stored;
- what happens when it fails;
- anything explicitly deferred.

Use these classifications only when useful:

- **FIRST BUILD** — required for the first usable web build.
- **SOON AFTER** — useful immediately after the first complete build.
- **LATER** — intentionally deferred.
- **MAYBE / RESEARCH** — requires testing, research or an owner decision.
- **NO / RETIRE** — deliberately excluded.

Do not turn provider preferences, technical proposals or V1 behavior into requirements without an owner decision.

---

# The 35 first-build decisions

## A. Release target and complete journey

1. **Who receives the first deployed web build?** Is it private to the owners, invite-only for selected reviewers/testers, or open for anyone to register?
2. **What is the exact happy path?** From arriving at the site through seeing the finished image in the gallery, what screens and decisions should a first-time user encounter?
3. **What proves the web build is ready for mobile review?** Name the smallest end-to-end result, reliability level and reviewer experience required before Flutter work begins.

## B. Accounts, profiles and onboarding

4. **How should users register and sign in initially?** Decide between email/password, magic link or another method, and settle email verification and password recovery.
5. **What information belongs in the first-build profile?** Identify the required fields, optional fields and settings the user can edit.
6. **What is the minimum onboarding experience?** Decide what a new user must be told or shown before creating a first image and what requires a dedicated onboarding-design workshop.
7. **What account controls are required?** Decide whether the first build needs sign-out-everywhere, account deletion and data export, and what deletion removes.

## C. Song selection and lyrics

8. **How does a user select a song?** Decide whether they search a catalog, type artist and title, choose from search results, or can also paste a link.
9. **Where do lyrics or equivalent song context come from?** Choose the first provider/source, confirm what its terms permit, and define what happens when the song, version or lyrics cannot be found.
10. **Can the user correct the selected song or supplied lyrics?** Define the minimum correction flow for wrong matches, alternate versions, covers and incomplete text.
11. **What song material may be retained?** Decide whether raw lyrics are stored, discarded after analysis or replaced by derived Song DNA, and what the user is told.

## D. Creative engine and V1 prompt value

12. **Which V1 creative stages are necessary in the first build?** Choose among song interpretation, Song DNA, visual narrative plan, artist visual identity, portrait plan, scene composition, prompt compilation, quality evaluation and controlled retry.
13. **Which V1 prompt behaviors and templates should be preserved first?** Identify the proven pieces to recover, the contradictions to remove and the parts that still require experiments.
14. **Which creative-engine results are visible or editable?** Decide whether users see only the final image or can review/edit interpretation, scene direction or another intermediate result.
15. **How should the artist or band's visual identity influence the image?** Define whether it is always applied, optional, cached and clearly distinct from imitating a living artist's exact style.
16. **What user direction is allowed without turning this into a generic prompt box?** Decide whether to include a personal meaning field, focus lyric, literal/metaphorical choice or short custom instruction.

## E. Curated styles and image controls

17. **Which visual styles ship first?** Select the initial V1-derived style set, its approximate size and whether each style needs a preview image.
18. **How are style prompts maintained?** Decide the minimum versioning and activation rules, and whether first-build editing requires an admin surface or can remain configuration-managed.
19. **Which image controls are user-facing?** Settle aspect ratio/orientation, number of outputs, portrait on/off and any other essential control.
20. **What do low, medium and high quality mean?** Define the provider/model, expected output characteristics, approximate cost and credit price for each tier—or reduce the number of tiers if three are not justified.

## F. Portrait experience and privacy

21. **What portrait library ships first?** Decide how many reusable portraits a user may store, whether one generation can include multiple people and which file types/limits apply.
22. **What is the portrait fidelity promise?** Define the minimum acceptable resemblance and whether identity fidelity or creative composition wins when they conflict.
23. **What happens when portrait generation fails?** Decide whether the system retries, asks permission to continue without the portrait, refunds credits or fails honestly; it must never silently remove the person.
24. **What portrait protections are required?** Settle privacy defaults, metadata stripping, deletion behavior, non-owner consent and the disclosure that portraits go to an AI provider.

## G. Generation jobs, providers and credits

25. **How should AI work be routed?** Choose the likely text and image providers/models for the first build, the job each performs and the adapter boundary that keeps them replaceable.
26. **What does the user experience while generation runs?** Define queued/running/progress states, whether the user can leave the page, how completion is communicated and whether cancellation is supported.
27. **What retry and failure policy protects cost and quality?** Define automatic retry limits, timeouts, provider errors, safety rejections and the point where the user must decide what happens next.
28. **How do subscriptions and complimentary access grant credits?** Decide when credits are reserved or deducted, how tier costs are shown and when failed work is refunded.

## H. Gallery, files and sharing

29. **What belongs in each gallery item?** Decide which song, artist, style, quality, portrait, date, generation status and provenance details users can see or reuse.
30. **What actions are available from the gallery?** Settle view, download, delete, regenerate/remix and whether users can reopen the original settings.
31. **What does “share” mean in the web build?** Choose between downloading for manual sharing, the browser/device share sheet, private share links or public links, including revocation and privacy behavior.
32. **What are the initial file-storage rules?** Define image formats/sizes, retention and deletion, per-user limits, backups and the minimum storage abstraction needed for a later Backblaze B2 move.

## I. Flutter readiness, administration and freeze exit

33. **What API contract must exist before Flutter work begins?** List the required account, portrait, song, generation-job, credit and gallery operations, plus mobile authentication and consistent error responses.
34. **What is the minimum owner/admin visibility?** Decide what is needed to inspect users, credits, generation jobs, failures, costs, styles and provider usage without building a large administration system.
35. **What exact checklist lifts the build freeze?** Confirm the approved feature contract, provider/lyrics feasibility, privacy and cost decisions, acceptance tests, deployment target and explicit owner authorization required before implementation starts.

---

# Required workshop output

When the questions are complete, produce a concise **First Build Feature Contract** with:

1. Product promise and intended first users
2. Exact end-to-end user journey
3. First-build screens and user-visible behavior
4. Account, profile and portrait contract
5. Song selection and lyric-handling contract
6. Creative-engine stages and V1 prompt functionality to preserve
7. Styles, controls and quality tiers
8. Provider routing, job states, retries and failure behavior
9. Subscription, complimentary-access and credit-ledger rules
10. Gallery, download, sharing and storage behavior
11. PHP HTTP/JSON API requirements for Flutter
12. Minimum administration and operations
13. Privacy, security and deletion choices
14. Acceptance criteria and build-freeze exit checklist
15. Explicit **SOON AFTER**, **LATER**, **MAYBE / RESEARCH** and **NO / RETIRE** lists
16. Remaining blockers, if any

The contract must be specific enough to test. It does not authorize application implementation until CuBiX Meow and Brut explicitly approve it and lift the build freeze.

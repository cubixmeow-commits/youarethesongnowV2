---
type: research
status: in-progress
updated: 2026-08-27
area: lyrics-legal
owners:
  - CuBiX Meow
  - Brut
related-contract: First Build Feature Contract
---

# Lyrics Retrieval and Legal Feasibility

## Purpose

Resolve build-freeze checklist item 2: confirm a commercially usable way to retrieve lyrics, process them temporarily into non-infringing creative analysis, delete the raw text and use a reliable song-context fallback.

This is product and technical research, not legal advice. Launch approval requires written provider terms and qualified legal review of the exact workflow.

## Current conclusion

**Use a licensed lyrics API as the primary path. Do not treat ordinary web search, scraped lyric sites or Gemini/Google Search grounding as a lyric license.**

Musixmatch is the leading candidate verified so far. Its official API materials describe a licensed lyrics catalog and commercial application integrations. That general statement is not enough by itself: the project must obtain a plan or written agreement that expressly permits the workflow below.

Google Search grounding is not an acceptable raw-lyrics path under the current published Google Cloud terms. Those terms restrict caching, analyzing, learning from and repurposing grounded results. The V2 workflow intentionally analyzes source material into Song DNA, so Google grounding should be limited to permitted song identification/context uses, if used at all, unless Google grants written permission for the exact use.

## Owner decision: development before licensing

CuBiX Meow and Brut will first build and test the lyrics-to-Song-DNA workflow privately with an AI/search provider before deciding whether to proceed with the commercial project and pay for lyrics licensing.

This is a **development-only feasibility path**, not launch clearance:

- access is limited to the owners and authorized developers;
- it must not be opened to invited beta testers, reviewers or paid users;
- lyrics are never shown to users or included in generated images;
- raw lyrics are processed ephemerally and are not retained in the database, prompts, logs or backups;
- retained Song DNA must avoid quotations and close paraphrases;
- provider terms still apply during development, so implementation may use only methods allowed by the selected AI/search provider;
- public-domain, owner-controlled or otherwise authorized songs should be used for repeatable development and evaluation whenever practical;
- no commercial launch or external beta occurs until the owners make a go/no-go decision and the licensed workflow receives provider and legal approval.

The build-freeze checklist may separate **development feasibility confirmed** from **commercial licensing confirmed**, but checklist item 2 remains incomplete until the commercial exit conditions are satisfied.

## Artist-direct commercial direction

The intended commercial model may rely primarily on direct relationships with bands and verified indie artists rather than a general public-lyrics catalog.

Preferred source hierarchy:

1. Lyrics supplied under a direct agreement with a band or its authorized rightsholder.
2. Lyrics submitted by a verified indie artist who owns or controls the necessary rights.
3. A commercially licensed lyrics API for broader catalog coverage, if the owners choose to purchase it.
4. Legally permitted factual song context when authorized lyrics are unavailable.

Publicly available lyrics are expected to serve mainly as private development material, subject to provider terms and the development safeguards above.

Artist-submitted lyrics cannot be treated as an unrestricted consumer paste box. The eventual artist workflow needs:

- verified artist/rightsholder accounts;
- an explicit ownership or authorization warranty;
- permission covering temporary AI processing and commercial image generation;
- territory, term, attribution, reporting and compensation rules where applicable;
- a way to replace or withdraw lyrics and stop future generations;
- a notice-and-takedown/dispute process;
- provenance and agreement-version records for every submitted work.

This is strategic direction with timing still open. It does not expand the approved first-build scope unless the owners explicitly add an artist-partner slice.

## Required licensed workflow

The provider agreement must explicitly permit all of these actions:

1. Search by user-entered artist/band and song title.
2. Retrieve sufficient lyric text server-side for the matched song.
3. Send the lyric text to named or contractually covered AI subprocessors for temporary analysis.
4. Derive themes, mood, symbols and narrative concepts without quoting or exposing lyrics.
5. Use that derived Song DNA to generate original commercial images.
6. Delete raw lyrics immediately after analysis.
7. Retain only song identification, provider/match metadata and non-quoting Song DNA.
8. Cache match or failure metadata to control cost and abuse.
9. Serve a paid web and mobile product in the intended territories.

If any of these rights are absent or ambiguous, obtain written permission or reject that provider for the lyrics-analysis path.

## Proposed retrieval policy

1. Normalize the user's exact artist and title without changing the visible entry.
2. Query one licensed primary provider.
3. Accept only a high-confidence artist/title/version match.
4. Process licensed lyrics ephemerally into Song DNA; never display lyrics.
5. Delete raw lyric text immediately after the derived artifact is safely produced.
6. If licensed lyrics are unavailable, use reliable, legally permitted song metadata and descriptive context—not scraped lyrics.
7. If reliable context is also unavailable, stop before image generation, charge no generation credits and ask for another song.
8. Apply per-account/IP limits, concurrency limits, negative-result caching and a fixed provider-call ceiling.

## Product safeguards

- Never display, return, log or share raw lyrics.
- Never retain lyric excerpts inside Song DNA, prompts, job logs, analytics or error traces.
- Never quote or closely paraphrase distinctive lyric lines in prompts, gallery labels or images.
- Block visible lyrics, song titles, artist names, album names, label marks and third-party logos in generated images.
- Use song and artist names as identification metadata only, subject to final counsel review and provider attribution rules.
- Record the provider, match identifier, confidence, retrieval status, deletion completion and fallback reason for auditability.
- Keep every lyrics and context source behind a replaceable provider adapter.

## Provider due-diligence questions

Send these questions to candidate providers before contracting:

1. Does the commercial license cover server-side retrieval for a paid web and iOS application?
2. May full lyric text be processed transiently without being displayed?
3. May it be sent to third-party AI subprocessors solely to create a non-quoting semantic analysis?
4. May that analysis guide original commercial image generation?
5. What raw-text retention, caching, logging and deletion rules apply?
6. May derived, non-reconstructive Song DNA be retained? For how long?
7. What attribution, reporting, territory, publisher exclusion and takedown obligations apply?
8. Are song/artist names and provider identifiers permitted in private gallery metadata and share pages?
9. What catalog coverage, rate limits, pricing and sandbox/testing rights apply?
10. Does the agreement provide written rights coverage or indemnity for this exact licensed use?

## Legal-review questions

- Does non-quoting visual inspiration generated from properly licensed, transient lyrics require any additional derivative-work permission?
- Are the proposed Song DNA constraints sufficiently non-reconstructive and non-substitutive?
- Is the fallback based on factual song metadata and general themes acceptable?
- Are song and artist names acceptable as identifying metadata, subject to trademark and endorsement safeguards?
- Are the Terms, Privacy Policy, portrait consent, AI-provider disclosure, sharing language and takedown process sufficient?
- Which launch territories should be permitted under the selected licenses?
- What rights, warranties, verification and takedown terms are required for direct band partnerships and indie-artist lyric submissions?
- Who must authorize lyrics when composition rights are shared among writers, publishers or administrators?

## Evidence reviewed

- Musixmatch official API materials describe its catalog as licensed and its API as a legal way to integrate lyrics into applications: https://github.com/musixmatch/musixmatch-sdk/blob/master/swagger/swagger.json
- Musixmatch's official developer workspace describes access to its licensed lyrics and metadata catalog: https://www.postman.com/musixmatch-dev
- Current Google Cloud service terms restrict analyzing, caching and repurposing Grounded Results: https://cloud.google.com/terms/service-terms
- The U.S. Copyright Office identifies lyrics as part of the protected musical work and says use generally requires a license or an applicable exception: https://copyright.gov/engage/docs/recording.pdf
- The U.S. Copyright Office explains that fair use is case-specific, commercial use weighs in the analysis, creative works receive stronger consideration and no fixed percentage is automatically safe: https://copyright.gov/fair-use/

## Exit conditions for checklist item 2

- [ ] A development-only AI/search provider and its permitted-use constraints are selected.
- [ ] The private, ephemeral lyrics-to-Song-DNA workflow is proven technically with authorized test material.
- [ ] CuBiX Meow and Brut make a go/no-go decision after reviewing development results and expected licensing cost.
- [ ] At least one provider confirms the complete licensed workflow in a signed agreement or clear written terms.
- [ ] Price, catalog, rate limits, territories, attribution and reporting are acceptable.
- [ ] AI subprocessors and deletion/logging behavior comply with that agreement.
- [ ] The non-lyrics fallback sources and permitted retained fields are documented.
- [ ] Product Terms, Privacy Policy, takedown process and user-facing language are drafted.
- [ ] Qualified counsel approves the exact paid-beta workflow.

Until all nine conditions are met, checklist item 2 remains open. Development feasibility may be completed before the commercial licensing conditions.

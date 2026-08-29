---
type: research
status: research-complete-development-plan
updated: 2026-08-28
area: song-rights-creative-engine
owners:
  - CuBiX Meow
  - Brut
related:
  - Lyrics Retrieval and Legal Feasibility
  - Controlled Song Test Set
  - AI Provider Benchmark Plan
---

# Public Song Information to Original Visual Art Report

## Executive conclusion

The product is technically feasible, but **publicly viewable lyrics are not automatically licensed for commercial analysis or image generation**. The scalable version needs a rights-aware source system, not a general lyric-site scraper.

V2 should develop two distinct commercial paths behind the same creative engine:

1. **General-song path:** accept any song only when a lyrics provider or rightsholder expressly authorizes server-side retrieval, temporary AI analysis, retention of non-reconstructive Song DNA and commercial image generation. When authorized lyrics are unavailable, use legally permitted factual context. If neither path is reliable, stop without charging generation credits.
2. **Artist-partner path:** bands, publishers and verified indie artists provide catalogs they control under a direct agreement authorizing the same processing and commercial outputs. This path can become the primary business even if broad popular-song licensing proves uneconomic or too restrictive.

The same matching, Song DNA, prompt, safety, gallery and merchandise systems can serve both paths. Only the source rights, catalog access and partner administration differ.

This report is product and engineering research, not legal advice. Qualified U.S. intellectual-property counsel must approve the exact paid-beta workflow and agreements.

## Owner decision: no development licensing purchase

CuBiX Meow and Brut will not purchase a broad lyrics license for the private development phase. The owners may use protected popular-song lyrics internally and ephemerally to determine whether the creative process works, with no public access, customer access, payment, distribution or commercial use of those lyric-based test outputs.

This is an owner-selected development risk boundary, not a finding that internal or noncommercial use is automatically exempt from copyright or provider terms. Development material must not be scraped or accessed in violation of a website or provider agreement.

Development controls:

- owners and authorized developers only;
- no invited testers, reviewers, customers or public links;
- no raw lyrics in Git, SQLite, queues, logs, analytics, backups or shared fixtures;
- lyrics held only in volatile memory for one analysis and then deleted;
- development outputs clearly marked private and not sold, promoted or distributed;
- retain only sanitized, non-reconstructive test scores and Song DNA where needed;
- use the approved 12-song benchmark to compare matching, analysis and imagery;
- supplement the fixed benchmark with a broad rotating private corpus spanning genres, eras, moods, languages, narrative structures, popularity levels and difficult lookup cases;
- do not interpret successful testing as commercial clearance.

Before any user-accessible release, the general-song catalog is restricted to works with a recorded rights basis: verified public-domain material, genuinely free material whose license permits the complete workflow, owner-controlled songs, directly authorized artist catalogs or a later commercial lyrics agreement.

The creative engine and the source gate are deliberately separate. Development uses broad private inputs to refine the engine. Commercialization keeps the refined engine but replaces unrestricted development sourcing with a mandatory legal-eligibility check before any lyric text enters memory.

## What the law supports and what it does not

United States copyright law protects musical works, including accompanying words. It does not extend copyright protection to ideas, concepts, principles or methods. The Copyright Office also identifies names, titles, short phrases and familiar symbols as generally uncopyrightable, although names and branding may still receive trademark protection.

That supports a carefully separated system that extracts broad ideas such as mood, theme, emotional movement and familiar visual language, then creates a new visual composition. It does **not** create a blanket right to copy or process lyrics from any public website.

Important limits:

- Copyright owners control reproduction and derivative works, subject to statutory exceptions.
- Fair use is case-specific. There is no safe number of lyric words, lines or percentage.
- A new medium, aesthetic, meaning or message is not automatically fair use, especially in a commercial product.
- The phrase `inspired by` is honest positioning, not a legal safe harbor.
- Song and band names are generally not protected by copyright, but trademark, endorsement and unfair-competition risk can still apply.
- Avoid album artwork, music-video scenes, band logos, signature stage designs and artist likenesses unless separately authorized.

The lower-risk position is therefore: **authorized source in, abstract and non-reconstructive Song DNA retained, independently composed artwork out**.

## Rights-aware source ladder

Every **user-accessible or commercial** song analysis must have a recorded rights basis. Use this order:

1. Direct band, publisher or rightsholder agreement.
2. Verified artist submission under an agreement covering the composition and lyrics.
3. Licensed lyrics provider whose written terms cover this exact AI and commercial-image workflow.
4. Verified public-domain lyrics, with territory and version recorded.
5. Legally usable factual metadata and general song context without lyric retrieval.
6. No reliable permitted source: return `cannot find enough information`, create no image and charge no generation credits.

Do not use these as production lyric sources without specific written permission:

- scraped lyric websites;
- search-result snippets;
- unofficial lyric APIs or copied lyric datasets;
- consumer streaming displays;
- an AI model's recalled or search-grounded reproduction of lyrics;
- user-submitted lyrics without an ownership or authorization workflow.

## Source candidates

### Musixmatch

Musixmatch is the strongest broad-catalog candidate identified so far. Its official materials describe a licensed lyrics catalog and API retrieval. That claim does not prove our derived-analysis use is included. Obtain a commercial proposal and written answers confirming temporary server-side processing, named AI subprocessors, retained Song DNA and commercial image generation.

### LyricFind and other licensed catalog providers

LyricFind publicly describes a licensed lyrics business used by major platforms. Treat it as a second commercial candidate and send it the same due-diligence questionnaire. A display license is not necessarily an analysis or derivative-output license.

### MusicBrainz

MusicBrainz is useful for title, artist, work, recording, version and identifier matching. It does not provide lyrics. Its core database is CC0, while supplementary data has different restrictions, and its public web service directs commercial users to commercial plans. It is a good metadata resolver if the selected access method and fields fit the license.

### Genius and ordinary web search

Do not plan on scraping Genius or another lyric page. The Genius developer API is useful for song metadata and links, but access to a page or URL does not grant lyric-processing rights. Search can help locate factual sources for human review, but search-result access is not a lyric license.

### Google Search grounding

Do not use Google Search Grounding results as retained Song DNA input under the current published Google Cloud terms. The terms restrict analyzing, caching, learning from and repurposing Grounded Results. Gemini Free tier is suitable only for the already approved nonconfidential development fixtures and is not a source license.

## Proposed technical workflow

### 1. Preserve and normalize the request

- Keep the user's exact artist and song entries.
- Normalize case, punctuation and spacing only for lookup.
- Search metadata for the work, recording and likely version.
- Never silently substitute a different song.
- Require user confirmation for a materially different close match.

### 2. Select a permitted source

- Resolve the applicable source and rights basis.
- Enforce territory, catalog, plan and agreement-version rules.
- Apply a fixed provider-call ceiling, rate limits and negative-result caching before expensive AI work.
- Do not retrieve protected lyrics when the source does not authorize the planned use.

### 3. Isolate raw source material

- Process permitted lyrics in volatile memory only.
- Never write raw lyrics to the application database, queue payload, prompt history, analytics, error trace or backup.
- Disable request and response logging for the analysis step.
- Send lyrics only to contractually approved AI subprocessors.
- Delete the raw text immediately after analysis or failure.

### 4. Produce a constrained Song Inspiration DNA

Allowed fields:

- broad themes and relationship archetypes;
- mood, energy and emotional progression at a high level;
- generic setting categories and era atmosphere;
- color, light, weather, texture and spatial feeling;
- familiar symbols and visual metaphors expressed generically;
- original composition directions;
- safe character roles without protected names or celebrity likenesses;
- confidence, ambiguity and source-quality flags.

Forbidden fields:

- lyric quotations;
- close paraphrases or memorable phrase substitutions;
- a reconstructive summary of the lyrics;
- distinctive character names, fictional places or unique story sequences;
- signature combinations of images that reproduce expressive lyric scenes;
- artist, song or album names inside the creative prompt;
- album-cover, music-video, concert-stage or merchandise imitation;
- band logos, labels, trademarks or public-figure likenesses.

The DNA should be useful for visual direction but insufficient to recreate the lyrics.

### 5. Sanitize before prompt construction

Use a separate safety pass that did not perform the original extraction:

- compare the DNA against the permitted source for quotations and suspicious phrase overlap;
- reject close paraphrases and distinctive narrative sequences;
- generalize overly specific places, props, characters and symbols;
- flag song/artist/album names, logos, public figures and protected visual references;
- return only the approved structured fields to the prompt compiler.

Word and phrase overlap detection is a safety heuristic, not a legal word-count rule.

### 6. Compile an original image prompt

The image model receives portraits, selected style, orientation, quality tier, lawful user instructions, the no-text setting and sanitized Song DNA. It never receives raw lyrics.

Require:

- a new composition centered on the uploaded person or people;
- no artist or band likenesses;
- no album art, music-video recreation, logos or implied endorsement;
- no copyrighted lyric text;
- when text is allowed, only original, user-owned, licensed or public-domain wording;
- when `No text in image` is selected, no readable text at all.

### 7. Inspect the generated image

Before delivery:

- run OCR and reject copyrighted lyrics, song/artist names, unwanted text and obvious marks;
- detect public-figure or unintended extra-person risk where practical;
- perform a human visual review during development for album-art, music-video and signature-imagery resemblance;
- retry with a comparable model after a technical or unusable-image failure without consuming credits;
- retain the approved image and safe provenance, not the lyric source.

### 8. Retain a minimal audit record

Store:

- user-entered and matched title/artist metadata;
- stable metadata identifier and matched version;
- source/provider identifier and rights-basis category;
- agreement or permission version;
- match confidence and fallback path;
- Song DNA schema and analysis version;
- raw-source deletion completion;
- safety checks and outcome;
- provider cost, latency and failures.

Do not store raw lyrics or reconstructive analysis.

## Two-track product architecture

### Track A: general-song service

Goal: let a user enter nearly any song.

Requirements:

- broad licensed catalog or a legally approved non-lyric context route;
- catalog and territory checks before retrieval;
- per-song reporting and attribution if required;
- provider cost included in credit economics;
- clear handling for unmatched, restricted and unavailable works;
- counsel approval before external or paid beta.

Go/no-go question: can we obtain sufficient catalog coverage and explicit rights at a cost that preserves the $20 subscription economics?

### Track B: artist-partner catalogs

Goal: let a band or verified indie artist offer a branded catalog experience to fans, including later prints and merchandise.

Minimum partner agreement must cover:

- identity and authority of the signing party;
- composition/lyric rights and any co-writer or publisher approvals;
- temporary AI processing and approved subprocessors;
- generation, display, sharing, sale and physical merchandise rights;
- use of artist, song and album names and approved brand assets;
- territories, term, compensation, reporting and taxes;
- fan portrait consent and generated-image ownership/license terms;
- catalog edits, withdrawal and termination;
- takedown, disputes, indemnity and surviving customer-order rules.

Minimum partner tooling:

- verified partner account and role controls;
- song catalog import and rights-status fields;
- lyric upload/edit with provenance and agreement version;
- activation, territory and withdrawal controls;
- approved brand and merchandise settings;
- usage, generation, share, order and revenue reporting;
- audit trail and takedown controls.

This track can succeed independently of broad popular-song licensing. Build the common engine once, then add partner catalog administration as a separate product phase unless the owners explicitly move a narrow partner pilot into the first build.

## Development and test plan

Internal development is not automatically exempt from copyright or provider terms. The owners have nevertheless chosen a private, ephemeral, noncommercial feasibility test before purchasing licensing. Use the following staged plan.

### Phase 1: metadata and context-only tests with popular songs

Use the approved 12-song set to test:

- exact, typo and ambiguous-title matching;
- artist/title reversal and version disambiguation;
- factual metadata and legally permitted general-context fallback;
- safe failure when information is insufficient;
- cost, caching, latency and abuse controls.

Run the context-only path first. The private development harness may then analyze temporarily obtained protected lyrics for comparison, provided the access method itself does not violate provider or website terms and all development controls above are enforced. The popular-song set is not proof of commercial lyric rights.

### Phase 2: private full-source feasibility analysis

Test two groups separately:

- owner-written original songs;
- public-domain works with verified status;
- one or more indie-artist songs under a written development authorization;
- internally tested popular protected songs from the approved benchmark, used ephemerally under the owner-selected noncommercial development boundary;
- adversarial synthetic lyrics created solely for leakage testing.

Use it to prove ephemeral processing, Song DNA quality, deletion, sanitizer effectiveness and prompt isolation.

### Phase 3: pre-release rights decision

After development proves the concept, choose the initial user-accessible catalog:

- verified public-domain and permissively licensed songs;
- direct band and verified indie-artist catalogs;
- a later licensed popular-song catalog only if the owners decide its coverage and cost are worthwhile.

Do not assume `free`, publicly viewable or a trial key means the workflow has commercial permission. Verify the license for every user-accessible source.

Compare:

- authorized-lyrics DNA against context-only DNA;
- multiple analysis models for cost and accuracy;
- multiple image generators by style and portrait fidelity;
- results against a blinded human rubric.

### Phase 4: paid-beta decision

Proceed only if:

- quality materially improves when authorized lyrics are used;
- leakage and resemblance controls pass the agreed thresholds;
- source and AI terms cover the exact workflow;
- cost fits the subscription and credit model;
- qualified counsel approves the workflow and user/partner terms.

Otherwise launch the artist-partner path first, use context-only general-song inspiration if approved, or remove unsupported songs.

## Test rubric

Score every test from 1 to 5:

1. Song match correctness.
2. Emotional and thematic relevance.
3. Visual originality and distance from protected expression.
4. Absence of lyric quotation or close paraphrase.
5. Absence of band/artist likeness, logos and recognizable album/video design.
6. Portrait identity preservation.
7. Style and orientation compliance.
8. Text-setting compliance.
9. Image usability.
10. Cost and latency.

Automatic failures:

- any recognizable protected lyric in DNA, prompt or image;
- a materially wrong song selected without confirmation;
- recognizable album-art or music-video recreation;
- unauthorized band logo, artist likeness or endorsement cue;
- raw lyrics in storage, logs or backups;
- use outside the source agreement's catalog, territory or purpose.

Red-team tests must explicitly ask the system to quote the chorus, recreate the album cover or video, depict the performer, add the band logo and hide lyric text in signs. Every request must be refused or safely transformed.

## V1 evidence and required V2 changes

V1 proved that a structured Song DNA can drive strong visual prompts and included instructions not to quote lyrics or include protected branding. It also stored lyrics in queue/render records and relied mainly on prompt instructions for safety.

V2 should preserve the creative idea but change the control boundary:

- raw lyrics never enter persistent queue or render records;
- the prompt compiler cannot access raw lyrics;
- structured extraction and independent sanitization are separate stages;
- rights basis and deletion completion are mandatory fields;
- OCR and human resemblance review are part of the benchmark;
- no claim that the output is safe merely because it is transformative or inspired.

## Recommended next action

Before writing application code:

1. Draft the Song DNA schema and leakage test corpus without adding product code.
2. Configure a private, non-persistent development harness for the authorized Private Development Build 1.
3. Run context-only and ephemeral full-source comparisons against the approved 12 popular songs.
4. Add owner-written, verified public-domain and indie-artist-authorized fixtures.
5. Decide whether the first user-accessible catalog contains public-domain/free-authorized songs, partner catalogs or both.
6. Research paid broad-catalog licensing only after the owners decide the proven results justify it.

Private Development Build 1 is authorized. This report guides its private research harness but does not authorize external access or commercial protected-lyrics use.

## Primary evidence

- U.S. Copyright Act, sections 101, 102, 103 and 106: https://www.copyright.gov/title17/92chap1.html
- U.S. Copyright Office Circular 33, ideas, titles, short phrases and familiar symbols: https://www.copyright.gov/circs/circ33.pdf
- U.S. Copyright Office Fair Use Index: https://copyright.gov/fair-use/
- U.S. Supreme Court, *Andy Warhol Foundation v. Goldsmith*: https://www.supremecourt.gov/opinions/22pdf/21-869_87ad.pdf
- U.S. Patent and Trademark Office, likelihood of confusion: https://www.uspto.gov/trademarks/search/likelihood-confusion
- Musixmatch official API specification: https://github.com/musixmatch/musixmatch-sdk/blob/master/swagger/swagger.json
- Musixmatch official developer workspace: https://www.postman.com/musixmatch-dev
- LyricFind official licensing and partner overview: https://www.lyricfind.com/
- LyricFind official sales contact: https://www.lyricfind.com/contact
- MusicBrainz API and data-license documentation: https://musicbrainz.org/doc/MusicBrainz_API and https://musicbrainz.org/doc/About/Data_License
- Google Cloud service-specific terms for Search Grounding: https://cloud.google.com/terms/service-terms
- Gemini API Additional Terms: https://ai.google.dev/gemini-api/terms

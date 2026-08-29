---
type: research
status: owner-approved
updated: 2026-08-28
area: song-fixtures
owners:
  - CuBiX Meow
  - Brut
---

# Controlled Song Test Set

## Purpose and boundary

Use this compact set to compare song lookup, Song DNA extraction, prompt construction and final image quality across providers. Store song and artist metadata in the fixture record, but never save lyrics anywhere. Lyrics are memory-only for one private analysis and are immediately discarded.

These commercial songs are private-development test targets only. Inclusion here is not a claim of lyric-processing or commercial-generation rights. External testers, paid access and production use remain blocked until the approved licensing and legal gates are cleared.

The 12 songs below are the fixed comparison core, not the entire development corpus. Private development should also use a broad rotating range of songs so the engine is refined against varied language, genre, era, emotional tone and narrative structure. No lyric text from either group is ever saved.

## Approved core set

| ID | Song | Artist | Primary coverage |
| --- | --- | --- | --- |
| T01 | At Last | Etta James | romance, classic wedding mood, warmth |
| T02 | A Thousand Years | Christina Perri | modern wedding mood, devotion, cinematic scale |
| T03 | Fast Car | Tracy Chapman | intimate narrative, longing, realistic environments |
| T04 | Dreams | Fleetwood Mac | ambiguous title, emotional distance, atmospheric imagery |
| T05 | Jolene | Dolly Parton | character-centered tension, jealousy, portrait drama |
| T06 | Dancing Queen | ABBA | celebration, movement, bright social scene |
| T07 | Blinding Lights | The Weeknd | modern energy, night-city imagery, strong color direction |
| T08 | Purple Rain | Prince | symbolic color, romance, loss, dramatic atmosphere |
| T09 | Space Oddity | David Bowie | surreal narrative, isolation, science-fiction imagery |
| T10 | Take Me to Church | Hozier | metaphor, intensity, dark symbolic interpretation |
| T11 | Bohemian Rhapsody | Queen | multi-part narrative, tonal shifts, abstract composition |
| T12 | This Must Be the Place (Naive Melody) | Talking Heads | home, belonging, couple-centered emotional warmth |

## Controlled use in the 90-image acceptance set

- Use T01 and T02 for the wedding or celebration fixtures.
- Use T03, T05 and T09 for solo narrative fixtures.
- Use T01, T04, T06 and T12 for two-person fixtures.
- Use T07, T08, T10 and T11 to stress style routing and abstract interpretation.
- Reuse the same portrait, song, orientation and style when comparing providers or quality tiers directly.
- Do not select a new song ad hoc during a provider comparison; changing the song invalidates the direct comparison.

## Lookup-behavior checks

Run these as inexpensive lookup tests before the generation benchmark. They must not trigger an image-generation charge.

1. Exact title and artist for every core fixture.
2. Case and punctuation variation, including `this must be the place naive melody`.
3. A minor title typo, such as `Blinding Lites`, with the correct artist.
4. A minor artist typo, such as `Fleetwood Mack`, with the correct title.
5. The ambiguous title `Dreams` with the correct artist supplied.
6. Artist and title accidentally entered in the opposite fields.
7. One clearly nonexistent title and artist pair to verify a safe `cannot find information` result.
8. Repeated invalid requests from one account and address to verify rate limits, caching and the AI-spend circuit breaker.

The system may offer a close match, but it must never silently replace the user's entry. The user must see and accept any materially different match before generation.

## Future rights-controlled fixtures

Add these before external beta use:

- at least one owner-controlled original song;
- at least one track and lyric set supplied under a written indie-artist development agreement;
- later, catalog fixtures supplied through direct band or licensed-provider agreements.

Rights-controlled fixtures should gradually replace commercial-development targets wherever they provide equivalent technical coverage.

## Broad rotating private-development corpus

Add temporary test selections across these dimensions without committing a lyric list or lyric text to the repository:

- rock, punk, metal, pop, folk, country, soul, R&B, hip-hop, electronic, jazz and musical theater;
- early recordings, mid-century songs, late twentieth-century songs and current music;
- romance, celebration, grief, anger, rebellion, humor, spirituality, nostalgia, home and belonging;
- literal narratives, abstract poetry, repeated refrains, character songs, nonlinear structures and instrumental tracks;
- solo, couple, family, friendship, wedding and group-oriented visual possibilities;
- English and later additional languages supported by the selected analysis provider;
- famous, moderately known, obscure, independent and intentionally ambiguous titles;
- covers, remixes, live versions, songs sharing a title and artist/title misspellings;
- source-rich songs and songs with very little reliable public context;
- high-risk leakage cases containing distinctive phrases, unusual characters or signature visual sequences.

The rotating corpus is exploratory. The fixed 12-song core remains the only set used for controlled provider-to-provider comparisons.

For every rotating test, record only a local test identifier, song/artist metadata, category labels, source method, result scores, cost, latency and sanitized Song DNA. Never record lyric text, lyric excerpts or a reconstructive summary.

## Commercial source switch

Development breadth and commercial catalog eligibility are separate controls. When the product becomes user-accessible:

1. Keep the refined matching, analysis, sanitization, prompt and image systems.
2. Disable unrestricted development sourcing.
3. Require an active rights record before any lyrics enter memory.
4. Permit only verified public-domain works, permissively licensed works covering the complete workflow, owner-controlled songs, directly authorized band/artist catalogs or a later licensed catalog.
5. Reject or use a legally approved context-only fallback for every song without an eligible source.

No development fixture becomes commercially eligible merely because it produced a good image.

## Data-handling rules

- Keep retrieved lyrics ephemeral unless qualified legal approval explicitly permits retention.
- Store only the minimum derived Song DNA needed for the approved workflow.
- Do not display lyrics to users or place recognizable lyric passages in prompts, logs, images or gallery metadata.
- Do not include song titles, artist names or lyrics in generated images.
- Record provider, lookup outcome, match confidence, fallback path, cost and duration by fixture ID.
- Cache safe lookup results and negative results according to the approved abuse and retention rules.

## Approval

CuBiX Meow and Brut approved this exact 12-song core set on 2026-08-28. It is now the fixed private-development benchmark unless both owners approve a revision.

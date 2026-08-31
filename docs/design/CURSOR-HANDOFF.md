# CURSOR-HANDOFF — Explore JSON decoder fix

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** Gemini Explore response decoding (after live `provider-invalid-json`)

## Live evidence (already confirmed)

iPhone diagnostic:

> `provider-invalid-json · build e51e28d7e092`

Meaning already proven before this change:

- Hostinger is on the private-diagnostics build (`e51e28d`)
- Routing, API key, model access, and Gemini transport work
- Explore fails while **decoding** the generateContent payload

Do **not** revisit deploy/auth/model-404 unless a new diagnostic says so.

## Exact root cause

`GeminiExploreService` reused `GeminiCreativeAdapter::decodeResponse()`.

That helper:

1. Concatenates **every** `candidates[0].content.parts[].text`
2. Runs `json_decode()` on the whole string
3. Maps any failure to a single invalid-JSON path

Live Explore uses **`gemini-3.6-flash`**, a Gemini 3 thinking model. generateContent commonly returns:

```json
"parts": [
  { "text": "…reasoning…", "thought": true },
  { "text": "{ \"directions\": [ … ] }" }
]
```

Concatenating thought prose + JSON makes `json_decode` fail — even when the answer part is valid structured JSON. A fixture in the test suite proves CreativeAdapter-style concatenation cannot decode that shape.

Secondary hardening in the same fix:

- fenced / embedded JSON recovery
- `MAX_TOKENS` truncation diagnosis
- empty candidate / safety / schema-mismatch diagnostics
- `thinkingConfig.thinkingLevel = minimal` + `maxOutputTokens = 4096` so structured JSON is less likely to be truncated by thinking budget

## Exact fix

1. Added dedicated `GeminiExploreService::decodeExploreResponse()` / `parseJsonObject()`.
2. Skips `thought: true` parts; only answer text is parsed.
3. Recovers direct JSON, fenced JSON, and embedded `{…}` objects.
4. Distinguishes diagnostics:
   - `provider-no-output-text`
   - `provider-fenced-json-recovered` (soft success, logged)
   - `provider-embedded-json-recovered` (soft success, logged)
   - `provider-malformed-json`
   - `provider-truncated-output`
   - `provider-safety-blocked`
   - `provider-generation-blocked`
   - `provider-schema-mismatch`
   - `provider-incomplete-output` (&lt; 3 usable directions)
5. Keeps true structured output: `responseMimeType=application/json` + `responseJsonSchema`.
6. Sanitized logs may include finishReason / textLength / part counts — never Song DNA, lyrics, prompts, keys, portraits, or full bodies.

### Files changed

- `src/AI/GeminiExploreService.php`
- `tests/run.php`
- `app/build-stamp.php`
- `docs/design/CURSOR-HANDOFF.md`

## Tests / results

```text
php tests/run.php
=== Results: 972 passed, 0 failed ===
```

## Deploy / build stamp

- No `.env` changes.
- Hostinger still needs a normal **git sync** of `main`.
- After sync, `/api/v1/health` → `build.commit` should move past `e51e28d7e092`.

## Exact iPhone retest

1. Sync Hostinger `/yatsnV2` to latest `main`.
2. Soft-refresh `/create`; badge SHA should match health `build.commit`.
3. Discover a song → portrait → **Explore options**.
4. **Success:** exactly 3 Song-DNA-specific directions; first recommended.
5. If it still fails, the status must include a precise diagnostic (not bare `provider-invalid-json`), e.g. `provider-truncated-output`, `provider-schema-mismatch`, or `provider-incomplete-output`.
6. Also retest **Generate for me**.

## Final commit

- **Hash:** 
- **Message:** Fix Explore JSON decode for Gemini 3 thought parts
- **Branch:** 
- **Build stamp:** 
- **Requires:** Hostinger git sync only (no  changes)

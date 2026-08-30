---
type: implementation-note
status: active
updated: 2026-08-30
area: song-dna
---

# V1-Compatible Song Analysis Mode

Status: active for private development by owner direction.

## Current V2 path (Interactions API)

V2 Song DNA search now uses the Gemini **Interactions API** so Google Search and strict JSON Schema structured output can run together:

1. `POST /v1beta/interactions` with model `gemini-3.6-flash`
2. built-in tool `{ "type": "google_search" }`
3. `response_format` text / `application/json` with the full Song DNA envelope schema
4. **`store=false` (mandatory)** — lyrics, retrieved search material, prompts, and provider output must not be retained as Gemini interaction state
5. no `previous_interaction_id`, no background execution
6. semantic validation after schema parsing (`hasUsableAnalysis`); incomplete results cannot claim lyric-grounded DNA
7. at most one retry for transient timeout / rate-limit / 5xx only
8. if Interactions structured-output-with-tools is rejected, fail with `interactions-structured-search-rejected` — never silently fall back to ungrounded analysis

Lyrics may be searched and analyzed transiently during private development. Raw lyrics, search page bodies, raw interaction responses, and complete provider requests are never logged or stored. Only approved abstracted Song DNA is persisted.

## Bruce Springsteen structural failure (legacy)

Live lookup of `Dancing in the Dark` by `Bruce Springsteen` under the legacy `generateContent` path succeeded at Google Search but returned incomplete Song DNA structure because Search and JSON MIME structured output could not be combined on that endpoint. The model answered with malformed or partial JSON despite successful retrieval.

## V1 historical precedent

V1 worked around incomplete JSON with a second “Repairing JSON structure” call at temperature 0.2. That remains historical evidence only. It is **not** the primary V2 solution. A V1-style JSON-repair call remains a possible emergency fallback only if the modern Interactions API proves unreliable in production.

## Why Interactions is the improved V2 solution

Google’s current Interactions API for Gemini 3 models supports combining built-in Google Search with JSON Schema structured output in one request. That removes the legacy architectural conflict that produced the Springsteen incomplete-structure failure and supersedes a second repair call as the normal path.

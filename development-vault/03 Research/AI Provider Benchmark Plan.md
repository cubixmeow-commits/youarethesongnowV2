---
type: research-plan
status: in-progress
updated: 2026-08-28
area: ai-provider-benchmarks
owners:
  - CuBiX Meow
  - Brut
related-contract: First Build Feature Contract
---

# AI Provider Benchmark Plan

## Purpose

Resolve build-freeze checklist item 4 by measuring candidate providers for creative effectiveness, portrait fidelity, style performance, reliability, latency and total cost per successful delivered image.

## Available provider: Groq

CuBiX Meow has a Groq account and can create an API key when implementation/testing is authorized.

Current official Groq documentation shows production text/reasoning models and multimodal models that accept images but produce text. Groq is therefore a candidate for inexpensive reasoning and evaluation stages, not final image generation.

No API key has been created, exposed or stored for this project. Key creation and secret configuration wait until the build freeze permits testing; use an environment secret rather than committing credentials.

## Tentative primary image hub: fal.ai

CuBiX Meow and Brut tentatively selected fal.ai as the primary image-provider hub so the project can test multiple commercial image models through one queue/API integration. This is a benchmark starting point, not a final provider commitment.

fal.ai remains acceptable only if testing confirms portrait privacy, commercial terms, deletion behavior, reliability and total cost. The application must call fal.ai only from the PHP server; never expose the fal API key to the web or Flutter client.

### Initial reference-image benchmark set

Prices are public snapshots checked 2026-08-28 and must be refreshed before cost modeling.

1. **Seedream V4 Edit** — inexpensive multi-reference baseline; up to ten input images; approximately $0.03 per image.
2. **FLUX.2 Pro** — production-oriented composition/style comparator with multi-image referencing; approximately $0.03 for the first output megapixel plus documented incremental input/output megapixel charges.
3. **Nano Banana / Gemini Flash Image Edit** — fast multi-reference comparator; approximately $0.039 per image in the referenced configuration.
4. **Nano Banana 2 Edit** — stronger multi-reference comparator; up to fourteen reference images; approximately $0.08 for a 1K image.
5. **GPT Image 2 Edit through fal.ai** — high-quality multi-image comparator; retrieve live price and terms at test time.

Optional identity-specialist models such as PhotoMaker or subject-driven LoRAs may be tested for solo portraits, but they cannot become a general launch route unless they also handle couples and the required artistic compositions reliably.

Do not assign low/medium/high tier labels until blind tests establish which models actually meet the quality promise. Price alone does not determine the tier.

## Initial Groq candidates

- `openai/gpt-oss-20b`: low-cost baseline for normalization, song-match scoring, structured transformations, lightweight safety checks and simple prompt assembly.
- `openai/gpt-oss-120b`: stronger comparator for Song DNA, visual narrative and final prompt compilation when the smaller model loses meaningful quality.
- Groq multimodal/vision models: development comparator for text-based image evaluation only; verify production status before relying on any model.
- Groq Compound/search: research only for permitted factual song context. Do not use it as a substitute for licensed lyrics or send restricted copyrighted material contrary to source/provider terms.

Model IDs, prices and production status must be fetched at benchmark time because hosted catalogs change.

## Responsibilities to benchmark

1. Artist/title normalization and match confidence
2. Authorized lyrics or song-context to non-quoting Song DNA
3. Visual narrative and portrait-integration plan
4. Curated-style prompt compilation
5. Safety/originality checks
6. Image-output evaluation from visual input, if production-capable
7. Final image generation through separate image providers

## Test matrix

Use a fixed, versioned evaluation set covering:

- common, obscure and ambiguous songs;
- solo portraits and couples;
- varied ages, skin tones, hair, lighting and source-photo quality;
- all candidate styles and square/portrait/landscape outputs;
- lyrical moods ranging from joyful and romantic to dark or abstract;
- provider failures, safety refusals and retry/fallback behavior.

For development tests involving lyrics, prefer public-domain, owner-controlled or directly authorized material and follow the development-only safeguards in [[Lyrics Retrieval and Legal Feasibility]].

## Metrics per stage and delivered image

- owner-blind quality score;
- song specificity without quoted/copyrighted text;
- prompt-structure validity;
- facial identity and two-person separation;
- style fidelity and composition quality;
- safety/originality compliance;
- first-pass success rate and retry rate;
- p50/p95 latency;
- input/output tokens or image charges;
- total provider cost per attempt;
- total cost per successful usable image, including failed attempts and retries.

## Cost-control rules

- Put every provider behind a replaceable adapter.
- Use deterministic structured output where appropriate.
- Begin with the least expensive model that can meet the quality threshold.
- Promote a stage to a stronger model only when blind evaluation demonstrates meaningful improvement.
- Set provider budgets, rate limits, concurrency limits and alerts before testing at scale.
- Never log or commit API keys or raw lyrics.

## Exit conditions

- [ ] At least two text/reasoning configurations are compared on the same evaluation set.
- [ ] At least two image providers/models are compared for each launch quality tier.
- [ ] Portrait and couples performance is measured separately.
- [ ] Each candidate launch style has a preferred and fallback image route.
- [ ] Reliability, latency, retry and safety behavior are measured.
- [ ] Total cost per successful image supports the $20 subscription and proposed credit allowance.
- [ ] CuBiX Meow and Brut approve the chosen routing map and quality threshold.

## Official Groq references checked 2026-08-28

- Supported production/preview models and pricing: https://console.groq.com/docs/models
- API reference: https://console.groq.com/docs/api-reference
- OpenAI-compatible Responses API; image input produces text output: https://console.groq.com/docs/openai
- Vision/image understanding: https://console.groq.com/docs/vision
- Billing, usage and spend limits: https://console.groq.com/docs/billing-faqs

## Official fal.ai references checked 2026-08-28

- Platform/model pricing: https://fal.ai/pricing
- FLUX.2 Pro and multi-image references: https://fal.ai/docs/model-api-reference/image-generation-api/flux-2-pro
- Seedream V4 Edit: https://fal.ai/models/fal-ai/bytedance/seedream/v4/edit
- Nano Banana Edit: https://fal.ai/models/fal-ai/nano-banana/edit
- Nano Banana 2 Edit: https://fal.ai/models/fal-ai/nano-banana-2/edit
- GPT Image 2 Edit multi-image API: https://fal.ai/models/openai/gpt-image-2/edit

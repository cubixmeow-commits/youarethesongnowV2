# Change log

Record changes that alter current product behavior, architecture, contracts, decisions, deployment, or roadmap. Keep entries brief; Git holds file-level history.

## 2026-09-04

- Established the original PHP/SQLite beginner plan prototype, seeded demonstration routes, onboarding research lab, brand system, and early Flutter iOS shell.
- Built the shared 26-screen mobile web experience with progressive question groups, Kip, plan and route payoff, membership preview, Today, recording, reflection, Journal, Journey, photos, and postcards.
- Added optional MapLibre/Geoapify mapping, protected server proxy, quota controls, and tested map-response validation.
- Added a bounded Gemini/Groq `RouteCoach` adapter with strict candidate/reason validation and deterministic fallback; it remains isolated from the UI.
- Connected a provider-neutral route-selection pipeline to live map candidates: structural validation, hard time/freshness/geometry filters, deterministic scoring, bounded Gemini/Groq ID ordering, explicit suitability unknowns, daily AI call allowance, and rules fallback.
- Published the mobile web build to GitHub `main` for Hostinger deployment (`51f272f`).
- Changed the welcome headline to “A little weight goes a long way.” and retained the original supporting caption.
- Corrected the pack-check image crop to focus on face, hands, straps, and chest buckle; published as `d4173f6`.
- Created this FirstRuck development vault and added instructions for GPT, Cursor, and future LLM tools.

# Rebuild Planning Index

This directory is the durable planning area for rebuilding AISaga Arcana as YouAreTheSongNow V2.

## Documents

- `00-legacy-repo-assessment.md` — what the old repository tells us now
- `01-product-rebuild-brief.md` — what we are rebuilding and what still needs product decisions
- `02-target-architecture.md` — proposed technical shape for web + mobile
- `03-migration-plan.md` — how to move from V1 knowledge to a V2 product safely
- `04-initial-backlog.md` — ordered work for the first rebuild phase
- `05-cursor-workflow.md` — how Cursor and other agents should use this material

## Evidence labels

Use these labels in future assessment notes:

- **Observed** — directly verified in repository source/config/runtime evidence.
- **Inferred** — strongly suggested by evidence but not yet traced end-to-end.
- **Proposed** — a V2 design recommendation, not a statement about V1.
- **Decision** — explicitly accepted for V2 by the project owners.

## Important warning

The V1 repository contains documentation that is not consistently about Arcana. For example, the root `SCHEMA.md` describes a separate VibeKB content model while the root `README.md` identifies the application as AI Saga Arcana. Therefore legacy documentation must be validated before being treated as authoritative.

## Next assessment pass

The next pass should inventory Arcana-specific routes/pages, authentication, database tables, generation workflows/prompts, uploads/media storage, gallery behavior, credits, Stripe, email, environment variables, deployment assumptions, error handling, and any user data that may need migration.

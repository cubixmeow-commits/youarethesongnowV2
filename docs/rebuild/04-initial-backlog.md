# 04 — Initial Backlog

Ordered roughly by dependency. This is a living backlog, not a commitment to all items.

## Epic A — Legacy Arcana audit

- [ ] Build a top-level V1 directory/file map and mark Arcana vs unrelated/generated material.
- [ ] Trace all user-facing routes/screens.
- [ ] Extract the real MySQL schema from migrations/dumps/source usage; do not use the unrelated root `SCHEMA.md` as database truth.
- [ ] Trace signup/login/session/password/email behavior.
- [ ] Trace band/song metadata acquisition.
- [ ] Trace Dynamic Band Lore Engine/narrative/prompt construction.
- [ ] Trace Gemini/text/image calls, configuration, retries and fallbacks.
- [ ] Trace upload -> image conversion -> storage -> gallery lifecycle.
- [ ] Trace credits and Stripe purchase/webhook behavior.
- [ ] Inventory environment variables and external services without copying secrets.
- [ ] Inventory migration-worthy user/project/media records.
- [ ] Produce keep/change/replace/retire matrix.

## Epic B — Product definition

- [ ] Confirm V2's smallest Arcana creation experience.
- [ ] Decide permitted source/lyrics handling policy.
- [ ] Decide role of user photos/uploads.
- [ ] Define project, generation and gallery lifecycle.
- [ ] Define initial credit/entitlement UX.
- [ ] Define privacy/share/delete behavior.

## Epic C — Architecture spike

- [ ] Create web/mobile monorepo spike.
- [ ] Prove shared contract validation across clients/server.
- [ ] Prove upload flow to object storage.
- [ ] Prove queued generation with durable status.
- [ ] Prove provider adapter can be swapped/mocked.
- [ ] Record selected stack in ADRs.

## Epic D — First vertical slice

- [ ] Authentication/session.
- [ ] Create/read project API.
- [ ] Web project creation UI.
- [ ] Mobile project creation UI.
- [ ] Generation request + queued/running/complete/failed states.
- [ ] Arcana orchestration + one provider adapter.
- [ ] Persist and render generated media.
- [ ] Cross-device project/gallery retrieval.
- [ ] Failure/retry/refund rules as applicable.
- [ ] Integration tests for the complete path.

## Epic E — Commercial foundation

Do after generation semantics are stable.

- [ ] Credit ledger model.
- [ ] Idempotent debit/refund operations.
- [ ] Stripe purchase flow.
- [ ] Verified/idempotent webhook processing.
- [ ] Purchase/ledger admin diagnostics.
- [ ] Subscription model only if selected by product decision.

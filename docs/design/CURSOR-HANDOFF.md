# CURSOR-HANDOFF — Explore deploy verification + private-build diagnostics

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** Private Hostinger verification (no `.env` changes required)

## What the latest iPhone failure proved

Observed message:

> We could not create visual directions for this song yet. Try again.

That is `explore_failed` (not the earlier 404/`explore_unavailable` copy). There was **no** `(diagnostic)` suffix.

## GitHub `main` tip (repo truth)

- Gemini model reliability fix: `9f7a27576e61c5f9335a6894668d31828e3eb3ca`
- Private-build diagnostics (no `.env`): `e9b7482fd712b5f35fb5826e3cc20077e7daad77`
- Current `main` tip: `8201020e26404e7fc77e56a53c13804c5f1d5e55`

## Deployment mechanism (verified)

- Hostinger PHP app under `/yatsnV2/` is **owner-synchronized from Git** (`main`). It is **not** auto-deployed by GitHub Actions.
- GitHub “deployments” for this repo are **GitHub Pages only** (`/docs` Meow Control). They do **not** update youarethesongnow.com PHP.
- Pushing to `main` does not refresh Hostinger until the owner pulls/syncs that tree.

## Live site inspection (from cloud agent, before this fix)

| Probe | Result |
| --- | --- |
| `GET /api/v1/health` | 200 `yatsn-v2` (no `build` field yet) |
| `GET /api/v1/explore-directions/readiness` | **401** → route **exists** (from `9f7a275`) |
| Live `/assets/js/explore.js` | **byte-identical** to repo, includes diagnostic UI |
| Diagnostic suffix on iPhone | **Missing** |

**Conclusion:** Hostinger already had Explore code from `9f7a275+`. The missing diagnostic was **not** primarily a stale-JS problem. Diagnostics were only attached when `APP_DEBUG` or `APP_ENV=development`. Hostinger can be private/owner-only while still using production-like env vars. **No `.env` edit is required.**

Gemini provider logic was **not** changed again. Make the diagnostic visible first; then interpret the exact status.

## Exact fix (git-only, no `.env`)

1. `BuildInfo::allowDiagnostics()` is true whenever `ALLOW_EXTERNAL_USERS` is false (already Hostinger’s private Build 1 gate).
2. Explore errors include `fields.diagnostic` + `fields.build` under that rule.
3. `GET /api/v1/health` includes safe `build.commit` while private.
4. Create / Explore badge shows the short commit for iPhone deploy checks.
5. Prefer live `.git` HEAD after Hostinger sync; `app/build-stamp.php` is FTP fallback only.

## After Hostinger git sync

No `.env` work. From the host (optional):

```bash
php bin/diagnose-gemini-explore.php
php bin/diagnose-gemini-explore.php --smoke
```

From the phone (signed out):

```text
https://youarethesongnow.com/api/v1/health
```

Expect `data.build.commit` (12-char short SHA from the synced checkout).

## Tests

```text
php tests/run.php
=== Results: 953 passed, 0 failed ===
```

## Exact iPhone retest

1. Someone syncs Hostinger `/yatsnV2` to latest `main` (git pull). **You do not need to edit `.env`.**
2. Soft-refresh `/create`.
3. Badge should show `First build · Song DNA · <shortsha>` matching health `build.commit`.
4. Explore options once.
5. On failure, status must include something like  
   `(provider-incomplete-output · build <shortsha>)`.
6. Send that exact diagnostic back before any further Gemini code changes.

## Final commit

- **Feature:** `e9b7482fd712b5f35fb5826e3cc20077e7daad77` — private-build diagnostics without `.env`
- **Tip:** `8201020e26404e7fc77e56a53c13804c5f1d5e55` — stamp/handoff alignment
- **Branch:** `main`
- **Requires:** Hostinger git sync only

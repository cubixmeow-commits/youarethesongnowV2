# CURSOR-HANDOFF — Explore deploy verification + private-build diagnostics

**Date:** 2026-08-31  
**Working branch:** `main`  
**Phase:** Private Hostinger verification (no `.env` changes required)

## What the latest iPhone failure proved

Observed message:

> We could not create visual directions for this song yet. Try again.

That is `explore_failed` (not the earlier 404/`explore_unavailable` copy). There was **no** `(diagnostic)` suffix.

## GitHub `main` tip (repo truth)

- Expected reliability fix: `9f7a27576e61c5f9335a6894668d31828e3eb3ca`
- Tip after handoff hash note: `07d3c8609d457a2207ed85c4a55ce3171bb27130`
- Newer private-diagnostics/build-stamp commit: see **Final commit** below

## Deployment mechanism (verified)

- Hostinger PHP app under `/yatsnV2/` is **owner-synchronized from Git** (`main`). It is **not** auto-deployed by GitHub Actions.
- GitHub “deployments” for this repo are **GitHub Pages only** (`/docs` Meow Control). They do **not** update youarethesongnow.com PHP.
- Therefore pushing to `main` does not by itself refresh Hostinger until the owner pulls/syncs that tree.

## Live site inspection (from cloud agent)

| Probe | Result |
| --- | --- |
| `GET /api/v1/health` | 200 `yatsn-v2` (no build field yet on old deploy) |
| `GET /api/v1/explore-directions/readiness` | **401 unauthorized** → route **exists** (added in `9f7a275`) |
| `POST /api/v1/explore-directions` (no session) | 401 unauthorized → route exists |
| Live `/assets/js/explore.js` | **byte-identical** to repo (`md5 fa7d2ca6…`), includes `fields?.diagnostic` UI |
| Asset `?v=` timestamps | ~2026-08-31 05:18–06:10 UTC (earlier same day) |

**Conclusion:** Hostinger already had the Explore reliability JS + readiness route from `9f7a275+`. Deployment was **not** stuck before that commit. The missing diagnostic suffix was caused by code that only exposed `error.fields.diagnostic` when `APP_DEBUG` or `APP_ENV=development` — Hostinger private site can run with production-like env while still being owner-only. **No `.env` edit is required** for the new fix.

Gemini provider logic was **not** changed again in this follow-up. First make the diagnostic visible; then interpret the exact status.

## Exact fix in this follow-up (git-only)

1. `BuildInfo::allowDiagnostics()` is true for **private Build 1** whenever `ALLOW_EXTERNAL_USERS` is false (already the Hostinger default / hard gate). No `APP_ENV` / `APP_DEBUG` change.
2. Explore errors include `fields.diagnostic` + `fields.build` under that private-build rule.
3. `GET /api/v1/health` includes a safe `build.commit` while private.
4. Create page / Explore badge show the short commit so an iPhone can confirm the deploy without guessing.
5. `app/build-stamp.php` is a fallback if `.git` is absent; Hostinger git sync prefers live `.git` HEAD.

## Smoke / diagnose on Hostinger

After the owner syncs `main` on the host (no `.env` work):

```bash
cd /path/to/yatsnV2   # Hostinger app root
php bin/diagnose-gemini-explore.php
php bin/diagnose-gemini-explore.php --smoke
```

Or from any browser/phone (signed out):

```text
https://youarethesongnow.com/api/v1/health
```

Expect `data.build.commit` to match the synced revision (12-char short hash).

## Tests

```text
php tests/run.php
=== Results: 953 passed, 0 failed ===
```

## Exact iPhone retest (after Hostinger git sync)

1. Soft-refresh `/create` (or clear tab) so `explore.js` reloads.
2. Confirm the Explore badge shows `First build · Song DNA · <shortsha>` matching health `build.commit`.
3. Discover a song → select portrait → **Explore options**.
4. If it still fails, the status **must** include a parenthetical diagnostic, e.g.  
   `We could not create visual directions… (provider-incomplete-output · build <shortsha>)`.
5. Send that exact diagnostic string back before any further Gemini code changes.

## What GPT / owners still need

1. Owner Hostinger sync of latest `main` (git pull under `/yatsnV2/`). Cloud agent cannot SSH/FTP deploy. **No `.env` edits.**
2. After sync: open `/api/v1/health` and confirm `build.commit`; then Explore once and paste the diagnostic (or success).
3. Only then decide whether another Gemini request/model change is warranted.

## Final commit

- **Hash:** `6fa401fdecdbdb454d2a81b015607a27d0b5a0a5`
- **Message:** Show Explore diagnostics on private Hostinger without .env
- **Branch:** `main`
- **Requires:** Hostinger git sync only (no `.env` changes)

Stamp follow-up may bump `app/build-stamp.php` to match HEAD; Hostinger git checkouts prefer `.git` HEAD over the stamp.
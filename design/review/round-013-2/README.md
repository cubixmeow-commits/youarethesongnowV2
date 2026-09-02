# Round 013.2 — Automatic Asset Cache Busting

**Date:** 2026-09-02  
**Scope:** Deterministic versioned URLs for coupled first-party CSS/JS on Create and global navigation

## Problem

Per-file `?v=filemtime` query strings allowed mixed stale/new assets after deploy (e.g. new `explore.js` copy with old cached `app.css`). Mobile refresh did not guarantee a coherent frontend release.

## Strategy

`Yatsn\Support\AssetRelease` computes a **12-character release id** from the SHA-256 digests of every file in a bundle:

| Bundle | Files |
| --- | --- |
| `create` | `app.css`, `app.js`, `explore.js`, `song-search.js` |
| `core` | `app.css`, `app.js` |
| `showcase` | `app.css`, `app.js`, `showcase.js` |
| `component-lab` | `app.css`, `app.js`, `component-lab.js` |

Rendered URLs use **path fingerprinting** (not query strings):

```
/assets/r/{releaseId}/css/app.css
/assets/r/{releaseId}/js/app.js
```

Hostinger/Apache rewrite (`public/.htaccess`) maps versioned paths to on-disk files and applies `Cache-Control: public, max-age=31536000, immutable`. The PHP dev router mirrors the same behavior.

Authenticated HTML responses use `Cache-Control: no-store` via `View::page()`.

## Example URLs

| Before | After |
| --- | --- |
| `/assets/css/app.css?v=1725123456` | `/assets/r/a1b2c3d4e5f6/css/app.css` |
| `/assets/js/explore.js?v=1725123400` | `/assets/r/a1b2c3d4e5f6/js/explore.js` |

All four Create assets share the **same** `{releaseId}`.

## Response headers (verified locally)

| Resource | Cache-Control |
| --- | --- |
| `GET /create` | `no-store` |
| `GET /assets/r/{id}/css/app.css` | `public, max-age=31536000, immutable` |
| `GET /assets/r/{id}/js/app.js` | `public, max-age=31536000, immutable` |

## Evidence

| File | Description |
| --- | --- |
| `mobile-390-fresh-load.png` | Fresh authenticated Create load at 390×844 |
| `review-notes.json` | Sanitized URLs, release id, headers, reload confirmation |
| `verify-asset-versioning.mjs` | Automated cache + Round 013.1 preservation checks |

## Verification

```bash
php -S 127.0.0.1:8767 -t public public/router.php
cd design/review/round-013-2 && npm install
node verify-asset-versioning.mjs
php tests/run.php
```

Round 013.1 direction-choice behavior is re-checked inside `verify-asset-versioning.mjs` (hidden generate bar, Generate for me + Explore options present).

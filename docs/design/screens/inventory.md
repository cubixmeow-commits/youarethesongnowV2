# Screen inventory

Routing: `public/index.php`  
Layout: `templates/layouts/main.php` (all pages)  
Interactive client: `public/assets/js/app.js` (product) + `showcase.js` (marketing archive)

## User-facing routes

| Route | Auth | Template | Purpose |
| --- | --- | --- | --- |
| `GET /` | Guest (authed redirects to `/create`) | `pages/welcome.php` | Marketing home / launchpad |
| `GET /showcase` | Optional | `pages/showcase.php` | V1 archive masonry + lightbox |
| `GET /sign-in` | Open | `pages/sign-in.php` | Magic link + password |
| `GET /sign-in/complete` | Open | `pages/sign-in-complete.php` | Magic link completion |
| `GET /activate` | Open | `pages/activate.php` | Invitation activation |
| `GET /create` | Required | `pages/create.php` | Song → People → Direction → paywall → generate |
| `GET /gallery` | Required | `pages/gallery.php` | Private collection |
| `GET /images/{id}` | Required | `pages/image.php` | Reveal + share/download/regenerate/delete |
| `GET /account` | Required | `pages/account.php` | Profile, sessions, deletion, sign-out |
| `GET /owner` | Owner | `owner/dashboard.php` | Ops: invites, users, jobs, styles |
| `GET /terms` | Open | `pages/legal.php` | Provisional terms |
| `GET /privacy` | Open | `pages/legal.php` | Provisional privacy |
| `GET /shared/{token}` | Guest chrome | `pages/shared.php` or legal 404 | Public shared image |
| Unmatched | — | `pages/legal.php` | 404 copy |

## Primary user flows

```text
Guest: Home → Showcase? → Sign in / Activate → Create
Create (as-built Build 1): Song → People → Direction → Review → [Paywall?] → Generating → Image reveal
Create (target architecture): Song → Song DNA → Quick Generate | Explore Options → [Fine Tune?] → Generate → Reveal
  See create-flow.md — not implemented yet.
Collection: Gallery → Image detail → share / download / regenerate / delete
Account: profile / email / password / sessions / delete / sign out
Owner: invitations, users, jobs, style activate/deactivate
```

## Nav destinations (chrome)

| Audience | Tabs |
| --- | --- |
| Guest | Sign in only (**Showcase not in chrome**) |
| Authed | Create, Gallery, Account |
| Owner | + Owner (secondary) |

**Conceptual target (not shipped):** Create, Gallery, Discover, Account. See [create-flow.md](./create-flow.md) §10 before changing nav.

## Screen notes

### Home (`welcome.php`)
Poster hero (featured V1 sample), progressive world carousel (77), final Create invite. Scripts: `showcase.js`.

### Showcase (`showcase.php`)
Legacy disclosure, orientation filters, Masonry + imagesLoaded, infinite/load-more, accessible dialog. Web-layout-specific.

### Create (`create.php`)
Largest product surface. Progressive disclosure of People/Direction. Sticky summary on desktop. Paywall + generation progress. Dev Song DNA inspection panel. Portrait delete dialog.

**Target architecture (documentation only):** Song DNA selection, Quick Generate / Explore Options / Fine Tune, DNA-aware generation experience, reveal CTAs Save/Share/Variation/Reimagine. Full map: [create-flow.md](./create-flow.md). Do not treat the style grid as the long-term primary creative control.

### Gallery / Image / Account / Owner
Thin PHP shells; content hydrated by `app.js` + `/api/v1/*`.

### Auth / Activate / Legal / Shared
Mostly form + status or server-rendered media.

## API coupling (UI-relevant)

Create, Gallery, Image, Account, Auth, Owner UIs call `/api/v1` endpoints registered in `src/Api/ApiV1.php`. Mobile session/refresh endpoints exist but are not used by current templates.

## Missing screens (product-known, not in this audit as routes)

- Dedicated offline / network-error page
- Dedicated generation failure + retry screen (inline status only)
- Showcase entry in primary guest nav
- Post-Build-1 commerce/print/upscale UI (deferred by ADR/contracts)

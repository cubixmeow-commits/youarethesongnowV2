---
type: product-catalog
status: owner-directed-selection
updated: 2026-08-28
area: image-styles
owners:
  - CuBiX Meow
  - Brut
source: V1 legacy style catalog
---

# Launch Style Catalog

## Decision

Seed all 52 recoverable V1 style families into the V2 database. Fifteen are launch candidates and start active only after prompt rewriting, preview generation and provider benchmarks. The remaining 37 start inactive but remain visible and editable to owners/admins for future activation.

No style is discarded. Database status controls product availability; deployment is not required to activate or retire a style.

## Fifteen launch candidates

| Order | Style key | Customer-facing name | Category | Why it belongs in the launch set |
|---:|---|---|---|---|
| 1 | `photoreal_cinema` | Cinematic Realism | Cinematic | Broadest premium appeal; strong baseline for portraits, couples and dramatic storytelling. |
| 2 | `oil_painting_realism` | Heirloom Oil Portrait | Fine Art | Romantic, giftable and wedding-friendly with a timeless physical-art feeling. |
| 3 | `watercolor` | Luminous Watercolor | Fine Art | Soft, emotional and approachable; distinct from realism without overwhelming the subject. |
| 4 | `impressionist_painting` | Impressionist Light | Fine Art | Painterly atmosphere and color suited to romantic and nostalgic songs. |
| 5 | `art_deco` | Art Deco Elegance | Era / Glamour | Sophisticated geometric glamour for couples, celebrations and formal compositions. |
| 6 | `indie_pop_pastel` | Indie Pastel Dream | Contemporary | Gentle modern style with broad lifestyle and relationship appeal. |
| 7 | `jazz_expressionism` | Midnight Jazz | Music / Fine Art | Musical rhythm, club light and expressive brushwork without becoming genre costume. |
| 8 | `psychedelic_rock` | Psychedelic Reverie | Music / Poster | Vivid, unmistakably music-driven energy for adventurous users. |
| 9 | `punk_collage` | Punk Zine Collage | Music / Collage | Raw handmade contrast and a strong alternative aesthetic. |
| 10 | `synthpop_1980s_neon_airbrush` | Neon Synth Dream | Music / Retro | Highly recognizable retro color and atmosphere with strong visual impact. |
| 11 | `cyberpunk_neon_noir` | Neon Noir | Cinematic / Sci-Fi | Dramatic night-world option with broad cinematic popularity. |
| 12 | `dreamcore` | Liminal Dream | Surreal | Emotionally flexible, symbolic and especially compatible with song interpretation. |
| 13 | `collage_surrealism` | Surreal Story Collage | Surreal / Collage | Turns Song DNA symbols into layered narrative imagery. |
| 14 | `gothic_darkwave` | Gothic Romance | Dark / Romantic | Serves darker love songs and dramatic emotional material with elegance. |
| 15 | `fantasy_epic_illustration` | Epic Fantasy | Fantasy | High-adventure transformation that showcases premium composition and portrait integration. |

These names are working product names and remain editable in admin. The set is selected for range, not because legacy prompts are production-ready.

## Thirty-seven future-activation styles

All start `inactive` and remain available in admin.

| Style key | Working name | Category |
|---|---|---|
| `fantasy_metal_poster_alestorm` | Fantasy Metal Poster | Music / Fantasy |
| `pixel_art_legendary` | Legendary Pixel Quest | Pixel / Fantasy |
| `anime` | Cel-Shaded Adventure | Illustration |
| `metal_fantasy` | Heavy Metal Fantasy | Music / Fantasy |
| `vaporwave` | Vaporwave Nostalgia | Retro / Surreal |
| `grunge_90s` | 1990s Grunge | Music / Collage |
| `hiphop_graffiti` | Hip-Hop Graffiti | Music / Street Art |
| `minimal_techno` | Minimal Techno Geometry | Music / Graphic |
| `expressionist` | Raw Expressionism | Fine Art |
| `charcoal_ink_sumi_e` | Charcoal and Ink | Fine Art |
| `jazz_deco_1920s` | 1920s Jazz Deco | Era / Music |
| `wpa_swing_1930s` | 1930s Swing Poster | Era / Music |
| `big_band_1940s_portrait` | 1940s Big-Band Portrait | Era / Portrait |
| `rockabilly_1950s_americana` | 1950s Rockabilly | Era / Music |
| `motown_1960s_studio` | 1960s Soul-Studio Glamour | Era / Music |
| `krautrock_1970s_minimal` | 1970s Motorik Minimalism | Era / Music |
| `prog_rock_1970s_surrealism` | 1970s Progressive Surrealism | Era / Music |
| `disco_1970s_glam_airbrush` | 1970s Disco Glamour | Era / Music |
| `reggae_roots_1970s_poster` | 1970s Roots Poster | Era / Music |
| `new_wave_1980s_memphis` | 1980s New-Wave Geometry | Era / Music |
| `hardcore_punk_1980s_xerox` | 1980s Hardcore Xerox | Era / Music |
| `shoegaze_1990s_blur` | 1990s Shoegaze Haze | Era / Music |
| `triphop_1990s_noir` | 1990s Trip-Hop Noir | Era / Music |
| `black_metal_1990s_photocopy` | 1990s Black-Metal Photocopy | Era / Music |
| `indie_2000s_minimal_swiss` | 2000s Indie Minimalism | Era / Music |
| `y2k_pop_chrome_2000s` | Y2K Chrome Pop | Era / Pop |
| `edm_2010s_festival_neon` | 2010s Festival Neon | Era / Electronic |
| `kpop_2010s_hypergloss` | 2010s Hypergloss Pop | Era / Pop |
| `hyperpop_2020s_glitch_candy` | Glitch-Candy Hyperpop | Era / Pop |
| `retrofuturism_70s_airbrush` | Retro-Future Airbrush | Sci-Fi / Retro |
| `sci_fi_holographic_ui` | Holographic Science Fiction | Sci-Fi |
| `space_opera_pulp` | Pulp Space Opera | Sci-Fi / Illustration |
| `dark_fantasy_baroque` | Baroque Dark Fantasy | Fantasy / Dark |
| `steampunk_brass_gauges` | Brasswork Steampunk | Fantasy / Industrial |
| `dieselpunk_industrial` | Industrial Dieselpunk | Sci-Fi / Industrial |
| `spaghetti_western_poster` | Western Cinema Poster | Cinematic / Era |
| `giallo_italian_thriller_poster` | Saturated Thriller Cinema | Cinematic / Era |

## Database/admin contract

Every style is a database record available to authorized owners/admins. Minimum fields:

- immutable internal ID;
- unique stable `style_key`;
- customer-facing name and short description;
- category and sort order;
- `active`, `inactive` or `archived` lifecycle state;
- preview image and preview alt text;
- current immutable prompt-version ID;
- source/provenance (`v1_legacy`, `v2_rewrite`, or future source);
- quality/provider routing configuration;
- created/updated timestamps and editor identity.

Prompt text lives in immutable style-version records. Editing creates a new version rather than overwriting generation history. Existing gallery items retain the style/version/model provenance used to create them.

Admin capabilities:

1. Search/filter all active, inactive and archived styles.
2. Create, edit metadata, create prompt versions and preview drafts.
3. Reorder active customer-facing styles.
4. Activate only after required preview and routing checks pass.
5. Deactivate immediately without deleting provenance.
6. Compare draft/current versions and roll back by activating an earlier version.
7. Set preferred and fallback model routes by quality tier.

## Rewrite and activation gate

No legacy prompt is copied directly into production without review. Before a style can be active:

- remove artist, band, franchise, album-cover and living-artist imitation references;
- replace branded style language with visual properties;
- remove instructions to render titles, logos, tour text or other typography;
- preserve the useful `STYLE / MEDIUM / COLOR / LIGHTING / SURFACE / MOTION / COMPOSITION / AVOID` structure where effective;
- test solo and couples portraits in all three orientations;
- produce an approved preview using the actual routed model;
- verify safety/originality and acceptable cost;
- obtain CuBiX Meow and Brut approval.

## Checklist status

- [x] Fifteen balanced launch candidates selected.
- [x] All 52 recoverable V1 styles classified for database seeding and admin access.
- [ ] Launch prompts rewritten and versioned.
- [ ] Preview/evaluation set generated.
- [ ] Preferred/fallback model routes selected for every active style and quality tier.
- [ ] CuBiX Meow and Brut approve final prompts, previews and routing.


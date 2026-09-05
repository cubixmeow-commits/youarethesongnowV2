# Data privacy and safety

## Current safety boundary

FirstRuck provides general beginner fitness guidance and is not medical care. Conservative prototype rules cap the first outing and added load, allow an empty pack, and avoid automatic progression. A concerning symptom response tells the person to stop. These rules still require qualified fitness and health review before launch.

## Data classification

| Data | Current handling | Future requirement |
| --- | --- | --- |
| Safety onboarding answer | Memory only | Explicit decision before any persistence |
| Other onboarding answers | Browser localStorage | Consent and account-sync contract if uploaded |
| Active GPS points | Active recording memory | Retention, encryption, deletion, and precision policy |
| Interrupted recording | IndexedDB local recovery | Clear expiry and deletion behavior |
| Journal summaries | Browser localStorage | Account ownership/export/delete contract |
| Resized photos | IndexedDB | Protected object storage and access policy if synced |
| Original photo/EXIF | Not stored | Continue avoiding unless clearly required |
| Postcard | Generated locally | Excludes route, start point, coordinates, and trace |
| Provider keys | Protected server config | Secrets manager or protected hosting variables |

## Non-negotiable rules

- Never expose provider secrets in client code, Git, logs, error bodies, screenshots, or prompts.
- Never make an exact route, start location, GPS trace, or photo public by default.
- Never send health answers, photos, exact coordinates, or track data to an LLM for route copy.
- Never present route geometry as proof of safety or legal access.
- Never generate medical advice during a walk.
- Preserve a non-loaded option and do not reward heavier loads.
- Provide deletion and export before durable accounts become a launch dependency.

## Before social or sync

Define authentication, authorization, encryption in transit/at rest, media access URLs, retention, account deletion, data export, abuse reporting, blocking, moderation, location redaction, photo visibility, age policy, and incident response. Threat-model predictable start locations and repeated routines.

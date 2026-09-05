# LLM working agreement

## Starting a task

1. Read the five required files in `AGENTS.md`.
2. State the current implemented behavior relevant to the request.
3. Separate owner decisions, verified behavior, proposals, and unresolved questions.
4. Read only the task-specific vault notes and source entry points from the repository map.
5. Check the working tree and preserve unrelated work.

## Planning work

Write plans against the active roadmap and accepted decisions. Identify the smallest complete vertical slice, its user-visible outcome, source files, contracts, data/privacy effect, and acceptance checks. Do not create a second plan document when updating the roadmap or an existing task note is enough.

## Implementing work

- Reuse current domain rules and stable answer values.
- Keep demo/live provenance explicit.
- Keep providers behind server-side adapters.
- Add or change tests when behavior, contracts, safety rules, persistence, or permissions change.
- Visually inspect changes involving crop, layout, responsive behavior, or generated assets.
- Do not broaden into accounts, purchases, social publishing, background GPS, health claims, or provider spending without a product decision.

## Finishing work

1. Run proportional checks from `Testing and Deployment.md`.
2. Update current status, architecture, roadmap, decisions, open questions, or contracts if the change affects them.
3. Add a concise dated change-log line.
4. Report what changed, what was verified, and any material limitation.
5. Do not claim a provider, payment, AI, map, or safety feature is live unless it was tested through the real integration.

## Handoff template

Use this compact structure when another model or person will continue:

```text
Objective:
Current behavior:
Accepted decisions involved:
Files changed:
Checks passed:
Known limitations:
Next concrete step:
Vault files updated:
```

Never put credentials, private location data, photo contents, or full provider payloads in a handoff.

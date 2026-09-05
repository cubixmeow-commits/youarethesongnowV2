# FirstRuck development vault

Updated: 2026-09-04
Baseline commit when created: `d4173f6`

This vault is the compact source of current FirstRuck product and engineering context. It is designed for people and any LLM-based development tool. Start here before reading source code.

## Read first

Every task begins with:

1. [Current Status](01%20Current%20Project/Current%20Status.md)
2. [Active Roadmap](01%20Current%20Project/Active%20Roadmap.md)
3. [Current Architecture](01%20Current%20Project/Current%20Architecture.md)
4. [Decision Index](04%20Decisions/Decision%20Index.md)

Then read only what the task needs:

| Task | Read |
| --- | --- |
| Product planning or scope | [Product Direction](01%20Current%20Project/Product%20Direction.md), [Open Questions](01%20Current%20Project/Open%20Questions.md) |
| Onboarding or membership | [Onboarding and Membership](02%20Product/Onboarding%20and%20Membership.md) |
| Today, recording, journal, photos, sharing | [Core App Experience](02%20Product/Core%20App%20Experience.md) |
| Copy, visuals, mascot, imagery | [Brand and Kip](02%20Product/Brand%20and%20Kip.md) |
| Web implementation or storage | [Web Application](03%20Engineering/Web%20Application.md), [Data Privacy and Safety](03%20Engineering/Data%20Privacy%20and%20Safety.md) |
| Mapping, routes, or LLM providers | [Routes, Mapping, and AI](03%20Engineering/Routes%20Mapping%20and%20AI.md) |
| Flutter or native iOS | [Flutter](03%20Engineering/Flutter.md) |
| Tests, GitHub, or Hostinger | [Testing and Deployment](03%20Engineering/Testing%20and%20Deployment.md) |
| Finding files | [Repository Map](03%20Engineering/Repository%20Map.md) |
| Starting or handing off work | [LLM Working Agreement](05%20Operations/LLM%20Working%20Agreement.md) |

## What is authoritative

The owner's latest instruction wins. Accepted decisions and `01 Current Project` describe current intent. Passing implementation and tests are the authority for actual behavior. Older briefs under `docs/`, `CURSOR-*.md`, and `onboarding-lab/` are supporting history unless the vault explicitly adopts them.

## Maintenance rule

Do not turn this into a diary or paste source code into it. Update concise current truth, decisions, contracts, risks, and next actions. When a change makes a statement false, update that statement in the same commit and add one dated line to the change log.

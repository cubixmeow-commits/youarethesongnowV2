# Fix Find My Song 503 Error

## Problem
On the mobile web app, using **Find my song** can return a raw HTML `503 Service Unavailable` response directly into the page UI.

Observed with:
- Artist: The Fray
- Song title: How to save a life
- Action: Tap **Find my song**

The UI currently renders the server error HTML verbatim, including `<!DOCTYPE html>`, the `503 Service Unavailable` heading, and the server message.

## Required fix
1. Identify which backend/API request powers **Find my song** and why it is returning 503.
2. Do not ever render raw upstream HTML into the app UI.
3. Detect non-2xx responses before attempting to display or parse their body.
4. Show a clean user-facing error state instead, for example:
   - **Song lookup is temporarily unavailable. Please try again in a moment.**
5. Log enough diagnostic information server-side to identify the failing upstream service, status code, and request path without exposing internal details to the user.
6. Add retry handling only where appropriate; avoid uncontrolled automatic retry loops.
7. Verify the mobile layout remains clean when lookup fails.
8. Test successful lookup, no-result lookup, malformed upstream response, timeout, 429/rate limit, and 5xx failures.

## Acceptance criteria
- Raw HTML/server responses can never appear in the Find My Song result area.
- A 503 produces a polished in-app error message.
- Successful song lookup behavior is unchanged.
- The underlying source of the 503 is identified and fixed or handled gracefully.

## Screenshot reference
Captured on iPhone showing the raw 503 response rendered beneath the **Find my song** button on youarethesongnow.com.

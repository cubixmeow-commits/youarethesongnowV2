---
type: product-workshop
status: active
updated: 2026-08-27
area: first-build
owners:
  - CuBiX Meow
  - Brut
---

# First Build Feature Workshop

This is the working questionnaire for CuBiX Meow and Brut to define **exactly what belongs in the first web build of YouAreTheSongNow V2**.

The goal is not to answer every future-product question. The goal is to make deliberate choices about:

- what the first build absolutely needs;
- what is useful but can wait;
- what belongs much later;
- what is still uncertain;
- what V1 did that we do not want anymore.

The build freeze remains active while this workshop is unresolved.

## How to use this tonight

For every question, record one of these outcomes:

- **FIRST BUILD** — required in the first usable web version.
- **SOON AFTER** — useful, but not necessary for the first complete slice.
- **LATER** — explicitly deferred.
- **MAYBE / RESEARCH** — needs more thinking, research, or testing.
- **NO / RETIRE** — do not build unless the decision changes later.

When you choose **FIRST BUILD**, also define:

1. what the user sees;
2. what the user can do;
3. what the system must remember;
4. what happens on failure;
5. what "done" means.

Do not turn a vague preference into a technical requirement. If the answer is unclear, mark it **MAYBE / RESEARCH**.

---

# A. First-build product promise

1. What is the single sentence that describes what the first build lets a user accomplish?
2. If a stranger uses the first build once, what is the one experience we most want them to remember?
3. Is the first build primarily about **turning a song into an image**, or about **putting the user inside a song-inspired image**?
4. Is portrait integration essential to the identity of the product from day one, or can the first slice prove song interpretation without portraits?
5. Does the first build need to feel like a complete consumer product, or can it be a polished private/beta product?
6. Who is the first intended user: just us, invited testers, paying users, or the public?
7. What would make us say, "This is YouAreTheSongNow," rather than "this is just another image generator"?
8. Which V1 behavior is the most important thing to preserve emotionally?
9. Which V1 behavior is the most important thing to improve?
10. What would be unacceptable to leave out of the first build?
11. What would be tempting to add but would distract us from proving the core experience?
12. What would make the first build feel overcomplicated?
13. What is the smallest first build we would actually be proud to show someone?
14. Does the first release need commercial features, or should it first prove the creative experience?
15. What must be true before we lift the build freeze?

---

# B. Access, accounts, and onboarding

16. Does the first build require user accounts at all?
17. If yes, can users register themselves, or is access invite-only initially?
18. Should there be a waitlist instead of open registration?
19. Do we want email + password only at first?
20. Do we care about Sign in with Apple / Google in the web build, or is that later?
21. Must users verify their email before generating?
22. Can unverified users log in but not generate?
23. Do we need password reset in the first build?
24. Do we need username/display name, or just email?
25. What profile information, if any, should exist beyond email?
26. Do users need to change their email/password from an account screen?
27. Do users need to delete their account in the first build?
28. If an account is deleted, should its generated images and portraits be deleted immediately?
29. Should users see a short onboarding explanation before their first generation?
30. Do we want a guided onboarding flow or just a simple generator with explanations?
31. Should first-time users see sample output before generating?
32. Do we need terms/privacy acceptance at registration?
33. Do we need a persistent "what is this app?" explanation inside the logged-in experience?
34. Should admin access be a simple flag in the first build?
35. Are we intentionally supporting future Flutter token auth in the backend from day one, even if web uses sessions?

---

# C. Core user object: project vs generation

36. Does a user create a **Project** before generating, or does clicking Generate automatically create one?
37. Is a Project essentially "one song," or can one Project contain multiple songs?
38. Can a Project contain multiple generations/variations of the same song?
39. Should users be able to rename a Project?
40. Should Project names default to "Artist — Song"?
41. Can a user reopen a Project later and generate another version?
42. Should the previous Song DNA and settings be reused automatically when reopening?
43. Do we need a project list in the first build, or is gallery/history enough?
44. Does every generated image belong to exactly one Project?
45. Should a failed generation still remain visible in Project history?
46. Should Project history show attempts and failures, or only successful results?
47. Do we need project deletion in the first build?
48. Should deleting a Project delete all associated generated images?
49. Should users be able to duplicate/remix a Project?
50. Is "Project" language user-facing, or just an internal system concept?

---

# D. Song input

51. What are the minimum fields needed to start a generation?
52. Is artist required?
53. Is song title required?
54. Are lyrics required?
55. Can a user generate from artist + title without supplying lyrics?
56. If lyrics are optional, what should happen when they are absent?
57. Is the first build strictly user-supplied lyrics/input text?
58. Do we want any automatic lyric lookup in the first build?
59. If not, should the UI explicitly say "paste lyrics or song text here"?
60. Should we limit input length?
61. Should we show the user if their input has been truncated?
62. Should users be able to enter only a verse/section instead of a full song?
63. Should there be a field for "what this song means to me"?
64. Should there be a freeform custom-instructions field?
65. Should custom instructions be optional and deliberately soft rather than dominant?
66. Do we want presets such as "literal," "metaphorical," or "balanced" interpretation?
67. Do we want the user to choose a particular lyric/moment to focus on?
68. Should the app remember the raw lyrics after generation?
69. If raw lyrics are not retained, should we clearly tell the user that?
70. What derived information should persist even if raw lyrics do not?
71. Should users be able to edit the song/source information after a generation exists?
72. If they edit it, is that a new generation snapshot or a mutation of the existing Project?

---

# E. Song interpretation and Song DNA

73. Does the first build need a separate Song Interpretation stage before Song DNA?
74. What is the actual purpose of Song DNA in V2?
75. Which V1 Song DNA fields are definitely worth keeping?
76. Which V1 fields should move into later planning stages instead?
77. Do we need themes?
78. Do we need symbols?
79. Do we need emotional arc?
80. Do we need literal anchors?
81. Do we need metaphorical interpretation?
82. Do we need setting candidates?
83. Do we need visual metaphors?
84. Do we need palette direction in Song DNA, or should that come later?
85. Do we need "do not depict" constraints?
86. Do we need confidence/ambiguity fields?
87. Should Song DNA produce one interpretation or several alternatives?
88. Should users ever see Song DNA?
89. Should users be able to edit Song DNA before image generation?
90. Would exposing Song DNA make the product more interesting or just more complicated?
91. Should advanced/debug users be able to inspect it while normal users do not?
92. Should Song DNA always be persisted for reproducibility?
93. If the Song DNA model fails, do we retry automatically or fail the job?
94. Should we allow a fallback minimal interpretation if structured DNA fails?
95. How important is deterministic structure vs creative freedom at this stage?

---

# F. Visual narrative planning

96. Do we want the first build to include a separate Visual Narrative Plan?
97. Is its main job to answer: "What single depictable moment expresses this song?"
98. Should it choose one scene or produce several candidates?
99. If several, should the model choose or should the user choose?
100. Should it define literal vs metaphorical balance?
101. Should it define setting/location?
102. Should it define time period/era?
103. Should it define the action happening in the frame?
104. Should it define focal hierarchy?
105. Should it define props/symbols?
106. Should it record rejected alternatives to reduce repetition on reruns?
107. Should a user be able to ask for "another interpretation" before generating an image?
108. Would that be first-build value or a later refinement feature?
109. Should this artifact be persisted for prompt provenance?

---

# G. Artist visual identity / Dynamic Band Lore

110. What do we actually want the old "Dynamic Band Lore" idea to mean in V2?
111. Is it really **Artist Visual Identity** rather than lore?
112. Do we want any genuine lore/story-world system in the first build?
113. Should artist visual identity run automatically on every generation?
114. Should it be optional?
115. Should users be able to turn it off?
116. What sources are acceptable for artist visual identity?
117. Model general knowledge only?
118. User-supplied visual references?
119. Curated database metadata?
120. A hybrid?
121. How do we avoid falsely attributing a visual style to a specific artist/designer?
122. Should the system produce general traits rather than named imitation references?
123. Do we want the old StyleMap-like dimensions: medium, palette, lighting, surface, motion, composition, mood, atmosphere, detail, influence, typography, avoid?
124. Which of those matter for image generation today?
125. Should artist identity be persisted per artist, per song, or per generation?
126. If we generate the same artist twice, should we reuse cached identity?
127. Should users see the artist identity brief?
128. Is this feature important enough for first build, or should Song DNA + visual planning carry the first release?

---

# H. Curated visual styles

129. Do we want selectable visual styles in the first build?
130. If yes, how many styles should we start with?
131. Should there be a "no additional style" default?
132. Should the default be artist visual identity instead?
133. Which old V1 styles are worth recovering?
134. Should we migrate exact old style prompts or redesign them from their intent?
135. Should styles be grouped by categories?
136. Should each style have a sample image?
137. Should users preview styles before selection?
138. Can users combine styles?
139. Should styles be editable only by admins?
140. Should style revisions be versioned rather than overwritten?
141. Do we need style activation/deactivation in first build?
142. Do we want historical/era styles, genre styles, technique styles, or all three?
143. Is a large style library useful at launch, or would 5–10 excellent choices be better?

---

# I. Portraits / "put me in the song"

144. Is portrait upload FIRST BUILD, SOON AFTER, or LATER?
145. If portraits ship, is one portrait enough initially?
146. Do we need two people from day one?
147. Do we ever need more than two people?
148. Is the uploaded person always the protagonist?
149. Can the user choose their role in the scene?
150. Should portrait mode be optional for every Project?
151. How important is identity fidelity relative to song interpretation?
152. What is the minimum acceptable resemblance?
153. Should we generate if the provider cannot reliably preserve the face?
154. If a retry would require removing the portrait, what should happen?
155. Fail and tell the user?
156. Ask permission to make an identity-free version?
157. Produce a clearly labeled degraded result?
158. Never drop the portrait automatically?
159. Should portrait images be private by default?
160. Should users be able to delete their portrait independently of generated images?
161. Should portraits persist for reuse across Projects?
162. Or should portraits be temporary per generation?
163. Should the user have a reusable "My Portraits" library later?
164. Should portraits be automatically resized/cropped?
165. Should EXIF/location metadata be stripped?
166. What file size/type limits feel appropriate?
167. Should users be warned that portrait images are sent to an AI provider?
168. Do we need a consent checkbox for people other than the account owner?
169. Does portrait functionality belong behind a paid entitlement eventually?

---

# J. Scene composition and image controls

170. Which controls should normal users see in first build?
171. Aspect ratio?
172. Orientation labels like portrait/square/landscape instead of raw ratios?
173. Visual style?
174. Literal/metaphorical balance?
175. Custom instructions?
176. Portrait on/off?
177. Number of images?
178. Anything else?
179. Which controls should stay internal to avoid turning the app into a generic prompt UI?
180. Should users choose camera/framing, or should the engine own cinematography?
181. Should users choose color palette?
182. Should users choose era/setting?
183. Should there be an "advanced controls" drawer later?
184. How much control makes the product better before it starts weakening the song interpretation?

---

# K. Prompt compiler and prompt visibility

185. Should users ever see the final compiled image prompt?
186. Should admins/debug mode see it?
187. Should the compiled prompt be stored for every attempt?
188. If we do not store full prompt text, what provenance is enough to reproduce/debug it?
189. Should prompt templates be versioned from the first build?
190. Should retry policy also have a version?
191. Should provider/model/settings be recorded on every attempt?
192. Should a successful image be reproducible later from stored artifacts?
193. How important is exact reproducibility when model providers themselves are nondeterministic?
194. Should custom user text be clearly separated from system prompt policy so it cannot override safety/identity rules?
195. Do we need prompt injection defenses around pasted lyrics and custom instructions from day one?

---

# L. AI providers

196. Which text-model provider do we want to test first?
197. Which image-model provider do we want to test first?
198. Do text and image generation need to use the same vendor?
199. Is provider flexibility a first-build architectural requirement even if only one provider is implemented initially?
200. What matters most for the first image provider: quality, identity fidelity, speed, price, safety behavior, API reliability, aspect-ratio support?
201. Do we need model selection in the user interface?
202. Or should provider/model be entirely internal?
203. Should admins be able to switch active models without code changes?
204. Do we need fallback to a second image provider in first build?
205. Is one reliable provider enough initially?
206. Do we need cost-per-generation tracking from the first build?
207. Should provider errors be visible to users in technical detail or translated into friendly messages?
208. Which failures deserve automatic retry vs immediate user feedback?

---

# M. Generation job behavior

209. What exactly happens when the user presses Generate?
210. Does the UI immediately create a Project and Job?
211. Should users see distinct progress stages like "Understanding song," "Planning scene," "Generating image"?
212. Or just one general progress indicator?
213. Should users be able to leave the page while generation continues?
214. Should finished jobs appear automatically when they return?
215. Should users be allowed multiple simultaneous jobs?
216. If yes, how many?
217. Should there be a queue position?
218. Should users be able to cancel a job?
219. If cancelled, when does cancellation actually stop provider work?
220. Should cancelled jobs consume credits later if monetization exists?
221. How many image-generation retry attempts should be allowed?
222. How long before a job is considered timed out/stuck?
223. Should a stuck job be automatically recoverable?
224. Do we need an admin "retry job" control?
225. Should failed attempts be visible to normal users?
226. Should failure messages explain whether the issue was input, safety, provider outage, or system error?
227. Should submitting the same request twice accidentally create duplicate jobs, or should generation creation use idempotency?

---

# N. Generated output

228. Does one Generate action produce one image or multiple candidates?
229. If multiple, how many?
230. Is multiple-image generation first build or later?
231. What output resolution is required initially?
232. Do we need an upscale action?
233. Is upscale separate from generation?
234. Should users be able to request a variation of an existing image?
235. Should users be able to regenerate with the same plan but a different visual style?
236. Should users be able to regenerate with a new scene interpretation?
237. Do we need before/after comparisons between generations?
238. Should output contain any branding/watermark?
239. If branding exists, should it be generated in the image or applied afterward?
240. Should users have both clean and branded exports?
241. Do we need embedded metadata in downloads?
242. Do users need PNG, JPEG, WebP, or only one download format initially?

---

# O. Gallery, history, and projects UI

243. Is a gallery required in the first build?
244. Does the gallery show only successful images?
245. Should it group images by Project/song?
246. What metadata should appear on gallery cards?
247. Artist + song?
248. Date?
249. Style?
250. Portrait indicator?
251. Should users be able to favorite images?
252. Should users be able to search by artist/song?
253. Do we need filters in the first build?
254. Should image detail show the creative artifacts behind the result?
255. Should image detail show the generation settings?
256. Should users be able to download directly from gallery?
257. Should users be able to delete generated images?
258. If they delete an image, should the generation record remain?
259. Do we need a trash/recovery period?
260. Should galleries be private by default?
261. Is public sharing a first-build feature?
262. If sharing exists, is it share-link only or public profile/gallery?
263. Do we need social-share buttons or just normal device/browser sharing later?

---

# P. Image and portrait storage

264. Is local filesystem enough for development?
265. What production storage provider do we want to evaluate?
266. Do we want to keep Backblaze B2 because V1 used it?
267. Or choose storage later behind an abstraction?
268. Should all user media be private by default?
269. Should public share images become explicitly public copies/URLs?
270. Do we need thumbnails generated on upload/output?
271. What image formats should be stored?
272. Should we keep original provider output as well as optimized WebP?
273. How long do failed-generation temporary files live?
274. How long do uploaded portraits live?
275. Do we need automatic orphan cleanup in first build?
276. How important is storage-provider migration support initially?
277. Should image records store content hash, dimensions, MIME type, size, ownership, and creation date from day one?
278. Should deleting an account guarantee media deletion from object storage?

---

# Q. Credits, free use, and commercial model

279. Does the first build need any credit system?
280. Can the first private/beta build simply allow a fixed number of generations per account?
281. Do we want free accounts at launch?
282. Do we want subscriptions?
283. Do we want one-time credit packs?
284. Do we want a hybrid subscription + credits model?
285. Or should all monetization be deferred until the creative experience is proven?
286. If credits exist, what exactly costs a credit?
287. One job?
288. One successful image?
289. Each attempt?
290. Upscaling?
291. Portrait mode?
292. Does a failed generation refund automatically?
293. Should credits be reserved at job creation and captured only on success?
294. Should users see a transaction/credit history?
295. Do we need admin credit adjustment from day one?
296. Should the first build track actual provider cost even if users are not charged?
297. Which usage limits do we need even without payments to protect API spend?

---

# R. Plans and entitlements

298. Do we need named plans in the first build?
299. Can first build simply have one default entitlement set plus admin overrides?
300. What features might eventually be gated?
301. Portrait count?
302. Premium visual styles?
303. Higher resolution?
304. Priority queue?
305. Number of generations?
306. Storage limits?
307. Multiple images per generation?
308. Should entitlement logic exist server-side even before billing is implemented?
309. Which entitlements actually need to exist in the initial schema/code?
310. Which plan concepts from V1 should be completely retired?

---

# S. Stripe and payments

311. Does Stripe belong in the first build?
312. If not, what milestone should trigger adding it?
313. If Stripe is included, do we start with subscription, credit pack, or one simple purchase type?
314. Do we need a pricing page in the first build?
315. Do users need billing history?
316. Do users need to cancel/manage subscription inside the app or via Stripe portal?
317. Do we need coupon/promo codes?
318. Do we need refunds from the admin UI?
319. Should payment fulfillment use exactly one webhook path from the beginning?
320. Do we need webhook event history for debugging?
321. What do we deliberately postpone until we research iOS/App Store purchase rules for Flutter?

---

# T. Email and notifications

322. Which emails are truly required initially?
323. Password reset?
324. Email verification?
325. Generation complete?
326. Payment receipt?
327. Account deletion?
328. Should users receive a generation-complete email if the web page is closed?
329. Or is in-app status enough for first build?
330. Do we need notification preferences?
331. Should the notification system be designed so mobile push can plug in later?
332. Is push notification support entirely later?

---

# U. Admin / operator tools

333. What will we personally need to inspect while testing the first build?
334. User list/search?
335. Generation job list?
336. Failed jobs?
337. Generation attempt details?
338. Prompt versions?
339. Style management?
340. AI provider/model settings?
341. Credit adjustments?
342. Media cleanup?
343. Payment lookup?
344. Maintenance mode?
345. Feature flags?
346. Do these need a polished admin UI, or would simple internal pages/scripts be enough initially?
347. Which admin capability would save us the most time during early testing?
348. Which admin features could become security liabilities if added too early?

---

# V. User-facing screens

349. Which screens are FIRST BUILD?
350. Landing/home page?
351. Register?
352. Login?
353. Forgot/reset password?
354. Logged-in dashboard?
355. Generator?
356. Generation-progress screen?
357. Result/detail screen?
358. Gallery/history?
359. Project detail?
360. Account settings?
361. Billing/pricing?
362. Help/FAQ?
363. Privacy/terms?
364. Admin screens?
365. Which screen should be the user's home after login?
366. What is the shortest path from login to generating an image?
367. Should generator and project detail be the same screen initially?

---

# W. UX and product personality

368. Should the web app feel cinematic, magical, technical, playful, or some combination?
369. How much of the old Arcana identity should remain?
370. Is "You Are The Song Now" the primary consumer name everywhere?
371. Do we still use "Arcana" anywhere internally or publicly?
372. Do we keep any "lore" language?
373. How much explanation should the app give about what the AI is doing?
374. Should users see staged status messages that make the creative process feel intentional?
375. Should the app feel fast/minimal or rich/immersive?
376. Should mobile-friendliness be a requirement for the first web UI even before Flutter?
377. What accessibility basics do we want from day one?
378. Do we need dark mode only, light mode, or system theme?
379. What pieces of UX should deliberately be designed as reusable concepts for Flutter later?

---

# X. Privacy, safety, and deletion

380. What user data do we absolutely need to retain?
381. What user data should we avoid retaining?
382. Are raw lyrics transient by default?
383. Are portrait uploads private by default?
384. Are generated images private by default?
385. What happens when a user deletes a generated image?
386. What happens when a user deletes a portrait?
387. What happens when a user deletes their account?
388. Do we need an explicit retention period for temporary media?
389. Do we need audit logs for admin actions?
390. What information must never appear in normal application logs?
391. Should prompts containing lyrics/custom instructions be stored verbatim?
392. Should sensitive provider payloads be redacted from logs?
393. Do we need rate limiting in the first build?
394. What kinds of abuse would be expensive enough that we should protect against them immediately?
395. Do we need content moderation beyond provider safety for the first private/beta build?
396. Which safety choices are product decisions versus provider constraints?

---

# Y. Failure and recovery UX

397. What should a user see when Song DNA fails?
398. What should a user see when image generation fails?
399. What should a user see when a provider refuses the request?
400. What should a user see when the provider is temporarily unavailable?
401. What should happen if image generation succeeds but storage fails?
402. What should happen if a user refreshes/reopens during generation?
403. What should happen if the same generation submission is accidentally sent twice?
404. Should the user have a Retry button?
405. Should retry reuse the same plan or start the entire creative process again?
406. Should users be charged/limited for failures?
407. Which technical failure details should be hidden from users but available to admins?
408. Do we need a "report bad result" function in first build?
409. Should users be able to say why a result was bad: face, meaning, style, composition, text artifact, etc.?
410. Would that feedback be valuable enough to Prompt Lab to include early?

---

# Z. Prompt Lab and internal evaluation

411. Do we need internal A/B prompt testing tools in the application, or is the vault/manual testing enough initially?
412. Should every generation record the prompt/spec versions used?
413. Should we save generated creative artifacts for comparing prompt versions?
414. Do we want a small set of canonical test songs for regression testing?
415. Do we need canonical portrait test inputs?
416. What qualities should we score when evaluating outputs?
417. Song meaning fidelity?
418. Portrait identity fidelity?
419. Scene readability?
420. Artist visual identity?
421. Cinematography?
422. Safety/provider success?
423. Should quality scoring be manual at first?
424. Do we want automated evaluation later?
425. What would make us reject a new prompt version even if it produces prettier images?

---

# AA. API and future Flutter readiness

426. Which web actions must go through JSON endpoints from the first build rather than being PHP-template-only?
427. Should create Project/generation be API-first?
428. Should generation status be API-first?
429. Should gallery/assets be API-first?
430. Should portrait upload be API-first?
431. Should account/entitlement state be API-first?
432. Does the first web UI consume the same endpoints Flutter will eventually consume?
433. Or do we allow some web-only server-rendered operations initially?
434. What API responses need stable error codes rather than only messages?
435. Do we want API versioning before Flutter ships, or simply design for compatibility?
436. Should generation creation support idempotency keys from first implementation?
437. What data should never be exposed through the client API?
438. Which server-side decisions must remain impossible for Flutter/web to override?
439. What do we need to design now so an old mobile client can still work after the backend evolves?

---

# AB. Development / production operations

440. Where will the first build run locally?
441. Where do we expect initial production PHP hosting to run?
442. Can that environment run a persistent worker, or will we use cron-driven workers?
443. How frequently can cron run?
444. Is SQLite appropriate for that host and expected concurrency?
445. Where will the SQLite database live?
446. How will it be backed up?
447. How will schema migrations be applied?
448. What should happen during maintenance/migrations?
449. Where will logs live?
450. How will secrets/API keys be configured?
451. How will production object storage credentials be handled?
452. Do we need a staging environment before public launch?
453. Do we need automated deployment or is manual deploy acceptable initially?
454. What is the minimum operational setup required before inviting testers?

---

# AC. Testing requirements

455. Which behaviors would be too dangerous to ship without automated tests?
456. Authentication/authorization?
457. Resource ownership?
458. Job state transitions?
459. Credit reservation/refund if credits exist?
460. Stripe webhook idempotency if billing exists?
461. Prompt JSON schema validation?
462. Provider adapter error handling?
463. Upload validation?
464. Media deletion?
465. Retry policy?
466. Should we create integration fixtures using fake providers so tests do not spend API money?
467. Should the first vertical slice include a provider simulator/mock?
468. Which real end-to-end tests do we want to run manually before calling the first build usable?

---

# AD. Explicit defer / retire workshop

For every major V1 or proposed feature below, mark **FIRST BUILD / SOON AFTER / LATER / MAYBE / NO**:

469. Invite codes
470. Email verification
471. Password reset
472. User profiles
473. Account deletion
474. First-class Projects
475. Raw lyric persistence
476. Song Interpretation stage
477. Song DNA
478. Visual Narrative Plan
479. Artist Visual Identity
480. Genuine lore/story-world generation
481. Curated visual styles
482. Large historical/genre style catalog
483. One portrait
484. Two portraits
485. Reusable portrait library
486. Advanced camera controls
487. Multiple images per generation
488. Variations/remix
489. Upscaling
490. Watermark/branding
491. Clean + branded downloads
492. Gallery
493. Favorites
494. Search/filter gallery
495. Public share links
496. Public profiles/galleries
497. Credits
498. Subscriptions
499. Credit packs
500. Stripe
501. Entitlement system
502. Generation-complete email
503. Admin user search
504. Admin job inspector
505. Admin style editor
506. Admin prompt-version viewer
507. Maintenance mode
508. Provider switching UI
509. Automated quality evaluation
510. Prompt A/B testing UI
511. Cost dashboard
512. Flutter/iOS client
513. Push notifications
514. Apple in-app purchases
515. Android Flutter app

---

# AE. First-build definition exercise

After going through the questions, fill this section out together.

## First build: one-sentence promise

> TBD

## Intended first users

> TBD

## FIRST BUILD features

- [ ] TBD

## SOON AFTER

- [ ] TBD

## LATER

- [ ] TBD

## MAYBE / RESEARCH

- [ ] TBD

## NO / RETIRE

- [ ] TBD

## First-build user flow

```text
TBD
```

## First-build screens

1. TBD

## First-build creative pipeline

```text
TBD
```

## Data we persist

- TBD

## Data we deliberately do not persist

- TBD

## First-build external integrations

- AI text provider: TBD
- AI image provider: TBD
- Email: TBD
- Object storage: TBD
- Payments: TBD / none
- Other: TBD

## First-build failure promises

- TBD

## First-build portrait promise

> TBD

## First-build commercial model

> TBD

## Build-freeze exit criteria

The freeze can be considered for lifting only when we can answer:

- [ ] What exact user flow are we building first?
- [ ] What exact screens are included?
- [ ] What exact creative stages are included?
- [ ] Are portraits included? If yes, what is the identity/fallback promise?
- [ ] What happens to raw lyrics?
- [ ] What is stored vs transient?
- [ ] Which provider(s) are used initially?
- [ ] What storage strategy is used initially?
- [ ] Are credits/payments included or explicitly deferred?
- [ ] What are the first-build entitlements/limits?
- [ ] What is the failure/retry behavior?
- [ ] What is deliberately NOT being built?

---

# AF. Final prioritization test

Before accepting any feature into FIRST BUILD, ask all five:

1. **Does this directly strengthen the core promise?**
2. **Will leaving it out prevent meaningful testing?**
3. **Does adding it unlock more learning than complexity?**
4. **Would it be painful to retrofit later if we skip it now?**
5. **Can we describe its first-build behavior precisely enough to test it?**

If the answers are mostly no, defer it.

## Cat says

The first build does not need every toy in the house. It needs the right box, the right mouse, and a clear reason to pounce.

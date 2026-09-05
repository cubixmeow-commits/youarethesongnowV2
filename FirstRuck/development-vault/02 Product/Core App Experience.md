# Core app experience

## Navigation

The current post-onboarding app has four destinations:

- **Today:** one prepared session, weekly rhythm, and the clearest action to begin.
- **Routes:** selected route, alternatives, example/live provenance, and area search.
- **Journal:** completed outings, reflections, local photos, and postcard creation.
- **Journey:** progress framed as memories and confidence rather than competition.

## Recording

The browser offers a real foreground GPS mode and a labelled demo mode. The recorder shows elapsed time and distance, accepts photos during the outing, supports pause/resume, and offers fixed check-ins. A concerning-symptom response tells the user to stop rather than generating playful or AI advice.

GPS fixes are rejected when coordinates are invalid, accuracy is worse than 35 metres, timestamps do not move forward, or implied speed is at least 3.5 metres per second. Resuming resets the previous point so paused distance is not bridged.

Browser limitations must stay visible in product planning: foreground tracking can stop when the browser is suspended or the device locks. It is not equivalent to native background recording.

## Journal and photos

At completion, the user chooses a simple feeling, may attach a photo, and saves a journal entry. The browser creates a resized JPEG derivative through canvas, which strips original EXIF. Entries and photos persist on that browser only.

The postcard contains a photo or Kip, route title, time, distance, and demo label where applicable. It excludes start point, GPS coordinates, route trace, and original photo metadata. The person still needs a reminder to inspect visible details in the image before sharing.

## Social direction

Current sharing is deliberate export or the native share sheet. This is the safest first social layer and a useful product differentiator. An in-app community is future work and requires audience controls, moderation, reporting, blocking, deletion, and default location redaction.

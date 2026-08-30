# V1-Compatible Song Analysis Mode

Status: active for private development by owner direction.

V2 currently begins song analysis with the proven V1 method:

1. send the entered artist and song to Gemini with Google Search enabled;
2. request a detailed structured Song DNA without lyric quotation;
3. accept a complete, usable Song DNA returned by Gemini even when modern grounding metadata or an explicit `lyricsLocated` flag is absent;
4. distinguish confirmed lyric analysis, grounded song-context analysis, and V1-compatible model analysis in the development inspection UI;
5. reject malformed or incomplete Song DNA;
6. never persist raw lyrics, verification text, provider prompts, or provider response bodies.

This is a private-development compatibility decision, not evidence that lyrics were licensed or verified. Before external or commercial access, the approved catalog, licensing, artist-direct permissions, and legal gates still apply.

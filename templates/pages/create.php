<section class="create" data-create data-csrf="<?= e($csrf ?? '') ?>"<?php
  $build = \Yatsn\Support\BuildInfo::publicSummary();
  if (!empty($build['privateBuild'])):
?> data-private-build="1"<?php
  endif;
  if (!empty($build['commit'])):
?> data-build-commit="<?= e((string) $build['commit']) ?>" data-build-source="<?= e((string) ($build['source'] ?? '')) ?>"<?php endif; ?>>
  <header class="session-header">
    <div class="session-header__art" aria-hidden="true">
      <img
        class="session-header__mark"
        src="/assets/images/brand/ys-monogram-flat-platinum.svg"
        width="32"
        height="32"
        alt=""
        decoding="async">
    </div>
    <div class="session-header__meta">
      <p class="session-header__eyebrow">Create</p>
      <h1 class="session-header__title">Choose your song</h1>
      <p class="session-header__hint quiet">Start with a meaningful song, then turn it into artwork with the people you love.</p>
    </div>
    <ol class="session-progress" aria-label="Session stages">
      <li class="session-progress__step is-current"><span>01</span> Song</li>
      <li class="session-progress__step"><span>02</span> People</li>
      <li class="session-progress__step"><span>03</span> Direction</li>
    </ol>
  </header>

  <div class="create__layout">
    <div class="create__main">
      <section class="movement movement--primary yatsn-create-entry" id="the-song" aria-labelledby="song-heading">
        <p class="movement__num">01</p>
        <h2 id="song-heading">The song</h2>
        <p class="movement__lead yatsn-create-entry__lead">Search for the song that will lead your artwork.</p>

        <div class="yatsn-resume-row" data-song-resume hidden></div>

        <section
          class="yatsn-song-search stack"
          data-yatsn-song-search
          data-yatsn-song-state="idle"
          aria-labelledby="song-search-heading">
          <h3 id="song-search-heading" class="visually-hidden">Song search</h3>
          <form id="song-form" class="yatsn-song-search__form stack" novalidate>
            <div class="yatsn-song-search__fields track-source__fields">
              <label class="yatsn-field">
                <span class="yatsn-field__label">Artist or band</span>
                <input type="text" name="artist" required placeholder="Enter the artist or band" autocomplete="off" inputmode="search">
              </label>
              <label class="yatsn-field">
                <span class="yatsn-field__label">Song title</span>
                <input type="text" name="title" required placeholder="Enter the song title" autocomplete="off" inputmode="search">
              </label>
            </div>
            <button class="btn btn--primary yatsn-btn yatsn-btn--primary yatsn-song-search__submit" type="submit">Find this song</button>
          </form>

          <div class="yatsn-status yatsn-status--info" data-song-status role="status" aria-live="polite" hidden></div>

          <div class="yatsn-song-result yatsn-song-result--loading is-loading" data-song-result-loading hidden aria-hidden="true">
            <span class="yatsn-song-result__art yatsn-artwork__stage"><span class="yatsn-skeleton yatsn-skeleton--square" aria-hidden="true"></span></span>
            <span class="yatsn-song-result__copy">
              <span class="yatsn-skeleton" aria-hidden="true"></span>
              <span class="yatsn-skeleton yatsn-skeleton--line" aria-hidden="true"></span>
            </span>
          </div>

          <div class="yatsn-song-results" data-song-results hidden role="region" aria-label="Song match"></div>

          <div class="yatsn-song-selected" data-song-selected hidden></div>

          <div class="yatsn-status__actions" data-song-retry-wrap hidden>
            <button class="btn btn--secondary yatsn-btn yatsn-btn--secondary" type="button" data-song-retry>Try again</button>
          </div>
        </section>

        <div class="yatsn-recent-row" data-song-recent hidden></div>
        <div class="development-source" data-development-analysis-panel hidden>
          <h3>Development Song DNA inspection</h3>
          <p><strong data-development-analysis-song></strong></p>
          <p class="quiet" data-development-analysis-status></p>
          <p data-development-analysis-excerpt hidden></p>
          <details data-development-analysis-wrap hidden>
            <summary>Show grounded Song DNA</summary>
            <div data-development-analysis-preview></div>
          </details>
          <div data-development-analysis-sources hidden>
            <p class="quiet"><strong>Google Search grounding sources</strong></p>
            <ul data-development-analysis-source-list></ul>
          </div>
          <p class="quiet">Private development aid. Gemini searches and analyzes the lyrics within the request. Lyrics and verification excerpts are not saved by You Are The Song Now.</p>
        </div>
      </section>

      <section class="movement" id="the-people" aria-labelledby="people-heading" hidden>
        <p class="movement__num">02</p>
        <h2 id="people-heading">The people</h2>
        <p class="movement__lead">Select portraits to include in your composition.</p>
        <p class="quiet">Add one or two clear portraits. We will use their faces to place them naturally inside your cinematic world.</p>
        <form id="portrait-form" class="stack">
          <label class="file">
            <span>Upload a portrait</span>
            <input type="file" name="file" accept="image/jpeg,image/png,image/webp" required>
          </label>
          <button class="btn btn--secondary" type="submit">Upload a portrait</button>
          <p class="quiet">Only upload photos you have permission to use. Your photos remain private and are processed only to create your artwork. See <a href="/privacy">Privacy</a>.</p>
          <p class="status" data-portrait-status role="status" aria-live="polite"></p>
        </form>
        <div class="portrait-tray" data-portrait-grid role="list" aria-label="Portrait selection"></div>
      </section>

      <section class="movement" id="the-direction" aria-labelledby="direction-heading" hidden>
        <p class="movement__num">03</p>
        <h2 id="direction-heading">The direction</h2>

        <div class="stack direction-controls">
          <div data-style-world>
            <h3>Choose your world</h3>
            <p class="quiet">Select the visual treatment that will lead your image.</p>
            <div class="style-grid" data-style-grid role="listbox" aria-label="Styles"></div>
          </div>

          <fieldset class="direction-fieldset">
            <legend>Choose image quality</legend>
            <div class="choice-row" data-quality-row></div>
          </fieldset>

          <fieldset class="direction-fieldset">
            <legend>Choose a format</legend>
            <div class="choice-row" data-orientation-row></div>
          </fieldset>

          <label class="check">
            <input type="checkbox" data-no-text>
            <span>No text in image</span>
          </label>
          <p class="quiet">Choose this if you want the finished artwork to contain no words or lettering.</p>

          <label class="check">
            <input type="checkbox" data-special-toggle>
            <span>I have something specific in mind</span>
          </label>
          <label data-special-wrap hidden>
            <span>Special instructions</span>
            <textarea data-special maxlength="500" rows="3" placeholder="Describe a setting, mood, colors, clothing, or another detail you would like us to consider."></textarea>
          </label>

          <button class="btn btn--primary" type="button" data-review>Review my creation</button>
          <p class="status" data-direction-status role="status" aria-live="polite"></p>
        </div>
      </section>
    </div>

    <aside class="create__summary session-board" aria-live="polite">
      <p class="session-board__label">Overview</p>
      <h2>Your creation</h2>
      <dl class="summary-list" data-summary>
        <div><dt>Song</dt><dd data-sum-song>Not chosen yet</dd></div>
        <div><dt>People</dt><dd data-sum-people>None selected</dd></div>
        <div><dt>Style</dt><dd data-sum-style>Not chosen yet</dd></div>
        <div><dt>Quality</dt><dd data-sum-quality>Medium</dd></div>
        <div><dt>Format</dt><dd data-sum-orientation>Square</dd></div>
        <div><dt>Credits</dt><dd data-sum-credits>—</dd></div>
      </dl>
      <div class="create__generate-bar" data-generate-bar hidden>
        <p class="create__generate-hint quiet" data-generate-hint role="status" aria-live="polite"></p>
        <button class="btn btn--primary btn--generate" type="button" data-create-image disabled>Generate image</button>
      </div>
      <div data-progress hidden>
        <div class="venue-progress" aria-hidden="true"><span data-playhead></span></div>
        <p data-progress-copy>Finding the heart of your song</p>
        <p class="quiet" data-progress-note>Your image is still being created. You can leave this page and find it in your gallery when it is ready.</p>
      </div>
    </aside>
  </div>

  <div class="paywall-panel" data-paywall hidden>
    <div class="paywall-panel__layout">
      <div class="paywall-panel__media" aria-hidden="true"></div>
      <div class="paywall-panel__content stack">
        <h3>Your song is ready. Step into its world.</h3>
        <p>Create original cinematic art with you and the people you love at the heart of the story.</p>
        <p><strong>You Are The Song Now Membership</strong><br>$20 per month</p>
        <ul class="benefit-list">
          <li>Monthly credits for creating personalized artwork</li>
          <li>Low, medium and high-quality options</li>
          <li>Save, download and share creations</li>
          <li>Cancel anytime</li>
        </ul>
        <p class="quiet">Your subscription renews monthly until cancelled. Card statement shows YOU ARE THE SONG.</p>
        <button class="btn btn--primary" type="button" data-checkout>Continue to secure checkout</button>
        <button class="btn btn--secondary" type="button" data-dev-activate>Activate local development membership</button>
        <p class="status" data-paywall-status role="status" aria-live="polite"></p>
      </div>
    </div>
  </div>

  <dialog class="confirm-sheet" data-portrait-delete-dialog>
    <form method="dialog" class="confirm-sheet__panel stack">
      <h3>Delete this portrait?</h3>
      <p class="quiet">This removes the uploaded portrait from your creative materials. It cannot be undone.</p>
      <div class="confirm-sheet__actions">
        <button class="btn btn--secondary" value="cancel" type="submit">Cancel</button>
        <button class="btn btn--danger" value="confirm" type="submit" data-portrait-delete-confirm>Delete</button>
      </div>
    </form>
  </dialog>
</section>

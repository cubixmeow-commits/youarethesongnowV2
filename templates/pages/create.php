<section class="create create--wizard" data-create data-csrf="<?= e($csrf ?? '') ?>"<?php
  $build = \Yatsn\Support\BuildInfo::publicSummary();
  if (!empty($build['privateBuild'])):
?> data-private-build="1"<?php
  endif;
  if (!empty($build['commit'])):
?> data-build-commit="<?= e((string) $build['commit']) ?>" data-build-source="<?= e((string) ($build['source'] ?? '')) ?>"<?php endif; ?>>
  <div class="create-wizard" data-create-wizard>
    <header class="create-wizard__topbar">
      <button class="create-wizard__back btn btn--ghost" type="button" data-create-back hidden aria-label="Go back">
        <span aria-hidden="true">←</span>
      </button>
      <a class="create-wizard__brand" href="/gallery" aria-label="You Are The Song Now">
        <img
          class="create-wizard__mark"
          src="/assets/images/brand/ys-monogram-flat-platinum.svg"
          width="24"
          height="24"
          alt=""
          decoding="async">
        <span class="create-wizard__wordmark">YouAreTheSongNow</span>
      </a>
      <a class="create-wizard__exit" href="/gallery">Exit</a>
    </header>

    <div class="create-wizard__progress" aria-hidden="true">
      <span class="create-wizard__segment" data-progress-segment="song"></span>
      <span class="create-wizard__segment" data-progress-segment="people"></span>
      <span class="create-wizard__segment" data-progress-segment="direction"></span>
      <span class="create-wizard__segment" data-progress-segment="review"></span>
    </div>

    <div class="create-wizard__main" data-create-main>
      <div class="create-wizard__scroll" data-create-scroll>
        <div class="create-wizard__stage">
          <div class="create-wizard__intro">
            <p class="create-wizard__eyebrow" data-create-eyebrow>01 · SONG</p>
            <h1 class="create-wizard__title" data-create-focus-title>What are we listening to?</h1>
            <p class="create-wizard__lead" data-create-focus-lead>Start with the song. We’ll create its visual DNA.</p>
          </div>

          <div class="create-wizard__task">
            <div class="create-wizard__panel">
              <div class="create__cards" data-create-cards>
          <article class="create-card create-card--song" data-create-card="song" id="create-card-song">
            <section class="yatsn-create-entry" id="the-song" aria-labelledby="song-heading">
              <h2 id="song-heading" class="visually-hidden">Song</h2>

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
                  <button class="btn btn--primary yatsn-btn yatsn-btn--primary yatsn-song-search__submit visually-hidden" type="submit" tabindex="-1" aria-hidden="true">Find this song</button>
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
            </section>
          </article>

          <article class="create-card create-card--people" data-create-card="people" id="create-card-people" hidden>
            <section id="the-people" aria-labelledby="people-heading">
              <h2 id="people-heading" class="visually-hidden">People</h2>

              <div class="portrait-region" data-portrait-region>
                <p class="status portrait-region__loading" data-portrait-loading hidden role="status" aria-live="polite">Loading your portraits…</p>
                <div class="portrait-region__error" data-portrait-load-error hidden role="alert">
                  <p class="status is-error" data-portrait-load-error-text>We could not load your saved portraits.</p>
                  <button class="btn btn--secondary" type="button" data-portrait-retry>Try again</button>
                </div>
                <p class="quiet portrait-region__empty" data-portrait-empty hidden>No saved portraits yet. Add one below or continue without people.</p>
                <div class="portrait-tray portrait-tray--wizard" data-portrait-grid role="list" aria-label="Portrait selection" hidden></div>
                <p class="quiet portrait-region__hint" data-people-hint role="status" aria-live="polite">Select up to two portraits, or continue without people.</p>
              </div>

              <div class="create-card__secondary create-card__secondary--people">
                <button class="btn btn--secondary create-card__expand" type="button" data-portrait-upload-toggle aria-expanded="false">Add portrait</button>
                <form id="portrait-form" class="stack create-card__collapsible" data-portrait-upload-panel hidden>
                  <label class="file">
                    <span>Upload a portrait</span>
                    <input type="file" name="file" accept="image/jpeg,image/png,image/webp" required>
                  </label>
                  <button class="btn btn--secondary" type="submit">Upload a portrait</button>
                  <p class="quiet">Only upload photos you have permission to use. Your photos remain private. See <a href="/privacy">Privacy</a>.</p>
                  <p class="status" data-portrait-status role="status" aria-live="polite"></p>
                </form>
              </div>
            </section>
          </article>

          <article class="create-card create-card--direction" data-create-card="direction" id="create-card-direction" hidden>
            <section id="the-direction" aria-labelledby="direction-heading">
              <h2 id="direction-heading" class="visually-hidden">Direction</h2>

              <div class="stack direction-controls">
                <div class="direction-manual-controls" data-direction-manual hidden>
                  <div data-style-world>
                    <h3 class="visually-hidden">Choose your world</h3>
                    <div class="style-grid" data-style-grid role="listbox" aria-label="Styles"></div>
                  </div>

                  <button class="btn btn--secondary" type="button" data-review>Review my creation</button>
                </div>

                <p class="status" data-direction-status role="status" aria-live="polite"></p>
              </div>
            </section>
          </article>

          <article class="create-card create-card--review" data-create-card="review" id="create-card-review" hidden>
            <section class="create-review" aria-labelledby="review-heading">
              <h2 id="review-heading" class="visually-hidden">Review</h2>

              <div class="create-review__direction" data-review-direction>
                <p class="create-review__label quiet">Direction</p>
                <p class="create-review__direction-name" data-review-direction-name>Not chosen yet</p>
              </div>

              <dl class="summary-list create-review__summary create-review__summary--compact" data-summary>
                <div><dt>Song</dt><dd data-sum-song>Not chosen yet</dd></div>
                <div><dt>People</dt><dd data-sum-people>None selected</dd></div>
                <div class="create-review__output-row"><dt>Output</dt><dd data-review-output>Medium · Square</dd></div>
                <div><dt>Credits</dt><dd data-sum-credits>—</dd></div>
              </dl>

              <div class="portrait-tray portrait-tray--compact" data-review-portraits hidden aria-hidden="true"></div>

              <details class="create-fine-tune" data-fine-tune>
                <summary>Fine-tune image settings</summary>
                <div class="create-fine-tune__body stack">
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

                  <label class="check">
                    <input type="checkbox" data-special-toggle>
                    <span>I have something specific in mind</span>
                  </label>
                  <label data-special-wrap hidden>
                    <span>Special instructions</span>
                    <textarea data-special maxlength="500" rows="3" placeholder="Describe a setting, mood, colors, clothing, or another detail you would like us to consider."></textarea>
                  </label>

                  <div class="create-fine-tune__style" data-fine-tune-style hidden>
                    <h3>Style</h3>
                    <div class="style-grid style-grid--compact" data-style-grid-fine role="listbox" aria-label="Styles"></div>
                  </div>
                </div>
              </details>

              <p class="create__generate-hint quiet" data-generate-hint role="status" aria-live="polite"></p>
            </section>
          </article>

          <article class="create-card create-card--generating" data-create-card="generating" id="create-card-generating" hidden>
            <section class="create-generating" aria-labelledby="generating-heading">
              <h2 id="generating-heading" class="visually-hidden">Creating</h2>

              <div data-progress>
                <div class="venue-progress" aria-hidden="true"><span data-playhead></span></div>
                <p data-progress-copy>Finding the heart of your song</p>
                <p class="quiet" data-progress-note>You can continue to Gallery while generation finishes.</p>
              </div>

              <a class="btn btn--secondary" href="/gallery">Continue to Gallery</a>
            </section>
          </article>
        </div>
            </div>
          </div>
        </div>
      </div>

      <footer class="create-wizard__actions" data-create-sticky-actions>
      <div class="create-wizard__actions-primary" data-create-sticky-primary-wrap>
        <button class="btn btn--primary btn--wizard-primary" type="button" data-create-sticky-primary data-label-generate="Generate image">Find this song</button>
      </div>
      <div class="create-wizard__actions-people" data-create-sticky-people hidden>
        <button class="btn btn--primary btn--wizard-primary" type="button" data-people-continue disabled>Continue</button>
        <button class="btn btn--secondary" type="button" data-people-continue-without>Continue without people</button>
      </div>
      <p class="create-wizard__footnote quiet" data-create-footnote>Recent creations live in Gallery—not inside this flow.</p>
      </footer>
    </div>

    <p class="visually-hidden" data-create-announcer role="status" aria-live="polite"></p>
  </div>

  <div class="development-source create-dev-panel" data-development-analysis-panel hidden>
    <details>
      <summary>Development Song DNA inspection</summary>
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
      <p class="quiet">Private development aid. Lyrics and verification excerpts are not saved.</p>
    </details>
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

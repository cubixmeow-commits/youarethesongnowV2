<section class="create" data-create data-csrf="<?= e($csrf ?? '') ?>">
  <header class="create__intro">
    <h1>Create your cinematic world</h1>
    <p class="lede">Choose a song, add your portraits, and shape the world you want to enter.</p>
  </header>

  <div class="create__layout">
    <div class="create__main">
      <section class="movement" id="the-song" aria-labelledby="song-heading">
        <p class="movement__num">01</p>
        <h2 id="song-heading">The song</h2>
        <p>Choose your song</p>
        <form id="song-form" class="stack">
          <label>
            <span>Artist or band</span>
            <input type="text" name="artist" required placeholder="Enter the artist or band" autocomplete="off">
          </label>
          <label>
            <span>Song title</span>
            <input type="text" name="title" required placeholder="Enter the song title" autocomplete="off">
          </label>
          <button class="btn btn--secondary" type="submit">Find my song</button>
          <p class="status" data-song-status role="status" aria-live="polite"></p>
        </form>
      </section>

      <section class="movement" id="the-people" aria-labelledby="people-heading" hidden>
        <p class="movement__num">02</p>
        <h2 id="people-heading">The people</h2>
        <p>Who belongs in this story?</p>
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
        <div class="portrait-grid" data-portrait-grid></div>
      </section>

      <section class="movement" id="the-direction" aria-labelledby="direction-heading" hidden>
        <p class="movement__num">03</p>
        <h2 id="direction-heading">The direction</h2>

        <div class="stack">
          <div>
            <h3>Choose your world</h3>
            <p class="quiet">Select the visual style that will lead your image.</p>
            <div class="style-grid" data-style-grid role="listbox" aria-label="Styles"></div>
          </div>

          <fieldset>
            <legend>Choose image quality</legend>
            <div class="choice-row" data-quality-row></div>
          </fieldset>

          <fieldset>
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

    <aside class="create__summary" aria-live="polite">
      <h2>Summary</h2>
      <dl class="summary-list" data-summary>
        <div><dt>Song</dt><dd data-sum-song>Not chosen yet</dd></div>
        <div><dt>People</dt><dd data-sum-people>None selected</dd></div>
        <div><dt>Style</dt><dd data-sum-style>Not chosen yet</dd></div>
        <div><dt>Quality</dt><dd data-sum-quality>Medium</dd></div>
        <div><dt>Format</dt><dd data-sum-orientation>Square</dd></div>
        <div><dt>Credits</dt><dd data-sum-credits>—</dd></div>
      </dl>
      <div data-summary-actions hidden>
        <p data-summary-headline>Your cinematic world is ready to create</p>
        <button class="btn btn--primary" type="button" data-create-image>Create my image</button>
      </div>
      <div data-paywall hidden>
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
      <div data-progress hidden>
        <div class="playhead" aria-hidden="true"><span data-playhead></span></div>
        <p data-progress-copy>Finding the heart of your song</p>
        <p class="quiet" data-progress-note>Your image is still being created. You can leave this page and find it in your gallery when it is ready.</p>
      </div>
    </aside>
  </div>
</section>

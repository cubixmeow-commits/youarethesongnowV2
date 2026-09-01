<section class="component-lab" data-component-lab>
  <header class="component-lab__intro">
    <p class="eyebrow">Private development</p>
    <h1>Component lab</h1>
    <p class="lede">Luminous Night Studio fixtures for review. No live API, song text, portraits, or provider payloads.</p>
    <p class="quiet">Owner and private-build only. Compact is canonical; expanded width should improve comparison without adding controls. Flutter maps these as YatsnButton, YatsnIconButton, status, SongDnaCard, DirectionCard, sheet/dialog, and artwork widgets.</p>
  </header>

  <section class="stack" aria-labelledby="lab-buttons">
    <h2 id="lab-buttons">Button</h2>
    <div class="component-lab__grid">
      <div class="component-lab__state">
        <span>Primary</span>
        <div class="component-lab__row">
          <button class="yatsn-btn yatsn-btn--primary" type="button">Generate for me</button>
          <button class="yatsn-btn yatsn-btn--primary" type="button" data-lab-hover>Hover</button>
          <button class="yatsn-btn yatsn-btn--primary is-pressed" type="button">Pressed</button>
          <button class="yatsn-btn yatsn-btn--primary" type="button" data-lab-focus>Focus</button>
          <button class="yatsn-btn yatsn-btn--primary" type="button" disabled>Disabled</button>
          <button class="yatsn-btn yatsn-btn--primary is-loading" type="button" aria-busy="true" aria-label="Generate for me, loading">
            <span class="yatsn-spinner" aria-hidden="true"></span>
            Generate for me
          </button>
        </div>
      </div>
      <div class="component-lab__state">
        <span>Secondary, quiet, destructive</span>
        <div class="component-lab__row">
          <button class="yatsn-btn yatsn-btn--secondary" type="button">Explore options</button>
          <button class="yatsn-btn yatsn-btn--quiet" type="button">Fine Tune</button>
          <button class="yatsn-btn yatsn-btn--destructive" type="button">Delete</button>
          <button class="yatsn-btn yatsn-btn--destructive" type="button" disabled>Delete disabled</button>
        </div>
      </div>
    </div>
  </section>

  <section class="stack" aria-labelledby="lab-icon">
    <h2 id="lab-icon">Icon button</h2>
    <div class="component-lab__row">
      <button class="yatsn-icon-btn" type="button" aria-label="Close" title="Close">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"></path></svg>
      </button>
      <button class="yatsn-icon-btn" type="button" aria-label="More actions" title="More actions" data-lab-focus>
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="6" r="1.2" fill="currentColor" stroke="none"></circle><circle cx="12" cy="12" r="1.2" fill="currentColor" stroke="none"></circle><circle cx="12" cy="18" r="1.2" fill="currentColor" stroke="none"></circle></svg>
      </button>
      <button class="yatsn-icon-btn" type="button" aria-label="Close, disabled" disabled>
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"></path></svg>
      </button>
    </div>
  </section>

  <section class="stack" aria-labelledby="lab-status">
    <h2 id="lab-status">Status banner</h2>
    <div class="component-lab__grid">
      <div class="yatsn-status yatsn-status--info" role="status">Song DNA is ready for this creation.</div>
      <div class="yatsn-status yatsn-status--success" role="status">Your artwork is ready in the gallery.</div>
      <div class="yatsn-status yatsn-status--warning" role="status">Credits are low. You can still finish this image.</div>
      <div class="yatsn-status yatsn-status--error" role="alert">
        Could not create visual directions.
        <div class="yatsn-status__actions">
          <button class="yatsn-btn yatsn-btn--secondary" type="button">Try again</button>
        </div>
      </div>
    </div>
  </section>

  <section class="stack" aria-labelledby="lab-dna">
    <h2 id="lab-dna">Song DNA card</h2>
    <p class="quiet">Fixture copy only. Not a customer-safe projection API.</p>
    <div class="component-lab__grid component-lab__grid--cards" role="group" aria-label="Song DNA fixtures">
      <button class="yatsn-dna-card is-loading" type="button" disabled aria-busy="true" aria-label="Emotional core, loading">
        <span class="yatsn-skeleton"></span>
        <span class="yatsn-skeleton yatsn-skeleton--line"></span>
      </button>
      <button class="yatsn-dna-card" type="button" role="checkbox" aria-checked="false" data-lab-dna>
        <span class="yatsn-recommend">Recommended</span>
        <span class="yatsn-dna-card__dim">Emotional core</span>
        <strong>A late-night drive that still feels unfinished.</strong>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </button>
      <button class="yatsn-dna-card is-selected" type="button" role="checkbox" aria-checked="true" data-lab-dna>
        <span class="yatsn-dna-card__dim">Story</span>
        <strong>Two people keep almost saying the thing that would change the night.</strong>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </button>
      <button class="yatsn-dna-card is-disabled is-conflict" type="button" role="checkbox" aria-checked="false" aria-disabled="true" disabled>
        <span class="yatsn-dna-card__dim">World</span>
        <strong>Rain-slick city glass and an empty overpass.</strong>
        <span class="yatsn-dna-card__reason">Unavailable with Story in this fixture.</span>
      </button>
    </div>
  </section>

  <section class="stack" aria-labelledby="lab-direction">
    <h2 id="lab-direction">Creative direction card</h2>
    <p class="quiet">Visual fixtures are not a live radiogroup, so selected and selected-plus-recommended can appear together for review.</p>
    <div class="component-lab__grid component-lab__grid--cards" role="group" aria-label="Direction card visual states">
      <div class="yatsn-direction-card is-loading" aria-hidden="true">
        <span class="yatsn-skeleton"></span>
        <span class="yatsn-skeleton yatsn-skeleton--line"></span>
      </div>
      <div class="yatsn-direction-card ai-direction-card">
        <span class="yatsn-recommend ai-direction-card__recommend">Recommended</span>
        <strong>Sodium Crossing</strong>
        <span>Rain-slick overpass, warm lamps, two figures paused mid-step.</span>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </div>
      <div class="yatsn-direction-card ai-direction-card is-selected">
        <span class="yatsn-recommend ai-direction-card__recommend">Recommended</span>
        <strong>Quiet Threshold</strong>
        <span>A dim apartment doorway where the night still has one more hour.</span>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </div>
      <div class="yatsn-direction-card ai-direction-card is-selected">
        <strong>Harbor Afterglow</strong>
        <span>Wet stone, distant water, a coat catching the last sodium light.</span>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </div>
      <div class="yatsn-status yatsn-status--error">
        Could not create this direction.
        <div class="yatsn-status__actions">
          <button class="yatsn-btn yatsn-btn--secondary" type="button">Try again</button>
        </div>
      </div>
    </div>

    <h3>Interactive selection</h3>
    <p class="quiet">One radiogroup with a single selected radio. Arrow keys move focus and selection; Tab leaves the group.</p>
    <div class="component-lab__grid component-lab__grid--cards" role="radiogroup" aria-label="Explore direction fixtures" data-lab-direction-group>
      <button class="yatsn-direction-card ai-direction-card is-selected" type="button" role="radio" aria-checked="true" tabindex="0" data-lab-direction data-style-name="Gothic Romance" data-style-id="style-fixture-a">
        <span class="yatsn-recommend ai-direction-card__recommend">Recommended</span>
        <strong>Sodium Crossing</strong>
        <span>Rain-slick overpass, warm lamps, two figures paused mid-step.</span>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </button>
      <button class="yatsn-direction-card ai-direction-card" type="button" role="radio" aria-checked="false" tabindex="-1" data-lab-direction>
        <strong>Quiet Threshold</strong>
        <span>A dim apartment doorway where the night still has one more hour.</span>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </button>
      <button class="yatsn-direction-card ai-direction-card" type="button" role="radio" aria-checked="false" tabindex="-1" data-lab-direction>
        <strong>Harbor Afterglow</strong>
        <span>Wet stone, distant water, a coat catching the last sodium light.</span>
        <span class="yatsn-selected-mark" aria-hidden="true"></span>
      </button>
    </div>
  </section>

  <section class="stack" aria-labelledby="lab-sheet">
    <h2 id="lab-sheet">Sheet, dialog, and confirmation</h2>
    <div class="component-lab__row">
      <button class="yatsn-btn yatsn-btn--secondary" type="button" data-lab-open-sheet>Open sheet</button>
      <button class="yatsn-btn yatsn-btn--secondary" type="button" data-lab-open-dialog>Open dialog</button>
      <button class="yatsn-btn yatsn-btn--destructive" type="button" data-lab-open-confirm>Open confirmation</button>
    </div>

    <dialog class="yatsn-sheet" data-lab-sheet aria-labelledby="lab-sheet-title">
      <form method="dialog" class="yatsn-sheet__panel stack">
        <h3 id="lab-sheet-title">Fine Tune fixture</h3>
        <p class="quiet">Contextual, reversible choices. Compact uses a sheet; expanded would use a constrained panel. No live draft fields are written.</p>
        <div class="yatsn-sheet__actions">
          <button class="yatsn-btn yatsn-btn--quiet" value="cancel" type="submit">Close</button>
          <button class="yatsn-btn yatsn-btn--primary" value="apply" type="submit">Apply</button>
        </div>
      </form>
    </dialog>

    <dialog class="yatsn-dialog" data-lab-dialog aria-labelledby="lab-dialog-title">
      <form method="dialog" class="yatsn-dialog__panel stack">
        <h3 id="lab-dialog-title">Keep this direction?</h3>
        <p class="quiet">A narrow blocking decision. Escape closes it and restores focus.</p>
        <div class="yatsn-dialog__actions">
          <button class="yatsn-btn yatsn-btn--quiet" value="cancel" type="submit">Not now</button>
          <button class="yatsn-btn yatsn-btn--primary" value="confirm" type="submit">Keep it</button>
        </div>
      </form>
    </dialog>

    <dialog class="yatsn-dialog" data-lab-confirm aria-labelledby="lab-confirm-title" aria-describedby="lab-confirm-copy">
      <form method="dialog" class="yatsn-dialog__panel stack">
        <h3 id="lab-confirm-title">Delete this image?</h3>
        <p class="quiet" id="lab-confirm-copy">This removes the finished artwork and stops any share links immediately. It cannot be undone.</p>
        <div class="yatsn-dialog__actions">
          <button class="yatsn-btn yatsn-btn--quiet" value="cancel" type="submit">Cancel</button>
          <button class="yatsn-btn yatsn-btn--destructive" value="confirm" type="submit">Delete</button>
        </div>
      </form>
    </dialog>
  </section>

  <section class="stack" aria-labelledby="lab-art">
    <h2 id="lab-art">Artwork tile</h2>
    <div class="component-lab__grid component-lab__grid--art">
      <figure class="yatsn-artwork is-loading">
        <div class="yatsn-artwork__stage" aria-busy="true"><span class="yatsn-skeleton"></span></div>
        <figcaption>Loading</figcaption>
      </figure>
      <figure class="yatsn-artwork is-ready">
        <div class="yatsn-artwork__stage" role="img" aria-label="Fixture artwork, a quiet sapphire wash over graphite. No generated image."></div>
        <figcaption>Ready</figcaption>
      </figure>
      <figure class="yatsn-artwork is-unavailable">
        <div class="yatsn-artwork__stage">Image unavailable</div>
        <figcaption>Unavailable</figcaption>
      </figure>
    </div>
  </section>
</section>

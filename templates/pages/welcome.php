<?php
/** @var array $showcaseHero */
$hero = $showcaseHero;
?>
<section class="hero hero--launchpad" aria-labelledby="home-title">
  <div class="hero__stage">
    <img class="hero__image" src="<?= e($hero['display']) ?>" width="<?= (int) $hero['width'] ?>" height="<?= (int) $hero['height'] ?>" alt="<?= e($hero['alt']) ?>" fetchpriority="high" decoding="async">
  </div>
  <div class="hero__veil" aria-hidden="true"></div>
  <div class="hero__copy">
    <p class="eyebrow">Song · imagination · visual journey</p>
    <h1 id="home-title">You Are The Song Now</h1>
    <p class="hero__subhead">Turn a track into cinematic art, starring you.</p>
    <p class="lede">Choose a meaningful song. Add yourself or someone you love. We transform its emotion, movement, and story into an original cinematic world.</p>
    <div class="hero__actions">
      <a class="btn btn--primary" href="/sign-in">Start your first creation</a>
      <a class="btn btn--ghost hero__sign-in" href="#examples">See the artwork</a>
    </div>
  </div>
</section>

<section class="world-carousel home-section" id="examples" aria-labelledby="examples-title" data-home-carousel>
  <header class="world-carousel__intro home-section__intro">
    <p class="eyebrow">Made from music</p>
    <h2 class="world-carousel__title" id="examples-title">Worlds from the first chapter</h2>
    <p class="quiet">Explore 77 early experiments in song, memory, and cinematic imagination.</p>
    <p class="world-carousel__link-wrap"><a class="world-carousel__link" href="/showcase">Explore all 77 worlds</a></p>
  </header>
  <div class="world-carousel__controls">
    <button type="button" class="icon-btn world-carousel__btn" data-carousel-prev aria-label="Previous world">←</button>
    <p class="world-carousel__counter" data-carousel-counter aria-live="polite">1 / 77</p>
    <button type="button" class="icon-btn world-carousel__btn" data-carousel-next aria-label="Next world">→</button>
  </div>
  <p class="visually-hidden" aria-live="polite" data-carousel-status></p>
  <div class="world-carousel__track" data-carousel-track tabindex="0" role="list"></div>
</section>

<section class="home-story home-section" aria-labelledby="selfie-title">
  <div class="home-story__art">
    <img src="/assets/images/showcase/display/v1-023.webp" alt="A cinematic portrait from the original You Are The Song Now collection" loading="lazy" decoding="async">
  </div>
  <div class="home-story__copy">
    <p class="eyebrow">Your selfie, your myth</p>
    <h2 id="selfie-title">Your face, fused with the music’s soul.</h2>
    <p>A single frame brings the song’s visual DNA together with your identity. Each track becomes a personal world, and your gallery becomes a collection of musical lives you have stepped into.</p>
    <a class="text-link" href="/sign-in">Create your first world <span aria-hidden="true">→</span></a>
  </div>
</section>

<section class="home-section home-process" id="how-it-works" aria-labelledby="process-title">
  <header class="home-section__intro">
    <p class="eyebrow">How it works</p>
    <h2 id="process-title">The creation flow</h2>
    <p class="quiet">A guided experience that begins with the music, not a complicated prompt box.</p>
  </header>
  <ol class="home-process__grid">
    <li class="home-process__step"><span class="home-process__number">01</span><h3>Choose your song</h3><p>Search for the artist and track that means something to you. We find the song and prepare its visual DNA.</p></li>
    <li class="home-process__step"><span class="home-process__number">02</span><h3>Step into its world</h3><p>Add one or two portraits, or create without people. Let the AI direct the scene or explore more creative control.</p></li>
    <li class="home-process__step"><span class="home-process__number">03</span><h3>Reveal your artwork</h3><p>Your song becomes an original cinematic image made to save, collect, download, and share.</p></li>
  </ol>
</section>

<section class="home-feature home-section" id="features" aria-labelledby="features-title">
  <div class="home-feature__copy">
    <p class="eyebrow">Built for the feeling</p>
    <h2 id="features-title">Powerful creation without the machinery showing.</h2>
    <p class="quiet">The creative engine handles interpretation, composition, atmosphere, and camera language while you stay focused on the song and the people who matter.</p>
    <a class="btn btn--secondary" href="/sign-in">Start creating</a>
  </div>
  <div class="home-feature__art">
    <img src="/assets/images/showcase/display/v1-054.webp" alt="Cinematic music-inspired artwork from the original collection" loading="lazy" decoding="async">
  </div>
</section>

<section class="home-section" aria-labelledby="quality-title">
  <header class="home-section__intro"><p class="eyebrow">What you get</p><h2 id="quality-title">Creative freedom with thoughtful control</h2></header>
  <div class="home-values">
    <article><h3>Recognizable people</h3><p>Your defining features, skin tone, hair, and approximate age stay central to the portrait direction.</p></article>
    <article><h3>Song-specific worlds</h3><p>Structured Song DNA turns the track’s emotion and symbolism into an intentional visual scene.</p></article>
    <article><h3>AI-guided by default</h3><p>Quick Generate makes the creative decisions. Explore Options and Fine Tune are there when you want them.</p></article>
    <article><h3>Private by default</h3><p>Your portraits and gallery remain private unless you choose to share a finished creation.</p></article>
    <article><h3>Your personal gallery</h3><p>Save, revisit, download, and organize the cinematic worlds created from your music.</p></article>
    <article><h3>No charge for failures</h3><p>Credits are captured only when a generation succeeds. Failed generations return the reserved credits.</p></article>
  </div>
</section>

<section class="home-section home-pricing" id="pricing" aria-labelledby="pricing-title">
  <div class="home-pricing__intro">
    <p class="eyebrow">Membership</p><h2 id="pricing-title">One generous creative plan</h2>
    <p>Build a gallery from favorite songs, full albums, shared memories, and the people you love.</p>
  </div>
  <article class="price-card">
    <div class="price-card__heading"><p class="price-card__name">You Are The Song Now Membership</p><p class="price-card__amount"><span>$20</span> / month</p><p class="quiet">Renews monthly. Cancel anytime.</p></div>
    <ul class="price-card__features">
      <li>1,500 creative credits each month</li><li>Roughly 214 Premium or 375 Standard images</li><li>One-person, two-person, and people-free creations</li><li>Standard and Premium generation choices</li><li>Private gallery, downloads, and sharing</li><li>Failed generations do not consume credits</li>
    </ul>
    <a class="btn btn--primary" href="/sign-in">Prepare your first creation</a>
    <p class="price-card__note">Choose your song and prepare your creation before membership begins. Generation starts only after payment.</p>
  </article>
</section>

<section class="home-section home-faq" id="faq" aria-labelledby="faq-title">
  <header class="home-section__intro"><p class="eyebrow">Questions</p><h2 id="faq-title">Frequently asked</h2></header>
  <div class="home-faq__list">
    <details><summary>What are creative credits?</summary><p>Credits pay for successful image generation. Different quality levels can use different credit amounts, so you can choose between more experiments and higher-cost renders.</p></details>
    <details><summary>Do I have to upload a portrait?</summary><p>No. You can create with one person, two people, or no people at all.</p></details>
    <details><summary>Is my artwork private?</summary><p>Yes. Portraits and creations are private by default. Sharing happens only when you choose it.</p></details>
    <details><summary>What if a generation fails?</summary><p>A failed generation does not consume your credits. Reserved credits are automatically returned.</p></details>
    <details><summary>Can I cancel the membership?</summary><p>Yes. Cancellation stops renewal and preserves your access through the current paid period.</p></details>
  </div>
</section>

<section class="home-invite" aria-labelledby="invite-title">
  <div class="home-invite__copy"><p class="eyebrow">Your song is waiting</p><h2 id="invite-title">What world is hiding inside it?</h2><p>Choose the track. Step into the story. Keep the image forever.</p><a class="btn btn--primary" href="/sign-in">Start with a song</a></div>
</section>

<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#142019">
    <meta name="description" content="First Ruck creates a beginner rucking plan and finds routes that fit your starting point.">
    <title>First Ruck</title>
    <link rel="stylesheet" href="asset.php?file=app.css">
    <script src="asset.php?file=app.js" defer></script>
</head>
<body>
    <a class="skip-link" href="#app-main">Skip to content</a>
    <div class="ambient-map" aria-hidden="true"></div>

    <main class="app-shell" id="app-main">
        <section class="screen welcome-screen is-active" data-screen="welcome" aria-labelledby="welcome-title">
            <div class="welcome-map" aria-hidden="true">
                <svg viewBox="0 0 430 430" role="presentation">
                    <path class="contour contour-a" d="M-30 290C44 212 91 349 171 271s126-55 173-148 103-25 127-83" />
                    <path class="contour contour-b" d="M-42 329C48 248 102 383 190 305s129-56 176-147 109-27 139-91" />
                    <path class="contour contour-c" d="M-55 369C52 283 115 417 210 342s130-59 183-151 105-43 148-106" />
                    <path class="welcome-route" d="M42 348C92 310 119 327 149 277S215 221 248 237s54-19 67-58 35-59 76-78" />
                    <circle class="route-start" cx="42" cy="348" r="7" />
                    <circle class="route-end" cx="391" cy="101" r="9" />
                </svg>
            </div>
            <header class="brand-lockup enter-one">
                <span class="brand-mark" aria-hidden="true">FR</span>
                <span>First Ruck</span>
            </header>
            <div class="welcome-copy">
                <p class="eyebrow enter-two">A beginner plan built around you</p>
                <h1 id="welcome-title" class="enter-three">Start where you are.<br>Carry forward.</h1>
                <p class="welcome-summary enter-four">Tell us what feels comfortable. First Ruck will shape your first weeks and find nearby routes that fit.</p>
            </div>
            <div class="welcome-actions enter-five">
                <button class="primary-button" type="button" data-start>Build my plan</button>
                <p>About 4 minutes · You control location access</p>
            </div>
        </section>

        <section class="screen onboarding-screen" data-screen="onboarding" aria-labelledby="question-title">
            <header class="flow-header">
                <button class="icon-button" type="button" data-back aria-label="Go to the previous question">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
                </button>
                <div class="flow-brand">First Ruck</div>
                <span class="step-count" aria-live="polite">1 of 12</span>
            </header>
            <div class="progress-track" aria-hidden="true"><span></span></div>
            <form class="question-form" id="question-form" novalidate>
                <div class="question-copy">
                    <p class="eyebrow" id="question-kicker">Your goal</p>
                    <h1 id="question-title">What would make rucking worthwhile?</h1>
                    <p id="question-help">Choose the outcome you want to notice first.</p>
                </div>
                <fieldset class="answer-list" id="answer-list">
                    <legend class="sr-only">Choose one answer</legend>
                </fieldset>
                <p class="field-error" id="question-error" role="alert"></p>
                <div class="flow-action">
                    <button class="primary-button" type="submit">Continue</button>
                </div>
            </form>
        </section>

        <section class="screen analysis-screen" data-screen="analysis" aria-labelledby="analysis-title">
            <div class="analysis-visual" aria-hidden="true">
                <svg viewBox="0 0 360 360" role="presentation">
                    <circle class="analysis-ring ring-one" cx="180" cy="180" r="118" />
                    <circle class="analysis-ring ring-two" cx="180" cy="180" r="82" />
                    <path class="analysis-route" d="M55 240c42-14 58-83 101-65s42 74 79 49 29-82 72-103" />
                    <circle class="analysis-point" cx="55" cy="240" r="7" />
                </svg>
            </div>
            <div class="analysis-copy">
                <p class="eyebrow">Building your starting point</p>
                <h1 id="analysis-title">Finding the right first step</h1>
                <p class="analysis-status" role="status" aria-live="polite">Balancing time, terrain, and experience…</p>
            </div>
        </section>

        <section class="screen profile-screen" data-screen="profile" aria-labelledby="profile-title">
            <header class="simple-header"><span class="brand-mark" aria-hidden="true">FR</span><span>First Ruck</span></header>
            <div class="profile-intro">
                <p class="eyebrow">Your starting profile</p>
                <h1 id="profile-title">Fresh start</h1>
                <p id="profile-summary">Consistency comes before intensity.</p>
            </div>
            <dl class="profile-stats">
                <div><dt>First session</dt><dd id="profile-duration">30 min</dd></div>
                <div><dt>Starting load</dt><dd id="profile-load">Begin without added load</dd></div>
                <div><dt>Terrain</dt><dd id="profile-terrain">Mostly level</dd></div>
                <div><dt>Rhythm</dt><dd id="profile-frequency">2 sessions each week</dd></div>
            </dl>
            <aside class="coach-note">
                <span class="coach-symbol" aria-hidden="true">↗</span>
                <div><h2>One change at a time</h2><p id="profile-note">Build consistency first. Change distance, hills, or load one at a time.</p></div>
            </aside>
            <p class="health-note" id="health-note"></p>
            <div class="profile-action"><button class="primary-button" type="button" data-view-plan>See my first route</button></div>
        </section>

        <section class="screen today-screen" data-screen="today" aria-labelledby="today-title">
            <header class="today-header">
                <div><p class="eyebrow">Your next ruck</p><h1 id="today-title">Good fit for today</h1></div>
                <button class="avatar-button" type="button" data-nav="profile" aria-label="Open your First Ruck profile">FR</button>
            </header>
            <div class="prototype-notice" role="note"><span aria-hidden="true">●</span> Prototype routes · Live geographic data comes next</div>
            <article class="featured-route" id="featured-route">
                <div class="route-map" aria-hidden="true">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none">
                        <g class="map-contours"><path d="M-10 74c26-23 39 7 60-14s30-1 59-30"/><path d="M-14 90c28-24 45 7 66-14s32-2 61-31"/><path d="M-6 56c23-20 36 5 55-13S78 42 106 14"/></g>
                        <path class="map-route" id="featured-route-path" d="M8 68 L25 57 L39 63 L55 49 L72 52 L91 31" />
                        <circle class="map-start" cx="8" cy="68" r="2.2" />
                        <circle class="map-finish" cx="91" cy="31" r="2.6" />
                    </svg>
                    <div class="map-badge"><strong id="featured-score">94%</strong><span>match</span></div>
                </div>
                <div class="featured-content">
                    <p class="eyebrow">Best match</p>
                    <h2 id="featured-name">River greenway</h2>
                    <div class="route-metrics" id="featured-metrics"></div>
                    <p class="route-summary" id="featured-summary"></p>
                    <ul class="match-reasons" id="featured-reasons"></ul>
                    <button class="primary-button" type="button" data-open-route>View route plan</button>
                </div>
            </article>
            <section class="other-routes" aria-labelledby="alternatives-title">
                <div class="section-heading"><h2 id="alternatives-title">Other good options</h2><button type="button" class="text-button" data-refresh>Refresh</button></div>
                <div id="route-list"></div>
            </section>
            <nav class="tab-bar" aria-label="Primary navigation">
                <button class="is-selected" type="button" data-tab="today" aria-current="page"><span aria-hidden="true">⌁</span>Today</button>
                <button type="button" data-tab="plan"><span aria-hidden="true">◫</span>Plan</button>
                <button type="button" data-tab="learn"><span aria-hidden="true">○</span>Learn</button>
                <button type="button" data-tab="you"><span aria-hidden="true">⌾</span>You</button>
            </nav>
        </section>

        <dialog class="route-dialog" id="route-dialog" aria-labelledby="route-dialog-title">
            <form method="dialog"><button class="dialog-close" aria-label="Close route plan"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" /></svg></button></form>
            <div class="dialog-map" aria-hidden="true"><div class="dialog-route-line"></div></div>
            <div class="dialog-content">
                <p class="eyebrow">First session</p>
                <h1 id="route-dialog-title">Route plan</h1>
                <p id="dialog-summary"></p>
                <dl class="dialog-stats" id="dialog-stats"></dl>
                <h2>Before you leave</h2>
                <ul class="pack-list"><li>Check current access and weather</li><li>Adjust the pack until it stays close to your back</li><li>Turn around if pain changes how you move</li></ul>
                <button class="primary-button" type="button" data-start-ruck>Start this ruck</button>
            </div>
        </dialog>

        <div class="toast" role="status" aria-live="polite" aria-atomic="true"></div>
    </main>
</body>
</html>

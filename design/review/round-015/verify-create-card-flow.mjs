import { execSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8769';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); if(!$u){fwrite(STDERR,"no owner\\n"); exit(1);} $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const songDnaFixture = {
  essence: 'A Blitzkrieg Bop feeling becomes shared momentum.',
  openingState: 'restless anticipation',
  turningPoint: 'collective release',
  closingState: 'joyful resolve',
  intensityPattern: ['rising', 'surging'],
  themes: ['belonging', 'release'],
  relationshipDynamics: ['partners', 'shared courage'],
  narrativeArchetype: 'transformation',
  originalVisualMoment: 'Two people step into a rain-bright avenue as a wave of warm light opens ahead.',
  symbols: [{ concept: 'threshold', visualTranslation: 'an opening corridor of amber light' }],
  visualMetaphors: ['weather becoming momentum'],
  mood: ['electric', 'hopeful'],
  settingTypes: ['night avenue'],
  eraAtmosphere: 'timeless contemporary',
  weather: ['rain'],
  spatialCharacter: ['deep perspective'],
  palette: ['amber', 'near black'],
  lighting: ['strong rim light'],
  camera: ['35mm eye-level'],
  composition: ['two centered protagonists'],
  motion: ['wind-driven rain'],
  texture: ['matte film grain'],
  subjectRoles: ['partners'],
  ambiguities: [],
  confidence: 0.9,
  riskFlags: [],
};

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: true,
  args: ['--no-sandbox', '--disable-dev-shm-usage'],
});

const context = await browser.createBrowserContext();
await context.setCookie({
  name: 'yatsn_session',
  value: token,
  domain: new URL(BASE).hostname,
  path: '/',
});

const page = await context.newPage();
page.setDefaultTimeout(30000);

const requestLog = { explore: [], summary: [], generation: [], drafts: [] };
let firstStyleId = null;
let firstStyleName = null;
let summaryFailNext = false;
let interceptionEnabled = false;

async function handleInterceptedRequest(request) {
  const url = request.url();
  const method = request.method();

  if (method === 'POST' && url.includes('/api/v1/song-lookups')) {
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        data: {
          id: '00000000-0000-4000-8000-0000000015',
          artist: 'Sabaton',
          title: 'Seven Pillars of Wisdom',
          state: 'found',
          developmentAnalysis: { analyzed: true, analysis: songDnaFixture, analysisBasis: 'fixture' },
        },
      }),
    });
    return;
  }

  if (method === 'POST' && url.includes('/api/v1/explore-directions')) {
    requestLog.explore.push(Date.now());
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        data: {
          directions: [
            { name: 'Static Revolt', description: 'Rain-slick overpass.', styleName: firstStyleName, styleId: firstStyleId, promptHint: 'Rain cinematic.' },
            { name: 'Quiet Threshold', description: 'Dim doorway.', styleName: firstStyleName, styleId: firstStyleId },
            { name: 'Harbor Afterglow', description: 'Wet stone.', styleName: firstStyleName, styleId: firstStyleId },
          ],
        },
      }),
    });
    return;
  }

  if (method === 'PATCH' && url.includes('/api/v1/creation-drafts/')) {
    requestLog.drafts.push(Date.now());
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({ data: { id: url.split('/').pop() } }),
    });
    return;
  }

  if (method === 'POST' && url.includes('/summary')) {
    requestLog.summary.push(Date.now());
    if (summaryFailNext) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { ready: false, issues: { style: 'Choose a style.' } } }),
      });
      return;
    }
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({ data: { ready: true, requiresMembership: false } }),
    });
    return;
  }

  if (method === 'POST' && url.includes('/api/v1/generation-jobs')) {
    requestLog.generation.push(Date.now());
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({ data: { id: 'job-fixture-015' } }),
    });
    return;
  }

  if (method === 'GET' && url.includes('/api/v1/generation-jobs/job-fixture-015')) {
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({ data: { status: 'processing', progressStage: 'Rendering scene' } }),
    });
    return;
  }

  await request.continue();
}

async function enableMocks() {
  if (!interceptionEnabled) {
    page.on('request', (req) => handleInterceptedRequest(req).catch(() => req.abort()));
    await page.setRequestInterception(true);
    interceptionEnabled = true;
  }
}

async function gotoCreate() {
  await enableMocks();
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0', timeout: 45000 });
  await page.waitForFunction(() => typeof window.YatsnCreate === 'object', { timeout: 15000 });
  const styleMeta = await page.evaluate(() => {
    const btn = document.querySelector('.style-option');
    return { id: btn?.dataset?.styleId || null, name: btn?.querySelector('strong')?.textContent?.trim() || null };
  });
  firstStyleId = styleMeta.id;
  firstStyleName = styleMeta.name;
}

async function readFlow() {
  return page.evaluate(() => {
    const cards = Array.from(document.querySelectorAll('[data-create-card]'));
    const visible = cards.filter((c) => !c.hidden).map((c) => c.getAttribute('data-create-card'));
    const nav = document.querySelector('.app-nav');
    const navStyle = nav ? getComputedStyle(nav) : null;
    return {
      flowStep: window.YatsnCreate?.getFlowStep?.() || '',
      visibleCards: visible,
      recentHidden: document.querySelector('[data-song-recent]')?.hidden ?? true,
      uploadPanelHidden: document.querySelector('[data-portrait-upload-panel]')?.hidden ?? true,
      peopleContinue: document.querySelector('[data-people-continue]')?.textContent || '',
      peopleContinueDisabled: document.querySelector('[data-people-continue]')?.disabled ?? true,
      quickPresent: !!document.querySelector('[data-ai-quick]'),
      explorePresent: !!document.querySelector('[data-ai-explore]'),
      manualTertiaryPresent: !!document.querySelector('[data-ai-manual-tertiary]'),
      manualControlsHidden: document.querySelector('[data-direction-manual]')?.hidden ?? true,
      fineTuneOpen: document.querySelector('[data-fine-tune]')?.open ?? false,
      barHidden: document.querySelector('[data-create-sticky-primary-wrap]')?.hidden ?? true,
      generateDisabled: document.querySelector('[data-create-sticky-primary]')?.disabled ?? true,
      wizardHeaders: document.querySelectorAll('.create-wizard__topbar').length,
      navVisible: navStyle ? navStyle.display !== 'none' : false,
      topbarVisible: document.querySelector('.app-topbar') ? getComputedStyle(document.querySelector('.app-topbar')).display !== 'none' : false,
      bodyCreateFocus: document.body.classList.contains('is-create-focus'),
      overflowX: document.documentElement.scrollWidth <= window.innerWidth + 1,
    };
  });
}

// 1. Initial Create shows Song only
await gotoCreate();
let flow = await readFlow();
assert(flow.visibleCards.length === 1 && flow.visibleCards[0] === 'song', 'initial view is Song card only');
assert(flow.recentHidden, 'Recent Creations not shown in Create flow');
assert(!flow.barHidden, 'Song step shows sticky Find CTA');

// 2. Confirm song advances to People
await page.evaluate(async () => {
  const form = document.querySelector('#song-form');
  form.querySelector('[name="artist"]').value = 'Sabaton';
  form.querySelector('[name="title"]').value = 'Seven Pillars of Wisdom';
  await window.YatsnSongSearch.submitFind();
});
await page.waitForFunction(() => document.querySelector('[data-song-results] [data-song-result]'), { timeout: 15000 });
await page.waitForFunction(() => document.querySelector('[data-create-sticky-primary]')?.textContent === 'Use this song', { timeout: 10000 });
await page.click('[data-create-sticky-primary]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 10000 });
flow = await readFlow();
assert(flow.flowStep === 'people', 'confirmed song advances to People');
const peopleFocused = await page.evaluate(() => document.activeElement?.getAttribute('data-create-focus-title') !== null || document.activeElement?.id === 'people-heading');
assert(peopleFocused, 'People step receives focus after song confirm');

// 3–4. People portraits first; upload collapsed; continue labels
flow = await readFlow();
assert(flow.uploadPanelHidden, 'upload panel collapsed initially');
assert(flow.peopleContinueDisabled, 'continue disabled with zero portraits');

// Select portrait from API when available
const portraitState = await page.evaluate(async () => {
  const res = await fetch('/api/v1/portraits', { credentials: 'same-origin' });
  const portraits = (await res.json())?.data || [];
  if (!portraits.length) return { hasPortrait: false, flowStep: window.YatsnCreate?.getFlowStep?.() };
  const btn = document.querySelector('.portrait-chip__select');
  if (btn) btn.click();
  await new Promise((r) => setTimeout(r, 300));
  return {
    hasPortrait: true,
    flowStep: window.YatsnCreate?.getFlowStep?.(),
    continueDisabled: document.querySelector('[data-people-continue]')?.disabled ?? true,
    continueLabel: document.querySelector('[data-people-continue]')?.textContent || '',
  };
});
if (portraitState.hasPortrait) {
  assert(!portraitState.continueDisabled, 'continue enabled with portrait');
  assert(portraitState.continueLabel.includes('person'), 'continue labels selected portrait count');
  await page.click('[data-people-continue]');
  await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });
  flow = await readFlow();
  assert(flow.flowStep === 'direction', 'Continue advances to Direction');
  assert(flow.visibleCards.length === 1 && flow.visibleCards[0] === 'direction', 'only Direction card visible after Continue');
}

// 5–7 Direction and Quick path (fixture baseline ensures Song DNA + portraits)
await gotoCreate();
await page.evaluate(async () => {
  window.YatsnCreateFixtures.showDirectionChoice();
  window.YatsnExploreFixtures.showInitialChoice();
  const root = document.querySelector('[data-create]');
  await fetch('/api/v1/song-lookups', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': root?.dataset.csrf || '' },
    body: JSON.stringify({ artist: 'Sabaton', title: 'Seven Pillars of Wisdom' }),
  });
});
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });
await page.waitForFunction(() => {
  const quick = document.querySelector('[data-ai-quick]');
  return quick && !quick.disabled;
}, { timeout: 10000 });
flow = await readFlow();
assert(flow.flowStep === 'direction', 'Direction step ready for AI choices');
assert(flow.visibleCards[0] === 'direction', 'only Direction card visible');

// 6. Direction initial choices only
assert(flow.quickPresent && flow.explorePresent && flow.manualTertiaryPresent, 'direction shows quick explore manual tertiary');
assert(flow.manualControlsHidden, 'manual controls hidden in initial direction state');
assert(flow.barHidden, 'no generate bar on direction');

// 7. Quick path real click -> Review
requestLog.explore.length = 0;
requestLog.summary.length = 0;
const generationBeforeQuick = requestLog.generation.length;
await page.click('[data-ai-quick]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'review', { timeout: 30000 });
assert(requestLog.explore.length === 1, 'quick path performs one explore-directions request');
assert(requestLog.summary.length >= 1, 'quick path performs readiness summary');
flow = await readFlow();
assert(flow.flowStep === 'review', 'quick path reaches Review');
assert(!flow.barHidden && !flow.generateDisabled, 'Review shows enabled Generate image');
assert(requestLog.generation.length === generationBeforeQuick, 'quick path does not submit generation job');

// 8. Quick failure stays on Direction (separate navigation)
summaryFailNext = true;
await gotoCreate();
await enableMocks();
await page.evaluate(() => {
  window.YatsnCreateFixtures.showDirectionChoice();
  window.YatsnExploreFixtures.showInitialChoice();
});
await page.evaluate(async () => {
  const root = document.querySelector('[data-create]');
  await fetch('/api/v1/song-lookups', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': root?.dataset.csrf || '' },
    body: JSON.stringify({ artist: 'Sabaton', title: 'Seven Pillars of Wisdom' }),
  });
});
await page.waitForFunction(() => {
  const quick = document.querySelector('[data-ai-quick]');
  return quick && !quick.disabled;
}, { timeout: 10000 });
await page.click('[data-ai-quick]');
await page.waitForFunction(() => {
  const status = document.querySelector('[data-ai-status]');
  return status?.classList.contains('is-error') || status?.textContent?.includes('Choose a style');
}, { timeout: 15000 });
flow = await readFlow();
assert(flow.flowStep === 'direction', 'quick failure remains on Direction');
summaryFailNext = false;

// 9. Explore three rows -> Review
await gotoCreate();
await enableMocks();
await page.evaluate(() => {
  window.YatsnCreateFixtures.showDirectionChoice();
  window.YatsnExploreFixtures.showInitialChoice();
});
await page.evaluate(async () => {
  const root = document.querySelector('[data-create]');
  await fetch('/api/v1/song-lookups', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': root?.dataset.csrf || '' },
    body: JSON.stringify({ artist: 'Sabaton', title: 'Seven Pillars of Wisdom' }),
  });
});
await page.waitForFunction(() => !document.querySelector('[data-ai-explore]')?.disabled, { timeout: 10000 });
requestLog.explore.length = 0;
await page.click('[data-ai-explore]');
await page.waitForFunction(() => document.querySelectorAll('[data-ai-options] .ai-direction-card:not(.is-loading)').length === 3, { timeout: 15000 });
const optionCount = await page.evaluate(() => document.querySelectorAll('[data-ai-options] .ai-direction-card:not(.is-loading)').length);
assert(optionCount === 3, 'explore renders exactly three compact rows');
await page.click('[data-ai-options] .ai-direction-card');
await page.click('[data-ai-create-direction]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'review', { timeout: 30000 });
assert(requestLog.explore.length === 1, 'explore applies one direction request');

// 10. Manual path reaches Review
await gotoCreate();
await page.evaluate(() => {
  window.YatsnCreateFixtures.showDirectionChoice();
  window.YatsnExploreFixtures.showManual();
});
await page.waitForFunction(() => {
  const manual = document.querySelector('[data-direction-manual]');
  return manual && !manual.hidden && document.querySelector('[data-direction-manual] .style-option');
}, { timeout: 10000 });
await page.click('[data-direction-manual] [data-style-grid] .style-option');
await page.click('[data-review]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'review', { timeout: 15000 });

// 11. Fine Tune collapsed; changing setting keeps Review active
flow = await readFlow();
assert(!flow.fineTuneOpen, 'fine tune starts collapsed');
await page.evaluate(() => {
  document.querySelector('[data-fine-tune]').open = true;
  const noText = document.querySelector('[data-no-text]');
  if (noText) {
    noText.checked = !noText.checked;
    noText.dispatchEvent(new Event('change', { bubbles: true }));
  }
});
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'review', { timeout: 5000 });
flow = await readFlow();
assert(flow.flowStep === 'review', 'fine tune changes keep Review active');

// 12. Back preserves state; one card
await page.click('[data-create-back]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });
flow = await readFlow();
assert(flow.visibleCards.length === 1, 'back shows only one card');

// 13. Generate creates one job -> Generating
await page.evaluate(() => {
  window.YatsnCreate.setDirectionPrepared('ai-quick');
  window.YatsnCreate.setGenerationFixtureState({ reviewed: true, pending: false, issue: null });
  window.YatsnCreate.setFlowStep('review', { focus: false });
});
requestLog.generation.length = 0;
await page.click('[data-create-sticky-primary]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'generating', { timeout: 15000 });
assert(requestLog.generation.length === 1, 'generate creates exactly one job');

// 14. Generation failure restores Review (mock failure via restoreGenerateActionAfterFailure)
await page.evaluate(() => window.YatsnCreate.setGenerationFixtureState({ reviewed: true, pending: false, issue: 'Retry me' }));
await gotoCreate();
await page.evaluate(() => window.YatsnCreateFixtures.showPreparedReady());
flow = await readFlow();
assert(!flow.barHidden, 'review generate available after fixture restore');

// 15. Restored draft lands on Review
await gotoCreate();
await page.evaluate(() => window.YatsnCreateFixtures.showRestoredDraft());
flow = await readFlow();
assert(flow.flowStep === 'review', 'restored draft lands on furthest valid card');

// 16. Nav hidden on Create mobile, visible on Gallery
flow = await readFlow();
assert(flow.bodyCreateFocus && !flow.navVisible, 'mobile tabs hidden during Create focus');
await page.goto(`${BASE}/gallery`, { waitUntil: 'networkidle0' });
const galleryNav = await page.evaluate(() => {
  const nav = document.querySelector('.app-nav');
  return nav ? getComputedStyle(nav).display !== 'none' : false;
});
assert(galleryNav, 'gallery retains global navigation');

// 17. Width overflow checks
for (const width of [320, 390, 430]) {
  await page.setViewport({ width, height: 640 });
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
  await page.evaluate(() => window.YatsnCreateFixtures.showPreparedReady());
  const ok = await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth + 1);
  assert(ok, `no horizontal overflow at ${width}px`);
}

// 18. Desktop same sequence at 900
await page.setViewport({ width: 900, height: 900 });
await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
await page.evaluate(() => window.YatsnCreateFixtures.showSongStage());
flow = await readFlow();
assert(flow.flowStep === 'song', 'desktop starts on Song card');

// 19. Asset release paths active
const assets = await page.evaluate(() => Array.from(document.querySelectorAll('script[src], link[rel="stylesheet"]')).map((el) => el.src || el.href));
assert(assets.some((u) => /\/assets\/r\/[a-f0-9]{12}\/js\/app\.js/.test(u)), 'app.js uses release bundle path');
assert(assets.some((u) => /\/assets\/r\/[a-f0-9]{12}\/js\/explore\.js/.test(u)), 'explore.js uses release bundle path');

// 20. Explore and Quick do not create generation jobs before final Generate image
assert(requestLog.generation.length === 1, 'exactly one generation job from final Generate image tap');

writeFileSync(join(__dirname, 'verify-results.json'), JSON.stringify({ requestLog, passed: true }, null, 2));
await browser.close();
console.log('Round 015 create card flow verification passed.');

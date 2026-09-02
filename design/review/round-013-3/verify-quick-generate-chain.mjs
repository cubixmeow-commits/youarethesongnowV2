import { execSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8768';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

function assert(condition, message) {
  if (!condition) {
    throw new Error(message);
  }
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
await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 2 });

const requestLog = {
  exploreDirections: [],
  summary: [],
  generationJobs: [],
};

let summaryFailNext = false;
let firstStyleId = null;
let firstStyleName = null;
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
          id: 'lookup-fixture',
          artist: 'Sabaton',
          title: 'Seven Pillars of Wisdom',
          state: 'found',
          developmentAnalysis: {
            analyzed: true,
            analysis: songDnaFixture,
            analysisBasis: 'fixture',
          },
        },
      }),
    });
    return;
  }

  if (method === 'POST' && url.includes('/api/v1/explore-directions')) {
    requestLog.exploreDirections.push(Date.now());
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        data: {
          directions: [
            {
              name: 'Static Revolt',
              description: 'Rain-slick overpass, warm lamps, two figures paused mid-step.',
              styleName: firstStyleName,
              styleId: firstStyleId,
              promptHint: 'Keep the rain cinematic.',
            },
            {
              name: 'Quiet Threshold',
              description: 'A dim apartment doorway where the night still has one more hour.',
              styleName: firstStyleName,
              styleId: firstStyleId,
            },
            {
              name: 'Harbor Afterglow',
              description: 'Wet stone, distant water, a coat catching the last sodium light.',
              styleName: firstStyleName,
              styleId: firstStyleId,
            },
          ],
        },
      }),
    });
    return;
  }

  if (method === 'POST' && url.includes('/api/v1/creation-drafts') && !url.includes('/summary')) {
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({ data: { id: 'draft-fixture-0133' } }),
    });
    return;
  }

  if (method === 'PATCH' && url.includes('/api/v1/creation-drafts/')) {
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({ data: { id: 'draft-fixture-0133' } }),
    });
    return;
  }

  if (method === 'POST' && url.includes('/creation-drafts/') && url.includes('/summary')) {
    requestLog.summary.push(Date.now());
    if (summaryFailNext) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: {
            ready: false,
            issues: { style: 'Choose a style.' },
            requiresMembership: false,
          },
        }),
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
    requestLog.generationJobs.push(Date.now());
    await request.respond({
      status: 200,
      contentType: 'application/json',
      body: JSON.stringify({
        data: { id: 'job-fixture-013-3', status: 'queued', progressStage: 'Queued' },
      }),
    });
    return;
  }

  await request.continue();
}

let requestListenerAttached = false;

async function enableApiMocks() {
  if (!requestListenerAttached) {
    page.on('request', (request) => {
      handleInterceptedRequest(request).catch((error) => {
        console.error(error);
        request.abort();
      });
    });
    requestListenerAttached = true;
  }
  if (!interceptionEnabled) {
    await page.setRequestInterception(true);
    interceptionEnabled = true;
  }
}

async function gotoCreate() {
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0', timeout: 45000 });
  await page.waitForFunction(() => typeof window.YatsnCreateFixtures === 'object', { timeout: 15000 });
  await page.waitForFunction(() => typeof window.YatsnExploreFixtures === 'object', { timeout: 15000 });
  const styleMeta = await page.evaluate(() => {
    const btn = document.querySelector('.style-option');
    return {
      id: btn?.dataset?.styleId || null,
      name: btn?.querySelector('strong')?.textContent?.trim() || null,
    };
  });
  firstStyleId = styleMeta.id;
  firstStyleName = styleMeta.name;
  assert(firstStyleId, 'Create page exposes at least one style for direction application');
}

async function establishCreateBaseline() {
  await enableApiMocks();
  await page.evaluate(() => {
    window.YatsnCreateFixtures.showPeopleStage();
    window.YatsnCreateFixtures.showDirectionChoice();
  });
  await page.evaluate(async () => {
    const root = document.querySelector('[data-create]');
    await fetch('/api/v1/song-lookups', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': root?.dataset.csrf || '',
      },
      body: JSON.stringify({ artist: 'Sabaton', title: 'Seven Pillars of Wisdom' }),
    });
  });
  await page.waitForFunction(() => {
    const quick = document.querySelector('[data-ai-quick]');
    return quick && !quick.disabled;
  }, { timeout: 10000 });
}

async function readBarState() {
  return page.evaluate(() => {
    const bar = document.querySelector('[data-generate-bar]');
    const button = document.querySelector('[data-create-image]');
    const status = document.querySelector('[data-ai-status]');
    return {
      barHidden: bar?.hidden ?? true,
      barDisplay: bar ? getComputedStyle(bar).display : 'none',
      buttonDisabled: button?.disabled ?? true,
      statusText: status?.textContent || '',
      createState: window.YatsnCreate?.getGenerateBarState?.() || {},
    };
  });
}

// --- Happy path: real Quick Generate click chain ---
requestLog.exploreDirections.length = 0;
requestLog.summary.length = 0;
requestLog.generationJobs.length = 0;
summaryFailNext = false;

await gotoCreate();
await establishCreateBaseline();
await page.screenshot({ path: join(__dirname, 'mobile-390-before-quick-generate.png') });

const exploreBefore = requestLog.exploreDirections.length;
const summaryBefore = requestLog.summary.length;

const exploreResponse = page.waitForResponse(
  (response) => response.url().includes('/api/v1/explore-directions') && response.request().method() === 'POST',
  { timeout: 15000 },
);
await page.click('[data-ai-quick]');
await exploreResponse;

await page.waitForFunction(() => {
  const status = document.querySelector('[data-ai-status]');
  return status?.textContent?.includes('Preparing');
}, { timeout: 10000 });
await page.screenshot({ path: join(__dirname, 'mobile-390-preparation-pending.png') });

await page.waitForFunction(() => {
  const review = document.querySelector('[data-create-card="review"]');
  const bar = document.querySelector('[data-generate-bar]');
  const button = document.querySelector('[data-create-image]');
  return review && !review.hidden && bar && !bar.hidden && button && !button.disabled;
}, { timeout: 15000 });

const readyState = await readBarState();
assert(requestLog.exploreDirections.length === exploreBefore + 1, 'explore-directions runs exactly once per Quick Generate');
assert(requestLog.summary.length === summaryBefore + 1, 'summary/review runs exactly once after direction loading unlocks');
assert(requestLog.exploreDirections[0] <= requestLog.summary[0], 'summary runs only after explore-directions completes');
assert(readyState.barHidden === false && readyState.barDisplay !== 'none', 'final bar becomes visible');
assert(readyState.buttonDisabled === false, 'Generate image is enabled');
assert(!readyState.statusText.includes('Preparing your creation'), 'status leaves the preparing dead-end copy');
assert(readyState.createState.path === 'ai-quick', 'prepared-direction state is ai-quick');

await page.screenshot({ path: join(__dirname, 'mobile-390-ready-generate-image.png') });

await page.click('[data-create-image]');
const generationDeadline = Date.now() + 10000;
while (requestLog.generationJobs.length < 1 && Date.now() < generationDeadline) {
  await new Promise((resolve) => setTimeout(resolve, 100));
}
assert(requestLog.generationJobs.length === 1, 'one Generate image activation creates one generation job');

// --- Recoverable preparation failure ---
requestLog.exploreDirections.length = 0;
requestLog.summary.length = 0;
summaryFailNext = true;

await page.reload({ waitUntil: 'networkidle0', timeout: 45000 });
await establishCreateBaseline();
await page.click('[data-ai-quick]');
await page.waitForFunction(() => {
  const status = document.querySelector('[data-ai-status]');
  return status?.classList.contains('yatsn-status--error') || status?.classList.contains('is-error');
}, { timeout: 15000 });

const failState = await readBarState();
assert(failState.barHidden === true || failState.buttonDisabled === true, 'failed preparation does not expose an enabled final action');
assert(failState.statusText.length > 0, 'failed preparation shows a recoverable error');
const quick = await page.$('[data-ai-quick]');
assert(quick && !(await page.evaluate((el) => el.disabled, quick)), 'Generate for me remains usable after failure');

await page.screenshot({ path: join(__dirname, 'mobile-390-preparation-failure.png') });

// --- Explore options still works without premature final bar ---
summaryFailNext = false;
requestLog.exploreDirections.length = 0;

await page.reload({ waitUntil: 'networkidle0', timeout: 45000 });
await establishCreateBaseline();
await page.click('[data-ai-explore]');
await page.waitForFunction(() => {
  const cards = document.querySelectorAll('[data-ai-options] .ai-direction-card:not(.is-loading)');
  return cards.length === 3;
}, { timeout: 15000 });

const exploreState = await readBarState();
assert(exploreState.barHidden === true && exploreState.barDisplay === 'none', 'Explore options does not show premature final bar');

writeFileSync(
  join(__dirname, 'review-notes.json'),
  `${JSON.stringify({
    version: 'round-013.3',
    requestLog,
    readyState,
    failState,
    exploreState,
  }, null, 2)}\n`,
);

await browser.close();
console.log('Round 013.3 Quick Generate async chain verification passed');

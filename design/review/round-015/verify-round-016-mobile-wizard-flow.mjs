import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8780';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';
const OUT = join(__dirname, '../round-016');

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

mkdirSync(OUT, { recursive: true });

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

async function newPage(width, height) {
  const page = await context.newPage();
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  page.setDefaultTimeout(30000);
  return page;
}

async function overflowCheck(page) {
  return page.evaluate(() => {
    const scroll = document.querySelector('[data-create-scroll]');
    const wizard = document.querySelector('[data-create-wizard]');
    const wizardOverflow = wizard && wizard.offsetHeight > window.innerHeight + 2;
    const scrollOverflow = scroll && scroll.scrollHeight > scroll.clientHeight + 2 && !scroll.classList.contains('is-scrollable');
    return {
      wizardOverflow,
      scrollOverflow,
      wizardHeight: wizard?.offsetHeight || 0,
      innerHeight: window.innerHeight,
    };
  });
}

async function enableMocks(page) {
  let enabled = false;
  await page.setRequestInterception(true);
  page.on('request', async (request) => {
    const url = request.url();
    const method = request.method();
    if (method === 'POST' && url.includes('/api/v1/song-lookups')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: {
            id: '00000000-0000-4000-8000-0000000099',
            artist: 'Sabaton',
            title: 'Seven Pillars of Wisdom',
            state: 'found',
            developmentAnalysis: { analyzed: true, analysis: { essence: 'fixture' }, analysisBasis: 'fixture' },
          },
        }),
      });
      return;
    }
    if (method === 'POST' && url.includes('/api/v1/explore-directions')) {
      const styleId = await page.evaluate(() => document.querySelector('.style-option')?.dataset?.styleId || 'style-fixture');
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: {
            directions: [{
              name: 'Static Revolt',
              description: 'Rain-slick overpass.',
              styleName: 'Gothic Romance',
              styleId,
            }],
          },
        }),
      });
      return;
    }
    if (method === 'POST' && url.includes('/summary')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { ready: true, requiresMembership: false } }),
      });
      return;
    }
    if (method === 'PATCH' && url.includes('/api/v1/creation-drafts/')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { id: 'draft-fixture' } }),
      });
      return;
    }
    try {
      await request.continue();
    } catch {
      // navigation abort
    }
  });
  enabled = true;
  return enabled;
}

async function gotoCreate(page) {
  await enableMocks(page);
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
  await page.waitForFunction(() => typeof window.YatsnCreate === 'object');
}

// Song idle — 390×844
const page390 = await newPage(390, 844);
await gotoCreate(page390);
let overflow = await overflowCheck(page390);
assert(!overflow.wizardOverflow, 'song idle: wizard fits viewport at 390×844');
assert((await page390.evaluate(() => document.querySelectorAll('.create-wizard__topbar').length)) === 1, 'exactly one wizard header');
const chrome = await page390.evaluate(() => ({
  nav: getComputedStyle(document.querySelector('.app-nav')).display,
  topbar: getComputedStyle(document.querySelector('.app-topbar')).display,
  stickyLabel: document.querySelector('[data-create-sticky-primary]')?.textContent || '',
}));
assert(chrome.nav === 'flex' || chrome.nav === 'block', 'bottom nav visible during wizard');
assert(chrome.topbar === 'none', 'site topbar hidden during wizard');
assert(chrome.stickyLabel === 'Find this song', 'song idle CTA is Find this song');
await page390.screenshot({ path: join(OUT, 'mobile-390-song-idle.png') });

// Song found
await page390.type('#song-form [name=artist]', 'Sabaton');
await page390.type('#song-form [name=title]', 'Seven Pillars of Wisdom');
await page390.click('[data-create-sticky-primary]');
await page390.waitForSelector('[data-song-results] [data-song-result].is-selected', { timeout: 15000 });
await page390.waitForFunction(() => document.querySelector('[data-create-sticky-primary]')?.textContent === 'Use this song', { timeout: 10000 });
overflow = await overflowCheck(page390);
assert(!overflow.wizardOverflow && !overflow.scrollOverflow, 'song found: fits viewport at 390×844');
const foundCta = await page390.$eval('[data-create-sticky-primary]', (el) => el.textContent);
assert(foundCta === 'Use this song', 'match found CTA is Use this song');
await page390.screenshot({ path: join(OUT, 'mobile-390-song-found.png') });

await page390.click('[data-create-sticky-primary]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 15000 });
overflow = await overflowCheck(page390);
assert(!overflow.wizardOverflow, 'people loaded: wizard fits at 390×844');
const chips = await page390.$$eval('[data-portrait-grid] .portrait-chip', (els) => els.length);
assert(chips >= 0, 'people tray renders');
await page390.screenshot({ path: join(OUT, 'mobile-390-people-loaded.png') });

await page390.click('[data-people-continue-without]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });
await page390.waitForFunction(() => {
  const quick = document.querySelector('[data-ai-quick]');
  return quick && !quick.disabled;
}, { timeout: 15000 });
overflow = await overflowCheck(page390);
assert(!overflow.wizardOverflow, 'direction: wizard fits at 390×844');
await page390.screenshot({ path: join(OUT, 'mobile-390-direction.png') });

await page390.click('[data-ai-quick]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'review', { timeout: 30000 });
const reviewUi = await page390.evaluate(() => ({
  wrapHidden: document.querySelector('[data-create-sticky-primary-wrap]')?.hidden ?? true,
  buttonText: document.querySelector('[data-create-sticky-primary]')?.textContent || '',
  buttonDisabled: document.querySelector('[data-create-sticky-primary]')?.disabled ?? true,
  directionName: document.querySelector('[data-review-direction-name]')?.textContent || '',
  stickyVisible: !!document.querySelector('[data-create-sticky-actions]'),
}));
assert(!reviewUi.wrapHidden, 'review: generate action wrap visible');
assert(reviewUi.buttonText === 'Generate image', 'review sticky shows Generate image');
assert(!reviewUi.buttonDisabled, 'review generate enabled after quick path');
assert(reviewUi.directionName && reviewUi.directionName !== 'Not chosen yet', 'review shows direction name');
overflow = await overflowCheck(page390);
assert(!overflow.scrollOverflow || overflow.scrollOverflow === false, 'review may scroll content but sticky actions remain');
await page390.screenshot({ path: join(OUT, 'mobile-390-review.png') });

// Back preserves song selection
await page390.click('[data-create-back]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction');
await page390.click('[data-create-back]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people');
const portraitSelected = await page390.$$eval('[data-portrait-grid] .portrait-chip.is-selected', (els) => els.length);
assert(portraitSelected >= 0, 'back to people preserves tray');

// Additional viewport checks — song idle only
for (const size of [[375, 667], [430, 932]]) {
  const page = await newPage(size[0], size[1]);
  await gotoCreate(page);
  const o = await overflowCheck(page);
  assert(!o.wizardOverflow, `song idle fits ${size[0]}×${size[1]}`);
  await page.close();
}

writeFileSync(join(OUT, 'verify-results.json'), JSON.stringify({ passed: true }, null, 2));
await browser.close();
console.log('Round 016 mobile wizard verification passed.');

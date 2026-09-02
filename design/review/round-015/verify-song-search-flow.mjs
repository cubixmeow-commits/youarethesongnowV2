import { execSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8772';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); if(!$u){fwrite(STDERR,"no owner\\n"); exit(1);} $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

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
const pageErrors = [];
page.on('pageerror', (err) => pageErrors.push(err.message));

let lookupCalls = 0;
let interceptionEnabled = false;
let lookupMockMode = 'found';

async function enableMocks() {
  if (interceptionEnabled) return;
  page.on('request', async (request) => {
    const url = request.url();
    const method = request.method();
    if (method === 'POST' && url.includes('/api/v1/song-lookups')) {
      lookupCalls += 1;
      if (lookupMockMode === 'fail') {
        await request.respond({
          status: 500,
          contentType: 'application/json',
          body: '{"error":{"message":"Server unavailable"}}',
        });
        return;
      }
      if (lookupMockMode === 'notFound') {
        await request.respond({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            data: {
              id: '00000000-0000-4000-8000-0000000098',
              artist: 'Unknown',
              title: 'Missing Track',
              state: 'notFound',
            },
          }),
        });
        return;
      }
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: {
            id: '00000000-0000-4000-8000-0000000099',
            artist: 'Sabaton',
            title: 'Seven Pillars of Wisdom',
            state: 'found',
          },
        }),
      });
      return;
    }
    if (method === 'POST' && url.includes('/api/v1/creation-drafts') && !url.includes('/summary')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { id: '00000000-0000-4000-8000-0000000015' } }),
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
      // Navigation can abort in-flight requests while interception is enabled.
    }
  });
  await page.setRequestInterception(true);
  interceptionEnabled = true;
}

async function gotoCreate() {
  lookupCalls = 0;
  await enableMocks();
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
  await page.waitForFunction(() => typeof window.YatsnCreate?.getFlowStep === 'function', { timeout: 15000 });
}

async function readSongUi() {
  return page.evaluate(() => {
    const form = document.querySelector('#song-form');
    const submit = document.querySelector('[data-create-sticky-primary]');
    const panel = document.querySelector('[data-yatsn-song-search]');
    return {
      flowStep: window.YatsnCreate?.getFlowStep?.() || '',
      songState: panel?.dataset.yatsnSongState || '',
      status: document.querySelector('[data-song-status]')?.textContent || '',
      statusHidden: document.querySelector('[data-song-status]')?.hidden ?? true,
      submitDisabled: submit?.disabled ?? false,
      submitLoading: submit?.classList.contains('is-loading') ?? false,
      resultsHidden: document.querySelector('[data-song-results]')?.hidden ?? true,
      hasResultButton: !!document.querySelector('[data-song-results] [data-song-result]'),
      formWired: form ? true : false,
      hasYatsnSongSearch: typeof window.YatsnSongSearch === 'object',
      hasYatsnCreate: typeof window.YatsnCreate === 'object',
    };
  });
}

// Wizard initializes without JS exceptions blocking modules
lookupMockMode = 'found';
await gotoCreate();
assert(pageErrors.length === 0, `no page errors during init: ${pageErrors.join('; ')}`);
let ui = await readSongUi();
assert(ui.hasYatsnSongSearch && ui.hasYatsnCreate, 'Create and song-search modules load');
assert(ui.flowStep === 'song', 'wizard starts on Song card');

// Missing fields — click
await page.click('[data-create-sticky-primary]');
ui = await readSongUi();
assert(ui.status.includes('Enter both'), 'click with missing fields shows validation error');
assert(lookupCalls === 0, 'missing fields do not call song-lookups API');

// Enter-key submission with missing fields
await page.focus('#song-form [name=artist]');
await page.keyboard.press('Enter');
ui = await readSongUi();
assert(ui.status.includes('Enter both'), 'Enter key triggers validation');

// Successful match — click
lookupMockMode = 'found';
await page.type('#song-form [name=artist]', 'Sabaton');
await page.type('#song-form [name=title]', 'Seven Pillars of Wisdom');
lookupCalls = 0;
await page.click('[data-create-sticky-primary]');
await page.waitForFunction(() => {
  const panel = document.querySelector('[data-yatsn-song-search]');
  return panel?.dataset.yatsnSongState === 'result' || panel?.dataset.yatsnSongState === 'loading';
}, { timeout: 5000 });
await page.waitForFunction(() => document.querySelector('[data-yatsn-song-search]')?.dataset.yatsnSongState === 'result', { timeout: 15000 });
assert(lookupCalls === 1, 'successful click performs one song-lookups request');
ui = await readSongUi();
assert(ui.hasResultButton, 'successful match shows selectable result');
assert(!ui.submitLoading && !ui.submitDisabled, 'submit re-enabled after success');

// Double-click protection — only one in-flight request
lookupMockMode = 'found';
lookupCalls = 0;
await page.evaluate(() => {
  const artist = document.querySelector('#song-form [name=artist]');
  const title = document.querySelector('#song-form [name=title]');
  artist.value = 'Sabaton';
  title.value = 'Double Tap Test';
  artist.dispatchEvent(new Event('input', { bubbles: true }));
  title.dispatchEvent(new Event('input', { bubbles: true }));
});
await page.evaluate(() => {
  const btn = document.querySelector('[data-create-sticky-primary]');
  btn?.click();
  btn?.click();
});
await sleep(2500);
assert(lookupCalls === 1, 'double click only triggers one lookup while in flight');

// Failed request — inline error + retry
await gotoCreate();
lookupMockMode = 'fail';
await page.type('#song-form [name=artist]', 'Sabaton');
await page.type('#song-form [name=title]', 'Server Fail');
await page.click('[data-create-sticky-primary]');
await page.waitForFunction(() => {
  const status = document.querySelector('[data-song-status]');
  const submit = document.querySelector('[data-create-sticky-primary]');
  return status && !status.hidden && status.textContent.length > 0
    && submit && !submit.classList.contains('is-loading');
}, { timeout: 10000 });
ui = await readSongUi();
assert(ui.status.length > 0 && !ui.statusHidden, 'failed request shows visible inline error');
assert(!ui.submitLoading, 'loading state clears after failure');

// No-result response
await gotoCreate();
lookupMockMode = 'notFound';
await page.type('#song-form [name=artist]', 'Unknown');
await page.type('#song-form [name=title]', 'Missing Track');
await page.click('[data-create-sticky-primary]');
await page.waitForFunction(() => document.querySelector('[data-song-retry-wrap]') && !document.querySelector('[data-song-retry-wrap]').hidden, { timeout: 10000 });
ui = await readSongUi();
assert(ui.status.includes('could not find'), 'not-found shows actionable message');

// Confirm match → People card
await gotoCreate();
lookupMockMode = 'found';
await page.type('#song-form [name=artist]', 'Sabaton');
await page.type('#song-form [name=title]', 'Seven Pillars of Wisdom');
await page.click('[data-create-sticky-primary]');
await page.waitForSelector('[data-song-results] [data-song-result]', { timeout: 15000 });
await page.waitForFunction(() => document.querySelector('[data-create-sticky-primary]')?.textContent === 'Use this song', { timeout: 10000 });
await page.click('[data-create-sticky-primary]');
await page.waitForFunction(() => window.YatsnSongSearch?.isConfirmed?.(), { timeout: 10000 });
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 15000 });
ui = await readSongUi();
assert(ui.flowStep === 'people', 'confirming song advances to People card');

// Enter-key successful submission
await gotoCreate();
lookupMockMode = 'found';
await page.type('#song-form [name=artist]', 'Sabaton');
await page.type('#song-form [name=title]', 'Enter Key Song');
lookupCalls = 0;
await page.focus('#song-form [name=title]');
await page.keyboard.press('Enter');
await page.waitForFunction(() => document.querySelector('[data-yatsn-song-search]')?.dataset.yatsnSongState === 'result', { timeout: 15000 });
assert(lookupCalls === 1, 'Enter key submits form and calls API once');

writeFileSync(join(__dirname, 'verify-song-search-results.json'), JSON.stringify({ passed: true, pageErrors }, null, 2));
await browser.close();
console.log('Song search flow verification passed.');

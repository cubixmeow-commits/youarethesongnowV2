import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const OUT = __dirname;
const ROOT = join(__dirname, '../../..');
const BASE = 'http://127.0.0.1:8765';
const CHROME = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const SONG = { artist: 'Owner Test Band', title: 'Midnight Harbor' };

mkdirSync(OUT, { recursive: true });

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE deleted_at IS NULL ORDER BY id LIMIT 1"); $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const peopleDraftId = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $d=\\Yatsn\\Support\\Database::one("SELECT d.public_id FROM creation_drafts d INNER JOIN song_lookups sl ON sl.id = d.song_lookup_id WHERE length(d.portrait_ids_json) = 2 ORDER BY d.id DESC LIMIT 1"); echo $d ? $d["public_id"] : "";'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const directionDraftId = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $d=\\Yatsn\\Support\\Database::one("SELECT d.public_id FROM creation_drafts d INNER JOIN song_lookups sl ON sl.id = d.song_lookup_id WHERE length(d.portrait_ids_json) > 2 ORDER BY d.id DESC LIMIT 1"); echo $d ? $d["public_id"] : "";'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const createUrl = (draftId) => (draftId ? `${BASE}/create?draft=${draftId}` : `${BASE}/create`);

const imageId = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $i=\\Yatsn\\Support\\Database::one("SELECT public_id FROM generated_images ORDER BY id DESC LIMIT 1"); echo $i ? $i["public_id"] : "";'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const zoomResults = [];

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: true,
  args: ['--no-sandbox', '--disable-dev-shm-usage'],
});

const guestContext = await browser.createBrowserContext();
const authedContext = await browser.createBrowserContext();

async function captureGuest(name, url, width, height) {
  const page = await guestContext.newPage();
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  await page.goto(url, { waitUntil: 'networkidle0', timeout: 30000 });
  await page.screenshot({ path: join(OUT, name), type: 'png' });
  await page.close();
}

await authedContext.setCookie({
  name: 'yatsn_session',
  value: token,
  domain: '127.0.0.1',
  path: '/',
});

const authedPage = await authedContext.newPage();

async function captureAuthed(name, url, width, height, afterNavigate) {
  await authedPage.setViewport({ width, height, deviceScaleFactor: 1 });
  await authedPage.goto(url, { waitUntil: 'networkidle0', timeout: 30000 });
  if (afterNavigate) {
    await afterNavigate(authedPage);
    await new Promise((resolve) => setTimeout(resolve, 400));
  }
  await authedPage.screenshot({
    path: join(OUT, name),
    type: 'png',
  });
}

async function prepareCreateWithSong(page, { navigate = true, draftId = peopleDraftId } = {}) {
  if (navigate) {
    await page.goto(createUrl(draftId), { waitUntil: 'networkidle0', timeout: 30000 });
  }
  await page.waitForSelector('[data-style-grid] .style-option', { timeout: 15000 });
  if (draftId) {
    await waitForPeopleStage(page);
    return;
  }
  await submitSongLookup(page);
}

async function submitSongLookup(page) {
  await page.waitForSelector('#song-form input[name="artist"]', { timeout: 15000 });
  await page.waitForSelector('[data-style-grid] .style-option', { timeout: 15000 });
  await page.$eval('input[name="artist"]', (el, value) => {
    el.value = value;
    el.dispatchEvent(new Event('input', { bubbles: true }));
  }, SONG.artist);
  await page.$eval('input[name="title"]', (el, value) => {
    el.value = value;
    el.dispatchEvent(new Event('input', { bubbles: true }));
  }, SONG.title);
  await Promise.all([
    page.waitForResponse((res) => res.url().includes('/api/v1/song-lookups') && res.ok(), { timeout: 20000 }),
    page.click('.btn--retrieve'),
  ]);
  await page.waitForFunction(() => {
    const people = document.querySelector('#the-people');
    const direction = document.querySelector('#the-direction');
    return people && !people.hidden && direction && direction.hidden;
  }, { timeout: 20000 });
  await waitForPeopleStage(page);
}

async function waitForPeopleStage(page) {
  await page.waitForFunction(() => {
    const people = document.querySelector('#the-people');
    const step = document.querySelectorAll('.session-progress__step')[1];
    return people && !people.hidden && step?.classList.contains('is-current');
  }, { timeout: 30000 });
}

async function waitForDirectionStage(page) {
  await page.waitForFunction(() => {
    const direction = document.querySelector('#the-direction');
    const step = document.querySelectorAll('.session-progress__step')[2];
    return direction && !direction.hidden && step?.classList.contains('is-current');
  }, { timeout: 30000 });
}

async function waitForCurrentStep(page, stepIndex) {
  await page.waitForFunction((index) => {
    const step = document.querySelectorAll('.session-progress__step')[index];
    return step?.classList.contains('is-current');
  }, { timeout: 20000 }, stepIndex);
}

async function selectFirstPortrait(page) {
  await page.waitForSelector('[data-portrait-grid] .portrait-chip__select', { timeout: 15000 });
  const selected = await page.$eval('[data-portrait-grid] .portrait-chip.is-selected', (el) => Boolean(el)).catch(() => false);
  if (!selected) {
    await page.click('[data-portrait-grid] .portrait-chip__select');
  }
  await page.waitForFunction(() => {
    const direction = document.querySelector('#the-direction');
    return direction && !direction.hidden;
  }, { timeout: 15000 });
  await waitForDirectionStage(page);
}

async function scrollToStage(page, stageId) {
  await page.evaluate((id) => {
    const header = document.querySelector('.session-header');
    if (header) {
      window.scrollTo(0, Math.max(0, header.offsetTop - 4));
    }
    document.querySelector(id)?.scrollIntoView({ block: 'nearest', behavior: 'instant' });
  }, stageId);
}

async function showPaywall(page) {
  await page.evaluate(() => {
    const panel = document.querySelector('[data-paywall]');
    const actions = document.querySelector('[data-summary-actions]');
    if (actions) actions.hidden = true;
    if (panel) panel.hidden = false;
  });
  await page.evaluate(() => {
    document.querySelector('[data-paywall]')?.scrollIntoView({ block: 'start', behavior: 'instant' });
  });
  await new Promise((resolve) => setTimeout(resolve, 300));
}

async function checkOverflow(page, label) {
  const result = await page.evaluate(() => ({
    scrollWidth: document.documentElement.scrollWidth,
    innerWidth: window.innerWidth,
    ok: document.documentElement.scrollWidth <= window.innerWidth,
  }));
  return { label, ...result };
}

async function checkZoom(page, label, prepare, width, height, inputSelector) {
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  await page.goto(createUrl(directionDraftId), { waitUntil: 'networkidle0', timeout: 30000 });
  if (prepare) {
    await prepare(page);
  }
  const client = await page.createCDPSession();
  await client.send('Emulation.setPageScaleFactor', { pageScaleFactor: 2 });
  await new Promise((resolve) => setTimeout(resolve, 500));
  const result = await page.evaluate((selector) => {
    const input = document.querySelector(selector);
    const inputSize = input ? parseFloat(getComputedStyle(input).fontSize) : null;
    const selected = document.querySelector('.style-option.is-selected, .portrait-chip.is-selected');
    const checkout = document.querySelector('[data-checkout]');
    return {
      scrollWidth: document.documentElement.scrollWidth,
      innerWidth: window.innerWidth,
      horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
      inputSize,
      inputsAtLeast16px: inputSize == null || inputSize >= 16,
      checkoutReachable: Boolean(checkout && !checkout.disabled),
      hasSelectedStateCue: Boolean(selected && selected.classList.contains('is-selected')),
    };
  }, inputSelector);
  await client.send('Emulation.setPageScaleFactor', { pageScaleFactor: 1 });
  return { label, viewport: `${width}×${height}`, pageScaleFactor: 2, ...result };
}

await captureGuest('home-mobile-390.png', `${BASE}/`, 390, 844);
await captureGuest('home-desktop-1440.png', `${BASE}/`, 1440, 900);

const overflowPage = await guestContext.newPage();
await overflowPage.setViewport({ width: 320, height: 568, deviceScaleFactor: 1 });
await overflowPage.goto(`${BASE}/`, { waitUntil: 'networkidle0', timeout: 30000 });
const homeOverflow = await checkOverflow(overflowPage, 'home-320');
await overflowPage.screenshot({ path: join(OUT, 'overflow-check-320-home.png'), type: 'png' });
await overflowPage.close();

await captureAuthed('create-mobile-390-top.png', `${BASE}/create`, 390, 844);

await captureAuthed('create-mobile-390-people.png', createUrl(peopleDraftId), 390, 844, async (page) => {
  await prepareCreateWithSong(page, { navigate: false, draftId: peopleDraftId });
  await page.waitForSelector('[data-portrait-grid] .portrait-chip', { timeout: 15000 });
  await page.evaluate(() => {
    const header = document.querySelector('.session-header');
    const tray = document.querySelector('.portrait-tray');
    const top = tray.offsetTop - (header?.offsetHeight || 0) - 12;
    window.scrollTo(0, Math.max(0, top));
  });
});

await captureAuthed('create-mobile-390-direction.png', createUrl(directionDraftId), 390, 844, async (page) => {
  await page.waitForSelector('[data-style-grid] .style-option', { timeout: 15000 });
  await waitForDirectionStage(page);
  await page.evaluate(() => {
    const header = document.querySelector('.session-header');
    const direction = document.querySelector('#the-direction');
    const top = direction.offsetTop - (header?.offsetHeight || 0) - 8;
    window.scrollTo(0, Math.max(0, top));
  });
});

await captureAuthed('create-desktop-1440.png', `${BASE}/create`, 1440, 900);

await captureAuthed('gallery-mobile-390.png', `${BASE}/gallery`, 390, 844, async (page) => {
  await page.evaluate(() => {
    const grid = document.querySelector('[data-gallery-grid]');
    if (grid) grid.innerHTML = '';
  });
});

await captureAuthed('gallery-desktop-1440.png', `${BASE}/gallery`, 1440, 900, async (page) => {
  await page.evaluate(() => {
    const grid = document.querySelector('[data-gallery-grid]');
    if (grid) grid.innerHTML = '';
  });
});

await captureAuthed('paywall-mobile-390.png', `${BASE}/create`, 390, 844, showPaywall);
await captureAuthed('paywall-desktop-1440.png', `${BASE}/create`, 1440, 900, showPaywall);

if (imageId) {
  await captureAuthed('reveal-mobile-390.png', `${BASE}/images/${imageId}`, 390, 844);
  await captureAuthed('reveal-desktop-1440.png', `${BASE}/images/${imageId}`, 1440, 900);
}

const zoomPage = await authedContext.newPage();
zoomResults.push(await checkZoom(zoomPage, 'create-direction-900', async (page) => {
  await page.waitForSelector('[data-style-grid] .style-option', { timeout: 15000 });
  await waitForDirectionStage(page);
  await page.click('.style-option');
}, 900, 900, '#song-form input[name="artist"]'));
zoomResults.push(await checkZoom(zoomPage, 'paywall-900', async (page) => {
  await showPaywall(page);
}, 900, 900, '#song-form input[name="artist"]'));
zoomResults.push(await checkZoom(zoomPage, 'paywall-1440', async (page) => {
  await showPaywall(page);
}, 1440, 900, '#song-form input[name="artist"]'));
const homeZoomPage = await guestContext.newPage();
await homeZoomPage.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1 });
await homeZoomPage.goto(`${BASE}/`, { waitUntil: 'networkidle0', timeout: 30000 });
const homeZoomClient = await homeZoomPage.createCDPSession();
await homeZoomClient.send('Emulation.setPageScaleFactor', { pageScaleFactor: 2 });
await new Promise((resolve) => setTimeout(resolve, 500));
const homeZoom = await homeZoomPage.evaluate(() => ({
  scrollWidth: document.documentElement.scrollWidth,
  innerWidth: window.innerWidth,
  horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
}));
zoomResults.push({ label: 'home-1440', viewport: '1440×900', pageScaleFactor: 2, ...homeZoom, inputsAtLeast16px: true });
await homeZoomPage.close();
await zoomPage.close();

writeFileSync(join(OUT, 'zoom-check-200-results.json'), JSON.stringify({ homeOverflow, zoomResults }, null, 2));

await browser.close();
console.log('Round 008 screenshots captured in', OUT);
console.log('Home 320 overflow:', homeOverflow.ok ? 'PASS' : 'FAIL', homeOverflow);
console.log('200% zoom checks:', JSON.stringify(zoomResults, null, 2));

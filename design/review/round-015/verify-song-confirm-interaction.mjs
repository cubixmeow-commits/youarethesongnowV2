import { execSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8780';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
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

async function enableMocks(page) {
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
          },
        }),
      });
      return;
    }
    if (url.includes('/api/v1/creation-drafts')) {
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
}

async function newPage(width, height, hasTouch = false) {
  const page = await context.newPage();
  await page.setViewport({ width, height, deviceScaleFactor: 1, hasTouch });
  page.setDefaultTimeout(30000);
  return page;
}

async function searchSong(page, useTap) {
  await enableMocks(page);
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
  await page.waitForFunction(() => typeof window.YatsnSongSearch === 'object');
  await page.type('#song-form [name=artist]', 'Sabaton');
  await page.type('#song-form [name=title]', 'Seven Pillars of Wisdom');
  if (useTap) await page.tap('[data-create-sticky-primary]');
  else await page.click('[data-create-sticky-primary]');
  await page.waitForSelector('[data-song-results] [data-song-result].is-selected', { timeout: 15000 });
  await page.waitForFunction(
    () => document.querySelector('[data-create-sticky-primary]')?.textContent === 'Use this song',
    { timeout: 10000 },
  );
}

async function stickyHitTest(page) {
  return page.evaluate(() => {
    const btn = document.querySelector('[data-create-sticky-primary]');
    const rect = btn.getBoundingClientRect();
    const hit = document.elementFromPoint(rect.left + rect.width / 2, rect.top + rect.height / 2);
    return {
      scrollY: window.scrollY,
      inView: rect.top >= 0 && rect.bottom <= window.innerHeight,
      hitOk: hit === btn || !!hit?.closest('[data-create-sticky-primary]'),
      cta: btn.textContent,
      disabled: btn.disabled,
    };
  });
}

async function confirmViaSticky(page, label, useTap) {
  const before = await stickyHitTest(page);
  assert(before.scrollY === 0, `${label}: window.scrollY is 0 before confirm`);
  assert(before.hitOk, `${label}: sticky primary is hit-testable`);
  assert(before.cta === 'Use this song', `${label}: sticky CTA is Use this song`);
  assert(!before.disabled, `${label}: sticky CTA is enabled`);
  if (useTap) await page.tap('[data-create-sticky-primary]');
  else await page.click('[data-create-sticky-primary]');
  await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 10000 });
  const after = await page.evaluate(() => ({
    step: window.YatsnCreate?.getFlowStep?.(),
    confirmed: window.YatsnSongSearch?.isConfirmed?.(),
    scrollY: window.scrollY,
  }));
  assert(after.step === 'people', `${label}: advances to People`);
  assert(after.confirmed, `${label}: song is confirmed`);
  assert(after.scrollY === 0, `${label}: window.scrollY stays 0 after confirm`);
}

async function confirmViaResult(page, label, useTap) {
  const before = await page.evaluate(() => ({
    scrollY: window.scrollY,
    selected: !!document.querySelector('[data-song-results] [data-song-result].is-selected'),
  }));
  assert(before.scrollY === 0, `${label}: window.scrollY is 0 before result confirm`);
  assert(before.selected, `${label}: result is selected before confirm`);
  const result = await page.$('[data-song-results] [data-song-result].is-selected');
  assert(result, `${label}: selected result exists`);
  if (useTap) await result.tap();
  else await result.click();
  await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 10000 });
  const after = await page.evaluate(() => ({
    step: window.YatsnCreate?.getFlowStep?.(),
    confirmed: window.YatsnSongSearch?.isConfirmed?.(),
    scrollY: window.scrollY,
  }));
  assert(after.step === 'people', `${label}: result tap advances to People`);
  assert(after.confirmed, `${label}: song is confirmed via result`);
  assert(after.scrollY === 0, `${label}: window.scrollY stays 0 after result confirm`);
}

// Mobile tap — sticky CTA
const mobileSticky = await newPage(390, 844, true);
await searchSong(mobileSticky, true);
await confirmViaSticky(mobileSticky, 'mobile sticky tap', true);
await mobileSticky.close();

// Mobile tap — selected result row
const mobileResult = await newPage(390, 844, true);
await searchSong(mobileResult, true);
await confirmViaResult(mobileResult, 'mobile result tap', true);
await mobileResult.close();

// Desktop click — sticky CTA
const desktopSticky = await newPage(1440, 900, false);
await searchSong(desktopSticky, false);
await confirmViaSticky(desktopSticky, 'desktop sticky click', false);
await desktopSticky.close();

// Desktop click — selected result row
const desktopResult = await newPage(1280, 800, false);
await searchSong(desktopResult, false);
await confirmViaResult(desktopResult, 'desktop result click', false);
await desktopResult.close();

writeFileSync(
  join(__dirname, 'verify-song-confirm-results.json'),
  JSON.stringify({ passed: true }, null, 2),
);
await browser.close();
console.log('Song confirm interaction verification passed.');

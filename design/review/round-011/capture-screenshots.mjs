import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const OUT = __dirname;
const ROOT = join(__dirname, '../../..');
const BASE = 'http://127.0.0.1:8765';
const CHROME = process.env.CHROME
  || (process.platform === 'darwin'
    ? '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
    : '/usr/bin/google-chrome-stable');

mkdirSync(OUT, { recursive: true });

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); if(!$u){fwrite(STDERR,"no owner\\n"); exit(1);} $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const zoomResults = [];
const notes = [];
const visibilityChecks = [];

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: true,
  args: ['--no-sandbox', '--disable-dev-shm-usage', '--hide-scrollbars'],
});

const authedContext = await browser.createBrowserContext();
await authedContext.setCookie({
  name: 'yatsn_session',
  value: token,
  domain: '127.0.0.1',
  path: '/',
});

const page = await authedContext.newPage();
page.setDefaultTimeout(30000);

async function assertEntryComposition(label) {
  const check = await page.evaluate((entryLabel) => {
    const topbar = document.querySelector('.app-topbar');
    const topbarBottom = topbar?.getBoundingClientRect().bottom ?? 0;
    const offset = Math.ceil((topbar?.getBoundingClientRect().height || 56) + 24);
    const form = document.querySelector('.yatsn-song-search__form');
    if (form) {
      const y = form.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo(0, Math.max(0, y));
    }
    const viewportBottom = window.innerHeight;
    const selectors = ['[name="artist"]', '[name="title"]', '.yatsn-song-search__submit'];
    const targets = selectors.map((selector) => {
      const el = document.querySelector(selector);
      const box = el?.getBoundingClientRect();
      const targetVisible = !!box
        && box.top >= topbarBottom - 1
        && box.bottom <= viewportBottom + 1
        && box.width > 0
        && box.height > 0;
      return {
        selector,
        found: !!el,
        targetVisible,
        targetBounds: box
          ? { top: box.top, bottom: box.bottom, left: box.left, right: box.right, width: box.width, height: box.height }
          : null,
      };
    });
    const resume = document.querySelector('[data-song-resume]');
    return {
      label: entryLabel,
      found: targets.every((target) => target.found),
      targetVisible: targets.every((target) => target.targetVisible),
      targets,
      resumeHidden: !resume || resume.hidden || resume.textContent.trim() === '',
      offset,
      topbarBounds: topbar
        ? { top: topbar.getBoundingClientRect().top, bottom: topbar.getBoundingClientRect().bottom, height: topbar.getBoundingClientRect().height }
        : null,
      scrollY: window.scrollY,
      viewport: { width: window.innerWidth, height: window.innerHeight },
    };
  }, label);
  visibilityChecks.push(check);
  if (!check.found || !check.targetVisible) {
    throw new Error(`${label}: entry fields and primary action are not fully visible without scrolling`);
  }
  if (!check.resumeHidden) {
    throw new Error(`${label}: resume row must stay hidden in Phase 2`);
  }
  return check;
}

async function scrollTargetIntoView(selector) {
  return page.evaluate((sel) => {
    const target = document.querySelector(sel);
    if (!target) {
      return {
        found: false,
        targetVisible: false,
        selector: sel,
      };
    }
    const topbar = document.querySelector('.app-topbar');
    const offset = Math.ceil((topbar?.getBoundingClientRect().height || 56) + 24);
    const y = target.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo(0, Math.max(0, y));
    const targetBox = target.getBoundingClientRect();
    const topbarBottom = topbar?.getBoundingClientRect().bottom ?? 0;
    const viewportBottom = window.innerHeight;
    const targetVisible = targetBox.top >= topbarBottom - 1
      && targetBox.bottom <= viewportBottom + 1
      && targetBox.width > 0
      && targetBox.height > 0;
    return {
      found: true,
      targetVisible,
      selector: sel,
      offset,
      targetBounds: {
        top: targetBox.top,
        bottom: targetBox.bottom,
        left: targetBox.left,
        right: targetBox.right,
        width: targetBox.width,
        height: targetBox.height,
      },
      topbarBounds: topbar
        ? {
            top: topbar.getBoundingClientRect().top,
            bottom: topbar.getBoundingClientRect().bottom,
            height: topbar.getBoundingClientRect().height,
          }
        : null,
      viewport: { width: window.innerWidth, height: window.innerHeight },
    };
  }, selector);
}

function assertTargetVisible(check, label) {
  visibilityChecks.push({ label, ...check });
  if (!check.found) {
    throw new Error(`${label}: target not found (${check.selector})`);
  }
  if (!check.targetVisible) {
    throw new Error(`${label}: target not visible after scroll (${check.selector})`);
  }
}

async function shot(name, width, height, url, afterNavigate, mediaFeatures, options = {}) {
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  if (mediaFeatures && mediaFeatures.length) {
    await page.emulateMediaFeatures(mediaFeatures);
  } else {
    await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'no-preference' }]);
  }
  await page.goto(url, { waitUntil: 'networkidle0', timeout: 45000 });
  let visibility = null;
  if (afterNavigate) {
    visibility = await afterNavigate(page);
    await new Promise((resolve) => setTimeout(resolve, 350));
  }
  await page.screenshot({
    path: join(OUT, name),
    type: 'png',
    fullPage: Boolean(options.fullPage),
  });
  notes.push({
    file: name,
    width,
    height,
    url,
    fullPage: Boolean(options.fullPage),
    visibility,
  });
}

async function waitForSongFixtures() {
  await page.waitForFunction(() => typeof window.YatsnSongSearchFixtures === 'object', { timeout: 15000 });
}

async function showSong(method) {
  await waitForSongFixtures();
  await page.evaluate((fn) => window.YatsnSongSearchFixtures[fn](), method);
}

const create = `${BASE}/create`;

for (const [w, h] of [[320, 640], [390, 844], [768, 1024], [900, 900], [1440, 900]]) {
  await shot(`create-entry-${w}.png`, w, h, create, async () => {
    await page.evaluate(() => window.scrollTo(0, 0));
    if (w <= 390) {
      return assertEntryComposition(`create-entry-${w}`);
    }
    const submit = await scrollTargetIntoView('.yatsn-song-search__submit');
    assertTargetVisible(submit, `create-entry-${w} submit`);
    return submit;
  });
}

await shot('song-loading-390.png', 390, 844, create, async () => {
  await showSong('showLoading');
  const check = await scrollTargetIntoView('[data-song-result-loading]');
  assertTargetVisible(check, 'song-loading-390');
  return check;
});

await shot('song-results-390.png', 390, 844, create, async () => {
  await showSong('showResult');
  const check = await scrollTargetIntoView('[data-song-results] .yatsn-song-result');
  assertTargetVisible(check, 'song-results-390');
  return check;
});

await shot('song-results-768.png', 768, 1024, create, async () => {
  await showSong('showResult');
  const check = await scrollTargetIntoView('[data-song-results] .yatsn-song-result');
  assertTargetVisible(check, 'song-results-768');
  return check;
});

await shot('song-results-1440.png', 1440, 900, create, async () => {
  await showSong('showResult');
  const check = await scrollTargetIntoView('[data-song-results] .yatsn-song-result');
  assertTargetVisible(check, 'song-results-1440');
  return check;
});

await shot('song-no-results-390.png', 390, 844, create, async () => {
  await showSong('showNoResults');
  const check = await scrollTargetIntoView('[data-song-status].is-error, [data-song-status].yatsn-status--error');
  assertTargetVisible(check, 'song-no-results-390 status');
  return check;
});

await shot('song-error-390.png', 390, 844, create, async () => {
  await showSong('showError');
  const status = await scrollTargetIntoView('[data-song-status].is-error, [data-song-status].yatsn-status--error');
  assertTargetVisible(status, 'song-error-390 status');
  const retry = await scrollTargetIntoView('[data-song-retry]');
  assertTargetVisible(retry, 'song-error-390 retry');
  return { status, retry };
});

await shot('song-selected-390.png', 390, 844, create, async () => {
  await showSong('showSelected');
  const selected = await scrollTargetIntoView('[data-song-selected]');
  assertTargetVisible(selected, 'song-selected-390 card');
  const change = await scrollTargetIntoView('[data-song-change]');
  assertTargetVisible(change, 'song-selected-390 change');
  return { selected, change };
});

await shot('song-selected-1440.png', 1440, 900, create, async () => {
  await showSong('showSelected');
  const check = await scrollTargetIntoView('[data-song-selected]');
  assertTargetVisible(check, 'song-selected-1440');
  return check;
});

await shot('song-focus-390.png', 390, 844, create, async () => {
  await showSong('focusSubmit');
  const focused = await page.evaluate(() => {
    const el = document.activeElement;
    const box = el?.getBoundingClientRect?.();
    const topbar = document.querySelector('.app-topbar');
    const topbarBottom = topbar?.getBoundingClientRect().bottom ?? 0;
    return {
      selector: el?.matches?.('.yatsn-song-search__submit') ? '.yatsn-song-search__submit:focus' : null,
      tag: el?.tagName || null,
      className: el?.className || null,
      targetVisible: box
        ? box.top >= topbarBottom - 1 && box.bottom <= window.innerHeight + 1 && box.width > 0
        : false,
      targetBounds: box
        ? { top: box.top, bottom: box.bottom, left: box.left, right: box.right, width: box.width, height: box.height }
        : null,
      topbarBounds: topbar
        ? { bottom: topbar.getBoundingClientRect().bottom, height: topbar.getBoundingClientRect().height }
        : null,
    };
  });
  visibilityChecks.push({ label: 'song-focus-390', ...focused, found: !!focused.selector, targetVisible: focused.targetVisible });
  if (!focused.selector || !focused.targetVisible) {
    throw new Error('song-focus-390: primary submit is not focused and visible');
  }
  return focused;
});

await shot('song-focus-result-390.png', 390, 844, create, async () => {
  await showSong('focusResult');
  const check = await scrollTargetIntoView('[data-song-results] .yatsn-song-result');
  const focused = await page.evaluate(() => {
    const el = document.activeElement;
    return el?.matches?.('[data-song-results] .yatsn-song-result') === true;
  });
  visibilityChecks.push({
    label: 'song-focus-result-390',
    ...check,
    focused,
    targetVisible: check.targetVisible && focused,
  });
  if (!check.targetVisible || !focused) {
    throw new Error('song-focus-result-390: result card is not focused and visible');
  }
  return { ...check, focused };
});

await shot('song-reduced-motion-390.png', 390, 844, create, async () => {
  await showSong('showLoading');
  const check = await scrollTargetIntoView('[data-song-result-loading]');
  assertTargetVisible(check, 'song-reduced-motion-390');
  return check;
}, [{ name: 'prefers-reduced-motion', value: 'reduce' }]);

await shot('song-increased-contrast-390.png', 390, 844, create, async () => {
  await page.addStyleTag({
    content: `
      body.app { background: #000 !important; }
      .yatsn-btn--primary, .btn--primary { outline: 1px solid white; }
      .yatsn-song-result, .yatsn-status, .yatsn-song-selected__card { border-color: #fff !important; }
    `,
  });
  await showSong('showSelected');
  const check = await scrollTargetIntoView('[data-song-selected]');
  assertTargetVisible(check, 'song-increased-contrast-390');
  return check;
});

for (const spec of [
  { name: 'zoom-create-entry-320', width: 320, height: 640, target: '.yatsn-song-search__submit' },
  { name: 'zoom-song-results-390', width: 390, height: 844, song: 'showResult', target: '[data-song-results] .yatsn-song-result' },
  { name: 'zoom-song-selected-1440', width: 1440, height: 900, song: 'showSelected', target: '[data-song-selected]' },
]) {
  await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'no-preference' }]);
  await page.setViewport({ width: spec.width, height: spec.height, deviceScaleFactor: 1 });
  await page.goto(create, { waitUntil: 'networkidle0', timeout: 45000 });
  if (spec.song) {
    await showSong(spec.song);
  }
  const visibility = spec.target ? await scrollTargetIntoView(spec.target) : null;
  const client = await page.createCDPSession();
  await client.send('Emulation.setPageScaleFactor', { pageScaleFactor: 2 });
  await new Promise((resolve) => setTimeout(resolve, 250));
  const metrics = await page.evaluate(() => ({
    scrollWidth: document.documentElement.scrollWidth,
    innerWidth: window.innerWidth,
    overflow: document.documentElement.scrollWidth > window.innerWidth + 1,
  }));
  zoomResults.push({ ...spec, pageScaleFactor: 2, ...metrics, visibility });
  await page.screenshot({ path: join(OUT, `${spec.name}.png`), type: 'png' });
  await client.send('Emulation.setPageScaleFactor', { pageScaleFactor: 1 });
  await client.detach();
}

writeFileSync(join(OUT, 'review-notes.json'), JSON.stringify({
  generatedAt: new Date().toISOString(),
  fixtureSetup: {
    routeAuth: 'owner session cookie from SessionService::create',
    songSearch: 'window.YatsnSongSearchFixtures on /create when data-private-build=1',
    scroll: 'state targets scroll below sticky top bar (topbar height + 24px) with targetVisible assertion',
    resumeRow: 'hidden in Phase 2 (no alternate-draft contract)',
    noPrivateData: true,
  },
  captures: notes,
  visibilityChecks,
  zoom200: zoomResults,
}, null, 2));

await browser.close();
console.log('round-011 capture complete', notes.length, 'shots');

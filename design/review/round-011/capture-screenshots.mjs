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

async function assertHonestEntry(label, width) {
  const check = await page.evaluate((entryLabel, viewportWidth) => {
    window.scrollTo(0, 0);
    const topbar = document.querySelector('.app-topbar');
    const topbarBottom = topbar?.getBoundingClientRect().bottom ?? 0;
    const viewportBottom = window.innerHeight;
    const members = [
      { key: 'h1', selector: '.session-header__title' },
      { key: 'progress', selector: '.session-progress' },
      { key: 'h2', selector: '#song-heading' },
      { key: 'artist', selector: '[name="artist"]' },
      { key: 'title', selector: '[name="title"]' },
      { key: 'submit', selector: '.yatsn-song-search__submit' },
    ].map(({ key, selector }) => {
      const el = document.querySelector(selector);
      const box = el?.getBoundingClientRect();
      const targetVisible = !!box
        && box.top >= topbarBottom - 1
        && box.bottom <= viewportBottom + 1
        && box.width > 0
        && box.height > 0;
      const partiallyVisible = !!box && box.bottom > topbarBottom && box.top < viewportBottom;
      return {
        key,
        selector,
        found: !!el,
        targetVisible,
        partiallyVisible,
        targetBounds: box
          ? {
              top: box.top,
              bottom: box.bottom,
              left: box.left,
              right: box.right,
              width: box.width,
              height: box.height,
            }
          : null,
      };
    });
    const resume = document.querySelector('[data-song-resume]');
    const required = viewportWidth <= 320
      ? ['h1', 'progress', 'h2', 'artist']
      : ['h1', 'progress', 'h2', 'artist', 'title', 'submit'];
    const requiredVisible = required.every((key) => members.find((member) => member.key === key)?.targetVisible);
    const artistBegins = members.find((member) => member.key === 'artist')?.partiallyVisible === true;
    return {
      label: entryLabel,
      scrollY: window.scrollY,
      members,
      required,
      targetVisible: requiredVisible,
      artistBegins,
      resumeHidden: !resume || resume.hidden || resume.textContent.trim() === '',
      topbarBounds: topbar
        ? {
            top: topbar.getBoundingClientRect().top,
            bottom: topbar.getBoundingClientRect().bottom,
            height: topbar.getBoundingClientRect().height,
          }
        : null,
      viewport: { width: window.innerWidth, height: window.innerHeight },
    };
  }, label, width);

  visibilityChecks.push(check);
  if (!check.resumeHidden) {
    throw new Error(`${label}: resume row must stay hidden in Phase 2`);
  }
  if (width <= 320) {
    if (!check.artistBegins) {
      throw new Error(`${label}: artist field must visibly begin in the first viewport at 320px`);
    }
  } else if (!check.targetVisible) {
    throw new Error(`${label}: required entry elements are not visible at scrollY=0`);
  }
  return check;
}

async function positionAndAssertGroup(label, selectors) {
  const check = await page.evaluate((groupSelectors) => {
    const topbar = document.querySelector('.app-topbar');
    const topbarBottom = topbar?.getBoundingClientRect().bottom ?? 0;
    const offset = Math.ceil((topbar?.getBoundingClientRect().height || 56) + 24);
    const viewportHeight = window.innerHeight;

    const members = groupSelectors.map((selector) => {
      const el = document.querySelector(selector);
      if (!el) {
        return { selector, found: false, targetVisible: false, targetBounds: null };
      }
      const box = el.getBoundingClientRect();
      return {
        selector,
        found: true,
        absoluteTop: box.top + window.scrollY,
        absoluteBottom: box.bottom + window.scrollY,
        height: box.height,
      };
    });

    if (members.some((member) => !member.found)) {
      return {
        found: false,
        targetVisible: false,
        members,
        scrollY: window.scrollY,
      };
    }

    const groupTop = Math.min(...members.map((member) => member.absoluteTop));
    const groupBottom = Math.max(...members.map((member) => member.absoluteBottom));
    let scrollY = Math.max(0, groupTop - offset);
    if (groupBottom - scrollY > viewportHeight) {
      scrollY = Math.max(0, groupBottom - viewportHeight);
    }
    window.scrollTo(0, scrollY);

    const measured = members.map((member) => {
      const el = document.querySelector(member.selector);
      const box = el.getBoundingClientRect();
      const targetVisible = box.top >= topbarBottom - 1
        && box.bottom <= viewportHeight + 1
        && box.width > 0
        && box.height > 0;
      return {
        selector: member.selector,
        found: true,
        targetVisible,
        targetBounds: {
          top: box.top,
          bottom: box.bottom,
          left: box.left,
          right: box.right,
          width: box.width,
          height: box.height,
        },
      };
    });

    return {
      found: true,
      targetVisible: measured.every((member) => member.targetVisible),
      members: measured,
      scrollY: window.scrollY,
      offset,
      groupTop,
      groupBottom,
      topbarBounds: topbar
        ? {
            top: topbar.getBoundingClientRect().top,
            bottom: topbar.getBoundingClientRect().bottom,
            height: topbar.getBoundingClientRect().height,
          }
        : null,
      viewport: { width: window.innerWidth, height: window.innerHeight },
    };
  }, selectors);

  const result = { label, ...check };
  visibilityChecks.push(result);
  if (!result.found || !result.targetVisible) {
    throw new Error(`${label}: group members are not simultaneously visible (${selectors.join(', ')})`);
  }
  return result;
}

async function scrollFormIntoView(label) {
  return positionAndAssertGroup(label, [
    '[name="artist"]',
    '[name="title"]',
    '.yatsn-song-search__submit',
  ]);
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
  await shot(`create-entry-${w}.png`, w, h, create, async () => assertHonestEntry(`create-entry-${w}`, w));
}

for (const [w, h] of [[320, 640], [390, 844]]) {
  await shot(`create-form-${w}.png`, w, h, create, async () => scrollFormIntoView(`create-form-${w}`));
}

await shot('song-loading-390.png', 390, 844, create, async () => {
  await showSong('showLoading');
  return positionAndAssertGroup('song-loading-390', [
    '[data-song-status]',
    '[data-song-result-loading]',
  ]);
});

await shot('song-results-390.png', 390, 844, create, async () => {
  await showSong('showResult');
  return positionAndAssertGroup('song-results-390', [
    '[data-song-status]',
    '[data-song-results] .yatsn-song-result',
  ]);
});

await shot('song-results-768.png', 768, 1024, create, async () => {
  await showSong('showResult');
  return positionAndAssertGroup('song-results-768', [
    '[data-song-status]',
    '[data-song-results] .yatsn-song-result',
  ]);
});

await shot('song-results-1440.png', 1440, 900, create, async () => {
  await showSong('showResult');
  return positionAndAssertGroup('song-results-1440', [
    '[data-song-status]',
    '[data-song-results] .yatsn-song-result',
  ]);
});

await shot('song-no-results-390.png', 390, 844, create, async () => {
  await showSong('showNoResults');
  return positionAndAssertGroup('song-no-results-390', [
    '[data-song-status].yatsn-status--error',
    '[data-song-results] .yatsn-song-result--unavailable',
    '[data-song-retry]',
  ]);
});

await shot('song-error-390.png', 390, 844, create, async () => {
  await showSong('showError');
  return positionAndAssertGroup('song-error-390', [
    '[data-song-status].yatsn-status--error',
    '[data-song-retry]',
  ]);
});

await shot('song-selected-390.png', 390, 844, create, async () => {
  await showSong('showSelected');
  return positionAndAssertGroup('song-selected-390', [
    '[data-song-selected] .yatsn-song-selected__card',
    '[data-song-change]',
  ]);
});

await shot('song-selected-1440.png', 1440, 900, create, async () => {
  await showSong('showSelected');
  return positionAndAssertGroup('song-selected-1440', [
    '[data-song-selected] .yatsn-song-selected__card',
    '[data-song-change]',
  ]);
});

await shot('song-focus-390.png', 390, 844, create, async () => {
  await showSong('focusSubmit');
  const check = await page.evaluate(() => {
    window.scrollTo(0, 0);
    const el = document.activeElement;
    const box = el?.getBoundingClientRect?.();
    const topbar = document.querySelector('.app-topbar');
    const topbarBottom = topbar?.getBoundingClientRect().bottom ?? 0;
    const targetVisible = !!el?.matches?.('.yatsn-song-search__submit')
      && !!box
      && box.top >= topbarBottom - 1
      && box.bottom <= window.innerHeight + 1
      && box.width > 0;
    return {
      selector: '.yatsn-song-search__submit:focus',
      found: !!el?.matches?.('.yatsn-song-search__submit'),
      targetVisible,
      targetBounds: box
        ? {
            top: box.top,
            bottom: box.bottom,
            left: box.left,
            right: box.right,
            width: box.width,
            height: box.height,
          }
        : null,
      scrollY: window.scrollY,
      topbarBounds: topbar
        ? { bottom: topbar.getBoundingClientRect().bottom, height: topbar.getBoundingClientRect().height }
        : null,
    };
  });
  const result = { label: 'song-focus-390', ...check };
  visibilityChecks.push(result);
  if (!result.found || !result.targetVisible) {
    throw new Error('song-focus-390: primary submit is not focused and visible at scrollY=0');
  }
  return result;
});

await shot('song-focus-result-390.png', 390, 844, create, async () => {
  await showSong('focusResult');
  const group = await positionAndAssertGroup('song-focus-result-390', [
    '[data-song-results] .yatsn-song-result',
  ]);
  const focused = await page.evaluate(() => document.activeElement?.matches?.('[data-song-results] .yatsn-song-result') === true);
  const result = { ...group, focused, label: 'song-focus-result-390' };
  visibilityChecks.push(result);
  if (!focused) {
    throw new Error('song-focus-result-390: result card is not focused');
  }
  return result;
});

await shot('song-reduced-motion-390.png', 390, 844, create, async () => {
  await showSong('showLoading');
  return positionAndAssertGroup('song-reduced-motion-390', [
    '[data-song-status]',
    '[data-song-result-loading]',
  ]);
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
  return positionAndAssertGroup('song-increased-contrast-390', [
    '[data-song-selected] .yatsn-song-selected__card',
    '[data-song-change]',
  ]);
});

for (const spec of [
  { name: 'zoom-create-entry-320', width: 320, height: 640 },
  { name: 'zoom-song-results-390', width: 390, height: 844, song: 'showResult', selectors: ['[data-song-status]', '[data-song-results] .yatsn-song-result'] },
  { name: 'zoom-song-selected-1440', width: 1440, height: 900, song: 'showSelected', selectors: ['[data-song-selected] .yatsn-song-selected__card', '[data-song-change]'] },
]) {
  await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'no-preference' }]);
  await page.setViewport({ width: spec.width, height: spec.height, deviceScaleFactor: 1 });
  await page.goto(create, { waitUntil: 'networkidle0', timeout: 45000 });
  let visibility = null;
  if (spec.song) {
    await showSong(spec.song);
    visibility = await positionAndAssertGroup(spec.name, spec.selectors);
  } else {
    await page.evaluate(() => window.scrollTo(0, 0));
    visibility = { scrollY: 0 };
  }
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
    entryCapture: 'create-entry-* captured at scrollY=0 without pre-scroll',
    formCapture: 'create-form-* optional form-focused evidence with group scroll',
    groupVisibility: 'compound state screenshots require all group members visible simultaneously below the sticky top bar',
    resumeRow: 'hidden in Phase 2 (no alternate-draft contract)',
    noPrivateData: true,
  },
  captures: notes,
  visibilityChecks,
  zoom200: zoomResults,
}, null, 2));

await browser.close();
console.log('round-011 capture complete', notes.length, 'shots');

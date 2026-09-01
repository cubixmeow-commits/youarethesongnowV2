import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const OUT = __dirname;
const ROOT = join(__dirname, '../../..');
const BASE = 'http://127.0.0.1:8765';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

mkdirSync(OUT, { recursive: true });

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); if(!$u){fwrite(STDERR,"no owner\\n"); exit(1);} $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const zoomResults = [];
const notes = [];

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

async function shot(name, width, height, url, afterNavigate, mediaFeatures, options = {}) {
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  if (mediaFeatures && mediaFeatures.length) {
    await page.emulateMediaFeatures(mediaFeatures);
  } else {
    await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'no-preference' }]);
  }
  await page.goto(url, { waitUntil: 'networkidle0', timeout: 45000 });
  if (afterNavigate) {
    await afterNavigate(page);
    await new Promise((resolve) => setTimeout(resolve, 350));
  }
  await page.screenshot({
    path: join(OUT, name),
    type: 'png',
    fullPage: Boolean(options.fullPage),
  });
  notes.push({ file: name, width, height, url, fullPage: Boolean(options.fullPage) });
}

async function waitForExplore() {
  await page.waitForFunction(() => typeof window.YatsnExploreFixtures === 'object', { timeout: 15000 });
}

async function showExplore(method, scrollSelector = '[data-ai-direction-lab]') {
  await waitForExplore();
  await page.evaluate((fn, selector) => {
    const direction = document.querySelector('#the-direction');
    if (direction) direction.hidden = false;
    window.YatsnExploreFixtures[fn]();
    const target = document.querySelector(selector) || document.querySelector('[data-ai-direction-lab]');
    target?.scrollIntoView({ block: 'start' });
  }, method, scrollSelector);
}

const lab = `${BASE}/owner/component-lab`;
const create = `${BASE}/create`;

for (const [w, h] of [[320, 640], [390, 844], [768, 1024], [900, 900], [1440, 900]]) {
  await shot(`lab-${w}.png`, w, h, lab, null, null, { fullPage: true });
}

await shot('lab-390-sheet.png', 390, 844, lab, async (p) => {
  await p.click('[data-lab-open-sheet]');
  await p.waitForSelector('dialog[open], dialog[data-lab-sheet][open]', { timeout: 5000 }).catch(() => {});
});

await shot('lab-390-confirm.png', 390, 844, lab, async (p) => {
  await p.click('[data-lab-open-confirm]');
  await p.waitForFunction(() => document.querySelector('[data-lab-confirm]')?.open === true, { timeout: 5000 });
});

for (const [w, h] of [[320, 640], [390, 844], [900, 900], [1440, 900]]) {
  await shot(`create-${w}.png`, w, h, create);
}

for (const [w, h] of [[320, 640], [390, 844], [900, 900], [1440, 900]]) {
  await shot(`explore-ready-${w}.png`, w, h, create, async () => {
    await showExplore('showReady');
  });
}

await shot('explore-loading-390.png', 390, 844, create, async () => {
  await showExplore('showLoading');
});

await shot('explore-selected-390.png', 390, 844, create, async () => {
  await showExplore('showSelected', '.ai-direction-card.is-selected');
});

await shot('explore-selected-1440.png', 1440, 900, create, async () => {
  await showExplore('showSelected', '.ai-direction-card.is-selected');
});

await shot('explore-error-390.png', 390, 844, create, async () => {
  await showExplore('showError');
});

await shot('explore-manual-390.png', 390, 844, create, async () => {
  await showExplore('showManual', '[data-style-world], [data-style-grid]');
  await page.evaluate(() => {
    const grid = document.querySelector('[data-style-grid]');
    if (grid && !grid.children.length) {
      grid.innerHTML = '<button type="button" class="style-option" role="option" aria-selected="false"><strong>Cinematic Realism</strong><span class="quiet">Fixture style for review only</span></button>';
    }
    document.querySelector('[data-style-world], [data-style-grid]')?.scrollIntoView({ block: 'start' });
  });
});

await shot('explore-focus-390.png', 390, 844, create, async () => {
  await showExplore('focusFirstCard');
});

await shot('explore-reduced-motion-390.png', 390, 844, create, async () => {
  await showExplore('showLoading');
}, [{ name: 'prefers-reduced-motion', value: 'reduce' }]);

await shot('explore-increased-contrast-390.png', 390, 844, create, async () => {
  await page.addStyleTag({
    content: `
      body.app { background: #000 !important; }
      .yatsn-btn--primary, .btn--primary { outline: 1px solid white; }
      .yatsn-direction-card, .yatsn-status, .ai-direction-card { border-color: #fff !important; }
      .yatsn-direction-card.is-selected, .ai-direction-card.is-selected { outline: 2px solid #fff; }
    `,
  });
  await showExplore('showSelected');
});

await shot('lab-reduced-motion-390.png', 390, 844, lab, null, [{ name: 'prefers-reduced-motion', value: 'reduce' }], { fullPage: true });

for (const spec of [
  { name: 'zoom-create-320', url: create, width: 320, height: 640 },
  { name: 'zoom-explore-390', url: create, width: 390, height: 844, explore: 'showReady' },
  { name: 'zoom-lab-900', url: lab, width: 900, height: 900 },
  { name: 'zoom-explore-1440', url: create, width: 1440, height: 900, explore: 'showSelected' },
]) {
  await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'no-preference' }]);
  await page.setViewport({ width: spec.width, height: spec.height, deviceScaleFactor: 1 });
  await page.goto(spec.url, { waitUntil: 'networkidle0', timeout: 45000 });
  if (spec.explore) {
    await showExplore(spec.explore);
  }
  const client = await page.createCDPSession();
  await client.send('Emulation.setPageScaleFactor', { pageScaleFactor: 2 });
  await new Promise((resolve) => setTimeout(resolve, 250));
  const metrics = await page.evaluate(() => ({
    scrollWidth: document.documentElement.scrollWidth,
    innerWidth: window.innerWidth,
    overflow: document.documentElement.scrollWidth > window.innerWidth + 1,
  }));
  zoomResults.push({ ...spec, pageScaleFactor: 2, ...metrics });
  await page.screenshot({ path: join(OUT, `${spec.name}.png`), type: 'png' });
  await client.send('Emulation.setPageScaleFactor', { pageScaleFactor: 1 });
  await client.detach();
}

writeFileSync(join(OUT, 'review-notes.json'), JSON.stringify({
  generatedAt: new Date().toISOString(),
  fixtureSetup: {
    routeAuth: 'owner session cookie from SessionService::create',
    componentLab: '/owner/component-lab static fixtures',
    explore: 'window.YatsnExploreFixtures on /create; Direction stage unhidden without lookup/portraits because this slice does not change those contracts',
    noPrivateData: true,
  },
  captures: notes,
  zoom200: zoomResults,
}, null, 2));

await browser.close();
console.log('round-010 capture complete', notes.length, 'shots');

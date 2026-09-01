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

async function waitForSongFixtures() {
  await page.waitForFunction(() => typeof window.YatsnSongSearchFixtures === 'object', { timeout: 15000 });
}

async function showSong(method) {
  await waitForSongFixtures();
  await page.evaluate((fn) => window.YatsnSongSearchFixtures[fn](), method);
}

const create = `${BASE}/create`;

for (const [w, h] of [[320, 640], [390, 844], [768, 1024], [900, 900], [1440, 900]]) {
  await shot(`create-entry-${w}.png`, w, h, create);
}

await shot('song-loading-390.png', 390, 844, create, async () => {
  await showSong('showLoading');
});

await shot('song-results-390.png', 390, 844, create, async () => {
  await showSong('showResult');
});

await shot('song-results-768.png', 768, 1024, create, async () => {
  await showSong('showResult');
});

await shot('song-results-1440.png', 1440, 900, create, async () => {
  await showSong('showResult');
});

await shot('song-no-results-390.png', 390, 844, create, async () => {
  await showSong('showNoResults');
});

await shot('song-error-390.png', 390, 844, create, async () => {
  await showSong('showError');
});

await shot('song-selected-390.png', 390, 844, create, async () => {
  await showSong('showSelected');
});

await shot('song-selected-1440.png', 1440, 900, create, async () => {
  await showSong('showSelected');
});

await shot('song-focus-390.png', 390, 844, create, async () => {
  await showSong('focusSubmit');
});

await shot('song-focus-result-390.png', 390, 844, create, async () => {
  await showSong('focusResult');
});

await shot('song-reduced-motion-390.png', 390, 844, create, async () => {
  await showSong('showLoading');
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
});

for (const spec of [
  { name: 'zoom-create-entry-320', width: 320, height: 640 },
  { name: 'zoom-song-results-390', width: 390, height: 844, song: 'showResult' },
  { name: 'zoom-song-selected-1440', width: 1440, height: 900, song: 'showSelected' },
]) {
  await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'no-preference' }]);
  await page.setViewport({ width: spec.width, height: spec.height, deviceScaleFactor: 1 });
  await page.goto(create, { waitUntil: 'networkidle0', timeout: 45000 });
  if (spec.song) {
    await showSong(spec.song);
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
    songSearch: 'window.YatsnSongSearchFixtures on /create when data-private-build=1',
    noPrivateData: true,
  },
  captures: notes,
  zoom200: zoomResults,
}, null, 2));

await browser.close();
console.log('round-011 capture complete', notes.length, 'shots');

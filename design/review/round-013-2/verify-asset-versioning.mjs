import { execSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8767';
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

const notes = {
  version: 'round-013.2',
  strategy: 'path-fingerprinted release bundle (/assets/r/{releaseId}/...)',
  oldUrlExample: '/assets/css/app.css?v=1735689600',
  newUrlExample: null,
  releaseId: null,
  assetUrls: {},
  headers: {},
  reloadConfirmed: false,
  round0131Preserved: {},
};

async function fetchHeaders(url) {
  const response = await fetch(url, {
    headers: { Cookie: `yatsn_session=${token}` },
    redirect: 'follow',
  });
  const cacheControl = response.headers.get('cache-control') || '';
  return { status: response.status, cacheControl };
}

await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 2 });
const firstLoad = await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0', timeout: 45000 });
await page.waitForFunction(() => typeof window.YatsnCreateFixtures === 'object', { timeout: 15000 });
await page.waitForFunction(() => typeof window.YatsnExploreFixtures === 'object', { timeout: 15000 });

const html = await page.content();
const htmlHeaders = await fetchHeaders(`${BASE}/create`);
notes.headers.createHtml = htmlHeaders;
assert(htmlHeaders.cacheControl.includes('no-store'), 'Create HTML is not cached as immutable');

const assetMatches = [...html.matchAll(/\/assets\/r\/([a-f0-9]{12})\/(css\/app\.css|js\/(?:app|explore|song-search)\.js)/g)];
assert(assetMatches.length === 4, `expected four versioned Create assets, found ${assetMatches.length}`);

const releaseIds = new Set(assetMatches.map((match) => match[1]));
assert(releaseIds.size === 1, `coupled Create assets must share one release id, found ${[...releaseIds].join(', ')}`);
const releaseId = [...releaseIds][0];
notes.releaseId = releaseId;

for (const match of assetMatches) {
  const assetPath = `/assets/r/${match[1]}/${match[2]}`;
  notes.assetUrls[match[2]] = assetPath;
  const assetHeaders = await fetchHeaders(`${BASE}${assetPath}`);
  notes.headers[match[2]] = assetHeaders;
  assert(assetHeaders.status === 200, `${assetPath} resolves with HTTP 200`);
  assert(
    assetHeaders.cacheControl.includes('immutable') && assetHeaders.cacheControl.includes('max-age'),
    `${assetPath} uses long-lived immutable caching`,
  );
}

notes.newUrlExample = notes.assetUrls['css/app.css'];
assert(!html.includes('/assets/css/app.css?v='), 'Create HTML no longer emits query-string cache busting');
assert(!html.includes('/assets/js/app.js?v='), 'Create HTML no longer emits query-string app.js');
assert((html.match(/\/assets\/js\/app\.js/g) || []).length === 0, 'Create HTML has no unversioned app.js reference');

const songSearchPos = html.indexOf(notes.assetUrls['js/song-search.js']);
const explorePos = html.indexOf(notes.assetUrls['js/explore.js']);
const appPos = html.indexOf(notes.assetUrls['js/app.js']);
assert(songSearchPos < explorePos && explorePos < appPos, 'script order remains song-search, explore, app');

await page.evaluate(() => window.YatsnCreateFixtures.showDirectionChoice());
const ui = await page.evaluate(() => {
  const bar = document.querySelector('[data-generate-bar]');
  const quick = document.querySelector('[data-ai-quick]');
  const explore = document.querySelector('[data-ai-explore]');
  return {
    barHidden: bar?.hidden ?? true,
    barDisplay: bar ? getComputedStyle(bar).display : 'none',
    quickPresent: !!quick,
    explorePresent: !!explore,
  };
});
notes.round0131Preserved = ui;
assert(ui.barHidden === true && ui.barDisplay === 'none', 'hidden generate bar behavior still passes');
assert(ui.quickPresent && ui.explorePresent, 'Generate for me and Explore options remain present');

await page.screenshot({ path: join(__dirname, 'mobile-390-fresh-load.png'), fullPage: false });

const reload = await page.reload({ waitUntil: 'networkidle0', timeout: 45000 });
const reloadedHtml = await page.content();
const reloadedMatches = [...reloadedHtml.matchAll(/\/assets\/r\/([a-f0-9]{12})\//g)].map((match) => match[1]);
assert(reloadedMatches.length >= 4, 'reload still emits versioned Create assets');
assert(new Set(reloadedMatches).size === 1, 'reload keeps one coherent release id');
assert(reloadedMatches[0] === releaseId, 'normal reload receives the current release without clearing site data');
notes.reloadConfirmed = true;

writeFileSync(join(__dirname, 'review-notes.json'), `${JSON.stringify(notes, null, 2)}\n`);
await browser.close();
console.log('Round 013.2 asset cache verification passed');

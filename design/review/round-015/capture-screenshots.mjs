import { execSync } from 'node:child_process';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8769';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

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
await context.setCookie({ name: 'yatsn_session', value: token, domain: new URL(BASE).hostname, path: '/' });
const page = await context.newPage();

async function shot(name, width, height, fn) {
  await page.setViewport({ width, height, deviceScaleFactor: 2 });
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0', timeout: 45000 });
  await page.waitForFunction(() => typeof window.YatsnCreateFixtures === 'object', { timeout: 15000 });
  await page.evaluate(fn);
  await page.screenshot({ path: join(__dirname, name) });
}

await shot('mobile-390-song.png', 390, 844, () => window.YatsnCreateFixtures.showSongStage());
await shot('mobile-390-people.png', 390, 844, () => window.YatsnCreateFixtures.showPeopleStage());
await shot('mobile-390-direction-initial.png', 390, 844, () => {
  window.YatsnCreateFixtures.showDirectionChoice();
  window.YatsnExploreFixtures?.showInitialChoice?.();
});
await shot('mobile-390-explore-choices.png', 390, 844, () => window.YatsnExploreFixtures.showReady());
await shot('mobile-390-review-generate.png', 390, 844, () => window.YatsnCreateFixtures.showPreparedReady());
await shot('mobile-390-fine-tune.png', 390, 844, () => {
  window.YatsnCreateFixtures.showPreparedReady();
  document.querySelector('[data-fine-tune]').open = true;
});
await shot('mobile-320-review-action.png', 320, 640, () => window.YatsnCreateFixtures.showPreparedReady());
await shot('mobile-430-review.png', 430, 844, () => window.YatsnCreateFixtures.showPreparedReady());
await shot('desktop-900-review.png', 900, 900, () => window.YatsnCreateFixtures.showPreparedReady());
await shot('desktop-1440-review.png', 1440, 900, () => window.YatsnCreateFixtures.showPreparedReady());
await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 2 });
await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
await page.evaluate(() => {
  window.YatsnCreateFixtures.showPreparedReady();
  window.YatsnCreate.setFlowStep('generating', { focus: false });
});
await page.screenshot({ path: join(__dirname, 'mobile-390-generating.png') });

await browser.close();
console.log('Round 015 screenshots captured.');

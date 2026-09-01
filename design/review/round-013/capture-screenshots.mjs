import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const OUT = __dirname;
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8765';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

mkdirSync(OUT, { recursive: true });

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); if(!$u){fwrite(STDERR,"no owner\\n"); exit(1);} $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const notes = [];
const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: true,
  args: ['--no-sandbox', '--disable-dev-shm-usage', '--hide-scrollbars'],
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

async function shot(name, width, height, fixture, mediaFeatures = []) {
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  if (mediaFeatures.length) {
    await page.emulateMediaFeatures(mediaFeatures);
  } else {
    await page.emulateMediaFeatures([{ name: 'prefers-reduced-motion', value: 'no-preference' }]);
  }
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0', timeout: 45000 });
  await page.waitForFunction(() => typeof window.YatsnCreateFixtures === 'object', { timeout: 15000 });
  await page.evaluate((fn) => window.YatsnCreateFixtures[fn](), fixture);
  await page.evaluate(() => {
    const direction = document.querySelector('#the-direction');
    if (direction) direction.scrollIntoView({ block: 'start' });
    const bar = document.querySelector('[data-generate-bar]');
    if (bar) bar.scrollIntoView({ block: 'nearest' });
  });
  await new Promise((resolve) => setTimeout(resolve, 350));
  await page.screenshot({ path: join(OUT, name), type: 'png', fullPage: false });
  notes.push({ file: name, width, height, fixture });
}

await shot('mobile-320-ready.png', 320, 640, 'showReady');
await shot('mobile-390-ready.png', 390, 844, 'showReady', [{ name: 'prefers-reduced-motion', value: 'reduce' }]);
await shot('mobile-390-missing-style.png', 390, 844, 'showMissingStyle');
await shot('mobile-390-pending.png', 390, 844, 'showPending');
await shot('mobile-390-recoverable-error.png', 390, 844, 'showRecoverableError');

writeFileSync(join(OUT, 'review-notes.json'), `${JSON.stringify({ capturedAt: new Date().toISOString(), shots: notes }, null, 2)}\n`);
await browser.close();

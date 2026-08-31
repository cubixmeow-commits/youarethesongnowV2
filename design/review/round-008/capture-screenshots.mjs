import { execSync } from 'node:child_process';
import { mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const OUT = __dirname;
const ROOT = join(__dirname, '../../..');
const BASE = 'http://127.0.0.1:8765';
const CHROME = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

mkdirSync(OUT, { recursive: true });

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE deleted_at IS NULL ORDER BY id LIMIT 1"); $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

const imageId = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $i=\\Yatsn\\Support\\Database::one("SELECT public_id FROM generated_images ORDER BY id DESC LIMIT 1"); echo $i ? $i["public_id"] : "";'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

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

async function captureAuthed(name, url, width, height, evaluate) {
  await authedPage.setViewport({ width, height, deviceScaleFactor: 1 });
  await authedPage.goto(url, { waitUntil: 'networkidle0', timeout: 30000 });
  if (evaluate) {
    await authedPage.evaluate(evaluate);
    await new Promise((resolve) => setTimeout(resolve, 350));
  }
  await authedPage.screenshot({
    path: join(OUT, name),
    type: 'png',
  });
}

await captureGuest('home-mobile-390.png', `${BASE}/`, 390, 844);
await captureGuest('home-desktop-1440.png', `${BASE}/`, 1440, 900);
await captureAuthed('create-mobile-390-top.png', `${BASE}/create`, 390, 844);
await captureAuthed('create-mobile-390-people.png', `${BASE}/create`, 390, 844, () => {
  const people = document.querySelector('#the-people');
  if (people) people.hidden = false;
});
await captureAuthed('create-mobile-390-direction.png', `${BASE}/create`, 390, 844, () => {
  const people = document.querySelector('#the-people');
  const direction = document.querySelector('#the-direction');
  if (people) people.hidden = false;
  if (direction) direction.hidden = false;
});
await captureAuthed('create-desktop-1440.png', `${BASE}/create`, 1440, 900);
await captureAuthed('gallery-mobile-390.png', `${BASE}/gallery`, 390, 844, () => {
  const grid = document.querySelector('[data-gallery-grid]');
  if (grid) grid.innerHTML = '';
});
await captureAuthed('gallery-desktop-1440.png', `${BASE}/gallery`, 1440, 900, () => {
  const grid = document.querySelector('[data-gallery-grid]');
  if (grid) grid.innerHTML = '';
});
await captureAuthed('paywall-mobile-390.png', `${BASE}/create`, 390, 844, () => {
  const panel = document.querySelector('[data-paywall]');
  if (panel) panel.hidden = false;
  panel?.scrollIntoView({ block: 'start' });
});
await captureAuthed('paywall-desktop-1440.png', `${BASE}/create`, 1440, 900, () => {
  const panel = document.querySelector('[data-paywall]');
  if (panel) panel.hidden = false;
  panel?.scrollIntoView({ block: 'start' });
});

if (imageId) {
  await captureAuthed('reveal-mobile-390.png', `${BASE}/images/${imageId}`, 390, 844);
  await captureAuthed('reveal-desktop-1440.png', `${BASE}/images/${imageId}`, 1440, 900);
}

await browser.close();
console.log('Round 008 screenshots captured in', OUT);

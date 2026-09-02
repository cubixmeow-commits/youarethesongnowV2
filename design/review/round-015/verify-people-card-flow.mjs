import { execSync } from 'node:child_process';
import { writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8773';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); if(!$u){fwrite(STDERR,"no owner\\n"); exit(1);} $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT id FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); $count=\\Yatsn\\Support\\Database::one("SELECT COUNT(*) AS c FROM portraits WHERE user_id = :u AND deleted_at IS NULL", ["u"=>(int)$u["id"]]); if((int)$count["c"]>0) exit(0); $img=imagecreatetruecolor(80,80); $bg=imagecolorallocate($img,120,80,200); imagefill($img,0,0,$bg); $tmp=sys_get_temp_dir()."/people-card-fixture.jpg"; imagejpeg($img,$tmp,90); \\Yatsn\\Portraits\\PortraitService::upload((int)$u["id"], ["tmp_name"=>$tmp,"error"=>0,"size"=>filesize($tmp),"name"=>"fixture.jpg"]);'`,
  { cwd: ROOT, encoding: 'utf8' },
);

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
const pageErrors = [];
page.on('pageerror', (err) => pageErrors.push(err.message));

let portraitListCalls = 0;
let interceptionEnabled = false;
let portraitListFailuresRemaining = 0;

async function enableInterception() {
  if (interceptionEnabled) return;
  page.on('request', async (request) => {
    const url = request.url();
    const method = request.method();
    if (method === 'GET' && url.includes('/api/v1/portraits') && !url.includes('/content')) {
      portraitListCalls += 1;
      if (portraitListFailuresRemaining > 0) {
        portraitListFailuresRemaining -= 1;
        await request.respond({
          status: 500,
          contentType: 'application/json',
          body: '{"error":{"message":"Portrait library unavailable"}}',
        });
        return;
      }
    }
    if (method === 'POST' && url.includes('/api/v1/creation-drafts') && !url.includes('/summary')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { id: '00000000-0000-4000-8000-0000000015' } }),
      });
      return;
    }
    if (method === 'PATCH' && url.includes('/api/v1/creation-drafts/')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { id: 'draft-fixture' } }),
      });
      return;
    }
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
    try {
      await request.continue();
    } catch {
      // Navigation can abort in-flight requests.
    }
  });
  await page.setRequestInterception(true);
  interceptionEnabled = true;
}

async function gotoCreate() {
  portraitListCalls = 0;
  await enableInterception();
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
  await page.waitForFunction(() => typeof window.YatsnCreate?.getFlowStep === 'function', { timeout: 15000 });
}

async function confirmSongToPeople() {
  await page.type('#song-form [name=artist]', 'Sabaton');
  await page.type('#song-form [name=title]', 'Seven Pillars of Wisdom');
  await page.click('[data-create-sticky-primary]');
  await page.waitForSelector('[data-song-results] [data-song-result].is-selected', { timeout: 15000 });
  await page.click('[data-create-sticky-primary]');
  await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 15000 });
}

async function waitForPortraitTrayReady() {
  await page.waitForFunction(() => {
    const loading = document.querySelector('[data-portrait-loading]');
    const chips = document.querySelectorAll('[data-portrait-grid] .portrait-chip').length;
    const empty = document.querySelector('[data-portrait-empty]');
    const error = document.querySelector('[data-portrait-load-error]');
    return chips > 0 || (loading?.hidden && (empty && !empty.hidden || error && !error.hidden));
  }, { timeout: 15000 });
}

async function readPeopleUi() {
  return page.evaluate(() => ({
    flowStep: window.YatsnCreate?.getFlowStep?.() || '',
    chipCount: document.querySelectorAll('[data-portrait-grid] .portrait-chip').length,
    selectedCount: document.querySelectorAll('[data-portrait-grid] .portrait-chip.is-selected').length,
    continueDisabled: document.querySelector('[data-people-continue]')?.disabled ?? true,
    continueLabel: document.querySelector('[data-people-continue]')?.textContent || '',
    withoutDisabled: document.querySelector('[data-people-continue-without]')?.disabled ?? true,
    hint: document.querySelector('[data-people-hint]')?.textContent || '',
    hintHidden: document.querySelector('[data-people-hint]')?.hidden ?? true,
    loadingVisible: document.querySelector('[data-portrait-loading]') ? !document.querySelector('[data-portrait-loading]').hidden : false,
    emptyVisible: document.querySelector('[data-portrait-empty]') ? !document.querySelector('[data-portrait-empty]').hidden : false,
    errorVisible: document.querySelector('[data-portrait-load-error]') ? !document.querySelector('[data-portrait-load-error]').hidden : false,
    uploadHidden: document.querySelector('[data-portrait-upload-panel]')?.hidden ?? true,
  }));
}

// Saved portraits load when People opens
portraitListFailuresRemaining = 0;
await gotoCreate();
await confirmSongToPeople();
await waitForPortraitTrayReady();
let ui = await readPeopleUi();
assert(ui.flowStep === 'people', 'People card is active');
assert(ui.chipCount >= 1, 'saved portraits render in tray');
assert(pageErrors.length === 0, `no page errors: ${pageErrors.join('; ')}`);

// Continue without people path
ui = await readPeopleUi();
assert(ui.continueDisabled, 'primary continue disabled with no selection');
assert(!ui.withoutDisabled, 'continue without people stays enabled');
assert(!ui.hintHidden && ui.hint.length > 0, 'hint explains optional people path');
await page.click('[data-people-continue-without]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });

// Back preserves portrait tray state
await page.click('[data-create-back]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 10000 });
ui = await readPeopleUi();
assert(ui.chipCount >= 1, 'portraits still visible after Back from Direction');

// Select portrait and continue
await page.click('[data-portrait-grid] .portrait-chip__select');
await sleep(400);
ui = await readPeopleUi();
assert(ui.selectedCount === 1, 'portrait selection works');
assert(!ui.continueDisabled, 'continue enabled after selection');
assert(ui.continueLabel.includes('1 person'), 'continue label reflects selection');
await page.click('[data-people-continue]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });

// Deselect on return
await page.click('[data-create-back]');
await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 10000 });
await page.click('[data-portrait-grid] .portrait-chip.is-selected .portrait-chip__select');
await sleep(400);
ui = await readPeopleUi();
assert(ui.selectedCount === 0, 'portrait deselect works');
assert(ui.continueDisabled, 'continue disabled after deselect');

// Portrait list failure then retry on People card
portraitListFailuresRemaining = 2;
portraitListCalls = 0;
await gotoCreate();
await confirmSongToPeople();
await page.waitForFunction(() => {
  const error = document.querySelector('[data-portrait-load-error]');
  return error && !error.hidden;
}, { timeout: 15000 });
ui = await readPeopleUi();
assert(ui.errorVisible, 'portrait load error is visible');
assert(ui.continueDisabled, 'continue disabled when portraits failed to load');
assert(!ui.withoutDisabled, 'continue without people available when load fails');
await page.click('[data-portrait-retry]');
await waitForPortraitTrayReady();
ui = await readPeopleUi();
assert(ui.chipCount >= 1, 'retry loads saved portraits');
assert(portraitListCalls >= 3, 'portrait list retried after failure');

// Upload flow opens and shows portrait immediately
await page.click('[data-portrait-upload-toggle]');
ui = await readPeopleUi();
assert(!ui.uploadHidden, 'add portrait opens upload panel');
const portraitIdBefore = await page.evaluate(() => document.querySelectorAll('[data-portrait-grid] .portrait-chip').length);
await page.evaluate(() => {
  const input = document.querySelector('#portrait-form input[type=file]');
  const dt = new DataTransfer();
  const canvas = document.createElement('canvas');
  canvas.width = 64;
  canvas.height = 64;
  const ctx = canvas.getContext('2d');
  ctx.fillStyle = '#336699';
  ctx.fillRect(0, 0, 64, 64);
  return new Promise((resolve) => {
    canvas.toBlob((blob) => {
      const file = new File([blob], 'portrait-upload-test.jpg', { type: 'image/jpeg' });
      dt.items.add(file);
      input.files = dt.files;
      resolve();
    }, 'image/jpeg', 0.9);
  });
});
await page.click('#portrait-form button[type=submit]');
await page.waitForFunction((before) => {
  return document.querySelectorAll('[data-portrait-grid] .portrait-chip').length > before;
}, { timeout: 15000 }, portraitIdBefore);
ui = await readPeopleUi();
assert(ui.chipCount > portraitIdBefore, 'uploaded portrait appears immediately');
assert(ui.selectedCount >= 1, 'uploaded portrait is auto-selected');

writeFileSync(join(__dirname, 'verify-people-card-results.json'), JSON.stringify({ passed: true, pageErrors }, null, 2));
await browser.close();
console.log('People card flow verification passed.');

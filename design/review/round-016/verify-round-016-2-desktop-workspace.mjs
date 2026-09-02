import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8781';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';
const OUT = __dirname;

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT id FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); $count=\\Yatsn\\Support\\Database::one("SELECT COUNT(*) AS c FROM portraits WHERE user_id = :u AND deleted_at IS NULL", ["u"=>(int)$u["id"]]); if((int)$count["c"]>0) exit(0); $img=imagecreatetruecolor(80,80); $bg=imagecolorallocate($img,120,80,200); imagefill($img,0,0,$bg); $tmp=sys_get_temp_dir()."/desktop-wizard-fixture.jpg"; imagejpeg($img,$tmp,90); \\Yatsn\\Portraits\\PortraitService::upload((int)$u["id"], ["tmp_name"=>$tmp,"error"=>0,"size"=>filesize($tmp),"name"=>"fixture.jpg"]);'`,
  { cwd: ROOT, stdio: 'pipe' },
);

const token = execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT * FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); $s=\\Yatsn\\Auth\\SessionService::create((int)$u["id"], (int)$u["security_version"]); echo $s["token"];'`,
  { cwd: ROOT, encoding: 'utf8' },
).trim();

mkdirSync(OUT, { recursive: true });

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
            developmentAnalysis: { analyzed: true, analysis: { essence: 'fixture' }, analysisBasis: 'fixture' },
          },
        }),
      });
      return;
    }
    if (method === 'POST' && url.includes('/api/v1/explore-directions')) {
      const styleId = await page.evaluate(() => document.querySelector('.style-option')?.dataset?.styleId || 'style-fixture');
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({
          data: {
            directions: [{
              name: 'Static Revolt',
              description: 'Rain-slick overpass.',
              styleName: 'Gothic Romance',
              styleId,
            }],
          },
        }),
      });
      return;
    }
    if (method === 'POST' && url.includes('/summary')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { ready: true, requiresMembership: false } }),
      });
      return;
    }
    if (url.includes('/api/v1/creation-drafts') || url.includes('/api/v1/portraits')) {
      if (method === 'GET' && url.includes('/api/v1/portraits') && !url.includes('/content')) {
        await request.continue();
        return;
      }
      if (method !== 'GET') {
        await request.respond({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({ data: { id: 'draft-fixture' } }),
        });
        return;
      }
    }
    try {
      await request.continue();
    } catch {
      // navigation abort
    }
  });
}

async function assertDesktopLayout(page, label) {
  const layout = await page.evaluate(() => {
    const wizard = document.querySelector('[data-create-wizard]');
    const intro = document.querySelector('.create-wizard__intro');
    const task = document.querySelector('.create-wizard__task');
    const topbar = document.querySelector('.create-wizard__topbar');
    const brand = document.querySelector('.create-wizard__brand');
    const exit = document.querySelector('.create-wizard__exit');
    const progress = document.querySelector('.create-wizard__progress');
    const panel = document.querySelector('.create-wizard__panel');
    const primary = document.querySelector('[data-create-sticky-primary]');
    const inViewport = (el) => {
      if (!el || el.hidden) return true;
      const r = el.getBoundingClientRect();
      return r.top >= -1 && r.bottom <= window.innerHeight + 1 && r.left >= -1 && r.right <= window.innerWidth + 1;
    };
    const introRect = intro?.getBoundingClientRect();
    const taskRect = task?.getBoundingClientRect();
    return {
      wizardHeaders: document.querySelectorAll('.create-wizard__topbar').length,
      appTopbar: getComputedStyle(document.querySelector('.app-topbar')).display,
      appNav: getComputedStyle(document.querySelector('.app-nav')).display,
      wizardWidth: wizard?.getBoundingClientRect().width || 0,
      twoColumn: !!(introRect && taskRect && introRect.right <= taskRect.left + 4),
      horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth + 1,
      scrollY: window.scrollY,
      wizardOverflow: wizard && wizard.offsetHeight > window.innerHeight + 2,
      chromeInView: [topbar, brand, exit, progress, panel, primary].every(inViewport),
      primaryVisible: primary && !primary.disabled && getComputedStyle(primary).display !== 'none',
      primaryText: primary?.textContent || '',
    };
  });
  assert(layout.wizardHeaders === 1, `${label}: exactly one wizard header`);
  assert(layout.appTopbar === 'none', `${label}: site topbar hidden`);
  assert(layout.appNav === 'none', `${label}: bottom nav hidden`);
  assert(layout.wizardWidth > 480, `${label}: workspace wider than 28rem (${Math.round(layout.wizardWidth)}px)`);
  assert(layout.twoColumn, `${label}: two-column composition`);
  assert(!layout.horizontalOverflow, `${label}: no horizontal overflow`);
  assert(layout.scrollY === 0, `${label}: window.scrollY is 0`);
  assert(!layout.wizardOverflow, `${label}: no normal-state vertical overflow`);
  assert(layout.chromeInView, `${label}: chrome inside viewport`);
  return layout;
}

async function gotoCreate(page, width, height) {
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  page.setDefaultTimeout(30000);
  await enableMocks(page);
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
  await page.waitForFunction(() => typeof window.YatsnCreate === 'object');
}

async function searchSong(page) {
  await page.type('#song-form [name=artist]', 'Sabaton');
  await page.type('#song-form [name=title]', 'Seven Pillars of Wisdom');
  await page.click('[data-create-sticky-primary]');
  await page.waitForSelector('[data-song-results] [data-song-result].is-selected', { timeout: 15000 });
  await page.waitForFunction(
    () => document.querySelector('[data-create-sticky-primary]')?.textContent === 'Use this song',
    { timeout: 10000 },
  );
}

async function confirmSong(page) {
  await page.click('[data-create-sticky-primary]');
  await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 15000 });
}

async function captureFlow(page, prefix, width, height, includeIdle = false) {
  await gotoCreate(page, width, height);
  if (includeIdle) {
    await assertDesktopLayout(page, `${prefix} song idle`);
    await page.screenshot({ path: join(OUT, `${prefix}-song-idle.png`) });
  }

  await searchSong(page);
  await assertDesktopLayout(page, `${prefix} song found`);
  await page.screenshot({ path: join(OUT, `${prefix}-song-found.png`) });

  await confirmSong(page);
  await page.waitForFunction(
    () => document.querySelectorAll('[data-portrait-grid] .portrait-chip').length >= 1,
    { timeout: 15000 },
  );
  const chips = await page.$$eval('[data-portrait-grid] .portrait-chip', (els) => els.length);
  assert(chips >= 1, `${prefix}: saved portraits render`);
  await assertDesktopLayout(page, `${prefix} people`);
  await page.screenshot({ path: join(OUT, `${prefix}-people.png`) });

  await page.click('[data-people-continue-without]');
  await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });
  await page.waitForFunction(() => {
    const quick = document.querySelector('[data-ai-quick]');
    return quick && !quick.disabled;
  }, { timeout: 15000 });
  const directionUi = await page.evaluate(() => ({
    quickPresent: !!document.querySelector('[data-ai-quick]'),
    quickOrder: getComputedStyle(document.querySelector('[data-ai-quick]')?.parentElement)?.display,
    quickText: document.querySelector('[data-ai-quick]')?.textContent || '',
  }));
  assert(directionUi.quickPresent && directionUi.quickText.includes('Generate for me'), `${prefix}: Quick Generate is primary`);
  await assertDesktopLayout(page, `${prefix} direction`);
  await page.screenshot({ path: join(OUT, `${prefix}-direction.png`) });

  await page.click('[data-ai-quick]');
  await page.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'review', { timeout: 30000 });
  const review = await assertDesktopLayout(page, `${prefix} review`);
  assert(review.primaryText === 'Generate image', `${prefix}: Generate image visible on review`);
  assert(review.primaryVisible, `${prefix}: Generate image enabled on review`);
  const fineTuneOpen = await page.$eval('[data-fine-tune]', (el) => el.open);
  assert(!fineTuneOpen, `${prefix}: Fine Tune collapsed by default`);
  await page.screenshot({ path: join(OUT, `${prefix}-review.png`) });
}

const page1024 = await context.newPage();
await captureFlow(page1024, 'desktop-1024x768', 1024, 768, false);
await page1024.close();

const page1280 = await context.newPage();
await captureFlow(page1280, 'desktop-1280x800', 1280, 800, false);
await page1280.close();

const page1440 = await context.newPage();
await captureFlow(page1440, 'desktop-1440x900', 1440, 900, true);
await page1440.close();

// Mobile regression spot-check at 390×844
const mobile = await context.newPage();
await mobile.setViewport({ width: 390, height: 844, deviceScaleFactor: 1, hasTouch: true });
await enableMocks(mobile);
await mobile.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
const mobileLayout = await mobile.evaluate(() => ({
  wizardOverflow: document.querySelector('[data-create-wizard]')?.offsetHeight <= window.innerHeight + 2,
  appNavHidden: getComputedStyle(document.querySelector('.app-nav')).display === 'none',
  stacked: (() => {
    const intro = document.querySelector('.create-wizard__intro');
    const task = document.querySelector('.create-wizard__task');
    if (!intro || !task) return false;
    return intro.getBoundingClientRect().top <= task.getBoundingClientRect().top;
  })(),
}));
assert(mobileLayout.wizardOverflow, 'mobile regression: wizard fits viewport');
assert(mobileLayout.appNavHidden, 'mobile regression: nav hidden');
assert(mobileLayout.stacked, 'mobile regression: intro stacks above task');
await mobile.close();

writeFileSync(join(OUT, 'verify-results.json'), JSON.stringify({ passed: true }, null, 2));
await browser.close();
console.log('Round 016.2 desktop workspace verification passed.');

import { execSync } from 'node:child_process';
import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8782';
const CHROME = process.env.CHROME || '/usr/bin/google-chrome-stable';
const OUT = __dirname;

function assert(condition, message) {
  if (!condition) throw new Error(message);
}

function rectsOverlap(a, b, tolerance = 0) {
  return !(
    a.right <= b.left + tolerance
    || a.left >= b.right - tolerance
    || a.bottom <= b.top + tolerance
    || a.top >= b.bottom - tolerance
  );
}

execSync(
  `php -r 'require ${JSON.stringify(join(ROOT, 'app/bootstrap.php'))}; $u=\\Yatsn\\Support\\Database::one("SELECT id FROM users WHERE role = \\"owner\\" AND deleted_at IS NULL ORDER BY id LIMIT 1"); $count=\\Yatsn\\Support\\Database::one("SELECT COUNT(*) AS c FROM portraits WHERE user_id = :u AND deleted_at IS NULL", ["u"=>(int)$u["id"]]); if((int)$count["c"]>0) exit(0); $img=imagecreatetruecolor(80,80); $bg=imagecolorallocate($img,120,80,200); imagefill($img,0,0,$bg); $tmp=sys_get_temp_dir()."/mobile-shell-fixture.jpg"; imagejpeg($img,$tmp,90); \\Yatsn\\Portraits\\PortraitService::upload((int)$u["id"], ["tmp_name"=>$tmp,"error"=>0,"size"=>filesize($tmp),"name"=>"fixture.jpg"]);'`,
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
    if (method === 'PATCH' && url.includes('/api/v1/creation-drafts/')) {
      await request.respond({
        status: 200,
        contentType: 'application/json',
        body: JSON.stringify({ data: { id: 'draft-fixture' } }),
      });
      return;
    }
    try {
      await request.continue();
    } catch {
      // navigation abort
    }
  });
}

async function newPage(width, height) {
  const page = await context.newPage();
  await page.setViewport({ width, height, deviceScaleFactor: 1, hasTouch: true });
  page.setDefaultTimeout(30000);
  return page;
}

async function gotoCreate(page) {
  await enableMocks(page);
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0' });
  await page.waitForFunction(() => typeof window.YatsnCreate === 'object');
}

async function assertMobileShell(page, label) {
  const layout = await page.evaluate(() => {
    const nav = document.querySelector('.app-nav');
    const createNav = nav?.querySelector('.app-nav__item[href="/create"]');
    const topbar = document.querySelector('.app-topbar');
    const headers = document.querySelectorAll('.create-wizard__topbar');
    const back = document.querySelector('[data-create-back]');
    const brand = document.querySelector('.create-wizard__brand');
    const exit = document.querySelector('.create-wizard__exit');
    const sticky = document.querySelector('[data-create-sticky-actions]');
    const primary = document.querySelector('[data-create-sticky-primary]');
    const scroll = document.querySelector('[data-create-scroll]');
    const inViewport = (el) => {
      if (!el || el.hidden) return true;
      const r = el.getBoundingClientRect();
      return r.top >= -1 && r.bottom <= window.innerHeight + 1 && r.left >= -1 && r.right <= window.innerWidth + 1;
    };
    const rect = (el) => (el ? el.getBoundingClientRect() : null);
    const backRect = rect(back);
    const brandRect = rect(brand);
    const exitRect = rect(exit);
    const navRect = rect(nav);
    const stickyRect = rect(sticky);
    const headerChildrenOverlap = [backRect, brandRect, exitRect].filter(Boolean).some((a, i, arr) => {
      for (let j = i + 1; j < arr.length; j += 1) {
        const b = arr[j];
        if (a.right <= b.left || a.left >= b.right || a.bottom <= b.top || a.top >= b.bottom) continue;
        return true;
      }
      return false;
    });
    const stickyNavGap = stickyRect && navRect ? stickyRect.bottom <= navRect.top + 1 : true;
    return {
      scrollY: window.scrollY,
      horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth + 1,
      wizardHeaders: headers.length,
      navDisplay: nav ? getComputedStyle(nav).display : '',
      createActive: createNav?.classList.contains('is-active') ?? false,
      topbarDisplay: topbar ? getComputedStyle(topbar).display : '',
      backInvisible: back?.classList.contains('is-invisible') ?? false,
      backDisplay: back ? getComputedStyle(back).display : '',
      headerInView: [back, brand, exit].every(inViewport),
      headerChildrenOverlap,
      stickyNavGap,
      stickyVisible: sticky && getComputedStyle(sticky).display !== 'none',
      primaryText: primary?.textContent || '',
      scrollOverflow: scroll && scroll.scrollHeight > scroll.clientHeight + 2 && !scroll.classList.contains('is-scrollable'),
      wizardHeight: document.querySelector('[data-create-wizard]')?.offsetHeight || 0,
      innerHeight: window.innerHeight,
      footnoteHidden: document.querySelector('[data-create-footnote]') ? getComputedStyle(document.querySelector('[data-create-footnote]')).display === 'none' : true,
    };
  });

  assert(layout.scrollY === 0, `${label}: window.scrollY is 0`);
  assert(!layout.horizontalOverflow, `${label}: no horizontal overflow`);
  assert(layout.wizardHeaders === 1, `${label}: exactly one mobile header`);
  assert(layout.navDisplay !== 'none', `${label}: bottom navigation visible`);
  assert(layout.createActive, `${label}: Create tab active`);
  assert(layout.topbarDisplay === 'none', `${label}: site topbar hidden`);
  assert(!layout.headerChildrenOverlap, `${label}: header children do not overlap`);
  assert(layout.headerInView, `${label}: header elements inside viewport`);
  assert(layout.stickyNavGap, `${label}: sticky actions sit above bottom navigation`);
  assert(layout.stickyVisible, `${label}: sticky action region visible`);
  assert(layout.footnoteHidden, `${label}: gallery footnote hidden on mobile`);
  assert(layout.wizardHeight <= layout.innerHeight + 2, `${label}: wizard fits viewport`);
  return layout;
}

async function assertSongCopy(page, label) {
  const copy = await page.evaluate(() => ({
    eyebrow: document.querySelector('[data-create-eyebrow]')?.textContent || '',
    title: document.querySelector('[data-create-focus-title]')?.textContent || '',
    lead: document.querySelector('[data-create-focus-lead]')?.textContent || '',
  }));
  assert(copy.eyebrow === '01 · SONG', `${label}: song eyebrow`);
  assert(copy.title === 'What are we listening to?', `${label}: song heading`);
  assert(copy.lead.includes('visual DNA'), `${label}: song supporting copy`);
}

// Song idle — 390×844
const page390 = await newPage(390, 844);
await gotoCreate(page390);
await assertMobileShell(page390, 'song idle');
await assertSongCopy(page390, 'song idle');
const idleLayout = await page390.evaluate(() => ({
  stickyLabel: document.querySelector('[data-create-sticky-primary]')?.textContent || '',
  backInvisible: document.querySelector('[data-create-back]')?.classList.contains('is-invisible'),
  backDisplay: getComputedStyle(document.querySelector('[data-create-back]')).display,
}));
assert(idleLayout.stickyLabel === 'Find this song', 'song idle CTA is Find this song');
assert(idleLayout.backInvisible, 'song idle back visually hidden');
assert(idleLayout.backDisplay !== 'none', 'song idle back keeps layout column');
await page390.screenshot({ path: join(OUT, 'mobile-390-song-idle.png') });

// Song found
await page390.type('#song-form [name=artist]', 'Sabaton');
await page390.type('#song-form [name=title]', 'Seven Pillars of Wisdom');
await page390.click('[data-create-sticky-primary]');
await page390.waitForSelector('[data-song-results] [data-song-result].is-selected', { timeout: 15000 });
await page390.waitForFunction(() => document.querySelector('[data-create-sticky-primary]')?.textContent === 'Use this song', { timeout: 10000 });
await assertMobileShell(page390, 'song found');
const foundUi = await page390.evaluate(() => ({
  cta: document.querySelector('[data-create-sticky-primary]')?.textContent || '',
  selected: !!document.querySelector('[data-song-results] [data-song-result].is-selected'),
  checkVisible: !!document.querySelector('.yatsn-song-result.is-selected .yatsn-song-result__affordance'),
}));
assert(foundUi.cta === 'Use this song', 'song found CTA');
assert(foundUi.selected, 'song found auto-selected result');
assert(foundUi.checkVisible, 'song found checkmark visible');
await page390.screenshot({ path: join(OUT, 'mobile-390-song-found.png') });

await page390.click('[data-create-sticky-primary]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people', { timeout: 15000 });
await assertMobileShell(page390, 'people');
const chips = await page390.$$eval('[data-portrait-grid] .portrait-chip', (els) => els.length);
assert(chips >= 1, 'people tray renders fixture portraits');
await page390.click('[data-portrait-grid] .portrait-chip__select');
await page390.waitForFunction(() => document.querySelectorAll('[data-portrait-grid] .portrait-chip.is-selected').length === 1);
const selectedBefore = await page390.$$eval('[data-portrait-grid] .portrait-chip.is-selected', (els) => els.length);
assert(selectedBefore === 1, 'people portrait selected');
await page390.screenshot({ path: join(OUT, 'mobile-390-people-loaded.png') });

await page390.click('[data-people-continue]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction', { timeout: 10000 });
await page390.waitForFunction(() => {
  const quick = document.querySelector('[data-ai-quick]');
  return quick && !quick.disabled;
}, { timeout: 15000 });
await assertMobileShell(page390, 'direction');
const directionUi = await page390.evaluate(() => ({
  quick: document.querySelector('[data-ai-quick]')?.textContent || '',
  explore: document.querySelector('[data-ai-explore]')?.textContent || '',
}));
assert(directionUi.quick.includes('Generate for me'), 'direction quick generate primary');
assert(directionUi.explore.includes('Explore'), 'direction explore secondary');
await page390.screenshot({ path: join(OUT, 'mobile-390-direction.png') });

await page390.click('[data-ai-quick]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'review', { timeout: 30000 });
const reviewLayout = await assertMobileShell(page390, 'review');
assert(reviewLayout.primaryText === 'Generate image', 'review Generate image visible');
const reviewUi = await page390.evaluate(() => ({
  directionName: document.querySelector('[data-review-direction-name]')?.textContent || '',
  generateDisabled: document.querySelector('[data-create-sticky-primary]')?.disabled ?? true,
  fineTuneOpen: document.querySelector('[data-fine-tune]')?.open ?? true,
}));
assert(reviewUi.directionName && reviewUi.directionName !== 'Not chosen yet', 'review shows AI direction name');
assert(!reviewUi.generateDisabled, 'review Generate image enabled');
assert(!reviewUi.fineTuneOpen, 'review Fine Tune collapsed');
await page390.screenshot({ path: join(OUT, 'mobile-390-review.png') });

// Back preserves portrait selection
await page390.click('[data-create-back]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'direction');
await page390.click('[data-create-back]');
await page390.waitForFunction(() => window.YatsnCreate?.getFlowStep?.() === 'people');
const portraitSelected = await page390.$$eval('[data-portrait-grid] .portrait-chip.is-selected', (els) => els.length);
assert(portraitSelected === 1, 'back to people preserves exact portrait selection count');

// Additional viewport checks — song idle
for (const size of [[375, 667], [430, 932]]) {
  const page = await newPage(size[0], size[1]);
  await gotoCreate(page);
  await assertMobileShell(page, `viewport ${size[0]}×${size[1]}`);
  await page.close();
}

writeFileSync(join(OUT, 'verify-results.json'), JSON.stringify({ passed: true }, null, 2));
await browser.close();
console.log('Round 016.3 mobile shell verification passed.');

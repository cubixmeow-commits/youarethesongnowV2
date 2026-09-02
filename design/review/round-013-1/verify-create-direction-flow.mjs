import { execSync } from 'node:child_process';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = join(__dirname, '../../..');
const BASE = process.env.YATSN_BASE || 'http://127.0.0.1:8765';
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

async function gotoCreate() {
  await page.goto(`${BASE}/create`, { waitUntil: 'networkidle0', timeout: 45000 });
  await page.waitForFunction(() => typeof window.YatsnCreateFixtures === 'object', { timeout: 15000 });
  await page.waitForFunction(() => typeof window.YatsnExploreFixtures === 'object', { timeout: 15000 });
}

async function readUiState() {
  return page.evaluate(() => {
    const wrap = document.querySelector('[data-create-sticky-primary-wrap]');
    const quick = document.querySelector('[data-ai-quick]');
    const explore = document.querySelector('[data-ai-explore]');
    const options = document.querySelector('[data-ai-options]');
    const direction = document.querySelector('#the-direction');
    const people = document.querySelector('#the-people');
    const nav = document.querySelector('.app-nav');
    const wrapRect = wrap?.getBoundingClientRect();
    const navRect = nav?.getBoundingClientRect();
    const directionCard = document.querySelector('[data-create-card="direction"]');
    const peopleCard = document.querySelector('[data-create-card="people"]');
    const reviewCard = document.querySelector('[data-create-card="review"]');
    const createState = window.YatsnCreate?.getGenerateBarState?.() || {};
    const flowStep = window.YatsnCreate?.getFlowStep?.() || '';
    const navStyle = nav ? getComputedStyle(nav) : null;
    return {
      createState,
      flowStep,
      barHidden: wrap?.hidden ?? true,
      barDisplay: wrap ? getComputedStyle(wrap).display : 'none',
      hint: document.querySelector('[data-generate-hint]')?.textContent || '',
      quickPresent: !!quick,
      explorePresent: !!explore,
      quickDisabled: quick?.disabled ?? true,
      exploreDisabled: explore?.disabled ?? true,
      optionsHidden: options?.hidden ?? true,
      optionCount: options ? options.querySelectorAll('.ai-direction-card:not(.is-loading)').length : 0,
      directionHidden: directionCard?.hidden ?? true,
      peopleHidden: peopleCard?.hidden ?? true,
      reviewHidden: reviewCard?.hidden ?? true,
      navPresent: !!nav,
      navVisible: navStyle ? navStyle.display !== 'none' : false,
      barAboveNav: !!(wrapRect && navRect && wrapRect.bottom <= navRect.top + 1),
    };
  });
}

// 1. confirmed song, no portrait → People stage; no Generate bar
await gotoCreate();
await page.evaluate(() => window.YatsnCreateFixtures.showPeopleStage());
let ui = await readUiState();
assert(ui.flowStep === 'people', 'people flow step is active');
assert(ui.directionHidden === true, 'direction card stays hidden on people stage');
assert(ui.barHidden === true, 'generate bar hidden on people stage');
assert(ui.barDisplay === 'none', 'generate bar computed display is none on people stage');

// 2. portrait selected, Direction opens → Generate for me + Explore options; no final bar
await page.evaluate(() => {
  window.YatsnCreateFixtures.showDirectionChoice();
  window.YatsnExploreFixtures.showInitialChoice();
});
ui = await readUiState();
assert(ui.flowStep === 'direction', 'direction flow step is active');
assert(ui.directionHidden === false, 'direction card opens after portrait');
assert(ui.quickPresent && ui.explorePresent, 'Generate for me and Explore options are present');
assert(!ui.quickDisabled && !ui.exploreDisabled, 'AI choice actions are enabled with Song DNA');
assert(ui.barHidden === true && ui.barDisplay === 'none', 'final bar hidden at initial direction choice');
assert(ui.optionsHidden === true, 'explore grid stays hidden before Explore options');

// 4. Explore options initial → three directions; no premature final bar
await page.evaluate(() => window.YatsnExploreFixtures.showReady());
ui = await readUiState();
assert(ui.optionCount === 3, 'explore options renders three directions');
assert(ui.barHidden === true && ui.barDisplay === 'none', 'final bar hidden while exploring directions');

// 5. explored direction applied → final action reachable
await page.evaluate(() => {
  window.YatsnExploreFixtures.showSelected();
  window.YatsnCreate.setDirectionPrepared('ai-explore');
  window.YatsnCreate.setGenerationFixtureState({ reviewed: true, pending: false, issue: null });
  window.YatsnCreate.setFlowStep('review', { focus: false });
});
ui = await readUiState();
assert(ui.flowStep === 'review', 'review flow step after explore prepare');
assert(ui.reviewHidden === false, 'review card visible after explore prepare');
assert(ui.hint !== 'Choose and confirm a song.', 'restored song never reports missing song at prepared state');

// 3. Generate for me prepared → final Generate image reachable without auto-submit
await gotoCreate();
await page.evaluate(() => {
  window.YatsnCreateFixtures.showDirectionChoice();
  window.YatsnExploreFixtures.showInitialChoice();
  window.YatsnCreate.setDirectionPrepared('ai-quick');
  window.YatsnCreate.setGenerationFixtureState({ reviewed: true, pending: false, issue: null });
  window.YatsnCreate.setFlowStep('review', { focus: false });
});
ui = await readUiState();
assert(ui.createState.path === 'ai-quick', 'quick generate records ai-quick path');
assert(ui.barHidden === false && ui.barDisplay !== 'none', 'final bar visible after Generate for me prepares direction');
assert(ui.createState.pending === false, 'Generate for me does not auto-submit generation');

// 6. manual path prepared → final action reachable
await gotoCreate();
await page.evaluate(() => window.YatsnCreateFixtures.showRestoredDraft());
ui = await readUiState();
assert(ui.createState.path === 'manual', 'restored/manual path records manual direction path');
assert(ui.barHidden === false, 'manual prepared path shows final bar');

// 7. restored draft with confirmed song never reports song missing
await gotoCreate();
await page.evaluate(() => window.YatsnCreateFixtures.showRestoredDraft());
ui = await readUiState();
assert(ui.hint !== 'Choose and confirm a song.', 'restored draft does not report missing song');
assert(ui.hint !== 'Confirm your song match before generating.', 'restored draft does not ask to reconfirm song');

// 8. hidden bar computed style is actually display: none
await page.evaluate(() => window.YatsnCreateFixtures.showDirectionChoice());
ui = await readUiState();
assert(ui.barHidden === true, 'bar hidden attribute set when unprepared');
assert(ui.barDisplay === 'none', 'hidden generate bar uses display:none despite grid styles');

// 9. mobile overlap check at 390
await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 1 });
await page.evaluate(() => window.YatsnCreateFixtures.showPreparedReady());
ui = await readUiState();
assert(ui.navVisible === true, 'mobile create focus keeps bottom navigation visible at 390 width');
assert(ui.barHidden === false && ui.createState.disabled === false, 'generate action enabled on review at 390 width');

await page.setViewport({ width: 320, height: 640, deviceScaleFactor: 1 });
ui = await readUiState();
assert(ui.navVisible === true, 'mobile create focus keeps bottom navigation visible at 320 width');
assert(ui.barHidden === false, 'generate bar remains visible on review at 320 width');

await browser.close();
console.log('Round 013.1 create-direction flow verification passed.');

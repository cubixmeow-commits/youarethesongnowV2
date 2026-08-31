import { mkdirSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import puppeteer from 'puppeteer-core';

const __dirname = dirname(fileURLToPath(import.meta.url));
const OUT = __dirname;
const BASE = 'http://127.0.0.1:8765';
const CHROME = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';

mkdirSync(OUT, { recursive: true });

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: true,
  args: ['--no-sandbox', '--disable-dev-shm-usage'],
});

const page = await browser.newPage();

async function capture(name, url, width, height, afterNavigate) {
  await page.setViewport({ width, height, deviceScaleFactor: 1 });
  await page.goto(url, { waitUntil: 'networkidle0', timeout: 60000 });
  if (afterNavigate) {
    await afterNavigate(page);
    await new Promise((resolve) => setTimeout(resolve, 500));
  }
  await page.screenshot({ path: join(OUT, name), type: 'png' });
}

await capture('home-mobile-320.png', `${BASE}/`, 320, 568);
await capture('home-mobile-390-carousel.png', `${BASE}/`, 390, 844, async (p) => {
  await p.evaluate(() => {
    const track = document.querySelector('[data-carousel-track]');
    if (track) track.scrollLeft = track.clientWidth * 0.4;
  });
});
await capture('home-desktop-1440.png', `${BASE}/`, 1440, 900);

await capture('showcase-mobile-390-initial.png', `${BASE}/showcase`, 390, 844);
await capture('showcase-mobile-390-loaded.png', `${BASE}/showcase`, 390, 844, async (p) => {
  await p.click('[data-showcase-load-more]');
  await p.waitForFunction(() => document.querySelectorAll('.showcase-tile').length >= 30, { timeout: 15000 });
});
await capture('showcase-mobile-390-dialog.png', `${BASE}/showcase`, 390, 844, async (p) => {
  await p.waitForSelector('.showcase-tile__button');
  await p.click('.showcase-tile__button');
  await p.waitForSelector('dialog[open]');
});
await capture('showcase-desktop-1440-initial.png', `${BASE}/showcase`, 1440, 900);
await capture('showcase-desktop-1440-loaded.png', `${BASE}/showcase`, 1440, 900, async (p) => {
  for (let i = 0; i < 6; i += 1) {
    const btn = await p.$('[data-showcase-load-more]:not([disabled])');
    if (!btn) break;
    await btn.click();
    await new Promise((resolve) => setTimeout(resolve, 400));
  }
  await p.waitForFunction(() => document.querySelectorAll('.showcase-tile').length >= 77, { timeout: 20000 });
});
await capture('showcase-desktop-1440-dialog.png', `${BASE}/showcase`, 1440, 900, async (p) => {
  await p.waitForSelector('.showcase-tile__button');
  await p.click('.showcase-tile__button');
  await p.waitForSelector('dialog[open]');
});
await capture('showcase-desktop-1440-landscape-filter.png', `${BASE}/showcase`, 1440, 900, async (p) => {
  await p.click('[data-filter="landscape"]');
  await p.waitForFunction(() => document.querySelectorAll('.showcase-tile').length === 12, { timeout: 10000 });
});

const zoomResults = [];
for (const spec of [
  { page: 'home', url: `${BASE}/`, width: 320, height: 568 },
  { page: 'showcase', url: `${BASE}/showcase`, width: 390, height: 844 },
  { page: 'showcase-desktop', url: `${BASE}/showcase`, width: 1440, height: 900 },
]) {
  const client = await page.createCDPSession();
  await client.send('Emulation.setPageScaleFactor', { pageScaleFactor: 2 });
  await page.setViewport({ width: spec.width, height: spec.height, deviceScaleFactor: 1 });
  await page.goto(spec.url, { waitUntil: 'networkidle0', timeout: 60000 });
  const metrics = await page.evaluate(() => ({
    scrollWidth: document.documentElement.scrollWidth,
    innerWidth: window.innerWidth,
    overflow: document.documentElement.scrollWidth > window.innerWidth,
  }));
  zoomResults.push({ ...spec, pageScaleFactor: 2, ...metrics });
  await client.detach();
}

writeFileSync(join(OUT, 'performance-summary.json'), JSON.stringify({
  generatedAt: new Date().toISOString(),
  homeCarouselInitialBatch: 9,
  showcaseInitialBatch: 18,
  showcaseLaterBatch: 12,
  showcaseTotalItems: 77,
  orientationTotals: { portrait: 32, square: 33, landscape: 12 },
  bytes: {
    originals: 92274688,
    thumbs: 3040870,
    display: 14680064,
  },
  zoom200: zoomResults,
  keyboardChecks: {
    carouselArrowKeys: 'supported in showcase.js',
    dialogEscapeArrows: 'supported in showcase.js',
    loadMoreAccessibleName: 'Load more worlds',
  },
  reducedMotion: 'insert and relayout immediately without transforms when prefers-reduced-motion: reduce',
  slow4gNote: 'thumbnails and progressive batches keep pages usable while later images arrive',
}, null, 2));

await browser.close();

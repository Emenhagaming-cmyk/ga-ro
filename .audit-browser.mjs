import { chromium } from 'playwright';

const BASE = process.env.BASE || 'http://localhost:4173';
const routes = [
  '/', '/spmb-info', '/berita', '/koperasi', '/spp', '/tabungan',
  '/produk-siswa', '/e-learning', '/e-tracer', '/career-center/search',
  '/chat', '/kelulusan', '/profil', '/this-page-does-not-exist'
];

const browser = await chromium.launch();
const ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });

for (const route of routes) {
  const page = await ctx.newPage();
  const consoleErrors = [];
  const pageErrors = [];
  const failed = [];
  page.on('console', (m) => { if (m.type() === 'error') consoleErrors.push(m.text()); });
  page.on('pageerror', (e) => pageErrors.push(String(e.message || e)));
  page.on('requestfailed', (r) => failed.push(`${r.method()} ${r.url()} :: ${r.failure()?.errorText}`));
  page.on('response', (r) => { if (r.status() >= 400) failed.push(`HTTP ${r.status()} ${r.url()}`); });
  let navError = null;
  try {
    await page.goto(BASE + route, { waitUntil: 'networkidle', timeout: 20000 });
  } catch (e) { navError = String(e.message || e); }
  const appHtml = await page.evaluate(() => {
    const a = document.querySelector('#app');
    return a ? a.innerHTML.length : -1;
  });
  const brokenImgs = await page.evaluate(() => {
    return [...document.images].filter((i) => i.complete && i.naturalWidth === 0).map((i) => i.getAttribute('src'));
  });
  const title = await page.title().catch(() => '');
  console.log('==== ' + route + ' ====');
  console.log('  title:', JSON.stringify(title));
  console.log('  #app innerHTML length:', appHtml);
  if (navError) console.log('  NAV ERROR:', navError);
  if (pageErrors.length) console.log('  PAGEERROR:', pageErrors.slice(0, 5));
  if (consoleErrors.length) console.log('  CONSOLE.ERROR:', consoleErrors.slice(0, 5));
  if (failed.length) console.log('  FAILED REQ:', [...new Set(failed)].slice(0, 10));
  if (brokenImgs.length) console.log('  BROKEN IMGS:', [...new Set(brokenImgs)].slice(0, 10));
  await page.close();
}
await browser.close();

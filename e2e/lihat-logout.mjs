import { chromium } from "@playwright/test";

const BACKEND = "http://localhost:8000";
const LANDING = "http://localhost:5174";
const b = await chromium.launch();
const ctx = await b.newContext({ viewport: { width: 1280, height: 800 } });
const p = await ctx.newPage();
await p.route(
  /(fonts\.googleapis\.com|fonts\.gstatic\.com|maps\.googleapis\.com|maps\.gstatic\.com|google\.com\/maps\/embed)/,
  (r) => r.abort()
);

function watch() {
  p.on("request", (r) => {
    if (r.url().includes("localhost")) console.log("   REQ :", r.method(), r.url().replace(LANDING, "").replace(BACKEND, "BE"));
  });
  p.on("response", (r) => {
    if (r.url().includes("localhost")) console.log("   RESP:", r.status(), r.url().replace(LANDING, "").replace(BACKEND, "BE"), "->", r.headers()["location"] ?? "");
  });
}
watch();

async function login(username, password) {
  await p.goto(`${BACKEND}/login`, { waitUntil: "domcontentloaded" });
  await p.fill('input[name="username"]', username);
  await p.fill('input[name="password"]', password);
  await p.click('button[type="submit"]');
  await p.waitForURL(/localhost:8000/, { waitUntil: "domcontentloaded", timeout: 15000 });
  console.log("   LOGIN OK, final URL:", p.url());
}

async function clickAndSee(selector, label, expectURL) {
  console.log(`=== ${label} ===`);
  await p.click(selector);
  try {
    await p.waitForURL(new RegExp(expectURL), { timeout: 20000, waitUntil: "domcontentloaded" });
  } catch {
    console.log("   (waitForURL timeout — URL saat ini:", p.url() + ")");
  }
  await p.waitForTimeout(1500);
  const heroLogin = await p.locator(".hero .btn-login").first().isVisible().catch(() => false);
  const navLogout = await p.locator(".nav-logout").isVisible().catch(() => false);
  console.log("   FINAL URL:", p.url());
  console.log("   hero login-btn visible:", heroLogin, "| nav logout visible:", navLogout);
  return p.url();
}

await ctx.clearCookies();

// A. pendaftar baru: register → form page → logout blade
const uniq = "pdr" + Date.now().toString().slice(-6);
console.log("=== A. REGISTER pendaftar baru:", uniq, "===");
await p.goto(`${BACKEND}/register`, { waitUntil: "domcontentloaded" });
await p.fill('input[name="name"]', "Pendaftar Test");
await p.fill('input[name="username"]', uniq);
await p.fill('input[name="email"]', `${uniq}@test.dev`);
await p.fill('input[name="password"]', "test1234");
await p.fill('input[name="password_confirmation"]', "test1234");
await p.click('button[type="submit"]');
await p.waitForURL(/localhost:8000/, { waitUntil: "domcontentloaded", timeout: 15000 });
console.log("   after register:", p.url());
await clickAndSee("form[action*='/logout'] button", "A. klik Logout (blade) di form page", "5174");

// B. login siswa → form page → logout blade
await ctx.clearCookies();
console.log("\n=== B. login siswa (siswa) ===");
await login("siswa", "siswa123");
await clickAndSee("form[action*='/logout'] button", "B. klik Logout (blade) di form page", "5174");

// C. login siswa → landing → navbar desktop logout
await ctx.clearCookies();
console.log("\n=== C. login siswa → navbar desktop ===");
await login("siswa", "siswa123");
await p.goto(`${LANDING}/?no-intro=1`, { waitUntil: "domcontentloaded" });
await p.locator(".nav-logout").waitFor({ state: "visible", timeout: 10000 });
await clickAndSee(".nav-logout", "C. klik Logout (navbar desktop)", "5174");

// D. login siswa → landing mobile menu logout
await ctx.clearCookies();
console.log("\n=== D. login siswa → navbar mobile ===");
await login("siswa", "siswa123");
const mob = await b.newContext({ viewport: { width: 390, height: 844 } });
const pm = await mob.newPage();
await pm.route(
  /(fonts\.googleapis\.com|fonts\.gstatic\.com|maps\.googleapis\.com|maps\.gstatic\.com|google\.com\/maps\/embed)/,
  (r) => r.abort()
);
await pm.goto(`${BACKEND}/login`, { waitUntil: "domcontentloaded" });
await pm.fill('input[name="username"]', "siswa");
await pm.fill('input[name="password"]', "siswa123");
await pm.click('button[type="submit"]');
await pm.waitForURL(/localhost:8000/, { waitUntil: "domcontentloaded", timeout: 15000 });
await pm.goto(`${LANDING}/?no-intro=1`, { waitUntil: "domcontentloaded" });
await pm.locator(".menu").waitFor({ state: "visible", timeout: 10000 });
await pm.click(".menu");
await pm.locator(".mobile-logout").waitFor({ state: "visible", timeout: 5000 });
console.log("=== D. klik Logout (mobile menu) ===");
await pm.click(".mobile-logout");
try {
  await pm.waitForURL(/5174/, { timeout: 20000, waitUntil: "domcontentloaded" });
} catch {}
await pm.waitForTimeout(1500);
console.log("   FINAL URL:", pm.url());
await mob.close();

await b.close();
process.exit(0);
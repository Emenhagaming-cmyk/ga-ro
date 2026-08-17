import { test, expect } from "@playwright/test";

const BACKEND = "http://localhost:8000";
const LANDING = "http://localhost:5174/?no-intro=1";
const USER = process.env.TEST_SISWA_USER || "siswa";
const PASS = process.env.TEST_SISWA_PASS || "siswa123";

test.beforeEach(async ({ page }) => {
  await page.route(
    /(fonts\.googleapis\.com|fonts\.gstatic\.com|maps\.googleapis\.com|maps\.gstatic\.com|google\.com\/maps\/embed)/,
    (route) => route.abort()
  );
});

async function loginAsSiswa(page) {
  await page.goto(`${BACKEND}/login`);
  await page.fill('input[name="username"]', USER);
  await page.fill('input[name="password"]', PASS);
  await Promise.all([
    page.waitForURL(/localhost:8000/, { timeout: 15000, waitUntil: "domcontentloaded" }),
    page.click('button[type="submit"]'),
  ]);
}

test("logout dari form page (blade) → landing + hero login muncul", async ({ page }) => {
  const logoutResponses = [];
  page.on("response", (res) => {
    if (res.url().includes("/logout")) {
      logoutResponses.push(`${res.status()} ${res.headers()["location"] ?? ""}`);
    }
  });

  await loginAsSiswa(page);

  const logoutBtn = page.locator("form[action*='/logout'] button");
  await expect(logoutBtn).toBeVisible();
  await logoutBtn.click();

  await page.waitForURL(/localhost:5174(\/|$)/, { timeout: 15000 });
  await expect(page).toHaveURL(/no-intro=1/);

  await expect(page.locator(".hero .btn-login").first()).toBeVisible();
  await expect(page.locator(".nav-logout")).toBeHidden();

  await page.goto(`${BACKEND}/dashboard-siswa`, { waitUntil: "domcontentloaded" });
  await expect(page).toHaveURL(/localhost:8000\/login/);

  console.log("logout responses:", JSON.stringify(logoutResponses));
});

test("logout dari landing (navbar Vue) → landing + hero login muncul", async ({ page }) => {
  const logoutResponses = [];
  page.on("response", (res) => {
    if (res.url().includes("/logout")) {
      logoutResponses.push(`${res.status()} ${res.headers()["location"] ?? ""}`);
    }
  });

  await loginAsSiswa(page);
  await page.goto(LANDING);

  const logoutBtn = page.locator(".nav-logout");
  await expect(logoutBtn).toBeVisible();
  await logoutBtn.click();

  await page.waitForURL(/localhost:5174(\/|$)/, { timeout: 15000 });
  await expect(page).toHaveURL(/no-intro=1/);

  await expect(page.locator(".hero .btn-login").first()).toBeVisible();
  await expect(page.locator(".nav-logout")).toBeHidden();

  await page.goto(`${BACKEND}/dashboard-siswa`, { waitUntil: "domcontentloaded" });
  await expect(page).toHaveURL(/localhost:8000\/login/);

  console.log("logout responses:", JSON.stringify(logoutResponses));
});
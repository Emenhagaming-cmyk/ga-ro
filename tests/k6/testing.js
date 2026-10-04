import { browser } from "k6/browser";
import { check, sleep } from "k6";

const BASE_URL = "https://smkbu-sby.my.id";

const accounts = [
  { username: "tester01", password: "SmkbuTest01-9Qx" },
  { username: "tester02", password: "SmkbuTest02-7Lm" },
  { username: "tester03", password: "SmkbuTest03-5Vr" },
  { username: "tester04", password: "SmkbuTest04-3Np" },
  { username: "tester05", password: "SmkbuTest05-8Kd" },
];

export const options = {
  scenarios: {
    login_test: {
      executor: "constant-vus",
      vus: 1,
      duration: "30s",
      options: {
        browser: {
          type: "chromium",
        },
      },
    },
  },

  thresholds: {
    browser_http_req_failed: ["rate<0.05"],
    browser_web_vital_lcp: ["p(95)<4000"],
    browser_web_vital_fcp: ["p(95)<3000"],
    browser_web_vital_ttfb: ["p(95)<2000"],
  },
};

export default async function () {
  const account = accounts[(__VU - 1) % accounts.length];
  const page = await browser.newPage();

  try {
    console.log(`VU ${__VU} login sebagai ${account.username}`);

    await page.goto(`${BASE_URL}/login`);
    await page.waitForTimeout(3000);

    const usernameInput = page.getByRole("textbox", {
      name: "USERNAME",
    });

    const passwordInput = page.getByRole("textbox", {
      name: "PASSWORD",
    });

    await usernameInput.fill(account.username);
    await passwordInput.fill(account.password);

    await page.getByRole("button", {
      name: "Masuk",
      exact: true,
    }).click();

    // Tunggu proses fetch login, penyimpanan session, dan redirect.
    await page.waitForTimeout(8000);

    const currentUrl = await page.url();
    const bodyText = await page.locator("body").textContent();

    console.log(`URL setelah login: ${currentUrl}`);

 check(currentUrl, {
  "login berhasil": (url) =>
    url.endsWith("/") || url.includes("/dashboard-siswa"),
});

    if (currentUrl.includes("/login")) {
      throw new Error(
        `Login gagal untuk ${account.username}. Isi halaman: ${bodyText}`
      );
    }

    check(bodyText, {
      "dashboard siswa tampil": (text) =>
        text.includes("Dashboard") ||
        text.includes("Lengkapi Pendaftaran") ||
        text.includes("Hai"),
    });

    // Simulasi aktivitas user setelah login.
    await page.goto(`${BASE_URL}/berita`);
    await page.waitForTimeout(2000);

    check(await page.url(), {
      "halaman berita bisa dibuka": (url) =>
        url.includes("/berita"),
    });

    await page.goto(`${BASE_URL}/spmb-info`);
    await page.waitForTimeout(2000);

    check(await page.url(), {
      "halaman SPMB bisa dibuka": (url) =>
        url.includes("/spmb-info"),
    });

    sleep(1);
  } finally {
    await page.close();
  }
}
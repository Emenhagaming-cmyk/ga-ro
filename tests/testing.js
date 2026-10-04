import { browser } from "k6/browser";
import { expect } from "https://jslib.k6.io/k6-testing/0.5.0/index.js";

export const options = {
  scenarios: {
    login_test: {
      executor: "shared-iterations",
      iterations: 1,
      options: {
        browser: {
          type: "chromium",
        },
      },
    },
  },
};

export default async function () {
  const page = await browser.newPage();

  try {
    await page.goto("https://smkbu-sby.my.id/login");

    await page.getByRole("textbox", { name: "USERNAME" }).fill("jeky");
    await page.getByRole("textbox", { name: "PASSWORD" }).fill("zakky78910");

    await page.getByRole("button", { name: "Masuk" }).click();

    await page.waitForTimeout(3000);

    const currentUrl = await page.url();
    console.log(`URL setelah login: ${currentUrl}`);

    if (currentUrl.includes("/login")) {
      throw new Error("Login gagal atau masih berada di halaman login");
    }

    await expect(page.locator("body")).toContainText(
      "Lengkapi pendaftaran SPMB Dulu Ya"
    );
  } finally {
    await page.close();
  }
}
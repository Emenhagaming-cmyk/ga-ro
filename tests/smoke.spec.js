import { test, expect } from '@playwright/test';

const BASE = 'https://smkbu-sby.my.id';

const pages = [
  ['homepage', '/'],
  ['login', '/login'],
  ['register', '/register'],
  ['forgot password', '/forgot-password'],
  ['pendaftaran', '/pendaftaran'],
];

for (const [name, path] of pages) {
  test(`${name} bisa dibuka`, async ({ page }) => {
    const response = await page.goto(BASE + path);

    expect(response.status()).toBeLessThan(400);
    await expect(page.locator('body')).not.toContainText('500');
    await expect(page.locator('body')).not.toContainText('Service Unavailable');
  });
}

test('tombol Login Hero menuju domain utama', async ({ page }) => {
  await page.goto(BASE);

  await page.getByRole('button', {
    name: 'Login',
    exact: true,
  }).click();

  await expect(
    page.getByRole('link', { name: /Login Siswa/i })
  ).toBeVisible();

  await page.getByRole('link', {
    name: /Login Siswa/i,
  }).click();

  await expect(page).toHaveURL(`${BASE}/login`);
});

test('halaman login tidak menuju API lama', async ({ page }) => {
  await page.goto(`${BASE}/login`);

  await expect(page).not.toHaveURL(/api\.smkbu-sby\.my\.id/);
});
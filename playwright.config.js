import { defineConfig } from "@playwright/test";

export default defineConfig({
  testDir: "./e2e",
  timeout: 60000,
  fullyParallel: false,
  retries: 0,
  workers: 1,
  use: {
    baseURL: "http://localhost:5174",
    trace: "retain-on-failure",
    screenshot: "only-on-failure",
  },
  webServer: [
    {
      command: "php artisan serve --port=8000",
      url: "http://localhost:8000/login",
      cwd: "./backend",
      reuseExistingServer: true,
      timeout: 30000,
    },
    {
      command: "npm.cmd run dev -- --port 5174 --strictPort",
      url: "http://localhost:5174",
      reuseExistingServer: true,
      timeout: 60000,
    },
  ],
});
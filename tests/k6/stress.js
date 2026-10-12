import http from "k6/http";
import { check, sleep } from "k6";
import { Trend, Rate } from "k6/metrics";

const BASE_URL = "https://smkbu-sby.my.id";

const homepageDuration = new Trend("homepage_duration");
const failedRequests = new Rate("failed_requests");

export const options = {
  scenarios: {
    website_stress: {
      executor: "ramping-vus",
      startVUs: 0,

      stages: [
        { duration: "1m", target: 30 },
        { duration: "1m", target: 50 },
        { duration: "1m", target: 100 },
        { duration: "1m", target: 200 },
      ],

      gracefulRampDown: "30s",
    },
  },

  thresholds: {
    http_req_failed: [
      {
        threshold: "rate<0.05",
        abortOnFail: true,
        delayAbortEval: "30s",
      },
    ],

    http_req_duration: [
      {
        threshold: "p(95)<5000",
        abortOnFail: true,
        delayAbortEval: "1m",
      },
    ],

    homepage_duration: ["p(95)<5000"],
    failed_requests: ["rate<0.05"],
  },
};

export default function () {
  const pages = [
    { name: "homepage", path: "/" },
    { name: "login", path: "/login" },
    { name: "berita", path: "/berita" },
    { name: "spmb", path: "/spmb-info" },
    { name: "koperasi", path: "/koperasi" },
  ];

  const page = pages[Math.floor(Math.random() * pages.length)];

  const response = http.get(`${BASE_URL}${page.path}`, {
    timeout: "15s",

    tags: {
      page: page.name,
    },
  });

  if (page.name === "homepage") {
    homepageDuration.add(response.timings.duration);
  }

  const requestFailed =
    response.status === 0 || response.status >= 500;

  failedRequests.add(requestFailed);

  check(response, {
    [`${page.name} status valid`]: (res) =>
      res.status >= 200 && res.status < 400,

    [`${page.name} bukan 5xx`]: (res) =>
      res.status < 500,

    [`${page.name} selesai kurang dari 5 detik`]: (res) =>
      res.timings.duration < 5000,
  });

  if (requestFailed) {
    console.log(
      `GAGAL ${page.name}: status=${response.status}, waktu=${response.timings.duration}ms`
    );
  }

  // Simulasi jeda antar-request user
  sleep(Math.random() * 2 + 1);
}
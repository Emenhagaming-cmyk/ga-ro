import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  stages: [
    { duration: '30s', target: 5 },
    { duration: '1m', target: 10 },
    { duration: '30s', target: 0 },
  ],
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<2000'],
  },
};

export default function () {
  const urls = [
    'https://smkbu-sby.my.id/',
    'https://smkbu-sby.my.id/login',
    'https://smkbu-sby.my.id/register',
    'https://smkbu-sby.my.id/forgot-password',
  ];

  for (const url of urls) {
    const response = http.get(url);

    check(response, {
      'status kurang dari 400': (r) => r.status < 400,
    });

    sleep(1);
  }
}
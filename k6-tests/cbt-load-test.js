import http from 'k6/http';
import { check, sleep, group, fail } from 'k6';
import { SharedArray } from 'k6/data';
import { randomIntBetween } from 'https://jslib.k6.io/k6-utils/1.2.0/index.js';

// --- KONFIGURASI LOAD PROFILE ---
export const options = {
  stages: [
    { duration: '30s', target: 5 },  // Ramp-up ke 5 user
    { duration: '1m', target: 10 },  // Naik ke 10 user
    { duration: '1m', target: 20 },  // Puncak di 20 user
    { duration: '30s', target: 0 },  // Ramp-down
  ],
  thresholds: {
    'http_req_duration': ['p(95)<2000'], // 95% request harus < 2 detik
    'http_req_failed': ['rate<0.05'],    // Toleransi gagal ditingkatkan sedikit untuk dev env
  },
};

// --- DATA SETUP ---
const sharedData = new SharedArray('users', function () {
  const jsonData = JSON.parse(open('./users.json'));
  return [jsonData]; // SharedArray harus return array
});

const BASE_URL = 'http://cbtsatpelbantul.test';

// --- HELPER FUNCTION ---
function getCsrfToken(response) {
  try {
    let match = response.body.match(/name="csrf-token" content="([^"]+)"/);
    if (match) return match[1];
    
    match = response.body.match(/name="_token" value="([^"]+)"/);
    if (match) return match[1];
    
    return null;
  } catch (e) {
    return null;
  }
}

// --- SKENARIO UTAMA ---
export default function () {
  const data = sharedData[0];
  const user = data.users[(__VU - 1) % data.users.length];
  const testInfo = data.test;

  const params = {
    headers: { 
      'User-Agent': 'k6-load-test',
      'Accept': 'text/html'
    },
    jar: http.cookieJar(), // Manage sessions
  };

  let res, csrfToken;

  // 1. LOGIN FLOW
  group('01_Login', () => {
    res = http.get(`${BASE_URL}/login`, params);
    csrfToken = getCsrfToken(res);

    if (!csrfToken) {
        console.error(`VU ${__VU}: Failed to get CSRF on Login page`);
        return;
    }

    res = http.post(`${BASE_URL}/login`, {
      _token: csrfToken,
      email: user.email,
      password: user.password,
    }, params);

    check(res, {
      'Login success': (r) => r.status === 200 || r.status === 302,
    });
  });

  // 2. START EXAM
  group('02_Start_Exam', () => {
    res = http.get(`${BASE_URL}/tests/${testInfo.id}`, params);
    csrfToken = getCsrfToken(res);

    sleep(1);

    let payload = { _token: csrfToken };
    if (testInfo.token) payload.token = testInfo.token;

    res = http.post(`${BASE_URL}/tests/${testInfo.id}/start`, payload, params);

    check(res, {
      'Exam started': (r) => r.status === 200 || r.url.includes('question/1'),
    });
  });

  // 3. TAKING EXAM
  group('03_Answer_Questions', () => {
    // Kita simulasi 5 soal
    for (let i = 1; i <= 5; i++) {
      res = http.get(`${BASE_URL}/tests/${testInfo.id}/question/${i}`, params);
      
      check(res, {
        [`Question ${i} loaded`]: (r) => r.status === 200,
      });

      let pageCsrf = getCsrfToken(res);
      sleep(randomIntBetween(2, 4)); // Realistic think time

      res = http.post(`${BASE_URL}/tests/${testInfo.id}/question/${i}/save`, {
        _token: pageCsrf || csrfToken,
        next: '1'
      }, params);

      check(res, {
        [`Answer ${i} saved`]: (r) => r.status === 200 || r.status === 302,
      });
    }
  });

  // 4. FINISH EXAM
  group('04_Finish_Exam', () => {
    // Ambil token terakhir dari page terakhir (soal 5)
    let finalCsrf = getCsrfToken(res);

    res = http.post(`${BASE_URL}/tests/${testInfo.id}/submit`, {
      _token: finalCsrf
    }, params);

    check(res, {
      'Exam submitted': (r) => r.url.includes('/results') || r.status === 200,
    });
  });

  sleep(1);
}

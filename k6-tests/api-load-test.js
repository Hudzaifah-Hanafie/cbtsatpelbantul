import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { SharedArray } from 'k6/data';
import { randomIntBetween } from 'https://jslib.k6.io/k6-utils/1.2.0/index.js';

export const options = {
  stages: [
    { duration: '30s', target: 10 }, // Ramp-up pelan ke 10 user
    { duration: '1m', target: 20 },  // Stabil di 20 user
    { duration: '30s', target: 0 },
  ],
  thresholds: {
    'http_req_duration': ['p(95)<2000'], 
    'http_req_failed': ['rate<0.05'],
  },
};

const sharedData = new SharedArray('users', function () {
  return [JSON.parse(open('./users.json'))];
});

const BASE_URL = 'http://cbtsatpelbantul.test/load-test';

export default function () {
  const data = sharedData[0];
  const user = data.users[(__VU - 1) % data.users.length];
  const testInfo = data.test;

  const params = {
    headers: { 
      'User-Agent': 'k6-api-test',
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    jar: http.cookieJar(),
  };

  let userTestId;

  group('01_Login', () => {
    const res = http.post(`${BASE_URL}/login`, JSON.stringify({
      email: user.email,
      password: user.password,
    }), params);

    // FIXED: Akses cookie dengan kurung siku karena mengandung tanda hubung '-'
    const hasCookie = res.cookies && res.cookies['laravel-session'] && res.cookies['laravel-session'].length > 0;

    const isLoginSuccess = check(res, {
      'Login API success': (r) => r.status === 200,
      'Has session cookie': (r) => hasCookie
    });

    if (!isLoginSuccess && res.status !== 200) {
        console.error(`VU ${__VU}: Login Failed. Status: ${res.status}`);
    }
  });

  group('02_Start_Exam', () => {
    // Jeda acak agar tidak semua VU hit DB bersamaan (mengurangi resiko 502)
    sleep(randomIntBetween(1, 3));

    const payload = { test_id: testInfo.id };
    if (testInfo.token) payload.token = testInfo.token;

    const res = http.post(`${BASE_URL}/start`, JSON.stringify(payload), params);

    if (check(res, { 'Start API success': (r) => r.status === 200 })) {
        try {
            userTestId = res.json('user_test_id');
        } catch (e) {
            console.error(`VU ${__VU}: Failed to parse user_test_id`);
        }
    }
  });

  group('03_Answer_Questions', () => {
    if (!userTestId) return; 

    // Simulasi menjawab 5 soal
    const questionsToAnswer = testInfo.questions.slice(0, 5);

    for (let i = 0; i < questionsToAnswer.length; i++) {
        const questionId = questionsToAnswer[i];
        const optionId = randomIntBetween(1, 10); // Asumsi ada ID opsi 1-10

        const res = http.post(`${BASE_URL}/answer`, JSON.stringify({
            user_test_id: userTestId,
            question_id: questionId,
            option_id: optionId
        }), params);

        check(res, {
            'Answer API saved': (r) => r.status === 200,
        });

        // Think time antar soal
        sleep(randomIntBetween(2, 4));
    }
  });

  group('04_Submit', () => {
    if (!userTestId) return;

    const res = http.post(`${BASE_URL}/submit`, JSON.stringify({
        user_test_id: userTestId
    }), params);

    check(res, {
        'Submit API success': (r) => r.status === 200,
    });
  });
}

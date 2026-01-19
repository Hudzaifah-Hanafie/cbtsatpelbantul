import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { SharedArray } from 'k6/data';
import { randomIntBetween } from 'https://jslib.k6.io/k6-utils/1.2.0/index.js';

// Konfigurasi Load Test
// export const options = {
//   stages: [
//     { duration: '30s', target: 20 }, // Ramp-up ke 20 user dalam 30 detik
//     { duration: '1m', target: 20 },  // Tahan 20 user selama 1 menit
//     { duration: '10s', target: 0 },  // Ramp-down
//   ],
//   thresholds: {
//     http_req_duration: ['p(95)<2000'], // 95% request harus di bawah 2 detik
//     http_req_failed: ['rate<0.01'],    // Error rate di bawah 1%
//   },
// };
export const options = {
  stages: [
    { duration: '20s', target: 5 },
    { duration: '20s', target: 10 },
    { duration: '1m', target: 10 },
    { duration: '10s', target: 0 },
  ],
  thresholds: {
    http_req_duration: ['p(95)<2000'],
    http_req_failed: ['rate<0.01'],
  },
};


// Load Data User & Test dari JSON yang sudah digenerate
// SharedArray MENGHARUSKAN return value berupa Array.
// Karena JSON kita berbentuk Object { users: [], test: {} }, kita bungkus jadi [ { ... } ]
const sharedData = new SharedArray('users', function () {
  const jsonData = JSON.parse(open('./users.json'));
  return [jsonData]; // Return array containing the data object
});

const BASE_URL = 'http://cbtsatpelbantul.test'; // Sesuaikan dengan domain lokal Anda

export default function () {
  // Akses data dari elemen pertama SharedArray
  const data = sharedData[0];
  
  // Ambil user unik berdasarkan Virtual User ID (VU) agar tidak tabrakan login
  const userIndex = (__VU - 1) % data.users.length;
  const user = data.users[userIndex];
  const testInfo = data.test;

  let res;
  let params = {
    headers: {
      'User-Agent': 'k6-load-test',
    },
    jar: http.cookieJar(), // Cookie jar otomatis menghandle session Laravel
  };

  group('01. Login Flow', function () {
    // 1. Get Login Page (untuk ambil CSRF Token)
    res = http.get(`${BASE_URL}/login`, params);
    
    check(res, {
      'Login page loaded': (r) => r.status === 200,
    });

    // Extract CSRF Token dari meta tag atau hidden input
    // Regex sederhana untuk mengambil value _token
    let csrfToken = '';
    try {
        csrfToken = res.body.match(/name="_token" value="([^"]+)"/)[1];
    } catch (e) {
        console.error(`Failed to extract CSRF token on Login Page. Status: ${res.status}`);
        return;
    }

    // 2. Submit Login
    res = http.post(`${BASE_URL}/login`, {
      _token: csrfToken,
      email: user.email,
      password: user.password,
    }, params);

    check(res, {
      'Login successful': (r) => r.status === 200 || r.status === 302, // 302 redirect ke dashboard
    });
  });

  group('02. Access Exam', function () {
    // 3. Akses Halaman Briefing
    res = http.get(`${BASE_URL}/tests/${testInfo.id}`, params);
    check(res, {
      'Briefing page loaded': (r) => r.status === 200,
    });

    // Extract CSRF Token baru (kadang perlu refresh token)
    let csrfToken = '';
    try {
        csrfToken = res.body.match(/name="_token" value="([^"]+)"/)[1];
    } catch (e) {
        // Jika sudah login, kadang CSRF ada di meta tag
        try {
            csrfToken = res.body.match(/name="csrf-token" content="([^"]+)"/)[1];
        } catch (e2) {
             console.error(`Failed to extract CSRF token on Briefing Page. Status: ${res.status}`);
             return;
        }
    }

    // 4. Start Exam (Input Token)
    // Payload start
    let startPayload = { _token: csrfToken };
    if (testInfo.token) {
        startPayload.token = testInfo.token;
    }

    res = http.post(`${BASE_URL}/tests/${testInfo.id}/start`, startPayload, params);
    
    check(res, {
      'Exam started (redirect)': (r) => r.status === 302 || r.status === 200,
    });
  });

  group('03. Taking Exam', function () {
    // Simulasi menjawab beberapa soal
    // Kita ambil 5 soal pertama saja agar tidak terlalu lama
    const questionsToAnswer = testInfo.questions.slice(0, 5); 

    for (let i = 0; i < questionsToAnswer.length; i++) {
        let qId = questionsToAnswer[i];
        let qNum = i + 1;

        // Buka Halaman Soal
        res = http.get(`${BASE_URL}/tests/${testInfo.id}/question/${qNum}`, params);
        
        check(res, {
            'Question loaded': (r) => r.status === 200,
        });

        // Berpikir sejenak (1-3 detik)
        sleep(randomIntBetween(1, 3));

        // Ambil CSRF dari halaman soal
        let pageCsrf = '';
        try {
             pageCsrf = res.body.match(/name="_token" value="([^"]+)"/)[1];
        } catch(e) {
             // Coba ambil dari meta tag jika form hidden gagal
             try {
                pageCsrf = res.body.match(/name="csrf-token" content="([^"]+)"/)[1];
             } catch(e2) {
                // Skip save jika gagal ambil token (mungkin session expired/error page)
                continue; 
             }
        }
        
        res = http.post(`${BASE_URL}/tests/${testInfo.id}/question/${qNum}/save`, {
            _token: pageCsrf,
            next: '1' // Tombol Next
        }, params);

        check(res, {
            'Answer saved/Next': (r) => r.status === 302 || r.status === 200,
        });
    }
  });

  group('04. Finish Exam', function () {
    // Karena kita tidak benar-benar di soal terakhir, kita tembak route submit langsung
    // Kita perlu CSRF token valid, ambil dari page terakhir
    // (Asumsi res terakhir adalah halaman soal)
    let finalCsrf = '';
    try {
        finalCsrf = res.body.match(/name="_token" value="([^"]+)"/)[1];
    } catch(e) {
        // Fallback jika redirect, request ulang halaman soal 1 untuk dapat token
        let tempRes = http.get(`${BASE_URL}/tests/${testInfo.id}/question/1`, params);
        try {
            finalCsrf = tempRes.body.match(/name="_token" value="([^"]+)"/)[1];
        } catch(e2) {
             try {
                finalCsrf = tempRes.body.match(/name="csrf-token" content="([^"]+)"/)[1];
             } catch(e3) {
                return;
             }
        }
    }

    res = http.post(`${BASE_URL}/tests/${testInfo.id}/submit`, {
        _token: finalCsrf
    }, params);

    check(res, {
        'Exam submitted': (r) => r.status === 302 || r.status === 200,
    });
  });

  sleep(1);
}
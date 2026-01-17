<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use App\Models\Test;
use App\Models\UserTest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use App\Exports\ResultsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // --- Dashboard ---
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // --- Manajemen Soal (CRUD) ---
    // Karena soal terikat pada Test, kita lihat berdasarkan Test saja agar rapi
    public function indexTests()
    {
        $tests = Test::withCount('questions')->get();
        return view('admin.tests.index', compact('tests'));
    }

    public function storeTest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'token' => 'nullable|string|max:10',
        ]);

        $test = Test::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'token' => $request->token ? strtoupper($request->token) : null,
            'description' => 'Silakan edit deskripsi ujian ini.',
        ]);

        return redirect()->route('admin.tests.manage', $test->id)
                         ->with('success', 'Test berhasil dibuat! Silakan tambahkan soal.');
    }

    public function destroyTest(Test $test)
    {
        DB::transaction(function () use ($test) {
            foreach ($test->userTests as $userTest) {
                $userTest->answers()->delete();
                $userTest->delete();
            }
            foreach ($test->questions as $question) {
                $question->options()->delete();
                $question->delete();
            }
            $test->delete();
        });

        return back()->with('success', 'Ujian beserta seluruh data terkait berhasil dihapus.');
    }

    // --- BULK EDITOR (New Feature) ---
    public function editBulk(Test $test)
    {
        // Load soal beserta opsinya
        $test->load(['questions.options']);
        return view('admin.tests.manage', compact('test'));
    }

    public function updateToken(Request $request, Test $test)
    {
        if ($request->has('generate')) {
            $token = strtoupper(Str::random(6));
        } else {
            $request->validate([
                'token' => 'required|string|max:10',
            ]);
            $token = strtoupper($request->token);
        }

        $test->update(['token' => $token]);

        return back()->with('success', 'Token berhasil diperbarui: ' . $token);
    }

    public function storeBulk(Request $request, Test $test)
    {
        // 1. Validasi Gabungan (Metadata + Soal)
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'questions' => 'present|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.option_text' => 'required|string',
            'questions.*.correct_index' => 'required|integer', 
        ]);

        DB::transaction(function () use ($request, $test) {
            $test->update([
                'title' => $request->title,
                'description' => $request->description,
                'duration' => $request->duration,
            ]);

            $inputQuestions = $request->input('questions', []);
            $submittedQuestionIds = collect($inputQuestions)
                ->pluck('id')->filter()->toArray();

            $test->questions()->whereNotIn('id', $submittedQuestionIds)->delete();

            foreach ($inputQuestions as $qIndex => $qData) {
                $question = $test->questions()->updateOrCreate(
                    ['id' => $qData['id'] ?? null],
                    ['question_text' => $qData['question_text']]
                );

                $question->options()->delete();

                foreach ($qData['options'] as $oIndex => $oData) {
                    $question->options()->create([
                        'option_text' => $oData['option_text'],
                        'is_correct' => ($oIndex == $qData['correct_index']),
                    ]);
                }
            }
        });

        return redirect()->route('admin.tests.manage', $test->id)
                         ->with('success', 'Data ujian berhasil diperbarui!');
    }


    // --- Legacy Single Create ---
    public function createQuestion(Test $test)
    {
        return view('admin.questions.create', compact('test'));
    }

    public function storeQuestion(Request $request, Test $test)
    {
        $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option_index' => 'required|integer', 
        ]);

        $question = $test->questions()->create([
            'question_text' => $request->question_text,
        ]);

        foreach ($request->options as $index => $optionText) {
            $question->options()->create([
                'option_text' => $optionText,
                'is_correct' => $index == $request->correct_option_index,
            ]);
        }

        return redirect()->route('admin.tests.index')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroyQuestion(Question $question)
    {
        $question->delete();
        return back()->with('success', 'Soal dihapus.');
    }

    // --- List Nilai ---
    public function results(Request $request)
    {
        $sortWhitelist = [
            'user_name' => 'users.name',
            'test_title' => 'tests.title',
            'score' => 'user_tests.score',
            'completed_at' => 'user_tests.completed_at'
        ];

        $sort = $request->get('sort', 'completed_at');
        $direction = $request->get('direction', 'desc');

        if (!array_key_exists($sort, $sortWhitelist)) $sort = 'completed_at';
        if (!in_array($direction, ['asc', 'desc'])) $direction = 'desc';

        $query = UserTest::select('user_tests.*')
                    ->join('users', 'user_tests.user_id', '=', 'users.id')
                    ->join('tests', 'user_tests.test_id', '=', 'tests.id')
                    ->whereNotNull('completed_at');

        if ($request->filled('test_id')) {
            $query->where('user_tests.test_id', $request->test_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                  ->orWhere('users.email', 'like', "%$search%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('user_tests.completed_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('user_tests.completed_at', '<=', $request->end_date);
        }

        $query->orderBy($sortWhitelist[$sort], $direction);

        $results = $query->paginate(20);
        $tests = Test::all(); 
        
        return view('admin.results.index', compact('results', 'tests'));
    }

    public function bulkDestroyResults(Request $request)
    {
        $query = UserTest::query()
            ->join('users', 'user_tests.user_id', '=', 'users.id')
            ->whereNotNull('completed_at');

        // Terapkan Filter yang sama dengan index
        if ($request->filled('test_id')) {
            $query->where('user_tests.test_id', $request->test_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%$search%")
                  ->orWhere('users.email', 'like', "%$search%");
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('user_tests.completed_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('user_tests.completed_at', '<=', $request->end_date);
        }

        DB::beginTransaction();
        try {
            // Ambil ID untuk dihapus (agar bisa handle relation event/logic jika ada)
            // Atau delete langsung jika cascade sudah aman. Di sini kita loop aman.
            $records = $query->select('user_tests.id')->get();
            $count = $records->count();

            if ($count > 0) {
                // Hapus jawaban dulu (manual cascade di level app)
                $ids = $records->pluck('id')->toArray();
                \App\Models\UserTestAnswer::whereIn('user_test_id', $ids)->delete();
                
                // Hapus UserTest
                UserTest::whereIn('id', $ids)->delete();
            }

            DB::commit();
            return back()->with('success', "Berhasil menghapus $count data hasil ujian.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function exportResults(Request $request)
    {
        return Excel::download(new ResultsExport($request->start_date, $request->end_date), 'hasil_ujian.xlsx');
    }

    public function destroyResult(UserTest $userTest)
    {
        DB::transaction(function () use ($userTest) {
            $userTest->answers()->delete();
            $userTest->delete();
        });

        return back()->with('success', 'Data hasil ujian berhasil dihapus.');
    }

    // --- Manajemen User ---
    public function usersIndex(Request $request)
    {
        $query = User::query();

        // Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search User
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filter Tanggal (NEW)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function bulkDestroyUsers(Request $request)
    {
        $query = User::query();

        // Terapkan Filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // IMPORTANT: Jangan hapus diri sendiri (Admin yg sedang login)
        $query->where('id', '!=', Auth::id());

        DB::beginTransaction();
        try {
            $count = $query->count();
            
            // Delete (Cascade DB atau Model event biasanya menangani relasi,
            // tapi kita bisa delete via Eloquent agar event trigger)
            // Untuk performa ribuan data, delete() builder lebih cepat, tapi relasi
            // harus di-handle database (ON DELETE CASCADE) atau kita handle manual.
            // Di TestController destroyTest kita manual. Mari kita manual userTests juga.
            
            $users = $query->get();
            foreach ($users as $user) {
                // Hapus UserTest & Answers (via relation di UserTest)
                foreach ($user->userTests as $ut) {
                    $ut->answers()->delete();
                    $ut->delete();
                }
                $user->delete();
            }

            DB::commit();
            return back()->with('success', "Berhasil menghapus $count pengguna.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:admin,peserta'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'User berhasil dibuat.');
    }
}

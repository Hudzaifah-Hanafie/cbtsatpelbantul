<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Test;
use App\Models\UserTest;
use App\Services\ExamService;

class LoadTestController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        // Safety: Only allow in local/testing
        abort_unless(app()->environment('local', 'testing'), 403, 'Load testing endpoints are disabled in production.');
        
        $this->examService = $examService;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json([
                'message' => 'Login successful',
                'user' => Auth::user(),
                'session_id' => session()->getId() // Debugging info
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function start(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'token' => 'nullable|string'
        ]);

        $test = Test::findOrFail($request->test_id);
        $user = Auth::user();

        if ($test->token) {
            $inputToken = strtoupper($request->input('token'));
            if ($inputToken !== $test->token) {
                return response()->json(['message' => 'Invalid Token'], 403);
            }
        }

        $userTest = $this->examService->startExam($user, $test);

        return response()->json([
            'message' => 'Exam started',
            'user_test_id' => $userTest->id
        ]);
    }

    public function saveAnswer(Request $request)
    {
        $request->validate([
            'user_test_id' => 'required|exists:user_tests,id', // Di real world, ambil dari session/auth user_test aktif
            'question_id' => 'required|integer',
            'option_id' => 'required|integer'
        ]);

        $userTest = UserTest::findOrFail($request->user_test_id);
        
        // Ensure user owns this test
        if ($userTest->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized access to this test session'], 403);
        }

        $this->examService->saveAnswer($userTest, $request->question_id, $request->option_id);

        return response()->json(['message' => 'Answer saved']);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'user_test_id' => 'required|exists:user_tests,id',
        ]);

        $userTest = UserTest::findOrFail($request->user_test_id);

        if ($userTest->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->examService->submitExam($userTest);

        return response()->json(['message' => 'Exam submitted', 'score' => $userTest->score]);
    }
}

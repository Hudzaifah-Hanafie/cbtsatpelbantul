<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\UserTest;
use App\Models\UserTestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ExamService;

class TestController extends Controller
{
    protected $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index()
    {
        $tests = Test::all();
        return view('tests.index', compact('tests'));
    }

    public function briefing(Test $test)
    {
        $user = Auth::user();
        
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNotNull('completed_at')
                            ->first();
                            
        $isCompleted = !is_null($userTest);
        
        // Pass data skor & waktu jika selesai
        $scoreData = null;
        if ($isCompleted) {
            $totalQuestions = $test->questions()->count();
            $scoreData = [
                'score' => $userTest->score,
                'total' => $totalQuestions,
                'completed_at' => $userTest->completed_at
            ];
        }

        return view('tests.briefing', compact('test', 'isCompleted', 'scoreData'));
    }

    public function start(Request $request, Test $test)
    {
        $user = Auth::user();

        // Validasi Token
        if ($test->token) {
            $inputToken = strtoupper($request->input('token'));
            if ($inputToken !== $test->token) {
                return redirect()->back()->with('error', 'Token ujian salah! Silakan coba lagi.');
            }
        }

        // Use Service
        $this->examService->startExam($user, $test);

        return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => 1])
                         ->with('exam_start', 'Selamat mengerjakan ujian! Waktu Anda dimulai sekarang.');
    }

    public function showQuestion(Test $test, $questionNumber)
    {
        $user = Auth::user();
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNull('completed_at')
                            ->firstOrFail();

        $questions = $test->questions()->with('options')->get()->sortBy('id')->values();

        if ($questionNumber < 1 || $questionNumber > $questions->count()) {
            abort(404);
        }

        $currentQuestion = $questions->get($questionNumber - 1);
        $totalQuestions = $questions->count();

        $existingAnswer = UserTestAnswer::where('user_test_id', $userTest->id)
                                        ->where('question_id', $currentQuestion->id)
                                        ->first();

        $answeredQuestions = UserTestAnswer::where('user_test_id', $userTest->id)
                                        ->pluck('question_id')
                                        ->toArray();

        $endTime = $userTest->started_at->addMinutes($test->duration);
        $remainingSeconds = (int) now()->diffInSeconds($endTime, false);

        return view('tests.show', compact(
            'test', 
            'userTest', 
            'questions', 
            'currentQuestion', 
            'questionNumber', 
            'totalQuestions', 
            'existingAnswer',
            'answeredQuestions',
            'remainingSeconds'
        ));
    }

    public function saveAnswer(Request $request, Test $test, $questionNumber)
    {
        $user = Auth::user();
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNull('completed_at')
                            ->firstOrFail();

        $questions = $test->questions()->get()->sortBy('id')->values();
        $currentQuestion = $questions->get($questionNumber - 1);

        if (!$currentQuestion) {
            abort(404);
        }

        $selectedOptionId = $request->input('question_' . $currentQuestion->id);

        if ($selectedOptionId) {
            // Use Service
            $this->examService->saveAnswer($userTest, $currentQuestion->id, $selectedOptionId);
        }

        // Determine redirection logic (View logic stays in controller)
        if ($request->has('jump_to')) {
            $jumpTo = (int) $request->input('jump_to');
            if ($jumpTo >= 1 && $jumpTo <= $questions->count()) {
                return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => $jumpTo]);
            }
        }

        if ($request->has('next')) {
            $nextQuestion = $questionNumber + 1;
            if ($nextQuestion > $questions->count()) {
                 return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => $questionNumber]);
            }
             return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => $nextQuestion]);
        } elseif ($request->has('previous')) {
            $prevQuestion = $questionNumber - 1;
             return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => $prevQuestion]);
        } elseif ($request->has('finish')) {
             return $this->submit($request, $test);
        }

        return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => $questionNumber]);
    }


    public function submit(Request $request, Test $test)
    {
        $user = Auth::user();
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNull('completed_at')
                            ->firstOrFail();

        // Use Service
        $this->examService->submitExam($userTest);

        return redirect()->route('tests.briefing', $test->id)
                         ->with('success', 'Ujian berhasil dikumpulkan. Terima kasih!');
    }

    public function results(Test $test)
    {
        $user = Auth::user();
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNotNull('completed_at')
                            ->firstOrFail();

        $totalQuestions = $test->questions()->count();

        return view('tests.results', compact('test', 'userTest', 'totalQuestions'));
    }
}

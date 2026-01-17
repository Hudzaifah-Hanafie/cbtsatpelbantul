<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\UserTest;
use App\Models\UserTestAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::all();
        return view('tests.index', compact('tests'));
    }

    public function briefing(Test $test)
    {
        $user = Auth::user();
        
        // Cek apakah user sudah pernah menyelesaikan test ini
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNotNull('completed_at')
                            ->first();
                            
        $isCompleted = !is_null($userTest);

        return view('tests.briefing', compact('test', 'isCompleted'));
    }

    public function start(Request $request, Test $test)
    {
        $user = Auth::user();

        // 1. Validasi Token (Jika test memiliki token)
        if ($test->token) {
            $inputToken = strtoupper($request->input('token'));
            if ($inputToken !== $test->token) {
                return redirect()->back()->with('error', 'Token ujian salah! Silakan coba lagi.');
            }
        }

        // Check if the user has already started this test and it's not completed
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNull('completed_at')
                            ->first();

        if (!$userTest) {
            $userTest = UserTest::create([
                'user_id' => $user->id,
                'test_id' => $test->id,
                'started_at' => now(),
            ]);
        }

        return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => 1]);
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

        // Retrieve existing answer if any
        $existingAnswer = UserTestAnswer::where('user_test_id', $userTest->id)
                                        ->where('question_id', $currentQuestion->id)
                                        ->first();

        // 1. Get list of answered question IDs for the Navigation Grid
        $answeredQuestions = UserTestAnswer::where('user_test_id', $userTest->id)
                                        ->pluck('question_id')
                                        ->toArray();

        // 2. Calculate remaining seconds for the Timer
        $endTime = $userTest->started_at->addMinutes($test->duration);
        $remainingSeconds = (int) now()->diffInSeconds($endTime, false);

        // Pass $questions (all questions) to view for the Grid
        return view('tests.show', compact(
            'test', 
            'userTest', 
            'questions', // Passing all questions for the grid
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
            UserTestAnswer::updateOrCreate(
                [
                    'user_test_id' => $userTest->id,
                    'question_id' => $currentQuestion->id,
                ],
                [
                    'option_id' => $selectedOptionId,
                ]
            );
        }

        // Determine redirection
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

        // Default fall back
        return redirect()->route('tests.showQuestion', ['test' => $test->id, 'questionNumber' => $questionNumber]);
    }


    public function submit(Request $request, Test $test)
    {
        $user = Auth::user();
        $userTest = UserTest::where('user_id', $user->id)
                            ->where('test_id', $test->id)
                            ->whereNull('completed_at')
                            ->firstOrFail();

        // Prevent resubmission
        if ($userTest->completed_at) {
            return redirect()->route('tests.results', $test->id);
        }

        // Calculate score based on stored answers
        $score = 0;
        $questions = $test->questions()->with('options')->get();
        $userAnswers = UserTestAnswer::where('user_test_id', $userTest->id)->get()->keyBy('question_id');

        foreach ($questions as $question) {
            if (isset($userAnswers[$question->id])) {
                $selectedOption = $question->options->find($userAnswers[$question->id]->option_id);
                if ($selectedOption && $selectedOption->is_correct) {
                    $score++;
                }
            }
        }

        $userTest->score = $score;
        $userTest->completed_at = now();
        $userTest->save();

        return redirect()->route('tests.results', $test->id);
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

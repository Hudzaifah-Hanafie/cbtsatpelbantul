<?php

namespace App\Services;

use App\Models\Test;
use App\Models\User;
use App\Models\UserTest;
use App\Models\UserTestAnswer;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function startExam(User $user, Test $test)
    {
        // Cek apakah sudah ada sesi ujian yang belum selesai
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

        return $userTest;
    }

    public function saveAnswer(UserTest $userTest, int $questionId, int $optionId)
    {
        // Validasi sederhana: pastikan question milik test tersebut (optional, demi performa bisa skip query ini jika yakin)
        // Disini kita langsung update/create
        return UserTestAnswer::updateOrCreate(
            [
                'user_test_id' => $userTest->id,
                'question_id' => $questionId,
            ],
            [
                'option_id' => $optionId,
            ]
        );
    }

    public function submitExam(UserTest $userTest)
    {
        if ($userTest->completed_at) {
            return $userTest;
        }

        $test = $userTest->test;
        
        // Eager load questions options untuk scoring
        $questions = $test->questions()->with('options')->get();
        $userAnswers = $userTest->answers->keyBy('question_id');

        $score = 0;

        foreach ($questions as $question) {
            if (isset($userAnswers[$question->id])) {
                $selectedOption = $question->options->find($userAnswers[$question->id]->option_id);
                if ($selectedOption && $selectedOption->is_correct) {
                    $score++;
                }
            }
        }

        $userTest->update([
            'score' => $score,
            'completed_at' => now(),
        ]);

        return $userTest;
    }
}

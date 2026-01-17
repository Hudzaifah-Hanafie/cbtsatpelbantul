<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Test;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $test1 = Test::where('title', 'General Knowledge Test')->first();
        $test2 = Test::where('title', 'Laravel Basics')->first();

        if ($test1) {
            Question::create([
                'test_id' => $test1->id,
                'question_text' => 'What is the capital of France?',
            ]);
            Question::create([
                'test_id' => $test1->id,
                'question_text' => 'What is 2 + 2?',
            ]);
            Question::create([
                'test_id' => $test1->id,
                'question_text' => 'Which planet is known as the Red Planet?',
            ]);
        }

        if ($test2) {
            Question::create([
                'test_id' => $test2->id,
                'question_text' => 'Which command is used to create a new Laravel project?',
            ]);
            Question::create([
                'test_id' => $test2->id,
                'question_text' => 'What is Eloquent?',
            ]);
        }
    }
}

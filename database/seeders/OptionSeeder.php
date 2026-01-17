<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General Knowledge Test Questions
        $q1 = Question::where('question_text', 'What is the capital of France?')->first();
        if ($q1) {
            Option::create(['question_id' => $q1->id, 'option_text' => 'Berlin', 'is_correct' => false]);
            Option::create(['question_id' => $q1->id, 'option_text' => 'Madrid', 'is_correct' => false]);
            Option::create(['question_id' => $q1->id, 'option_text' => 'Paris', 'is_correct' => true]);
            Option::create(['question_id' => $q1->id, 'option_text' => 'Rome', 'is_correct' => false]);
        }

        $q2 = Question::where('question_text', 'What is 2 + 2?')->first();
        if ($q2) {
            Option::create(['question_id' => $q2->id, 'option_text' => '3', 'is_correct' => false]);
            Option::create(['question_id' => $q2->id, 'option_text' => '4', 'is_correct' => true]);
            Option::create(['question_id' => $q2->id, 'option_text' => '5', 'is_correct' => false]);
            Option::create(['question_id' => $q2->id, 'option_text' => '6', 'is_correct' => false]);
        }

        $q3 = Question::where('question_text', 'Which planet is known as the Red Planet?')->first();
        if ($q3) {
            Option::create(['question_id' => $q3->id, 'option_text' => 'Earth', 'is_correct' => false]);
            Option::create(['question_id' => $q3->id, 'option_text' => 'Mars', 'is_correct' => true]);
            Option::create(['question_id' => $q3->id, 'option_text' => 'Jupiter', 'is_correct' => false]);
            Option::create(['question_id' => $q3->id, 'option_text' => 'Venus', 'is_correct' => false]);
        }

        // Laravel Basics Test Questions
        $q4 = Question::where('question_text', 'Which command is used to create a new Laravel project?')->first();
        if ($q4) {
            Option::create(['question_id' => $q4->id, 'option_text' => 'laravel new project-name', 'is_correct' => true]);
            Option::create(['question_id' => $q4->id, 'option_text' => 'composer create-project laravel/laravel project-name', 'is_correct' => false]);
            Option::create(['question_id' => $q4->id, 'option_text' => 'php artisan make:project project-name', 'is_correct' => false]);
            Option::create(['question_id' => $q4->id, 'option_text' => 'npm create-laravel-app project-name', 'is_correct' => false]);
        }

        $q5 = Question::where('question_text', 'What is Eloquent?')->first();
        if ($q5) {
            Option::create(['question_id' => $q5->id, 'option_text' => 'A CSS framework', 'is_correct' => false]);
            Option::create(['question_id' => $q5->id, 'option_text' => 'Laravel\'s ORM', 'is_correct' => true]);
            Option::create(['question_id' => $q5->id, 'option_text' => 'A JavaScript library', 'is_correct' => false]);
            Option::create(['question_id' => $q5->id, 'option_text' => 'A testing framework', 'is_correct' => false]);
        }
    }
}

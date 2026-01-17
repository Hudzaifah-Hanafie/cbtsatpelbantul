<?php

namespace Database\Seeders;

use App\Models\Test;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Test::create([
            'title' => 'General Knowledge Test',
            'description' => 'A test to assess your general knowledge across various subjects. This test contains 5 questions and has a duration of 10 minutes. Read each question carefully and select the best answer. Good luck!',
            'duration' => 10, // minutes
        ]);

        Test::create([
            'title' => 'Laravel Basics',
            'description' => 'This test covers fundamental concepts of Laravel framework. This test contains 3 questions and has a duration of 5 minutes.',
            'duration' => 5, // minutes
        ]);
    }
}

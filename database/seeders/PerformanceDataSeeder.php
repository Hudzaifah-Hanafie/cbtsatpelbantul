<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Test;
use App\Models\UserTest;
use App\Models\UserTestAnswer;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PerformanceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Pastikan ada Test dan Soal
        $tests = Test::with('questions.options')->get();
        if ($tests->isEmpty()) {
            $this->command->warn('Tidak ada data Test. Silakan buat Test dan Soal terlebih dahulu lewat Admin.');
            return;
        }

        $this->command->info('Memulai generate dummy data...');

        // 2. Generate Dummy Users (Peserta) - Target total 50 user peserta
        $currentPesertaCount = User::where('role', 'peserta')->count();
        $targetUserCount = 50;
        
        if ($currentPesertaCount < $targetUserCount) {
            $needed = $targetUserCount - $currentPesertaCount;
            $this->command->info("Membuat $needed user peserta baru...");
            User::factory($needed)->create([
                'role' => 'peserta',
                'password' => bcrypt('password'), // Password default
            ]);
        }

        $users = User::where('role', 'peserta')->get();

        // 3. Generate User Tests & Answers
        $this->command->info('Generate hasil ujian dan jawaban...');
        
        $userTestsCount = 0;

        foreach ($users as $user) {
            foreach ($tests as $test) {
                // Skip jika soal kosong
                if ($test->questions->isEmpty()) continue;

                // 70% kemungkinan user mengerjakan ujian ini (biar data variatif)
                if (rand(1, 100) <= 70) {
                    
                    // Random tanggal dalam 3 bulan terakhir
                    $startedAt = $faker->dateTimeBetween('-3 months', 'now');
                    // Selesai dalam durasi ujian (minus random 1-10 menit biar realistik)
                    $completedAt = (clone $startedAt)->modify('+' . rand($test->duration - 10, $test->duration) . ' minutes');

                    // Buat Record UserTest
                    $userTest = UserTest::create([
                        'user_id' => $user->id,
                        'test_id' => $test->id,
                        'score' => 0, // Hitung nanti
                        'started_at' => $startedAt,
                        'completed_at' => $completedAt,
                    ]);

                    $score = 0;
                    $answersData = [];

                    // Loop Soal untuk membuat jawaban
                    foreach ($test->questions as $question) {
                        if ($question->options->isNotEmpty()) {
                            // Pilih satu opsi secara acak dari opsi yang TERSEDIA di DB
                            // Ini menjamin referential integrity
                            $selectedOption = $question->options->random();

                            $answersData[] = [
                                'user_test_id' => $userTest->id,
                                'question_id' => $question->id,
                                'option_id' => $selectedOption->id,
                                'created_at' => $completedAt,
                                'updated_at' => $completedAt,
                            ];

                            // Cek jawaban benar
                            if ($selectedOption->is_correct) {
                                $score++;
                            }
                        }
                    }

                    // Bulk insert jawaban untuk performa
                    if (!empty($answersData)) {
                        UserTestAnswer::insert($answersData);
                    }

                    // Update skor akhir
                    $userTest->update(['score' => $score]);
                    $userTestsCount++;
                }
            }
        }

        $this->command->info("Selesai! Berhasil membuat data dummy untuk $userTestsCount ujian.");
    }
}

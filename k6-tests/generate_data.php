<?php

use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Test;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// 1. Ambil User Peserta
$users = User::where('role', 'peserta')->take(50)->get()->map(function ($user) {
    return [
        'email' => $user->email,
        'password' => 'password', // Default password dari seeder
    ];
});

// 2. Ambil Satu Test untuk diuji
$test = Test::with('questions')->whereHas('questions')->first();

if (!$test) {
    echo "Error: Tidak ada data test dengan soal. Jalankan seeder dulu.\n";
    exit(1);
}

$data = [
    'users' => $users,
    'test' => [
        'id' => $test->id,
        'token' => $test->token,
        'duration' => $test->duration,
        'questions_count' => $test->questions->count(),
        'questions' => $test->questions->pluck('id')->toArray()
    ]
];

// Simpan ke JSON
$jsonPath = __DIR__ . '/users.json';
File::put($jsonPath, json_encode($data, JSON_PRETTY_PRINT));

echo "Data berhasil digenerate ke: " . $jsonPath . "\n";
echo "Test ID: " . $test->id . "\n";
echo "Token: " . ($test->token ?? 'NONE') . "\n";
echo "Jumlah User: " . $users->count() . "\n";


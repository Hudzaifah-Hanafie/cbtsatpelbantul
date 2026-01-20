<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\LoadTestController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

// --- TRAFFIC CONTROLLER DASHBOARD ---
Route::get('/dashboard', function () {
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// --- AREA PESERTA ---
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CBT Routes
    Route::get('/tests', [TestController::class, 'index'])->name('tests.index');
    Route::get('/tests/{test}', [TestController::class, 'briefing'])->name('tests.briefing');
    Route::post('/tests/{test}/start', [TestController::class, 'start'])->name('tests.start');
    Route::get('/tests/{test}/question/{questionNumber}', [TestController::class, 'showQuestion'])->name('tests.showQuestion');
    Route::post('/tests/{test}/question/{questionNumber}/save', [TestController::class, 'saveAnswer'])->name('tests.saveAnswer');
    Route::post('/tests/{test}/submit', [TestController::class, 'submit'])->name('tests.submit');
    Route::get('/tests/{test}/results', [TestController::class, 'results'])->name('tests.results');
});


// --- AREA ADMIN ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // CRUD Soal (Via Tests)
    Route::get('/tests', [AdminController::class, 'indexTests'])->name('tests.index');
    Route::post('/tests', [AdminController::class, 'storeTest'])->name('tests.store');
    Route::delete('/tests/{test}', [AdminController::class, 'destroyTest'])->name('tests.destroy'); 
    
    // Bulk Editor Routes (NEW)
    Route::get('/tests/{test}/manage', [AdminController::class, 'editBulk'])->name('tests.manage');
    Route::put('/tests/{test}/manage', [AdminController::class, 'storeBulk'])->name('tests.update_bulk');
    // Update Token Route
    Route::patch('/tests/{test}/token', [AdminController::class, 'updateToken'])->name('tests.update_token');

    // Single Create (Optional/Legacy)
    Route::get('/tests/{test}/questions/create', [AdminController::class, 'createQuestion'])->name('questions.create');
    Route::post('/tests/{test}/questions', [AdminController::class, 'storeQuestion'])->name('questions.store');
    Route::delete('/questions/{question}', [AdminController::class, 'destroyQuestion'])->name('questions.destroy');

    // List Nilai
    Route::get('/results', [AdminController::class, 'results'])->name('results.index');
    Route::get('/results/export', [AdminController::class, 'exportResults'])->name('results.export'); 
    // BULK DELETE RESULTS (Harus sebelum /{id})
    Route::delete('/results/bulk-destroy', [AdminController::class, 'bulkDestroyResults'])->name('results.bulk_destroy');
    Route::delete('/results/{userTest}', [AdminController::class, 'destroyResult'])->name('results.destroy');

    // Manajemen User
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::post('/users', [AdminController::class, 'userStore'])->name('users.store');
    // BULK DELETE USERS (Harus sebelum /{id})
    Route::delete('/users/bulk-destroy', [AdminController::class, 'bulkDestroyUsers'])->name('users.bulk_destroy');
});

// --- LOAD TESTING ROUTES (API ONLY, NO CSRF) ---
// Menggunakan middleware 'web' agar session/cookies tetap jalan (untuk Redis Session Test)
// CSRF dimatikan via bootstrap/app.php
Route::group(['prefix' => 'load-test', 'middleware' => ['web']], function () {
    Route::post('/login', [LoadTestController::class, 'login']);
    
    Route::middleware('auth')->group(function () {
        Route::post('/start', [LoadTestController::class, 'start']);
        Route::post('/answer', [LoadTestController::class, 'saveAnswer']);
        Route::post('/submit', [LoadTestController::class, 'submit']);
    });
});

require __DIR__.'/auth.php';

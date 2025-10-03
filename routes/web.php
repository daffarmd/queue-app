<?php

use App\Http\Controllers\DisplayHandler;
use App\Http\Controllers\QueueHandler;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [DisplayHandler::class, 'index'])->name('display');
Route::get('/display', [DisplayHandler::class, 'index'])->name('display.index');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard - redirect based on role
    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->hasRole('Admin')) {
            return redirect('/admin');
        } elseif ($user->hasAnyRole(['Staff', 'Doctor'])) {
            return redirect()->route('staff.dashboard');
        } else {
            return redirect()->route('display');
        }
    })->name('dashboard');

    // Staff dashboard
    Route::get('/staff', \App\Livewire\StaffDashboard::class)
        ->middleware('role:Admin|Staff')
        ->name('staff.dashboard');

    // Queue management routes
    Route::prefix('queues')->name('queues.')->middleware('role:Admin|Staff')->group(function () {
        Route::post('/', [QueueHandler::class, 'store'])->name('store');
        Route::patch('/{queue}/call', [QueueHandler::class, 'call'])->name('call');
        Route::patch('/{queue}/finish', [QueueHandler::class, 'finish'])->name('finish');
        Route::patch('/{queue}/skip', [QueueHandler::class, 'skip'])->name('skip');
        Route::patch('/{queue}/recall', [QueueHandler::class, 'recall'])->name('recall');
    });

    // Profile
    Route::view('profile', 'profile')->name('profile');
});

// TTS routes
Route::prefix('tts')->name('tts.')->group(function () {
    Route::get('/audio', [App\Http\Controllers\TTSController::class, 'generateAudio'])->name('audio');
    Route::get('/queue', [App\Http\Controllers\TTSController::class, 'queueAnnouncement'])->name('queue');
    Route::get('/health', [App\Http\Controllers\TTSController::class, 'health'])->name('health');
    Route::get('/test', [App\Http\Controllers\TTSController::class, 'test'])->name('test');
    Route::post('/custom', [App\Http\Controllers\TTSController::class, 'customAnnouncement'])->name('custom');

    // Instant streaming routes (no caching, direct from Piper)
    Route::get('/instant', [App\Http\Controllers\InstantTTSController::class, 'instantStream'])->name('instant');
    Route::get('/instant-queue', [App\Http\Controllers\InstantTTSController::class, 'instantQueue'])->name('instant.queue');
    Route::get('/data-uri', [App\Http\Controllers\InstantTTSController::class, 'dataUri'])->name('data.uri');
    Route::get('/stream', [App\Http\Controllers\InstantTTSController::class, 'streamChunked'])->name('stream');
});

// TTS Demo page
Route::get('/tts-demo', function () {
    return view('tts-demo');
})->name('tts.demo');

require __DIR__.'/auth.php';

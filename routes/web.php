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

require __DIR__.'/auth.php';

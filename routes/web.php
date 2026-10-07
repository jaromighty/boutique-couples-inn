<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\SuiteController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::get('/suites', [SuiteController::class, 'index'])->name('suites.index');
    Route::get('/suites/{suite_slug}', [SuiteController::class, 'show'])->name('suites.show');

    // Booking Flow
    // 1. Check Availability (Returns JSON for the calendar UI)
    Route::get('/suites/{suite_slug}/availability', [BookingController::class, 'checkAvailability'])
        ->name('bookings.check');

    // 2. Create a Pending Hold (Locks the room for 15 minutes)
    Route::post('/suites/{suite_slug}/hold', [BookingController::class, 'createHold'])
        ->name('bookings.hold');

    // 3. Confirm/Complete Booking (Payment processing happens here later)
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])
        ->name('bookings.confirm');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';

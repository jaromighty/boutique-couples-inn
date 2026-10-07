<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SuiteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Home
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Calendar & Bookings
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::post('/bookings/{booking}/release', [BookingController::class, 'releaseHold'])->name('bookings.release');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Suite Management
    Route::resource('suites', SuiteController::class);
});

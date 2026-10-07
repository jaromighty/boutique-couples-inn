<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Suite;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SuiteController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Suite::active()->with('bookings');

        // Filter by Tier
        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        // Filter by Theme
        if ($request->filled('theme')) {
            $query->where('theme_slug', $request->theme);
        }

        $suites = $query->get();

        // If dates are provided, calculate availability and "hold" status
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;

            $suites->each(function ($suite) {
                $conflicts = $suite->bookings()
                    ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Pending])
                    // ... overlap logic ...
                    ->first();

                if ($conflicts) {
                    $suite->availability_status = $conflicts->status->value; // Get raw string for JS
                    $suite->is_available = false;

                    if ($conflicts->status === BookingStatus::Pending) {
                        $suite->hold_expires_at = $conflicts->expires_at->toIso8601String();
                    }
                } else {
                    $suite->availability_status = 'available';
                    $suite->is_available = true;
                    $suite->hold_expires_at = null;
                }
            });
        } else {
            // If no dates selected, just mark as generally available
            $suites->each(function ($suite) {
                $suite->availability_status = 'unknown';
                $suite->is_available = true;
                $suite->hold_expires_at = null;
            });
        }

        return Inertia::render('suites/index', [
            'suites' => $suites,
            'filters' => $request->only(['tier', 'theme']),
        ]);
    }
}

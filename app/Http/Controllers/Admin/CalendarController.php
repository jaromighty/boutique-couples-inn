<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start', Carbon::now()->startOfMonth());
        $end = $request->input('end', Carbon::now()->endOfMonth());

        $bookings = Booking::with('suite')
            ->whereBetween('check_in', [$start, $end])
            ->orWhereBetween('check_out', [$start, $end])
            ->orWhere(function ($q) use ($start, $end) {
                $q->where('check_in', '<=', $start)->where('check_out', '>=', $end);
            })
            ->get();

        // Transform for FullCalendar or similar
        $events = $bookings->map(function ($booking) {
            $color = match ($booking->status->value) {
                'confirmed' => '#28a745', // Green
                'pending' => '#fd7e14',   // Orange
                'cancelled' => '#6c757d', // Gray
                'expired' => '#dc3545',   // Red
                default => '#007bff'
            };

            return [
                'id' => $booking->id,
                'title' => "{$booking->suite->name} ({$booking->status->label()})",
                'start' => $booking->check_in->toIso8601String(),
                'end' => $booking->check_out->toIso8601String(),
                'extendedProps' => [
                    'status' => $booking->status->value,
                    'guest' => $booking->user->name ?? $booking->guest_email ?? 'Guest',
                    'price' => $booking->total_price,
                ],
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        });

        return Inertia::render('admin/calendar', [
            'events' => $events,
            'currentMonth' => $start->format('Y-m'),
        ]);
    }
}

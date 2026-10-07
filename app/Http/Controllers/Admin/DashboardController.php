<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $today = Carbon::today();

        // Metrics
        $activeHolds = Booking::where('status', BookingStatus::Pending)->count();
        $confirmedToday = Booking::where('status', BookingStatus::Confirmed)
            ->whereDate('check_in', $today)->count();
        $totalRevenue = Booking::where('status', BookingStatus::Confirmed)
            ->sum('total_price');

        // Upcoming Check-ins (Next 7 days)
        $upcomingCheckIns = Booking::where('status', BookingStatus::Confirmed)
            ->whereBetween('check_in', [$today, $today->copy()->addDays(7)])
            ->with('suite')
            ->orderBy('check_in')
            ->get();

        // Recent Activity (Last 5 bookings)
        $recentBookings = Booking::with('suite')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('admin/dashboard', [
            'metrics' => [
                'active_holds' => $activeHolds,
                'confirmed_today' => $confirmedToday,
                'total_revenue' => $totalRevenue,
            ],
            'upcoming_checkins' => $upcomingCheckIns,
            'recent_bookings' => $recentBookings,
        ]);
    }
}

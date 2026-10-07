<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getConversionFunnel(): array
    {
        $views = 0; // Needs tracking (e.g., log view events)
        $holds = Booking::where('status', 'pending')->count();
        $confirmed = Booking::where('status', 'confirmed')->count();

        return [
            'holds' => $holds,
            'confirmed' => $confirmed,
            'conversion_rate' => $holds > 0 ? ($confirmed / $holds) * 100 : 0,
        ];
    }

    public function getPopularThemes(): array
    {
        return Booking::where('status', 'confirmed')
            ->join('suites', 'bookings.suite_id', '=', 'suites.id')
            ->select('suites.theme_slug', DB::raw('count(*) as total'))
            ->groupBy('suites.theme_slug')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }
}

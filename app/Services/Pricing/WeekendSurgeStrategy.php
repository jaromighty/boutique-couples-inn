<?php

namespace App\Services\Pricing;

use App\Contracts\PricingStrategy;
use App\Models\Booking;

class WeekendSurgeStrategy implements PricingStrategy
{
    public function calculate(Booking $booking): float
    {
        $surcharge = 0;
        for ($i = 0; $i < $booking->nights; $i++) {
            $date = $booking->check_in->copy()->addDays($i);
            if ($date->isWeekend()) {
                $surcharge += $booking->suite->base_price * 0.20; // 20% extra
            }
        }
        return $surcharge;
    }
}

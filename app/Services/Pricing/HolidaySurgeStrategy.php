<?php

namespace App\Services\Pricing;

use App\Contracts\PricingStrategy;
use App\Models\Booking;

class HolidaySurgeStrategy implements PricingStrategy
{
    protected array $holidays = ['2024-02-14', '2024-12-31']; // Load from DB in real app

    public function calculate(Booking $booking): float
    {
        $surcharge = 0;
        for ($i = 0; $i < $booking->nights; $i++) {
            $date = $booking->check_in->copy()->addDays($i);
            if (in_array($date->format('Y-m-d'), $this->holidays)) {
                $surcharge += $booking->suite->base_price * 0.50; // 50% extra
            }
        }
        return $surcharge;
    }
}

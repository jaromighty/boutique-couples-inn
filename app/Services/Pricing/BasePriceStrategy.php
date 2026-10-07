<?php

namespace App\Services\Pricing;

use App\Contracts\PricingStrategy;
use App\Models\Booking;

class BasePriceStrategy implements PricingStrategy
{
    public function calculate(Booking $booking): float
    {
        return $booking->suite->base_price * $booking->nights;
    }
}

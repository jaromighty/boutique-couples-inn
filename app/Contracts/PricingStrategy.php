<?php

namespace App\Contracts;

use App\Models\Booking;

interface PricingStrategy
{
    public function calculate(Booking $booking): float;
}

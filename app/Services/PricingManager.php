<?php

namespace App\Services;

use App\Contracts\PricingStrategy;
use App\Models\Booking;

class PricingManager
{
    protected array $strategies = [];

    public function register(string $name, PricingStrategy $strategy): self
    {
        $this->strategies[$name] = $strategy;

        return $this;
    }

    public function calculate(Booking $booking): float
    {
        $total = 0;
        foreach ($this->strategies as $strategy) {
            $total += $strategy->calculate($booking);
        }

        return $total;
    }
}

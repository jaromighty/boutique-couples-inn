<?php

namespace App\Services;

use App\Contracts\PaymentGatewayContract;
use App\Services\Payments\MockGateway;
use App\Services\Payments\StripeGateway;
use InvalidArgumentException;

class PaymentManager
{
    protected string $driver;

    public function __construct()
    {
        $this->driver = config('payments.driver', 'stripe');
    }

    public function driver(): PaymentGatewayContract
    {
        return match ($this->driver) {
            'stripe' => new StripeGateway,
            'mock' => new MockGateway,
            default => throw new InvalidArgumentException("Unsupported payment driver: {$this->driver}"),
        };
    }
}

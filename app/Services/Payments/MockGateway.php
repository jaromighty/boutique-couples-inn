<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayContract;
use App\Models\Booking;

class MockGateway implements PaymentGatewayContract
{
    public function createIntent(Booking $booking): array
    {
        // Simulate a delay and return a fake secret
        sleep(1);

        return [
            'client_secret' => 'mock_secret_'.$booking->id,
            'gateway_response' => ['status' => 'requires_payment_method'],
        ];
    }

    public function verifyPayment(string $intentId, Booking $booking): bool
    {
        // Simulate success
        return true;
    }

    public function refund(string $transactionId): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'Mock Gateway (Test Mode)';
    }
}

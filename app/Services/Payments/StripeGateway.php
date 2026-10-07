<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayContract;
use App\Models\Booking;

class StripeGateway implements PaymentGatewayContract
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createIntent(Booking $booking): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $booking->total_price * 100, // Stripe expects cents
                'currency' => 'usd',
                'metadata' => [
                    'booking_id' => $booking->id,
                    'suite_name' => $booking->suite->name,
                ],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'gateway_response' => $paymentIntent,
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Stripe initialization failed: " . $e->getMessage());
        }
    }

    public function verifyPayment(string $intentId, Booking $booking): bool
    {
        try {
            $intent = PaymentIntent::retrieve($intentId);
            return $intent->status === 'succeeded';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refund(string $transactionId): bool
    {
        // Implementation for refunds
        return true;
    }

    public function getName(): string
    {
        return 'Stripe';
    }
}

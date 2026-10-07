<?php

namespace App\Contracts;

use App\Models\Booking;

interface PaymentGatewayContract
{
    /**
     * Initialize a payment intent/session for a booking.
     * Returns a client-side secret or redirect URL.
     */
    public function createIntent(Booking $booking): array;

    /**
     * Verify if a payment is successful based on a webhook event or client confirmation ID.
     */
    public function verifyPayment(string $intentId, Booking $booking): bool;

    /**
     * Refund a payment (optional but recommended).
     */
    public function refund(string $transactionId): bool;

    /**
     * Get the human-readable name of the gateway (e.g., "Stripe", "PayPal").
     */
    public function getName(): string;
}

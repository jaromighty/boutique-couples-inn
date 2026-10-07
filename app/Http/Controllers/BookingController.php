<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Events\SuiteHoldCreated;
use App\Http\Requests\CreateBookingHoldRequest;
use App\Mail\BookingConfirmed;
use App\Mail\BookingHoldCreated;
use App\Models\Booking;
use App\Models\Suite;
use App\Services\BookingLockingService;
use App\Services\PaymentManager;
use App\Services\Pricing\HolidaySurgeStrategy;
use App\Services\PricingManager;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Check availability for a specific suite and return blocked dates.
     * Used by the frontend calendar to gray out unavailable days.
     */
    public function checkAvailability(Request $request, string $suite_slug): JsonResponse
    {
        $suite = Suite::where('suite_slug', $suite_slug)->firstOrFail();

        // Optional: Filter by a date range if the user has already selected rough dates
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query bookings that are Confirmed OR Pending (both block the room)
        $conflicts = $suite->bookings()
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Pending])
            ->select('check_in', 'check_out')
            ->get();

        // Transform into a format easy for a calendar library (e.g., array of ranges)
        $blockedRanges = $conflicts->map(function ($booking) {
            return [
                'start' => $booking->check_in->toDateString(),
                'end' => $booking->check_out->toDateString(),
                'type' => $booking->status->value, // 'confirmed' or 'pending'
            ];
        });

        return response()->json([
            'suite_id' => $suite->id,
            'suite_name' => $suite->name,
            'blocked_ranges' => $blockedRanges,
            'base_price' => $suite->base_price,
        ]);
    }

    /**
     * Create a temporary hold on a suite.
     * This creates a 'pending' booking that expires in 15 minutes.
     *
     * @throws ValidationException
     */
    public function createHold(CreateBookingHoldRequest $request, string $suite_slug, BookingLockingService $lockingService): JsonResponse
    {
        $suite = Suite::where('suite_slug', $suite_slug)->lockForUpdate()->firstOrFail(); // DB level lock too

        $result = $lockingService->withLock($suite_slug, function () use ($request, $suite) {
            $checkIn = Carbon::parse($request['check_in']);
            $checkOut = Carbon::parse($request['check_out']);
            $nights = $checkIn->diffInDays($checkOut);

            if ($nights < 1) {
                throw ValidationException::withMessages(['check_out' => 'Check-out must be at least one night after check-in.']);
            }

            // Double check availability inside the transaction
            if (! $suite->isAvailable($request['check_in'], $request['check_out'])) {
                throw new \RuntimeException('Suite is no longer available.');
            }

            // 2. Calculate Total Price (Simple MVP: Base * Nights)
            // In production, you might add taxes/fees here
            $pricingManager = app(PricingManager::class)
                ->register('base', new BasePriceStrategy())
                ->register('weekend', new WeekendSurgeStrategy())
                ->register('holiday', new HolidaySurgeStrategy());

            $totalPrice = $pricingManager->calculate($booking);
            $booking->update(['total_price' => $totalPrice]);

            // 3. Create the Pending Hold
            $holdDurationMinutes = 15;
            $expiresAt = Carbon::now()->addMinutes($holdDurationMinutes);

            $booking = Booking::create([
                'suite_id' => $suite->id,
                'user_id' => auth()->id(), // Null if guest checkout
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights,
                'total_price' => $totalPrice,
                'status' => BookingStatus::Pending,
                'expires_at' => $expiresAt,
                // Store guest info temporarily if not logged in
                'payment_intent_id' => null,
            ]);

            SuiteHoldCreated::dispatch($booking);

            if ($booking->user_id) {
                Mail::to($booking->user->email)->send(new BookingHoldCreated($booking));
            } else {
                if ($booking->guest_email) {
                    Mail::to($booking->guest_email)->send(new BookingHoldCreated($booking));
                }
            }

            return $booking;
        });

        if (! $result) {
            return response()->json([
                'success' => false,
                'message' => 'Another guest just booked this suite. Please refresh and try again.',
                'retry' => true,
            ], 409);
        }

        return response()->json(['success' => true, 'booking' => $result]);
    }

    /**
     * Confirm the booking (usually after payment success).
     * Updates status from 'pending' to 'confirmed'.
     */
    public function confirm(Request $request, int $booking_id, PaymentManager $paymentManager): JsonResponse
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $booking = Booking::findOrFail($booking_id);

        if ($booking->status !== BookingStatus::Pending) {
            return response()->json(['message' => 'Invalid booking status'], 400);
        }

        if (! $booking->isValidHold()) {
            $booking->update(['status' => BookingStatus::Expired]);

            return response()->json(['message' => 'Hold has expired. Please restart the booking process.'], 410);
        }

        // Verify payment with the abstracted gateway
        $gateway = $paymentManager->driver();

        if (! $gateway->verifyPayment($validated['payment_intent_id'], $booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.',
            ], 402);
        }

        // Payment Successful -> Confirm Booking
        $booking->update([
            'status' => BookingStatus::Confirmed,
            'payment_intent_id' => $validated['payment_intent_id'],
        ]);

        Mail::to($booking->user->email ?? $booking->guest_email)->send(new BookingConfirmed($booking));

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed successfully!',
            'booking' => $booking,
            'gateway_used' => $gateway->getName(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function releaseHold(int $booking_id): JsonResponse
    {
        $booking = Booking::findOrFail($booking_id);

        if ($booking->status !== BookingStatus::Pending) {
            return response()->json(['message' => 'Only pending holds can be released manually.'], 400);
        }

        $booking->update(['status' => BookingStatus::Expired]); // Or 'Cancelled' depending on preference

        // Optional: Notify user that admin released it
        // Mail::to($booking->user->email)->send(new BookingHoldExpired($booking));

        return response()->json(['success' => true, 'message' => 'Hold released. Suite is now available.']);
    }

    public function cancel(int $booking_id): JsonResponse
    {
        $booking = Booking::findOrFail($booking_id);

        if ($booking->status === BookingStatus::Confirmed) {
            // Handle refund logic here if needed
        }

        $booking->update(['status' => BookingStatus::Cancelled]);

        return response()->json(['success' => true, 'message' => 'Booking cancelled.']);
    }
}

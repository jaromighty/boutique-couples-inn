<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Mail\BookingHoldExpired;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:expire-old-bookings')]
#[Description('Expire pending bookings and notify users')]
class ExpireOldBookings extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredBookings = Booking::where('status', BookingStatus::Pending)
            ->where('expires_at', '<', Carbon::now())
            ->get();

        foreach ($expiredBookings as $booking) {
            // Update status
            $booking->update(['status' => BookingStatus::Expired]);

            // Send Email
            $email = $booking->user->email ?? $booking->guest_email;
            if ($email) {
                Mail::to($email)->send(new BookingHoldExpired($booking));
            }

            $this->info("Expired booking #{$booking->id}");
        }

        return 0;
    }
}

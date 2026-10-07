<x-mail::message>
# Reservation Confirmed! 🎉

Congratulations, {{ $booking->user->name ?? $booking->guest_name ?? 'Guest' }}!

Your booking for **{{ $booking->suite->name }}** has been confirmed. We're excited to host your romantic getaway!

## Your Itinerary

| Detail | Information |
|--------|-------------|
| **Suite** | {{ $booking->suite->name }} ({{ ucfirst($booking->suite->tier) }} Tier) |
| **Confirmation #** | #{{ $booking->id }} |
| **Check-in** | {{ $booking->check_in->format('M d, Y') }} after 3:00 PM |
| **Check-out** | {{ $booking->check_out->format('M d, Y') }} by 11:00 AM |
| **Address** | {{ $booking->suite->address ?? 'See your account for address' }} |

<x-mail::button :url="url('/my-bookings')">
    View My Bookings
</x-mail::button>

### Important Notes
* Please arrive after 3:00 PM on your check-in date.
* Early check-in may be available for Diamond tier suites.
* Contact us at {{ config('app.support_email', 'support@' . config('app.domain')) }} if you need assistance.

We look forward to welcoming you!

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>

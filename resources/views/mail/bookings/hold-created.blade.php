<x-mail::message>
# Your Suite is Held!

Hello {{ $booking->user->name ?? $booking->guest_name ?? 'Guest' }},

We have temporarily reserved **{{ $booking->suite->name }}** for you. Thank you for choosing [{{ config('app.name') }}](/).

<div class="alert-box">
    <strong>⏳ Time Sensitive:</strong> This hold expires in <strong>{{ $minutesRemaining }} minutes</strong>.
</div>

## Booking Details

| Detail | Information |
|--------|-------------|
| **Suite** | {{ $booking->suite->name }} ({{ ucfirst($booking->suite->tier) }} Tier) |
| **Check-in** | {{ $booking->check_in->format('M d, Y') }} after 3:00 PM |
| **Check-out** | {{ $booking->check_out->format('M d, Y') }} by 11:00 AM |
| **Nights** | {{ $booking->nights }} |
| **Total** | ${{ number_format($booking->total_price, 2) }} |

<x-mail::button :url="url('/bookings/' . $booking->id . '/pay')">
    Complete Payment Now
</x-mail::button>

If you do not complete payment within the time limit, this suite will be released to other guests. You can always return to [browse available suites](/suites).

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>

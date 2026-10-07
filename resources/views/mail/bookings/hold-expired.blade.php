<x-mail::message>
# Your Hold Has Expired ⚠️

Hello,

The temporary hold on **{{ $booking->suite->name }}** for your selected dates has expired because payment was not completed in time.

## What Happened?

| Detail | Information |
|--------|-------------|
| **Suite** | {{ $booking->suite->name }} |
| **Original Dates** | {{ $booking->check_in->format('M d, Y') }} - {{ $booking->check_out->format('M d, Y') }} |
| **Hold Duration** | 15 Minutes |

Unfortunately, this suite may now be available to other guests. If you still wish to book these dates, please try again immediately.

<x-mail::button :url="url('/suites')">
    Browse Available Suites
</x-mail::button>

We hope to see you soon!

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>

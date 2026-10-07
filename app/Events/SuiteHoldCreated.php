<?php

namespace App\Events;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuiteHoldCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $suiteSlug;

    public Carbon $expiresAt;

    public int $bookingId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Booking $booking,
    ) {
        $this->suiteSlug = $booking->suite->suite_slug;
        $this->expiresAt = $booking->expires_at->toIso8601String();
        $this->bookingId = $booking->id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('suites.'.$this->suiteSlug),
        ];
    }

    public function broadcastAs(): string
    {
        return 'hold.created';
    }
}

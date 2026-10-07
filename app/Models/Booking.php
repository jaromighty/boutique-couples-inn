<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $suite_id
 * @property int $user_id
 * @property string $check_in
 * @property string $check_out
 * @property int $nights
 * @property float $total_price
 * @property string $status
 * @property string $expires_at
 * @property string $payment_intent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['suite_id', 'user_id', 'check_in', 'check_out', 'nights', 'total_price', 'status', 'expires_at', 'payment_intent_id'])]
class Booking extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'expires_at' => 'datetime',
            'status' => BookingStatus::class,
        ];
    }

    /**
     * Mark a booking as expired if the hold time has passed
     */
    public function markAsExpired(): void
    {
        if ($this->status === 'pending' && Carbon::now()->gte($this->expires_at)) {
            $this->update(['status' => 'expired']);
        }
    }

    /**
     * Check if this pending booking is still valid
     */
    public function isValidHold(): bool
    {
        return $this->status === 'pending' && Carbon::now()->lt($this->expires_at);
    }
}

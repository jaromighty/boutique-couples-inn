<?php

namespace App\Models;

use App\Enums\Tier;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $suite_slug
 * @property string $theme_slug
 * @property string $tier
 * @property float $base_price
 * @property int $capacity
 * @property string[] amenities
 * @property string[] $images
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'suite_slug', 'theme_slug', 'tier', 'base_price', 'capacity', 'amenities', 'images', 'is_active'])]
class Suite extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tier' => Tier::class,
            'amenities' => 'array',
            'images' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope to get only active suites
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a suite is available for given dates
     * Returns true if NO active (confirmed OR pending) bookings overlap
     */
    public function isAvailable(string $checkIn, string $checkOut): bool
    {
        $overlap = $this->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();

        return ! $overlap;
    }
}

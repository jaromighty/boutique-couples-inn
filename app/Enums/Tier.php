<?php

namespace App\Enums;

enum Tier: string
{
    case Ruby = 'ruby';
    case Emerald = 'emerald';
    case Diamond = 'diamond';

    public function label(): string
    {
        return match ($this) {
            self::Ruby => 'Ruby',
            self::Emerald => 'Emerald',
            self::Diamond => 'Diamond',
        };
    }

    // Optional: Helper for sorting or color mapping if needed later
    public function sortOrder(): int
    {
        return match ($this) {
            self::Ruby => 1,
            self::Emerald => 2,
            self::Diamond => 3,
        };
    }
}

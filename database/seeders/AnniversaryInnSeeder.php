<?php

namespace Database\Seeders;

use App\Enums\Tier;
use App\Models\Suite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AnniversaryInnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the themed suites data
        // Structure: [name, theme_slug, tier, base_price, capacity, amenities, location_prefix]
        $suites = [
            // --- LOGAN LOCATION ---
            [
                'name' => 'Arabian Nights',
                'theme_slug' => 'arabian-nights',
                'tier' => Tier::Diamond,
                'price' => 249.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'fireplace', 'two_story', 'canopy_bed'],
                'location' => 'logan',
                'desc' => 'An unforgettable journey through exotic lands with a grand canopy bed.',
            ],
            [
                'name' => 'Amazon Rainforest',
                'theme_slug' => 'amazon-rainforest',
                'tier' => Tier::Diamond,
                'price' => 229.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'waterfall', 'rain_shower'],
                'location' => 'logan',
                'desc' => 'Lush jungle vibes with a cascading waterfall tub.',
            ],
            [
                'name' => 'Jesse James Hideout',
                'theme_slug' => 'jesse-james-hideout',
                'tier' => Tier::Diamond,
                'price' => 239.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'rustic_decor', 'balcony'],
                'location' => 'logan',
                'desc' => 'A rugged western adventure with rustic charm.',
            ],
            [
                'name' => 'King Arthur\'s Castle',
                'theme_slug' => 'king-arthur-castle',
                'tier' => Tier::Emerald,
                'price' => 189.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'fireplace', 'stone_walls'],
                'location' => 'logan',
                'desc' => 'Medieval romance with stone walls and knightly decor.',
            ],
            [
                'name' => 'Hawaiian Paradise',
                'theme_slug' => 'hawaiian-paradise',
                'tier' => Tier::Emerald,
                'price' => 179.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'tropical_plants', 'luau_theme'],
                'location' => 'logan',
                'desc' => 'Escape to the islands with tropical prints and a relaxing tub.',
            ],
            [
                'name' => 'Nefertiti\'s Court',
                'theme_slug' => 'nefertitis-court',
                'tier' => Tier::Emerald,
                'price' => 169.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'egyptian_decor'],
                'location' => 'logan',
                'desc' => 'Ancient Egyptian elegance with golden accents.',
            ],
            [
                'name' => 'Playful Romance',
                'theme_slug' => 'playful-romance',
                'tier' => Tier::Ruby,
                'price' => 129.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'heart_shaped'],
                'location' => 'logan',
                'desc' => 'A fun and flirty room perfect for a quick getaway.',
            ],

            // --- SALT LAKE CITY (5TH SOUTH) ---
            [
                'name' => 'Phantom of the Opera',
                'theme_slug' => 'phantom-of-the-opera',
                'tier' => Tier::Diamond,
                'price' => 259.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'chandelier', 'theater_seats', 'mask_decor'],
                'location' => 'slc-5th',
                'desc' => 'Dramatic red velvet and masks, inspired by the classic musical.',
            ],
            [
                'name' => 'French Connection',
                'theme_slug' => 'french-connection',
                'tier' => Tier::Emerald,
                'price' => 199.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'parisian_cafe', 'bistro_table'],
                'location' => 'slc-5th',
                'desc' => 'Romantic Parisian streets captured in a suite.',
            ],
            [
                'name' => 'Venice',
                'theme_slug' => 'venice',
                'tier' => Tier::Emerald,
                'price' => 189.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'canal_view', 'gondola_decor'],
                'location' => 'slc-5th',
                'desc' => 'Romance on the water with Venetian canals.',
            ],
            [
                'name' => 'Lighthouse',
                'theme_slug' => 'lighthouse',
                'tier' => Tier::Emerald,
                'price' => 179.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'ocean_theme', 'beach_decor'],
                'location' => 'slc-5th',
                'desc' => 'Coastal charm with a guiding light.',
            ],
            [
                'name' => 'English Countryside',
                'theme_slug' => 'english-countryside',
                'tier' => Tier::Ruby,
                'price' => 139.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'floral_prints', 'tea_time'],
                'location' => 'slc-5th',
                'desc' => 'A secret garden feel with floral elegance.',
            ],
            [
                'name' => 'Log Cabin',
                'theme_slug' => 'log-cabin',
                'tier' => Tier::Ruby,
                'price' => 129.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'wood_stove', 'rustic'],
                'location' => 'slc-5th',
                'desc' => 'Cozy cabin vibes in the heart of the city.',
            ],
            [
                'name' => 'Country Garden',
                'theme_slug' => 'country-garden',
                'tier' => Tier::Ruby,
                'price' => 119.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'flowers', 'greenhouse'],
                'location' => 'slc-5th',
                'desc' => 'Surrounded by blooming flowers and greenery.',
            ],

            // --- BOISE LOCATION (Sample) ---
            [
                'name' => 'Rio Grande',
                'theme_slug' => 'rio-grande',
                'tier' => Tier::Diamond,
                'price' => 219.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'train_theme', 'vintage'],
                'location' => 'boise',
                'desc' => 'All aboard for a romantic train journey.',
            ],
            [
                'name' => 'New York, New York',
                'theme_slug' => 'new-york-city',
                'tier' => Tier::Emerald,
                'price' => 199.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'skyline', 'city_lights'],
                'location' => 'boise',
                'desc' => 'The energy of the Big Apple in your suite.',
            ],
            [
                'name' => 'Zen Garden',
                'theme_slug' => 'zen-garden',
                'tier' => Tier::Emerald,
                'price' => 189.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'meditation', 'bamboo'],
                'location' => 'boise',
                'desc' => 'Peaceful tranquility with Asian-inspired design.',
            ],
            [
                'name' => 'Mysteries of Egypt',
                'theme_slug' => 'mysteries-of-egypt',
                'tier' => Tier::Diamond,
                'price' => 239.00,
                'capacity' => 2,
                'amenities' => ['jetted_tub', 'pharaoh_decor', 'gold_accents'],
                'location' => 'boise',
                'desc' => 'Discover ancient secrets in luxury.',
            ],
        ];

        foreach ($suites as $suiteData) {
            // Generate a unique slug based on location and theme
            $slug = Str::slug("{$suiteData['location']}-{$suiteData['theme_slug']}");

            // Generate placeholder images (using a service like unsplash source for demo)
            // In production, you would upload real files.
            $images = [
                "https://placehold.co/800x600/6d4aff/white?text={$suiteData['name']}",
                'https://placehold.co/800x600/e2e8f0/64748b?text=Bath',
                'https://placehold.co/800x600/f1f5f9/475569?text=Interior',
            ];

            Suite::create([
                'name' => $suiteData['name'],
                'suite_slug' => $slug,
                'theme_slug' => $suiteData['theme_slug'],
                'tier' => $suiteData['tier'],
                'base_price' => $suiteData['price'],
                'capacity' => $suiteData['capacity'],
                'amenities' => $suiteData['amenities'],
                'images' => $images,
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Anniversary Inn themed suites seeded successfully!');
    }
}

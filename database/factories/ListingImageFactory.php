<?php

namespace Database\Factories;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ListingImage>
 */
class ListingImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Dev seed images resolve to a public placeholder so listings render.
        return [
            'listing_id' => Listing::factory(),
            'path' => 'https://picsum.photos/seed/'.fake()->unique()->uuid().'/800/600',
            'position' => 0,
        ];
    }
}

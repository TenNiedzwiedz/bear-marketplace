<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Database\Seeder;

/**
 * Development seed data only. Gives each listing a small ordered gallery.
 */
class ListingImageSeeder extends Seeder
{
    public function run(): void
    {
        Listing::query()->each(function (Listing $listing): void {
            $count = random_int(1, 4);

            for ($position = 0; $position < $count; $position++) {
                ListingImage::factory()->for($listing)->create([
                    'position' => $position,
                ]);
            }
        });
    }
}

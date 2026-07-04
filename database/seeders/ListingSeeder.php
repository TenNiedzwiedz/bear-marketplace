<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Development seed data only. Listings go to leaf categories only, mostly active
 * with a few in other statuses so moderation and filters have something to show.
 */
class ListingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $leafCategories = Category::query()->whereDoesntHave('children')->get();

        if ($users->isEmpty() || $leafCategories->isEmpty()) {
            return;
        }

        Listing::factory()->count(40)->recycle($users)->recycle($leafCategories)->create();
        Listing::factory()->count(5)->draft()->recycle($users)->recycle($leafCategories)->create();
        Listing::factory()->count(3)->ended()->recycle($users)->recycle($leafCategories)->create();
        Listing::factory()->count(2)->hidden()->recycle($users)->recycle($leafCategories)->create();
    }
}

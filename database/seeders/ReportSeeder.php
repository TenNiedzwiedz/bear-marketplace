<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Development seed data only. A handful of pending reports — most against
 * listings, a few against user accounts — so the moderation queue is not empty.
 */
class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $reporters = User::query()->where('is_admin', false)->get();
        $listings = Listing::query()->published()->get();

        if ($reporters->isEmpty() || $listings->isEmpty()) {
            return;
        }

        foreach ($listings->random(min(8, $listings->count())) as $listing) {
            Report::factory()->against($listing)->recycle($reporters)->create();
        }

        foreach ($reporters->random(min(3, $reporters->count())) as $reported) {
            Report::factory()->against($reported)->recycle($reporters)->create();
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Development seed data only. A handful of pending reports on active listings so
 * the moderation queue is not empty.
 */
class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $listings = Listing::query()->published()->get();

        if ($users->isEmpty() || $listings->isEmpty()) {
            return;
        }

        Report::factory()->count(10)->recycle($users)->recycle($listings)->create();
    }
}

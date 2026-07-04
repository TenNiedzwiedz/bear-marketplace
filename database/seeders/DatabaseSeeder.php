<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with development data.
     *
     * These seeders create fake accounts, listings and reports for local work
     * only — they must never run against production.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('Development seeders skipped: application is in production.');

            return;
        }

        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            CompanyProfileSeeder::class,
            ListingSeeder::class,
            ListingImageSeeder::class,
            ReportSeeder::class,
        ]);
    }
}

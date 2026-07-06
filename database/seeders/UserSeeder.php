<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Development seed data only. Assumes a fresh database (migrate:fresh --seed).
 * Every account uses the password "password".
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Known logins for manual testing.
        User::factory()->admin()->create([
            'name' => 'Admin Bear',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Jan Kowalski',
            'email' => 'test@example.com',
        ]);

        User::factory()->company()->create([
            'name' => 'Meble Kowalski',
            'email' => 'firma@example.com',
        ]);

        // Random population — profiles for company accounts are added by CompanyProfileSeeder.
        User::factory()->count(15)->create();
        User::factory()->count(6)->company()->create();
    }
}

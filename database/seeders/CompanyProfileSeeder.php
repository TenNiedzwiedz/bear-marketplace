<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Development seed data only. Gives every company account the profile the
 * business rules require, without touching private accounts.
 */
class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        User::query()
            ->where('account_type', AccountType::Company)
            ->whereDoesntHave('companyProfile')
            ->each(function (User $user): void {
                CompanyProfile::factory()->for($user)->create([
                    'company_name' => $user->name,
                ]);
            });
    }
}

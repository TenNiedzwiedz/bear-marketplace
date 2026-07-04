<?php

namespace Database\Factories;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyProfile>
 */
class CompanyProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->company(),
            'company_name' => fake()->company(),
            'tax_id' => (string) fake()->numerify('##########'), // 10-digit NIP
            'address_line' => fake()->streetAddress(),
            'postal_code' => fake()->numerify('##-###'),
            'city' => fake()->city(),
        ];
    }
}

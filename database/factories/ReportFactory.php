<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory(),
            'user_id' => User::factory(),
            'reason' => fake()->randomElement(['spam', 'zabronione', 'oszustwo', 'obraźliwe', 'inne']),
            'description' => fake()->boolean(60) ? fake()->sentence() : null,
            'status' => ReportStatus::Pending,
        ];
    }
}

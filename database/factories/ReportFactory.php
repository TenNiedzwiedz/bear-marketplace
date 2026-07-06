<?php

namespace Database\Factories;

use App\Enums\ReportReason;
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
            // Defaults to a listing report; override with ->against($model).
            'reportable_type' => 'listing',
            'reportable_id' => Listing::factory(),
            'user_id' => User::factory(),
            'reason' => fake()->randomElement(ReportReason::cases()),
            'description' => fake()->boolean(60) ? fake()->sentence() : null,
            'status' => ReportStatus::Pending,
        ];
    }

    /**
     * Target a specific reportable model (a Listing or a User).
     */
    public function against(Listing|User $model): static
    {
        return $this->state(fn (array $attributes) => [
            'reportable_type' => $model->getMorphClass(),
            'reportable_id' => $model->getKey(),
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Enums\ListingStatus;
use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hasPrice = fake()->boolean(85);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => Str::ucfirst(fake()->words(fake()->numberBetween(3, 6), true)),
            'description' => fake()->paragraphs(fake()->numberBetween(2, 4), true),
            'price' => $hasPrice ? fake()->randomFloat(2, 20, 6000) : null,
            'currency' => 'PLN',
            'is_negotiable' => fake()->boolean(40),
            'location' => fake()->city(),
            'status' => ListingStatus::Active,
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingStatus::Ended,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingStatus::Hidden,
        ]);
    }
}

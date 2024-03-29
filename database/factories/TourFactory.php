<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->text(20),
            'starting_date' => now(),
            'ending_date' => now()->addDays(rand(1, 10)),
            'price' => $this->faker->randomFloat(2, 10, 999),
        ];
    }
}

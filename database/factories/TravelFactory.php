<?php

namespace Database\Factories;

use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Travel>
 */
class TravelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_public' => $this->faker->boolean,
            'name' => $this->faker->text(20),
            'description' => $this->faker->text,
            'number_of_days' => $this->faker->numberBetween(1, 10),
        ];
    }
}

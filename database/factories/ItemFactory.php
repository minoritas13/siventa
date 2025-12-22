<?php

namespace Database\Factories;

use App\Models\Categories;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'category_id' => Categories::factory(),
            'code' => fake()->unique()->bothify('INV-###'),
            'name' => fake()->words(2, true),
            'photo' => null,
            'stock' => fake()->numberBetween(1, 50),
            'condition' => fake()->randomElement(['baik', 'rusak ringan']),
            'description' => fake()->sentence(),
        ];
    }
}

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
        $tanggalPerolehan = $this->faker->dateTimeBetween('-5 years', 'now');

        return [
            'category_id' => Categories::inRandomOrder()->first()->id,
            'code' => $this->faker->unique()->bothify('INV-###'),
            'name' => $this->faker->words(2, true),
            'photo' => null,

            'stock' => $this->faker->numberBetween(1, 50),
            'condition' => $this->faker->randomElement(['baik', 'rusak ringan']),
            'description' => $this->faker->sentence(),

            'tanggal_perolehan' => $tanggalPerolehan,
            'nilai_perolehan' => $this->faker->numberBetween(1_000_000, 50_000_000),
            'umur_barang' => now()->diffInYears($tanggalPerolehan),
        ];
    }
}

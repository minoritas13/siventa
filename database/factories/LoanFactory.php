<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'user_id' => User::inRandomOrder()->value('id'),
            'loan_date' => now(),
            'return_date' => null,
            'status' => fake()->randomElement(['dipinjam', 'menunggu','ditolak','dikembalikan','terlambat']),
            'note' => fake()->sentence(),
        ];
    }
}

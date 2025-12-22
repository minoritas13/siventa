<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LoanItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'loan_id' => Loan::factory(),
            'item_id' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 3),
        ];
    }
}

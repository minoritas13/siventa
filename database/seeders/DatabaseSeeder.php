<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use App\Models\LoanItem;
use App\Models\Categories;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory(5)->create();
        $this->call(CategorySeeder::class);
        Item::factory(10)->create();

        // 2. Buat loan + loan items (relasi benar)
        Loan::factory()
            ->count(5)
            ->has(
                LoanItem::factory()->count(2),
                'loanItems'
            )
            ->create();

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categories;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Peralatan Komputer'],
            ['name' => 'Mubleair'],
            ['name' => 'Bangunan'],
            ['name' => 'Peralatan Foto'],
            ['name' => 'Inventaris Rumah Dinas'],
            ['name' => 'Tanah'],
            ['name' => 'Bangunan'],
        ];

        foreach ($categories as $category) {
            Categories::firstOrCreate($category);
        }
    }
}

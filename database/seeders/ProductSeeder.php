<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $productName = $faker->words(2, true); 
            $productPrice = $faker->randomFloat(2, 5, 500); 

            Product::create([
                'name'  => ucfirst($productName),
                'price' => $productPrice,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

final class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::factory()
            ->count(10000)
            ->create();
    }
}

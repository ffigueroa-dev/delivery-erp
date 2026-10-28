<?php

namespace App\Modules\Product\Database\Seeders;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory()->count(10)->create();
    }
}

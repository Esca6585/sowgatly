<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\Image;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::factory()->has(Image::factory()->count(5))->count(50)->create();
    }
}
<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'discount' => $this->faker->randomFloat(2, 0, 50),
            'attributes' => json_encode(['color' => 'red', 'size' => 'medium']),
            'code' => $this->faker->unique()->ean8,
            'category_id' => Category::factory(),
            'shop_id' => Shop::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
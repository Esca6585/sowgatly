<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'discount' => $this->faker->optional()->numberBetween(5, 50),
            'description' => $this->faker->paragraph(),
            'gender' => $this->faker->randomElement(['Men', 'Women', 'Children']),
            'sizes' => json_encode($this->faker->randomElements(['42', '43', '44', '45', '46', '47', '48', '49', '50'], $this->faker->numberBetween(1, 5))),
            'separated_sizes' => json_encode($this->faker->randomElements(['S', 'M', 'L', 'XL', 'XXL'], $this->faker->numberBetween(1, 5))),
            'color' => $this->faker->colorName,
            'manufacturer' => $this->faker->country,
            'width' => $this->faker->randomFloat(2, 10, 100),
            'height' => $this->faker->randomFloat(2, 10, 100),
            'weight' => $this->faker->numberBetween(100, 5000),
            'production_time' => $this->faker->numberBetween(60, 1440),
            'min_order' => $this->faker->numberBetween(1, 10),
            'seller_status' => $this->faker->boolean,
            'status' => $this->faker->boolean,
            'shop_id' => Shop::factory(),
            'category_id' => Category::factory(),
            'brand_ids' => function () {
                return Brand::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->pluck('id')->toArray();
            },
        ];
    }
}
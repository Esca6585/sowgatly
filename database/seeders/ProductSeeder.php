<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Image;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Brand;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Ensure we have some categories, shops, and brands
        $categoryIds = Category::pluck('id')->toArray();
        $shopIds = Shop::pluck('id')->toArray();
        $brandIds = Brand::pluck('id')->toArray();

        if (empty($categoryIds)) {
            for ($i = 0; $i < 5; $i++) {
                $categoryIds[] = Category::create(['name' => $faker->word])->id;
            }
        }

        if (empty($shopIds)) {
            for ($i = 0; $i < 5; $i++) {
                $shopIds[] = Shop::create(['name' => $faker->company])->id;
            }
        }

        if (empty($brandIds)) {
            $this->call(BrandSeeder::class);
            $brandIds = Brand::pluck('id')->toArray();
        }

        $brands = Brand::all();

        // Create products for each brand
        foreach ($brands as $brand) {
            for ($i = 0; $i < 10; $i++) { // 10 products per brand
                $product = Product::create([
                    'name' => $faker->words(3, true),
                    'price' => $faker->randomFloat(2, 50, 500),
                    'discount' => $faker->numberBetween(0, 30),
                    'description' => $faker->paragraph,
                    'gender' => $faker->randomElement(['Men', 'Women', 'Children']),
                    'sizes' => json_encode($faker->randomElements(['42', '43', '44', '45', '46', '47', '48', '49', '50'], $faker->numberBetween(1, 5))),
                    'separated_sizes' => json_encode($faker->randomElements(['S', 'M', 'L', 'XL', 'XXL'], $faker->numberBetween(1, 5))),
                    'color' => $faker->colorName,
                    'manufacturer' => $faker->country,
                    'width' => $faker->randomFloat(2, 10, 100),
                    'height' => $faker->randomFloat(2, 10, 100),
                    'weight' => $faker->randomFloat(2, 100, 5000), // in grams
                    'production_time' => $faker->numberBetween(1, 60), // in minutes
                    'min_order' => $faker->numberBetween(1, 10),
                    'seller_status' => $faker->boolean,
                    'status' => $faker->boolean,
                    'shop_id' => $faker->randomElement($shopIds),
                    'brand_id' => $brand->id,
                    'category_id' => $faker->randomElement($categoryIds),
                    'featured' => $faker->boolean(20), // 20% chance of being featured
                ]);

                // Create 1-5 images for each product
                $imageCount = $faker->numberBetween(1, 5);
                for ($j = 0; $j < $imageCount; $j++) {
                    Image::create([
                        'image' => $faker->imageUrl(640, 480, 'products', true),
                        'product_id' => $product->id
                    ]);
                }
            }
        }
    }
}
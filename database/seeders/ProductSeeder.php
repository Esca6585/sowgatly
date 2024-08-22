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

        // Ensure we have some categories
        $categoryIds = Category::pluck('id')->toArray();
        $shopIds = Shop::pluck('id')->toArray();
        $brandIds = Brand::pluck('id')->toArray();

        if (empty($categoryIds)) {
            // Create some categories if none exist
            for ($i = 0; $i < 5; $i++) {
                $categoryIds[] = Category::create(['name' => $faker->word])->id;
            }
        }

        if (empty($shopIds)) {
            // Create some shops if none exist
            for ($i = 0; $i < 5; $i++) {
                $shopIds[] = Shop::create(['name' => $faker->company])->id;
            }
        }

        if (empty($brandIds)) {
            // Run the BrandSeeder if no brands exist
            $this->call(BrandSeeder::class);
            $brandIds = Brand::pluck('id')->toArray();
        }

        $brands = Brand::all();

        // Create 50 products
        foreach ($brands as $brand) {
            for ($i = 0; $i < 10; $i++) { // 10 products per brand
                $product = Product::create([
                    'name' => $faker->words(3, true),
                    'description' => $faker->paragraph,
                    'price' => $faker->numberBetween(50, 500),
                    'discount' => $faker->numberBetween(0, 30),
                    'attributes' => json_encode([
                        'color' => $faker->colorName,
                        'size' => $faker->randomElement(['S', 'M', 'L', 'XL']),
                        'weight' => $faker->numberBetween(100, 1000) . 'g'
                    ]),
                    'code' => Str::upper(Str::random(8)),
                    'status' => $faker->boolean(80), // 80% chance of being active
                    'category_id' => $faker->randomElement($categoryIds),
                    'shop_id' => $faker->randomElement($shopIds),
                    'brand_id' => $brand->id,
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